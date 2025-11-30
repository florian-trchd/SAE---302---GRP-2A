<?php
/*
 Minimal index.php
 This file only serves a single-page application for a very small UI.
 No DB, no server-side logic: PHP is present only to serve the page if you are running
 under a PHP-capable web server. To use, place this file in a PHP-enabled server root.
*/
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Interface - Recherche de failles (IP)</title>

    <!--
      Simple, clean embedded CSS for the single-page tool.
      You can extract this to a separate .css file if you prefer.
    -->
    <style>
      :root{--bg:#f7fafc;--card:#ffffff;--accent:#0ea5a0;--muted:#6b7280;--danger:#ef4444}
      html,body{height:100%;margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;background:var(--bg);color:#0f172a}
      .wrap{max-width:1100px;margin:32px auto;padding:24px}
      header{display:flex;align-items:center;gap:16px;margin-bottom:18px}
      h1{font-size:20px;margin:0}
      p.lead{margin:2px 0 0;color:var(--muted)}

      .grid{display:grid;grid-template-columns:1fr 360px;gap:18px;align-items:start}

      .card{background:var(--card);border-radius:12px;padding:16px;box-shadow:0 6px 18px rgba(12,18,26,0.06)}

      form.search {display:flex;gap:10px;margin-top:12px}
      input[type="text"].ip{flex:1;padding:10px 12px;border-radius:8px;border:1px solid #e6edf3;font-size:14px}
      button.primary{background:var(--accent);border:0;color:white;padding:10px 14px;border-radius:8px;cursor:pointer}
      button.primary:disabled{opacity:0.6;cursor:default}

      .hint{font-size:13px;color:var(--muted);margin-top:8px}

      table{width:100%;border-collapse:collapse;margin-top:12px}
      th,td{padding:8px 10px;text-align:left;border-bottom:1px solid #eef2f5;font-size:14px}
      tr.clickable{cursor:pointer}
      tr.clickable:hover{background:linear-gradient(90deg, rgba(14,165,160,0.06), transparent)}

      .small{font-size:13px;color:var(--muted)}

      .status-low{color:green;font-weight:600}
      .status-high{color:var(--danger);font-weight:700}

      .empty{color:var(--muted);padding:12px;border-radius:8px;background:linear-gradient(180deg, #fbfeff, #ffffff);border:1px dashed #e6edf3}

      /* Mobile adjustments */
      @media(max-width:880px){
        .grid{grid-template-columns:1fr;}
        header{flex-direction:column;align-items:flex-start}
      }
    </style>
  </head>
  <body>
    <div class="wrap">
      <header>
        <div>
          <h1>Recherche de failles par adresse IP</h1>
          <p class="lead">Saisissez une adresse IP et cliquez sur Rechercher — l'interface appellera une API locale et affichera le résultat.</p>
        </div>
      </header>

      <div class="card">
        <!-- Search box + instructions -->
        <strong>Rechercher une adresse IP</strong>
        <form class="search" id="searchForm" onsubmit="event.preventDefault(); searchByIP();">
          <!-- Input where user enters an IP address -->
          <input class="ip" id="ipInput" type="text" placeholder="Ex. 192.168.1.10" aria-label="Adresse IP" />
          <button class="primary" id="searchBtn" type="submit">Rechercher</button>
        </form>

        <div class="hint">Astuce : si l'API n'est pas encore disponible, l'interface utilise un jeu de données JSON de test défini en local.</div>
      </div>

      <div style="height:18px"></div>

      <div class="grid">
        <!-- Left column: results and table -->
        <div>
          <div class="card" id="resultsCard">
            <div style="display:flex;justify-content:space-between;align-items:center">
              <strong>Résultats</strong>
              <div class="small" id="resultsCount">Aucun résultat</div>
            </div>

            <!-- Container where search results or 'all' results are inserted -->
            <div id="results" style="margin-top:12px">
              <div class="empty">Aucune recherche lancée — utilisez le champ ci-dessus ou cliquez sur "Toutes les failles" ci-contre.</div>
            </div>

          </div>

          <!-- Toggle / debug area for instructions about hooking API later -->
          <div class="card" style="margin-top:12px">
            <strong>Notes développeur</strong>
            <ul class="small" style="margin:8px 0 0 18px;">
              <li>URL d'API actuelle (par défaut) : <code>http://localhost/api/failles</code></li>
              <li>Endpoint pour toutes les failles : <code>/api/failles/all</code></li>
              <li>Endpoint détail : <code>/api/failles/id?id=ID</code></li>
            </ul>
            <div class="small" style="margin-top:10px;color:var(--muted)">
              Pour brancher l'API réelle plus tard : remplacez la variable <code>API_BASE</code> dans le bloc JavaScript ci‑dessous par l'URL réelle de votre API.
            </div>
          </div>
        </div>

        <!-- Right column: all vulnerabilities + detail panel -->
        <div style="display:flex;flex-direction:column;gap:12px">
          <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center">
              <strong>Toutes les failles</strong>
              <button id="btnAll" class="primary" style="background:#2563eb">Charger</button>
            </div>

            <!-- 'All' list will be populated here -->
            <div id="allVulns" style="margin-top:12px"></div>
          </div>

          <div class="card" id="detailCard">
            <strong>Détail de la faille</strong>
            <div id="detail" style="margin-top:12px">
              <div class="small">Cliquez sur une faille dans le tableau ou la liste pour voir ses détails ici.</div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- JavaScript: fetch(), fallback JSON, render helpers, and comments -->
    <script>
      /*
        IMPORTANT: If your real API uses a different host/path or requires authentication,
        update the variable below. The page expects three endpoints:

        1) GET ${API_BASE}?ip=IP_ADDRESS  -> list of vulnerabilities for a given IP
        2) GET ${API_BASE}/all           -> list of all vulnerabilities
        3) GET ${API_BASE}/id?id=ID      -> details about a single vulnerability

        Example real URLs (adjust to your API):
          http://localhost/api/failles?ip=192.168.1.10
          http://localhost/api/failles/all
          http://localhost/api/failles/id?id=1

        All fetch() calls have a try/catch and will fall back to a small local
        example JSON when the API cannot be reached. Replace sampleData below with
        more representative test data as needed.
      */

      // Safe place to override the API base path later
      const API_BASE = 'http://localhost/api/failles';

      // Sample JSON - used when the live API can't be reached (development fallback)
      const sampleData = [
        { "id": 1, "ip": "192.168.1.10", "port": 22, "type": "SSH ouvert", "risque": "faible", "description": "Serveur SSH ouvert - mot de passe par défaut possible." },
        { "id": 2, "ip": "192.168.1.10", "port": 23, "type": "Telnet non sécurisé", "risque": "élevé", "description": "Service Telnet actif sans chiffrement, accès potentiellement non sécurisé." },
        { "id": 3, "ip": "10.0.0.42", "port": 80, "type": "HTTP sans TLS", "risque": "moyen", "description": "HTTP sans TLS — communication non chiffrée." }
      ];

      // Helper: show errors to user (simple method)
      function showError(message) {
        const results = document.getElementById('results');
        results.innerHTML = `<div class="empty" style="border-color:#fce7e7;color:#7f1d1d">Erreur : ${message}</div>`;
        document.getElementById('resultsCount').innerText = 'Erreur';
      }

      // Helper: render a list or table of vulnerabilities
      function renderVulns(vulns, containerId = 'results') {
        const container = document.getElementById(containerId);

        if (!Array.isArray(vulns) || vulns.length === 0) {
          container.innerHTML = '<div class="empty">Aucune faille trouvée.</div>';
          document.getElementById('resultsCount').innerText = '0 résultats';
          return;
        }

        // Build table header
        let html = '<table>' +
          '<thead><tr><th>ID</th><th>IP</th><th>Port</th><th>Type</th><th>Risque</th></tr></thead><tbody>';

        for (const v of vulns) {
          const riskClass = (v.risque && v.risque.toLowerCase().includes('élev')) ? 'status-high' : 'status-low';
          // Each row is clickable to show details; we attach data-id to the tr
          html += `<tr class="clickable" data-id="${v.id}">` +
            `<td>#${v.id}</td>` +
            `<td>${v.ip ?? '-'}</td>` +
            `<td>${v.port ?? '-'}</td>` +
            `<td>${v.type ?? '-'}</td>` +
            `<td class="${riskClass}">${v.risque ?? '-'}</td>` +
            `</tr>`;
        }

        html += '</tbody></table>';
        container.innerHTML = html;

        // Update count if this is the main results container
        if (containerId === 'results') {
          document.getElementById('resultsCount').innerText = `${vulns.length} résultat(s)`;
        }

        // Attach click handlers to rows
        container.querySelectorAll('tr.clickable').forEach(row => {
          row.addEventListener('click', () => {
            const id = row.getAttribute('data-id');
            if (id) loadVulnDetails(id);
          });
        });
      }

      // Fetch all vulnerabilities from API (/all) with fallback to sampleData
      async function loadAllVulns() {
        const out = document.getElementById('allVulns');
        out.innerHTML = '<div class="small">Chargement...</div>';
        try {
          const res = await fetch(`${API_BASE}/all`, { method: 'GET' });
          if (!res.ok) throw new Error('Réponse API non ok');
          const data = await res.json();
          // Render in the small panel as a list
          out.innerHTML = '<ul style="padding-left:18px;margin:8px 0 0 0;">' +
            data.map(v => `<li style="margin-bottom:6px;cursor:pointer;color:#0f172a" onclick="loadVulnDetails(${v.id})">[#${v.id}] ${v.ip} - ${v.type} <span style=\"color:var(--muted)\">(${v.risque})</span></li>`).join('') +
            '</ul>';
        } catch (err) {
          // API failed -> fallback to sampleData and explain in console and UI
          console.warn('loadAllVulns: API unreachable, using sampleData', err);
          out.innerHTML = '<div class="small" style="color:var(--muted)">API indisponible — utilisation de données de test</div>';
          out.innerHTML += '<ul style="padding-left:18px;margin:8px 0 0 0;">' +
            sampleData.map(v => `<li style="margin-bottom:6px;cursor:pointer;color:#0f172a" onclick="loadVulnDetails(${v.id})">[#${v.id}] ${v.ip} - ${v.type} <span style=\"color:var(--muted)\">(${v.risque})</span></li>`).join('') +
            '</ul>';
        }
      }

      // Search vulnerabilities by IP using the API with fallback
      async function searchByIP() {
        const ip = document.getElementById('ipInput').value.trim();
        if (!ip) {
          showError('Adresse IP vide — veuillez entrer une adresse IP valide.');
          return;
        }

        // Indicate loading state
        const results = document.getElementById('results');
        results.innerHTML = '<div class="small">Recherche en cours…</div>';

        try {
          const res = await fetch(`${API_BASE}?ip=${encodeURIComponent(ip)}`, { method: 'GET' });

          // If the endpoint returns 404/500 we treat it as an error and fallback
          if (!res.ok) throw new Error('API non disponible');

          const data = await res.json();

          // Render the results into the results container
          renderVulns(data, 'results');

        } catch (err) {
          // Network or API error: fallback to local sampleData filtered by IP
          console.warn('searchByIP: fetch failed, using sample fallback', err);
          const simulated = sampleData.filter(x => x.ip === ip);

          // Inform the user that the API was not reachable and show fallback
          if (simulated.length > 0) {
            renderVulns(simulated, 'results');
            const hint = document.createElement('div');
            hint.className = 'small';
            hint.style.color = 'var(--muted)';
            hint.textContent = 'Remarque : l’API est inaccessible — affichage basé sur le jeu de test local.';
            results.prepend(hint);
          } else {
            showError('Aucune donnée trouvée — l’API est peut-être hors ligne et le jeu de test ne contient pas cette IP.');
          }
        }
      }

      // Load vulnerability details by ID (API call) with fallback to sampleData
      async function loadVulnDetails(id) {
        const panel = document.getElementById('detail');
        panel.innerHTML = '<div class="small">Chargement du détail…</div>';

        try {
          const res = await fetch(`${API_BASE}/id?id=${encodeURIComponent(id)}`);
          if (!res.ok) throw new Error('Erreur API détails');
          const data = await res.json();

          // If the API returns an array or object, handle both
          const detail = Array.isArray(data) ? data[0] : data;

          panel.innerHTML = renderDetailHTML(detail);
        } catch (err) {
          console.warn('loadVulnDetails: API detail failed, using fallback', err);
          // fallback: find in sampleData
          const local = sampleData.find(x => String(x.id) === String(id));
          if (local) {
            panel.innerHTML = renderDetailHTML(local) + '<div class="small" style="color:var(--muted);margin-top:8px">Données fournies localement (API hors ligne)</div>';
          } else {
            panel.innerHTML = '<div class="empty">Détail introuvable pour l’ID ' + id + '.</div>';
          }
        }
      }

      // Helper to build nice detail markup for a vulnerability object
      function renderDetailHTML(detail) {
        if (!detail) return '<div class="empty">Aucun détail disponible.</div>';

        // Customize this HTML to present more fields or a nicer layout
        return `
          <div style="padding:8px 0">
            <div style="display:flex;justify-content:space-between;align-items:center">
              <strong>Faille #${detail.id}</strong>
              <span class="small">${detail.ip ?? 'IP inconnue'}</span>
            </div>
            <div style="margin-top:10px;color:var(--muted)"><strong>Type:</strong> ${detail.type ?? '-'}</div>
            <div style="margin-top:6px;color:var(--muted)"><strong>Port:</strong> ${detail.port ?? '-'}</div>
            <div style="margin-top:6px;color:var(--muted)"><strong>Risque:</strong> ${detail.risque ?? '-'}</div>
            <div style="margin-top:10px;background:#f8fafc;border-radius:8px;padding:10px;border:1px solid #eef2f5">
              <div style="font-size:13px;color:#0f172a"><strong>Description</strong></div>
              <div class="small" style="margin-top:6px;color:var(--muted)">${detail.description ?? 'Aucune description fournie par l’API.'}</div>
            </div>
          </div>
        `;
      }

      // Attach event handlers and auto-load the 'all' list on start
      function init() {
        document.getElementById('btnAll').addEventListener('click', loadAllVulns);
        document.getElementById('searchBtn').addEventListener('click', () => searchByIP());

        // When the page loads, automatically try to load /all so that users see data right away
        loadAllVulns();
      }

      // Run init when DOM is ready
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
      } else {
        init();
      }

      /*
        ---- How to hook real API later (quick instructions) ----

        1) Deploy real API and verify endpoints are reachable from the user's browser (CORS allowed if API and page are on different origins)
        2) Update the top constant `API_BASE` to the real path, for example:
             const API_BASE = 'https://api.example.com/failles'

        3) If endpoints are different shape or require headers (auth tokens), adjust fetch() calls in loadAllVulns(), searchByIP(), and loadVulnDetails(). Example adding headers:
             await fetch(url, {headers: { 'Authorization': 'Bearer <TOKEN>' }})

        4) If API returns nested payloads (e.g. { data: [...] }), update parsing code to extract the right field.

        5) Remove/replace sampleData after your API is stable. The fallback JSON is only for local development.
      */
    </script>

  <!-- Code injected by live-server -->
<script>
	// <![CDATA[  <-- For SVG support
	if ('WebSocket' in window) {
		(function () {
			function refreshCSS() {
				var sheets = [].slice.call(document.getElementsByTagName("link"));
				var head = document.getElementsByTagName("head")[0];
				for (var i = 0; i < sheets.length; ++i) {
					var elem = sheets[i];
					var parent = elem.parentElement || head;
					parent.removeChild(elem);
					var rel = elem.rel;
					if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
						var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
						elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
					}
					parent.appendChild(elem);
				}
			}
			var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
			var address = protocol + window.location.host + window.location.pathname + '/ws';
			var socket = new WebSocket(address);
			socket.onmessage = function (msg) {
				if (msg.data == 'reload') window.location.reload();
				else if (msg.data == 'refreshcss') refreshCSS();
			};
			if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
				console.log('Live reload enabled.');
				sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
			}
		})();
	}
	else {
		console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
	}
	// ]]>
</script>
</body>
</html>

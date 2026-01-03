<?php
/* Simple front-end UI for viewing vulnerabilities from the SAE302 DB */
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Interface - Recherche de failles (IP)</title>

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
          <p class="lead">Saisissez une adresse IP et cliquez sur Rechercher — l'interface appellera l’API locale (vos tables scanner_runs / findings) et affichera les résultats.</p>
        </div>
      </header>

      <div class="card">
        <strong>Rechercher une adresse IP</strong>
        <form class="search" id="searchForm" onsubmit="event.preventDefault(); searchByIP();">
          <input class="ip" id="ipInput" type="text" placeholder="Ex. 127.0.0.1" aria-label="Adresse IP" />
          <button class="primary" id="searchBtn" type="submit">Rechercher</button>
        </form>

        <div class="hint">
          L’API renvoie les données depuis la table <code>findings</code> (champ <code>target</code> = IP).
        </div>
      </div>

      <div style="height:18px"></div>

      <div class="grid">
        <div>
          <div class="card" id="resultsCard">
            <div style="display:flex;justify-content:space-between;align-items:center">
              <strong>Résultats</strong>
              <div class="small" id="resultsCount">Aucun résultat</div>
            </div>

            <div id="results" style="margin-top:12px">
              <div class="empty">Aucune recherche lancée — utilisez le champ ci-dessus ou cliquez sur "Toutes les failles".</div>
            </div>
          </div>


        <div style="display:flex;flex-direction:column;gap:12px">
          <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center">
              <strong>Toutes les failles</strong>
              <button id="btnAll" class="primary" style="background:#2563eb">Charger</button>
            </div>

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

    <script>
      // Base de l’API PHP dans /var/www/html/api/failles/
     const API_BASE = 'http://localhost:8080/api/failles';

      // Affichage d’une erreur simple
      function showError(message) {
        const results = document.getElementById('results');
        results.innerHTML = `<div class="empty" style="border-color:#fce7e7;color:#7f1d1d">Erreur : ${message}</div>`;
        document.getElementById('resultsCount').innerText = 'Erreur';
      }

      // Rendu d’un tableau de failles
      function renderVulns(vulns, containerId = 'results') {
        const container = document.getElementById(containerId);

        if (!Array.isArray(vulns) || vulns.length === 0) {
          container.innerHTML = '<div class="empty">Aucune faille trouvée.</div>';
          if (containerId === 'results') {
            document.getElementById('resultsCount').innerText = '0 résultat';
          }
          return;
        }

        let html = '<table>' +
          '<thead><tr><th>ID</th><th>IP</th><th>Port</th><th>Type</th><th>Risque</th></tr></thead><tbody>';

        for (const v of vulns) {
          const risk = (v.risque || '').toLowerCase();
          const riskClass = (risk.includes('élev') || risk.includes('critique')) ? 'status-high' : 'status-low';

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

        if (containerId === 'results') {
          document.getElementById('resultsCount').innerText = `${vulns.length} résultat(s)`;
        }

        container.querySelectorAll('tr.clickable').forEach(row => {
          row.addEventListener('click', () => {
            const id = row.getAttribute('data-id');
            if (id) loadVulnDetails(id);
          });
        });
      }

      // Charger toutes les failles : /all.php
      async function loadAllVulns() {
        const out = document.getElementById('allVulns');
        out.innerHTML = '<div class="small">Chargement...</div>';
        try {
          const res = await fetch(`${API_BASE}/all.php`, { method: 'GET' });
          if (!res.ok) throw new Error('Réponse API non OK');
          const data = await res.json();

          if (!Array.isArray(data) || data.length === 0) {
            out.innerHTML = '<div class="small" style="color:var(--muted)">Aucune faille en base.</div>';
            return;
          }

          out.innerHTML = '<ul style="padding-left:18px;margin:8px 0 0 0;">' +
            data.map(v =>
              `<li style="margin-bottom:6px;cursor:pointer;color:#0f172a" onclick="loadVulnDetails(${v.id})">[#${v.id}] ${v.ip} - ${v.type} <span style="color:var(--muted)">(${v.risque})</span></li>`
            ).join('') +
            '</ul>';
        } catch (err) {
          console.error('loadAllVulns error:', err);
          out.innerHTML = '<div class="small" style="color:var(--muted)">Erreur lors de l’appel à l’API.</div>';
        }
      }

      // Rechercher par IP : /index.php?ip=...
      async function searchByIP() {
        const ip = document.getElementById('ipInput').value.trim();
        if (!ip) {
          showError('Adresse IP vide — veuillez entrer une adresse IP valide.');
          return;
        }

        const results = document.getElementById('results');
        results.innerHTML = '<div class="small">Recherche en cours…</div>';

        try {
          const res = await fetch(`${API_BASE}/index.php?ip=${encodeURIComponent(ip)}`, { method: 'GET' });
          if (!res.ok) throw new Error('Réponse API non OK');
          const data = await res.json();
          renderVulns(data, 'results');
        } catch (err) {
          console.error('searchByIP error:', err);
          showError("Impossible d'appeler l’API pour cette IP.");
        }
      }

      // Détail par ID : /id.php?id=...
      async function loadVulnDetails(id) {
        const panel = document.getElementById('detail');
        panel.innerHTML = '<div class="small">Chargement du détail…</div>';

        try {
          const res = await fetch(`${API_BASE}/id.php?id=${encodeURIComponent(id)}`);
          if (!res.ok) throw new Error('Réponse API détails non OK');
          const data = await res.json();
          panel.innerHTML = renderDetailHTML(data);
        } catch (err) {
          console.error('loadVulnDetails error:', err);
          panel.innerHTML = '<div class="empty">Erreur lors du chargement du détail pour l’ID ' + id + '.</div>';
        }
      }

      function renderDetailHTML(detail) {
        if (!detail) {
          return '<div class="empty">Aucun détail disponible.</div>';
        }

        return `
          <div style="padding:8px 0">
            <div style="display:flex;justify-content:space-between;align-items:center">
              <strong>Faille #${detail.id}</strong>
              <span class="small">${detail.ip ?? 'IP inconnue'}</span>
            </div>
            <div style="margin-top:10px;color:var(--muted)"><strong>Type :</strong> ${detail.type ?? '-'}</div>
            <div style="margin-top:6px;color:var(--muted)"><strong>Port :</strong> ${detail.port ?? '-'}</div>
            <div style="margin-top:6px;color:var(--muted)"><strong>Risque :</strong> ${detail.risque ?? '-'}</div>
            <div style="margin-top:10px;background:#f8fafc;border-radius:8px;padding:10px;border:1px solid #eef2f5">
            <div style="font-size:13px;color:#0f172a"><strong>Description</strong></div>
            <div style="margin-top:10px;background:#f8fafc;border-radius:8px;padding:10px;border:1px solid #eef2f5">
  	      <div style="font-size:13px;color:#0f172a"><strong>Résultat brut</strong></div>
  	      <pre style="white-space:pre-wrap;font-size:12px;color:#334155;margin-top:6px">
	    ${detail.details ?? detail.description ?? 'Aucun résultat brut disponible.'}
  	      </pre>
   	    </div>

            </div>
          </div>
        `;
      }

      function init() {
        document.getElementById('btnAll').addEventListener('click', loadAllVulns);
        document.getElementById('searchBtn').addEventListener('click', () => searchByIP());
        loadAllVulns(); // charge toutes les failles au démarrage
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
      } else {
        init();
      }
    </script>
  </body>
</html>

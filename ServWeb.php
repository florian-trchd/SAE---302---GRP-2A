<?php
// ServWeb.php

// Simple web server endpoint to receive IP and subnet mask, call Java scanner, and return vulnerabilities

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_POST['ip'] ?? '';
    $mask = $_POST['mask'] ?? '';

    // Basic validation
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid IP address']);
        exit;
    }
    if (!preg_match('/^(?:\d{1,2}|3[0-2])$/', $mask)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid subnet mask']);
        exit;
    }

    // Call Java scanner (assume Scanner.jar exists and takes IP and mask as arguments)
    $cmd = escapeshellcmd("java -jar Scanner.jar $ip $mask");
    $output = shell_exec($cmd);

    if ($output === null) {
        http_response_code(500);
        echo json_encode(['error' => 'Scanner failed']);
        exit;
    }

    // Output from Java should be JSON with vulnerabilities
    header('Content-Type: application/json');
    echo $output;
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Network Vulnerability Scanner</title>
</head>
<body>
    <h1>Scan Network for Vulnerabilities</h1>
    <form method="POST">
        <label>IP Address: <input type="text" name="ip" required></label><br>
        <label>Subnet Mask (CIDR, e.g., 24): <input type="text" name="mask" required></label><br>
        <button type="submit">Scan</button>
    </form>
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
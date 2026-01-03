/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.3-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: sae302
-- ------------------------------------------------------
-- Server version	11.8.3-MariaDB-1+b1 from Debian

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `findings`
--

DROP TABLE IF EXISTS `findings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `findings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `scanner_run_id` int(10) unsigned DEFAULT NULL,
  `source_tool` varchar(32) NOT NULL,
  `severity` enum('INFO','LOW','MEDIUM','HIGH','CRITICAL') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `target` varchar(255) NOT NULL,
  `details` mediumtext NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_findings_scan_id` (`scanner_run_id`),
  KEY `idx_findings_sev` (`severity`),
  CONSTRAINT `fk_findings_scanner_run` FOREIGN KEY (`scanner_run_id`) REFERENCES `scanner_runs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `findings`
--

LOCK TABLES `findings` WRITE;
/*!40000 ALTER TABLE `findings` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `findings` VALUES
(29,15,'nmap','INFO','Résultat Nmap','Résultat brut de Nmap sur la cible.','192.168.56.102','Exit code: 0\nStarting Nmap 7.95 ( https://nmap.org ) at 2026-01-03 09:47 EST\nNmap scan report for 192.168.56.102\nHost is up (0.0042s latency).\nNot shown: 977 filtered tcp ports (no-response)\nPORT     STATE SERVICE     VERSION\n21/tcp   open  ftp         vsftpd 2.3.4\n22/tcp   open  ssh         OpenSSH 4.7p1 Debian 8ubuntu1 (protocol 2.0)\n23/tcp   open  telnet      Linux telnetd\n25/tcp   open  smtp        Postfix smtpd\n53/tcp   open  domain      ISC BIND 9.4.2\n80/tcp   open  http        Apache httpd 2.2.8 ((Ubuntu) DAV/2)\n111/tcp  open  rpcbind     2 (RPC #100000)\n139/tcp  open  netbios-ssn Samba smbd 3.X - 4.X (workgroup: WORKGROUP)\n445/tcp  open  netbios-ssn Samba smbd 3.X - 4.X (workgroup: WORKGROUP)\n512/tcp  open  exec        netkit-rsh rexecd\n513/tcp  open  login?\n514/tcp  open  shell       Netkit rshd\n1099/tcp open  java-rmi    GNU Classpath grmiregistry\n1524/tcp open  bindshell   Metasploitable root shell\n2049/tcp open  nfs         2-4 (RPC #100003)\n2121/tcp open  ftp         ProFTPD 1.3.1\n3306/tcp open  mysql       MySQL 5.0.51a-3ubuntu5\n5432/tcp open  postgresql  PostgreSQL DB 8.3.0 - 8.3.7\n5900/tcp open  vnc         VNC (protocol 3.3)\n6000/tcp open  X11         (access denied)\n6667/tcp open  irc         UnrealIRCd\n8009/tcp open  ajp13       Apache Jserv (Protocol v1.3)\n8180/tcp open  http        Apache Tomcat/Coyote JSP engine 1.1\nService Info: Hosts:  metasploitable.localdomain, irc.Metasploitable.LAN; OSs: Unix, Linux; CPE: cpe:/o:linux:linux_kernel\n\nService detection performed. Please report any incorrect results at https://nmap.org/submit/ .\nNmap done: 1 IP address (1 host up) scanned in 16.09 seconds\n','2026-01-03 09:47:38'),
(30,15,'masscan','INFO','Résultat Masscan','Résultat brut de Masscan sur la cible.','192.168.56.102','Exit code: 0\nStarting masscan 1.3.2 (http://bit.ly/14GZzcT) at 2026-01-03 14:47:38 GMT\nInitiating SYN Stealth Scan\nScanning 1 hosts [1000 ports/host]\nrate:  0.00-kpps,  0.10% done,46641:38:30 remaining, found=0       \nDiscovered open port 513/tcp on 192.168.56.102                                 \nDiscovered open port 111/tcp on 192.168.56.102                                 \nDiscovered open port 22/tcp on 192.168.56.102                                  \nDiscovered open port 25/tcp on 192.168.56.102                                  \nDiscovered open port 23/tcp on 192.168.56.102                                  \nDiscovered open port 445/tcp on 192.168.56.102                                 \nDiscovered open port 21/tcp on 192.168.56.102                                  \nDiscovered open port 53/tcp on 192.168.56.102                                  \nrate:  1.00-kpps, 76.40% done,   0:00:01 remaining, found=8       \nDiscovered open port 139/tcp on 192.168.56.102                                 \nDiscovered open port 80/tcp on 192.168.56.102                                  \nDiscovered open port 512/tcp on 192.168.56.102                                 \nDiscovered open port 514/tcp on 192.168.56.102                                 \nrate:  0.00-kpps, 100.00% done, waiting 0-secs, found=12       \nrate:  1.00-kpps, 100.00% done, waiting 10-secs, found=12       \nrate:  0.85-kpps, 100.00% done, waiting 10-secs, found=12       \nrate:  0.97-kpps, 100.00% done, waiting 10-secs, found=12       \nrate:  0.99-kpps, 100.00% done, waiting 10-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 9-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 9-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 9-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 9-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 8-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 8-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 8-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 8-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 7-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 7-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 7-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 7-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 6-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 6-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 6-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 6-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 5-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 5-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 5-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 5-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 4-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 4-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 4-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 4-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 3-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 3-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 3-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 3-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 2-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 2-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 2-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 2-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 1-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 1-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 1-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 1-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 0-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 0-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 0-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting 0-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -1-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -1-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -1-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -1-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -2-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -2-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -2-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -2-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -3-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -3-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -3-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -3-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -4-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -4-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -4-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -4-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -5-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -5-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -5-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -5-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -6-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -6-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -6-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -6-secs, found=12       \nrate:  0.00-kpps, 100.00% done, waiting -7-secs, found=12       \n                                                                             \n','2026-01-03 09:47:58'),
(31,15,'nikto','INFO','Résultat Nikto','Résultat brut de Nikto sur la cible.','192.168.56.102','Exit code: 1\n- Nikto v2.5.0\n---------------------------------------------------------------------------\n+ Target IP:          192.168.56.102\n+ Target Hostname:    192.168.56.102\n+ Target Port:        80\n+ Start Time:         2026-01-03 09:47:58 (GMT-5)\n---------------------------------------------------------------------------\n+ Server: Apache/2.2.8 (Ubuntu) DAV/2\n+ /: Retrieved x-powered-by header: PHP/5.2.4-2ubuntu5.10.\n+ /: The anti-clickjacking X-Frame-Options header is not present. See: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options\n+ /: The X-Content-Type-Options header is not set. This could allow the user agent to render the content of the site in a different fashion to the MIME type. See: https://www.netsparker.com/web-vulnerability-scanner/vulnerabilities/missing-content-type-header/\n+ /index: Uncommon header \'tcn\' found, with contents: list.\n+ /index: Apache mod_negotiation is enabled with MultiViews, which allows attackers to easily brute force file names. The following alternatives for \'index\' were found: index.php. See: http://www.wisec.it/sectou.php?id=4698ebdc59d15,https://exchange.xforce.ibmcloud.com/vulnerabilities/8275\n+ Apache/2.2.8 appears to be outdated (current is at least Apache/2.4.54). Apache 2.2.34 is the EOL for the 2.x branch.\n+ /: Web Server returns a valid response with junk HTTP methods which may cause false positives.\n+ /: HTTP TRACE method is active which suggests the host is vulnerable to XST. See: https://owasp.org/www-community/attacks/Cross_Site_Tracing\n+ /phpinfo.php: Output from the phpinfo() function was found.\n+ /doc/: Directory indexing found.\n+ /doc/: The /doc/ directory is browsable. This may be /usr/doc. See: http://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-1999-0678\n+ /?=PHPB8B5F2A0-3C92-11d3-A3A9-4C7B08C10000: PHP reveals potentially sensitive information via certain HTTP requests that contain specific QUERY strings. See: OSVDB-12184\n+ /?=PHPE9568F36-D428-11d2-A769-00AA001ACF42: PHP reveals potentially sensitive information via certain HTTP requests that contain specific QUERY strings. See: OSVDB-12184\n+ /?=PHPE9568F34-D428-11d2-A769-00AA001ACF42: PHP reveals potentially sensitive information via certain HTTP requests that contain specific QUERY strings. See: OSVDB-12184\n+ /?=PHPE9568F35-D428-11d2-A769-00AA001ACF42: PHP reveals potentially sensitive information via certain HTTP requests that contain specific QUERY strings. See: OSVDB-12184\n+ /phpMyAdmin/changelog.php: phpMyAdmin is for managing MySQL databases, and should be protected or limited to authorized hosts.\n+ /phpMyAdmin/ChangeLog: Server may leak inodes via ETags, header found with file /phpMyAdmin/ChangeLog, inode: 92462, size: 40540, mtime: Tue Dec  9 12:24:00 2008. See: http://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2003-1418\n+ /phpMyAdmin/ChangeLog: phpMyAdmin is for managing MySQL databases, and should be protected or limited to authorized hosts.\n+ /test/: Directory indexing found.\n+ /test/: This might be interesting.\n+ /phpinfo.php: PHP is installed, and a test script which runs phpinfo() was found. This gives a lot of system information. See: CWE-552\n+ /icons/: Directory indexing found.\n+ /icons/README: Apache default file found. See: https://www.vntweb.co.uk/apache-restricting-access-to-iconsreadme/\n+ /phpMyAdmin/: phpMyAdmin directory found.\n+ /phpMyAdmin/Documentation.html: phpMyAdmin is for managing MySQL databases, and should be protected or limited to authorized hosts.\n+ /phpMyAdmin/README: phpMyAdmin is for managing MySQL databases, and should be protected or limited to authorized hosts. See: https://typo3.org/\n+ /#wp-config.php#: #wp-config.php# file found. This file contains the credentials.\n+ 8910 requests: 0 error(s) and 27 item(s) reported on remote host\n+ End Time:           2026-01-03 09:48:27 (GMT-5) (29 seconds)\n---------------------------------------------------------------------------\n+ 1 host(s) tested\n','2026-01-03 09:48:27'),
(32,15,'whatweb','INFO','Résultat WhatWeb','Résultat brut de WhatWeb sur la cible.','192.168.56.102','Exit code: 0\n[1m[34mhttp://192.168.56.102[0m [200 OK] [1mApache[0m[[1m[32m2.2.8[0m], [1mCountry[0m[[0m[22mRESERVED[0m][[1m[31mZZ[0m], [1mHTTPServer[0m[[1m[31mUbuntu Linux[0m][[1m[36mApache/2.2.8 (Ubuntu) DAV/2[0m], [1mIP[0m[[0m[22m192.168.56.102[0m], [1mPHP[0m[[1m[32m5.2.4-2ubuntu5.10[0m], [1mTitle[0m[[1m[33mMetasploitable2 - Linux[0m], [1mWebDAV[0m[[1m[32m2[0m], [1mX-Powered-By[0m[[0m[22mPHP/5.2.4-2ubuntu5.10[0m]\n[1m[31mERROR Opening: https://192.168.56.102 - Connection refused - connect(2) for \"192.168.56.102\" port 443[0m\n','2026-01-03 09:48:30');
/*!40000 ALTER TABLE `findings` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `scanner_runs`
--

DROP TABLE IF EXISTS `scanner_runs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `scanner_runs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `target` varchar(255) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` enum('pending','running','done','error') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `idx_scans_status` (`status`),
  KEY `idx_scans_target` (`target`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scanner_runs`
--

LOCK TABLES `scanner_runs` WRITE;
/*!40000 ALTER TABLE `scanner_runs` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `scanner_runs` VALUES
(7,'127.0.0.1','2025-11-30 17:57:02',NULL,'running'),
(8,'127.0.0.1','2025-11-30 18:12:52',NULL,'running'),
(9,'127.0.0.1','2025-11-30 18:41:03','2025-11-30 18:41:35','done'),
(10,'192.168.56.102','2025-11-30 18:59:45','2025-11-30 19:02:00','done'),
(11,'192.168.56.102','2025-12-01 05:27:36','2025-12-01 05:29:21','done'),
(12,'192.168.56.101','2026-01-03 02:38:37','2026-01-03 02:39:58','done'),
(13,'192.168.56.102','2026-01-03 02:40:06','2026-01-03 02:41:37','done'),
(14,'192.168.56.102','2026-01-03 09:24:54','2026-01-03 09:27:14','done'),
(15,'192.168.56.102','2026-01-03 09:47:22','2026-01-03 09:48:30','done');
/*!40000 ALTER TABLE `scanner_runs` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2026-01-03 10:17:07

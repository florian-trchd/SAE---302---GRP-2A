CREATE DATABASE IF NOT EXISTS sae302
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;
CREATE USER 'sae302'@'localhost' IDENTIFIED BY 'sae302pwd';
GRANT ALL PRIVILEGES ON sae302.* TO 'sae302'@'localhost';
FLUSH PRIVILEGES;
USE sae302;
DROP TABLE IF EXISTS scan_exports;
DROP TABLE IF EXISTS findings;
DROP TABLE IF EXISTS scans;
CREATE TABLE scans (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tool          VARCHAR(32)     NOT NULL DEFAULT 'Nessus',
  target        VARCHAR(255)    NOT NULL,
  start_time    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  end_time      DATETIME        NULL,
  status        ENUM('running','done','error') NOT NULL DEFAULT 'running',
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_scans_status (status),
  KEY idx_scans_target (target)
) ENGINE=InnoDB;
CREATE TABLE findings (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  scan_id         INT UNSIGNED NOT NULL,
  severity        ENUM('LOW','MEDIUM','HIGH','CRITICAL') NOT NULL,
  title           VARCHAR(255)   NOT NULL,
  description     MEDIUMTEXT     NOT NULL,
  target          VARCHAR(255)   NOT NULL,
  cve             VARCHAR(64)    NULL,
  evidence        MEDIUMTEXT     NULL,
  raw_data        MEDIUMTEXT     NULL,
  plugin_id       INT            NULL,
  cvss            DECIMAL(3,1)   NULL,
  port            INT            NULL,
  protocol        VARCHAR(8)     NULL,
  created_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_findings_scan_id (scan_id),
  KEY idx_findings_sev (severity),
  KEY idx_findings_cve (cve),
  CONSTRAINT fk_findings_scan
    FOREIGN KEY (scan_id) REFERENCES scans(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE scan_exports (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  scan_id     INT UNSIGNED NOT NULL,
  file_type   ENUM('csv','nessus','json') NOT NULL DEFAULT 'csv',
  file_id     INT NULL,
  storage     VARCHAR(512) NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_exports_scan (scan_id),
  CONSTRAINT fk_exports_scan
    FOREIGN KEY (scan_id) REFERENCES scans(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;
#Exemple
#INSERT INTO scans(tool, target, status) VALUES ('Nessus', '192.168.1.0/24', 'running');
#UPDATE scans SET status='done', end_time=NOW() WHERE id=1;
#INSERT INTO findings (scan_id, severity, title, description, target, cve, evidence, raw_data, plugin_id, cvss, port, protocol)
#VALUES
#(1, 'HIGH', 'Apache vulnerable version', 'Version exposée détectée', '192.168.1.10:80', 'CVE-2021-41773',
# 'Server returned vulnerable banner',
# '{"plugin":"apache_version_check","risk":"High"}', 4767, 8.1, 80, 'tcp'),
#(1, 'MEDIUM', 'OpenSSH outdated', 'Version OpenSSH obsolète', '192.168.1.12:22', NULL,
#'Banner shows OpenSSH_7.2',
# '{"plugin":"ssh_banner","risk":"Medium"}', 10267, 5.0, 22, 'tcp');

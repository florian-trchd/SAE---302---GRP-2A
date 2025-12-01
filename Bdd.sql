CREATE DATABASE IF NOT EXISTS sae302
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'sae302'@'localhost' IDENTIFIED BY 'sae302pwd';
GRANT ALL PRIVILEGES ON sae302.* TO 'sae302'@'localhost';
FLUSH PRIVILEGES;

USE sae302;

-- 2) Nettoyage éventuel (optionnel : supprime les tables si elles existent)
DROP TABLE IF EXISTS findings;
DROP TABLE IF EXISTS scanner_runs;

-- 3) Table des scans (lancement global d’un scan sur une cible)
CREATE TABLE scanner_runs (
  id         INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  target     VARCHAR(255)     NOT NULL,      -- IP ou hostname scanné
  start_time DATETIME         DEFAULT NULL,  -- Début réel du scan
  end_time   DATETIME         DEFAULT NULL,  -- Fin du scan
  status     ENUM('pending','running','done','error')
                              NOT NULL DEFAULT 'pending',

  PRIMARY KEY (id),
  KEY idx_scans_status (status),
  KEY idx_scans_target (target)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- 4) Table des résultats (findings) produits par les outils
CREATE TABLE findings (
  id             INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  scanner_run_id INT(10) UNSIGNED DEFAULT NULL,  -- FK vers scanner_runs.id
  source_tool    VARCHAR(32)      NOT NULL,      -- nmap, nikto, masscan, whatweb...
  severity       ENUM('INFO','LOW','MEDIUM','HIGH','CRITICAL')
                                   NOT NULL,     -- niveau de gravité
  title          VARCHAR(255)     NOT NULL,      -- titre court
  description    MEDIUMTEXT       NOT NULL,      -- description courte
  target         VARCHAR(255)     NOT NULL,      -- cible concernée
  details        MEDIUMTEXT       NOT NULL,      -- sortie brute / détails
  created_at     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (id),
  KEY idx_findings_scan_id (scanner_run_id),
  KEY idx_findings_sev (severity),

  CONSTRAINT fk_findings_scanner_run
    FOREIGN KEY (scanner_run_id)
    REFERENCES scanner_runs(id)
    ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
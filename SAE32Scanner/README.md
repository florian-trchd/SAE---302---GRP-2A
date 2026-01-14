# SAE 32 – Application Java de Scan de Vulnérabilités

## 🎯 Objectif du projet

Cette application Java exécute automatiquement plusieurs outils de scan
(Nmap, Masscan, Nikto, WhatWeb) sur une adresse IP ou un réseau.
Les résultats sont enregistrés dans une base de données MySQL afin d’être
consultés par un serveur web.

Le programme fonctionne en lisant les scans “en attente” dans la base,
puis en lançant tous les outils et en stockant les vulnérabilités trouvées.

---

## 🧩 Squelette du programme Java  
*(Classes / attributs / entêtes des méthodes)*

### 1. `ScanApp` (classe principale)
Gère l’exécution complète d’un scan.

**Attributs :**
- `DatabaseManager dbManager`
- `List<ToolScanner> scanners`

**Méthodes :**
- `public static void main(String[] args)`
- `public ScanApp()`
- `public void processPendingScans() throws Exception`

---

### 2. `ToolScanner` (interface)
Contrat commun pour tous les outils (Nmap, Masscan, Nikto, WhatWeb).

**Méthodes :**
- `String getName()`
- `List<Finding> scanTarget(String target) throws Exception`

---

### 3. Scanners (implémentations)

- `NmapScanner`
- `MasscanScanner`
- `NiktoScanner`
- `WhatWebScanner`

**Méthodes :**
- `public String getName()`
- `public List<Finding> scanTarget(String target) throws Exception`

---

### 4. `ScannerRun` (entité = table scanner_runs)

Représente un scan demandé par le serveur web.

**Attributs :**
- `Long id`
- `String target`
- `LocalDateTime startTime`
- `LocalDateTime endTime`
- `String status`

---

### 5. `Finding` (entité = table findings)

Représente une vulnérabilité détectée par un outil.

**Attributs :**
- `Long id`
- `Long scannerRunId`
- `String sourceTool`
- `String severity`
- `String title`
- `String description`
- `String target`
- `String details`
- `LocalDateTime createdAt`

---

### 6. `DatabaseManager`

Gère la connexion MySQL et les opérations principales.

**Méthodes :**
- `public void connect()`
- `public void close()`
- `public List<ScannerRun> getPendingScans()`
- `public void updateScanStatus(long runId, String status)`
- `public void saveFindings(List<Finding> findings, long runId)`

---

## 🗃️ Base de Données

### Table `scanner_runs`
- id (PK)
- target
- start_time
- end_time
- status

### Table `findings`
- id (PK)
- scanner_run_id (FK → scanner_runs)
- source_tool
- severity
- title
- description
- target
- details
- created_at

Relation :
**1 scan → plusieurs findings**

---

## 🚀 Fonctionnement global

1. Le serveur web ajoute un scan avec `status = 'pending'`
2. L’application Java lit les scans en attente
3. Elle lance tous les outils (Nmap, Masscan, Nikto, WhatWeb)
4. Chaque outil retourne une liste de vulnérabilités (Findings)
5. L’application enregistre tout dans la base MySQL
6. Le statut du scan passe à `done`

---

## 📝 Notes

- Les outils sont simulés dans cette version (FAKE data)
- Le programme est extensible : ajouter un outil = créer une nouvelle classe qui implémente `ToolScanner`


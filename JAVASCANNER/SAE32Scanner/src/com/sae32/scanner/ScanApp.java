package com.sae32.scanner;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class ScanApp {

    private DatabaseManager dbManager;
    private List<ToolScanner> scanners;

    public ScanApp() {
        this.dbManager = new DatabaseManager();
        // Outils lancés automatiquement pour chaque cible
        this.scanners = Arrays.asList(
                new NmapScanner(),
                new MasscanScanner(),
                new NiktoScanner(),
                new WhatWebScanner()
        );
    }

    public static void main(String[] args) {
        ScanApp app = new ScanApp();
        try {
            app.processPendingScans();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     * Récupère les scans en attente dans la BDD
     * et lance tous les outils sur chaque cible.
     */
    public void processPendingScans() throws Exception {
        dbManager.connect();

        List<ScannerRun> pending = dbManager.getPendingScans();
        if (pending.isEmpty()) {
            System.out.println("Aucun scan en attente.");
            dbManager.close();
            return;
        }

        System.out.println("Scans en attente : " + pending.size());

        for (ScannerRun run : pending) {
            System.out.println("Traitement du scan #" + run.getId() + " sur " + run.getTarget());

            dbManager.updateScanStatus(run.getId(), "running");

            List<Finding> allFindings = new ArrayList<>();

            for (ToolScanner scanner : scanners) {
                try {
                    System.out.println("  -> Lancement de " + scanner.getName() + " sur " + run.getTarget());
                    List<Finding> findings = scanner.scanTarget(run.getTarget());
                    for (Finding f : findings) {
                        f.setScannerRunId(run.getId());
                        f.setSourceTool(scanner.getName());
                    }
                    allFindings.addAll(findings);
                } catch (Exception e) {
                    System.err.println("Erreur pendant le scan avec " + scanner.getName() + " : " + e.getMessage());
                }
            }

            if (!allFindings.isEmpty()) {
                dbManager.saveFindings(allFindings, run.getId());
                System.out.println("  -> " + allFindings.size() + " vulnérabilité(s) enregistrée(s).");
            } else {
                System.out.println("  -> Aucun résultat trouvé (ou parsing non implémenté).");
            }

            dbManager.updateScanStatus(run.getId(), "done");
            System.out.println("Scan #" + run.getId() + " terminé.");
        }

        dbManager.close();
    }
}

package com.sae32.scanner;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

public class NmapScanner implements ToolScanner {

    private String nmapBinary = "nmap";

    @Override
    public String getName() {
        return "nmap";
    }

    @Override
    public List<Finding> scanTarget(String target) throws Exception {
        List<Finding> results = new ArrayList<>();

        ProcessBuilder pb = new ProcessBuilder(
                nmapBinary,
                "-sV",           // détection de versions
                target
        );
        pb.redirectErrorStream(true);
        Process process = pb.start();

        StringBuilder output = new StringBuilder();
        try (BufferedReader br = new BufferedReader(
                new InputStreamReader(process.getInputStream()))) {
            String line;
            while ((line = br.readLine()) != null) {
                output.append(line).append(System.lineSeparator());
            }
        }

        int exitCode = process.waitFor();

        Finding f = new Finding();
        f.setSeverity("INFO");
        f.setTitle("Résultat Nmap");
        f.setDescription("Résultat brut de Nmap sur la cible.");
        f.setTarget(target);
        f.setDetails("Exit code: " + exitCode + System.lineSeparator() + output);
        results.add(f);

        return results;
    }
}

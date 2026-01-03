package com.sae32.scanner;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;

public class MasscanScanner implements ToolScanner {

    private String masscanBinary = "masscan";

    @Override
    public String getName() {
        return "masscan";
    }

    @Override
    public List<Finding> scanTarget(String target) throws Exception {
        List<Finding> results = new ArrayList<>();

        ProcessBuilder pb = new ProcessBuilder(
                masscanBinary,
                target,
                "--ports", "1-1000",
                "--rate", "1000"
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
        f.setTitle("Résultat Masscan");
        f.setDescription("Résultat brut de Masscan sur la cible.");
        f.setTarget(target);
        f.setDetails("Exit code: " + exitCode + System.lineSeparator() + output);
        results.add(f);

        return results;
    }
}

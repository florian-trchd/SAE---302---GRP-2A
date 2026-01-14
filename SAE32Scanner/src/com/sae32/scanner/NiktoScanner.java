package com.sae32.scanner;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.TimeUnit;

public class NiktoScanner implements ToolScanner {

    private String niktoBinary = "nikto";

    @Override
    public String getName() {
        return "nikto";
    }

    @Override
    public List<Finding> scanTarget(String target) throws Exception {
        List<Finding> results = new ArrayList<>();

        // On ajoute -ask no pour empêcher la question interactive
        ProcessBuilder pb = new ProcessBuilder(
                niktoBinary,
                "-host", target,
                "-ask", "no"          // <--- important
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

        // Timeout de 60 secondes pour éviter les blocages
        boolean finished = process.waitFor(60, TimeUnit.SECONDS);
        if (!finished) {
            process.destroyForcibly();
            throw new RuntimeException("Nikto a dépassé le délai (timeout).");
        }

        int exitCode = process.exitValue();

        Finding f = new Finding();
        f.setSeverity("INFO");
        f.setTitle("Résultat Nikto");
        f.setDescription("Résultat brut de Nikto sur la cible.");
        f.setTarget(target);
        f.setDetails("Exit code: " + exitCode + System.lineSeparator() + output);
        results.add(f);

        return results;
    }
}

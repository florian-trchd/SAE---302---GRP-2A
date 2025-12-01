package com.sae32.scanner;

import java.util.List;

public interface ToolScanner {

    String getName();

    /**
     * Lance un scan sur la cible (IP / réseau)
     * et retourne une liste de vulnérabilités.
     */
    List<Finding> scanTarget(String target) throws Exception;
}

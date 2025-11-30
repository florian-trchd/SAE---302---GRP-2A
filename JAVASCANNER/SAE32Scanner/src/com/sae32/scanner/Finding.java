package com.sae32.scanner;

import java.time.LocalDateTime;

public class Finding {

    private Long id;
    private Long scannerRunId;
    private String sourceTool;   // nmap, masscan, nikto, whatweb
    private String severity;     // LOW / MEDIUM / HIGH / CRITICAL / INFO
    private String title;
    private String description;
    private String target;
    private String details;
    private LocalDateTime createdAt = LocalDateTime.now();

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public Long getScannerRunId() { return scannerRunId; }
    public void setScannerRunId(Long scannerRunId) { this.scannerRunId = scannerRunId; }

    public String getSourceTool() { return sourceTool; }
    public void setSourceTool(String sourceTool) { this.sourceTool = sourceTool; }

    public String getSeverity() { return severity; }
    public void setSeverity(String severity) { this.severity = severity; }

    public String getTitle() { return title; }
    public void setTitle(String title) { this.title = title; }

    public String getDescription() { return description; }
    public void setDescription(String description) { this.description = description; }

    public String getTarget() { return target; }
    public void setTarget(String target) { this.target = target; }

    public String getDetails() { return details; }
    public void setDetails(String details) { this.details = details; }

    public LocalDateTime getCreatedAt() { return createdAt; }
    public void setCreatedAt(LocalDateTime createdAt) { this.createdAt = createdAt; }

    public String summary() {
        return severity + " - " + title + " (" + sourceTool + ") sur " + target;
    }
}

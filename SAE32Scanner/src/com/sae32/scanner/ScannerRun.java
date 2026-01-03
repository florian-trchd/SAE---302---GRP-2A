package com.sae32.scanner;

import java.time.LocalDateTime;

public class ScannerRun {

    private Long id;
    private String target;
    private LocalDateTime startTime;
    private LocalDateTime endTime;
    private String status;   // pending / running / done / error

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }

    public String getTarget() { return target; }
    public void setTarget(String target) { this.target = target; }

    public LocalDateTime getStartTime() { return startTime; }
    public void setStartTime(LocalDateTime startTime) { this.startTime = startTime; }

    public LocalDateTime getEndTime() { return endTime; }
    public void setEndTime(LocalDateTime endTime) { this.endTime = endTime; }

    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }

    public void updateStatus(String newStatus) {
        this.status = newStatus;
    }
}

package com.sae32.scanner;

import java.sql.*;
import java.time.LocalDateTime;
import java.util.ArrayList;
import java.util.List;

public class DatabaseManager {

    // À adapter pour bdd
    private static final String URL  = "jdbc:mysql://localhost:3306/ta_base";
    private static final String USER = "root";
    private static final String PASS = "root";

    private Connection connection;

    public void connect() throws SQLException {
        connection = DriverManager.getConnection(URL, USER, PASS);
        System.out.println("Connexion à la BDD OK.");
    }

    public void close() {
        try {
            if (connection != null) {
                connection.close();
                System.out.println("Connexion à la BDD fermée.");
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    public List<ScannerRun> getPendingScans() throws SQLException {
        List<ScannerRun> list = new ArrayList<>();
        String sql = "SELECT id, target, start_time, end_time, status FROM scanner_runs WHERE status = 'pending'";

        try (PreparedStatement ps = connection.prepareStatement(sql);
             ResultSet rs = ps.executeQuery()) {

            while (rs.next()) {
                ScannerRun run = new ScannerRun();
                run.setId(rs.getLong("id"));
                run.setTarget(rs.getString("target"));

                Timestamp st = rs.getTimestamp("start_time");
                if (st != null) {
                    run.setStartTime(st.toLocalDateTime());
                }

                Timestamp et = rs.getTimestamp("end_time");
                if (et != null) {
                    run.setEndTime(et.toLocalDateTime());
                }

                run.setStatus(rs.getString("status"));
                list.add(run);
            }
        }

        return list;
    }

    public void updateScanStatus(long runId, String status) throws SQLException {
        String sql = "UPDATE scanner_runs SET status = ?, " +
                     "start_time = CASE WHEN ? = 'running' AND start_time IS NULL THEN ? ELSE start_time END, " +
                     "end_time   = CASE WHEN ? IN ('done','error') THEN ? ELSE end_time END " +
                     "WHERE id = ?";

        LocalDateTime now = LocalDateTime.now();
        Timestamp nowTs = Timestamp.valueOf(now);

        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            ps.setString(1, status);
            ps.setString(2, status);
            ps.setTimestamp(3, nowTs);
            ps.setString(4, status);
            ps.setTimestamp(5, nowTs);
            ps.setLong(6, runId);
            ps.executeUpdate();
        }
    }

    public void saveFindings(List<Finding> findings, long runId) throws SQLException {
        if (findings == null || findings.isEmpty()) {
            return;
        }

        String sql = "INSERT INTO findings " +
                "(scanner_run_id, source_tool, severity, title, description, target, details, created_at) " +
                "VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        try (PreparedStatement ps = connection.prepareStatement(sql)) {
            for (Finding f : findings) {
                ps.setLong(1, runId);
                ps.setString(2, f.getSourceTool());
                ps.setString(3, f.getSeverity());
                ps.setString(4, f.getTitle());
                ps.setString(5, f.getDescription());
                ps.setString(6, f.getTarget());
                ps.setString(7, f.getDetails());
                ps.setTimestamp(8, Timestamp.valueOf(f.getCreatedAt()));
                ps.addBatch();
            }
            ps.executeBatch();
        }
    }
}

CREATE TABLE IF NOT EXISTS community_service_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    violation_report_id INT NOT NULL,
    hours_completed DECIMAL(5,2) NOT NULL DEFAULT 0,
    service_date DATETIME NOT NULL,
    date_updated DATETIME NOT NULL,
    remarks TEXT,
    updated_by INT NOT NULL,
    FOREIGN KEY (violation_report_id) REFERENCES violation_reports(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 
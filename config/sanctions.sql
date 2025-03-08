-- Table for Section 1 (Academic) sanctions
CREATE TABLE IF NOT EXISTS sec1_sanctions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    offense_id INT NOT NULL,
    violation_count INT NOT NULL,
    sanction TEXT NOT NULL,
    FOREIGN KEY (offense_id) REFERENCES sec1(id),
    UNIQUE KEY unique_offense_count (offense_id, violation_count)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for Section 2 (Non-Academic) sanctions
CREATE TABLE IF NOT EXISTS sec2_sanctions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    offense_id INT NOT NULL,
    violation_count INT NOT NULL,
    level ENUM('Light', 'Serious', 'Very Serious') NOT NULL,
    sanction TEXT NOT NULL,
    FOREIGN KEY (offense_id) REFERENCES sec2(id),
    UNIQUE KEY unique_offense_count_level (offense_id, violation_count, level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 
<?php
include('database.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $conn->begin_transaction();

    // Migrate Section 1 (Academic) sanctions
    $query = "SELECT id, first_sanction, second_sanction, third_sanction FROM sec1";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        // Insert first offense sanction
        $stmt = $conn->prepare("INSERT INTO sec1_sanctions (offense_id, violation_count, sanction) VALUES (?, 1, ?)");
        $stmt->bind_param("is", $row['id'], $row['first_sanction']);
        $stmt->execute();

        // Insert second offense sanction
        $stmt = $conn->prepare("INSERT INTO sec1_sanctions (offense_id, violation_count, sanction) VALUES (?, 2, ?)");
        $stmt->bind_param("is", $row['id'], $row['second_sanction']);
        $stmt->execute();

        // Insert third offense sanction
        $stmt = $conn->prepare("INSERT INTO sec1_sanctions (offense_id, violation_count, sanction) VALUES (?, 3, ?)");
        $stmt->bind_param("is", $row['id'], $row['third_sanction']);
        $stmt->execute();
    }

    // Migrate Section 2 (Non-Academic) sanctions
    $query = "SELECT id, level, first_sanction, second_sanction, third_sanction FROM sec2";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        // Insert first offense sanction
        $stmt = $conn->prepare("INSERT INTO sec2_sanctions (offense_id, violation_count, level, sanction) VALUES (?, 1, ?, ?)");
        $stmt->bind_param("iss", $row['id'], $row['level'], $row['first_sanction']);
        $stmt->execute();

        // Insert second offense sanction
        $stmt = $conn->prepare("INSERT INTO sec2_sanctions (offense_id, violation_count, level, sanction) VALUES (?, 2, ?, ?)");
        $stmt->bind_param("iss", $row['id'], $row['level'], $row['second_sanction']);
        $stmt->execute();

        // Insert third offense sanction
        $stmt = $conn->prepare("INSERT INTO sec2_sanctions (offense_id, violation_count, level, sanction) VALUES (?, 3, ?, ?)");
        $stmt->bind_param("iss", $row['id'], $row['level'], $row['third_sanction']);
        $stmt->execute();
    }

    $conn->commit();
    echo "Sanctions migration completed successfully.\n";

} catch (Exception $e) {
    $conn->rollback();
    echo "Error during migration: " . $e->getMessage() . "\n";
} 
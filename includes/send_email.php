<?php
require_once '../config/database.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendViolationEmail($studentId, $violationDetails) {
    global $conn;
    
    try {
        // Get student email
        $query = "SELECT full_name, email, password FROM students WHERE id = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            throw new Exception("Database prepare error: " . $conn->error);
        }
        
        $stmt->bind_param("i", $studentId);
        
        if (!$stmt->execute()) {
            throw new Exception("Database execute error: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        
        if (!$student) {
            throw new Exception("No student found with ID: " . $studentId);
        }
        
        if (empty($student['email'])) {
            throw new Exception("Student email not found for ID: " . $studentId);
        }

        // Only generate password if student doesn't have one
        $plainPassword = null;
        if (empty($student['password'])) {
            // Generate a random 8 character password
            $plainPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
            
            // Update student password in database
            $updateQuery = "UPDATE students SET password = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
            $updateStmt->bind_param("si", $hashedPassword, $studentId);
            
            if (!$updateStmt->execute()) {
                throw new Exception("Failed to update password: " . $updateStmt->error);
            }
        }

        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);

        try {
            // Load email configuration
            $config_file = __DIR__ . '/../config/email_config.php';
            if (!file_exists($config_file)) {
                throw new Exception("Email configuration file not found");
            }
            $email_config = require $config_file;

            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = $email_config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $email_config['smtp_username'];
            $mail->Password = $email_config['smtp_password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $email_config['smtp_port'];

            // Recipients
            $mail->setFrom($email_config['from_email'], $email_config['from_name']);
            $mail->addAddress($student['email'], $student['full_name']);

            // Content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Violation Report Notification';

            // Format section type for display
            $sectionDisplay = ($violationDetails['section'] === 'section1') ? 'Academic' : 'Non-Academic';

            // Email body
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background-color: #003366; color: white; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>Violation Report Notification</h2>
                </div>
                
                <div style='padding: 20px; background-color: #f9f9f9;'>
                    <p>Dear <strong>{$student['full_name']}</strong>,</p>";

            // Only include credentials if password was just generated
            if ($plainPassword !== null) {
                $body .= "
                    <div style='background-color: #fff3cd; padding: 15px; border-left: 4px solid #ffeeba; margin: 20px 0;'>
                        <h3 style='margin-top: 0; color: #856404;'>Your System Access Credentials</h3>
                        <p><strong>Email (Username):</strong> {$student['email']}</p>
                        <p><strong>Password:</strong> {$plainPassword}</p>
                        <p style='color: #856404;'><em>Please change your password upon first login for security purposes.</em></p>
                    </div>";
            }

            $body .= "
                    <p>This email is to inform you that a violation report has been filed with the following details:</p>
                    
                    <div style='background-color: white; padding: 15px; border-left: 4px solid #003366; margin: 20px 0;'>
                        <p><strong>Date of Incident:</strong> {$violationDetails['incident_datetime']}</p>
                        <p><strong>Section:</strong> {$sectionDisplay}</p>
                        <p><strong>Offense Level:</strong> {$violationDetails['offense_level']}</p>
                        <p><strong>Offense:</strong> {$violationDetails['offense']}</p>
                        <p><strong>Sanction:</strong> {$violationDetails['sanction']}</p>
                    </div>

                    <p>Please be reminded to comply with the university's code of conduct and regulations.</p>
                    
                    <p>If you have any questions or concerns, please visit the Student Affairs Office during office hours:</p>
                    <p>Monday - Friday<br>8:00 AM - 5:00 PM</p>
                </div>
                
                <div style='text-align: center; padding: 20px; font-size: 0.9em; color: #666;'>
                    <p>Best regards,<br>
                    <strong>Student Affairs Office</strong><br>
                    Cebu Technological University</p>
                    <small>This is an automated message. Please do not reply to this email.</small>
                </div>
            </div>";

            $mail->Body = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $body));

            $mail->send();
            
            return [
                'success' => true,
                'message' => 'Email sent successfully'
            ];

        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
            throw new Exception("Failed to send email: " . $e->getMessage());
        }

    } catch (Exception $e) {
        error_log("Error in sendViolationEmail: " . $e->getMessage());
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
} 
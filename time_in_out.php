<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();
date_default_timezone_set('Asia/Manila');

try {
    if (!isset($_SESSION['Email'])) {
        throw new Exception("Session expired. Please log in again.");
    }

    include 'dbconn/dbconn.php';
    $Email = $_SESSION['Email'];

    $sql = "SELECT EmpID FROM dict_employee WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $Email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $empId = $result->fetch_assoc()['EmpID'];
    } else {
        throw new Exception("No employee record found.");
    }

    $stmt->close();

    $now = new DateTime();
    $time = $now->format('H:i');
    $date = $now->format('Y-m-d');

    // Check if a record exists for today
    $sql = "SELECT * FROM employee_records WHERE EmpID = ? AND Date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $empId, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    $stmt->close();

    if (!$record) {
        // Create a new record if not exists
        $sql = "INSERT INTO employee_records (EmpID, Date) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $empId, $date);
        $stmt->execute();
        $stmt->close();
        $record = ['TimeInAM' => null, 'TimeOutAM' => null, 'TimeInPM' => null, 'TimeOutPM' => null];
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'timeIn') {
        if ($now->format('A') === 'AM') {
            $field = 'TimeInAM';
            $message = 'Time In (AM) recorded successfully.';
        } else {
            $field = 'TimeInPM';
            $message = 'Time In (PM) recorded successfully.';
        }
    } elseif ($action === 'timeOut') {
        if ($now->format('A') === 'AM') {
            $field = 'TimeOutAM';
            $message = 'Time Out (AM) recorded successfully.';
        } else {
            $field = 'TimeOutPM';
            $message = 'Time Out (PM) recorded successfully.';
        }
    } else {
        throw new Exception("Invalid action.");
    }

    // Update the record
    $sql = "UPDATE employee_records SET $field = ? WHERE EmpID = ? AND Date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $time, $empId, $date);
    $stmt->execute();
    $stmt->close();

    $conn->close();

    echo json_encode(['success' => true, 'message' => $message, 'field' => strtolower($field), 'time' => $time, 'date' => $date]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
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

    $date = date('Y-m-d');

    // Get today's record
    $sql = "SELECT * FROM employee_records WHERE EmpID = ? AND Date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $empId, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    $stmt->close();

    $conn->close();

    if ($record) {
        echo json_encode([
            'success' => true,
            'date' => $record['Date'],
            'timeInAM' => $record['TimeInAM'],
            'timeOutAM' => $record['TimeOutAM'],
            'timeInPM' => $record['TimeInPM'],
            'timeOutPM' => $record['TimeOutPM']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => "No record found for today."]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
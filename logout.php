<?php
session_start();
include 'dbconn/dbconn.php';

if (isset($_SESSION['ID'])) {
    $ID = $_SESSION['ID'];
    
    // Clear the session token in the database
    $sql = "UPDATE dict_employee SET session_token = NULL WHERE empID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ID);
    $stmt->execute();
    $stmt->close();
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit();
?>
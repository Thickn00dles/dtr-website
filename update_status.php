<?php
include 'dbconn/dbconn.php';

if(isset($_POST['status']) && isset($_POST['empID'])) {
    $status = $_POST['status'];
    $empID = $_POST['empID'];

    // Ensure status is either 0 or 1
    $status = ($status == 1) ? 1 : 0;

    $sql = "UPDATE dict_employee SET Status = ? WHERE empID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $status, $empID);
    
    if($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "error";
}
?>
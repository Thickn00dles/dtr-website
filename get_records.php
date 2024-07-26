<?php
session_start();
include 'dbconn/dbconn.php';

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['Email']) || ($_SESSION['Role'] != 'Admin' && $_SESSION['Role'] != 'Sadmin')) {
    // If not, redirect to login page
    header('Location: login.php');
    exit();
}

$empID = isset($_GET['empID']) ? $_GET['empID'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

$sql = "SELECT e.EmpID, CONCAT(e.FirstName, ' ', IFNULL(e.MiddleName, ''), ' ', e.LastName) AS FullName,
               r.R_ID, r.Date, r.TimeInAM, r.TimeOutAM, r.TimeInPM, r.TimeOutPM
        FROM dict_employee e
        LEFT JOIN employee_records r ON e.EmpID = r.EmpID
        WHERE 1=1 ";

if (!empty($empID)) {
    $sql .= " AND e.EmpID = ?";
}
if (!empty($startDate)) {
    $sql .= " AND r.Date >= ?";
}
if (!empty($endDate)) {
    $sql .= " AND r.Date <= ?";
}

$sql .= " ORDER BY e.LastName, e.FirstName, r.Date DESC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $types = '';
    $params = array();

    if (!empty($empID)) {
        $types .= 'i';
        $params[] = $empID;
    }
    if (!empty($startDate)) {
        $types .= 's';
        $params[] = $startDate;
    }
    if (!empty($endDate)) {
        $types .= 's';
        $params[] = $endDate;
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $currentEmployee = '';
        $recordCount = 0;
        $lastEmpID = null;
        echo "<div class='employee-records'>";
        while($row = $result->fetch_assoc()) {
            if ($row['FullName'] != $currentEmployee) {
                if ($currentEmployee != '') {
                    echo "</tbody></table>";
                    if ($recordCount > 5) {
                        echo "<button class='btn btn-primary show-more' data-empid='" . $lastEmpID . "'>Show More</button>";
                    }
                    echo "</div>";
                }
                
                $currentEmployee = $row['FullName'];
                $lastEmpID = $row['EmpID'];
                $recordCount = 0;
                echo "<div class='employee-section' data-empid='" . $lastEmpID . "'>";
                
                echo "<h3 class='employee-name'>" . htmlspecialchars($currentEmployee) . "</h3>";
                echo "<table class='table table-striped custom-table datatable'>";
                echo "<thead><tr><th>Record ID</th><th>Date</th><th>Time In AM</th><th>Time Out AM</th><th>Time In PM</th><th>Time Out PM</th><th>Total Hours</th></tr></thead><tbody>";
            }
            
            $recordCount++;
            $totalHours = calculateTotalHours($row['TimeInAM'], $row['TimeOutAM'], $row['TimeInPM'], $row['TimeOutPM']);
            
            echo "<tr" . ($recordCount > 5 ? " class='hidden-record' style='display:none;'" : "") . ">";
            echo "<td>" . htmlspecialchars($row['R_ID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['TimeInAM']) . "</td>";
            echo "<td>" . htmlspecialchars($row['TimeOutAM']) . "</td>";
            echo "<td>" . htmlspecialchars($row['TimeInPM']) . "</td>";
            echo "<td>" . htmlspecialchars($row['TimeOutPM']) . "</td>";
            echo "<td>" . $totalHours . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        if ($recordCount > 5) {
            echo "<button class='btn btn-primary show-more' data-empid='" . $lastEmpID . "'>Show More</button>";
        }
        echo "</div></div>";
    } else {
        echo "<p>No records found.</p>";
    }

    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

$conn->close();

function calculateTotalHours($timeInAM, $timeOutAM, $timeInPM, $timeOutPM) {
    $totalMinutes = 0;
    
    $totalMinutes += calculateDuration($timeInAM, $timeOutAM);
    $totalMinutes += calculateDuration($timeInPM, $timeOutPM);
    
    $hours = floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;
    
    return sprintf("%02d:%02d", $hours, $minutes);
}

function calculateDuration($timeIn, $timeOut) {
    if (empty($timeIn) || empty($timeOut)) {
        return 0;
    }
    
    $timeIn = strtotime($timeIn);
    $timeOut = strtotime($timeOut);
    
    if ($timeOut < $timeIn) {
        $timeOut += 24 * 3600; // Add 24 hours if time out is on the next day
    }
    
    return ($timeOut - $timeIn) / 60; // Return duration in minutes
}
?>
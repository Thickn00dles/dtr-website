<?php
session_start();
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['Email'])) {
    echo "Please log in to view records.";
    exit();
}

include 'dbconn/dbconn.php';

$Email = $_SESSION['Email'];

// Get the employee ID
$sql = "SELECT EmpID FROM dict_employee WHERE Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $Email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $empId = $result->fetch_assoc()['EmpID'];
} else {
    echo "No employee record found.";
    exit();
}

$stmt->close();

// Get the selected month and year (default to current month if not set)
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Fetch records for the employee for the selected month
$sql = "SELECT Date, TimeInAM, TimeOutAM, TimeInPM, TimeOutPM 
        FROM employee_records 
        WHERE EmpID = ? AND MONTH(Date) = ? AND YEAR(Date) = ?
        ORDER BY Date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $empId, $selectedMonth, $selectedYear);
$stmt->execute();
$result = $stmt->get_result();

$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}

$stmt->close();
$conn->close();

// Get list of months with records
$months = [];
for ($i = 1; $i <= 12; $i++) {
    $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Records</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Time Records</h2>
        <form class="mb-4">
            <div class="form-row">
                <div class="col-auto">
                    <select name="month" class="form-control">
                        <?php foreach ($months as $num => $name): ?>
                            <option value="<?php echo $num; ?>" <?php echo $num == $selectedMonth ? 'selected' : ''; ?>>
                                <?php echo $name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="year" class="form-control">
                        <?php 
                        $currentYear = date('Y');
                        for ($year = $currentYear; $year >= $currentYear - 5; $year--): 
                        ?>
                            <option value="<?php echo $year; ?>" <?php echo $year == $selectedYear ? 'selected' : ''; ?>>
                                <?php echo $year; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">View</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time In (AM)</th>
                    <th>Time Out (AM)</th>
                    <th>Time In (PM)</th>
                    <th>Time Out (PM)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No records found for this month.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $record): ?>
                    <tr>
                        <td><?php echo date('F d, Y', strtotime($record['Date'])); ?></td>
                        <td><?php echo $record['TimeInAM']; ?></td>
                        <td><?php echo $record['TimeOutAM']; ?></td>
                        <td><?php echo $record['TimeInPM']; ?></td>
                        <td><?php echo $record['TimeOutPM']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
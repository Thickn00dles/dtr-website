<?php
session_start();
include 'dbconn/dbconn.php';

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['Email']) || ($_SESSION['Role'] != 'Admin' && $_SESSION['Role'] != 'Sadmin')) {
    // If not, redirect to login page
    header('Location: login.php');
    exit();
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT empID, FirstName, MiddleName, LastName, Sex, Email, Contact, Position, Role, Status FROM dict_employee WHERE empID LIKE ? OR Email LIKE ? OR FirstName LIKE ? OR LastName LIKE ? ORDER BY empID";
$stmt = $conn->prepare($sql);
$searchParam = "%$search%";
$stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $fullName = trim(implode(' ', array_filter([
            $row['FirstName'],
            $row['MiddleName'],
            $row['LastName']
        ])));
        ?>
        <tr>
            <td>
                <h2><a href="#"><?php echo htmlspecialchars($row['empID']); ?></a></h2>
            </td>
            <td><?php echo htmlspecialchars($fullName); ?></td>
            <td><?php echo htmlspecialchars($row['Sex']); ?></td>
            <td><?php echo htmlspecialchars($row['Email']); ?></td>
            <td><?php echo htmlspecialchars($row['Contact']); ?></td>
            <td><?php echo htmlspecialchars($row['Position']); ?></td>
            <td><?php echo htmlspecialchars($row['Role']); ?></td>
            <td class="text-center">
                <div class="dropdown action-label">
                    <a class="custom-badge status-purple dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false" id="status-<?php echo htmlspecialchars($row['empID']); ?>">
                        <?php echo htmlspecialchars($row['Status']); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item status-change" href="#" data-status="Active" data-empid="<?php echo htmlspecialchars($row['empID']); ?>">Activate</a>
                        <a class="dropdown-item status-change" href="#" data-status="Deactive" data-empid="<?php echo htmlspecialchars($row['empID']); ?>">Deactivate</a>
                    </div>
                </div>
            </td>
            <td class="text-right">
                <div class="dropdown dropdown-action">
                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="updateaccount.php?empID=<?php echo htmlspecialchars($row['empID']); ?>"><i class="fa fa-pencil m-r-5"></i> Edit</a>
                    </div>
                </div>
            </td>
        </tr>
        <?php
    }
} else {
    echo "<tr><td colspan='9'>No records found</td></tr>";
}

$stmt->close();
$conn->close();
?>
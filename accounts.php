<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['Email']) || $_SESSION['Role'] != 'Admin' && $_SESSION['Role'] != 'Sadmin') {
    header('Location: login.php');
    exit();
}

include 'dbconn/dbconn.php';

$Email = $_SESSION['Email'];
$FullName = $_SESSION['FullName'];


// Initial empty search
$search = '';
$sql = "SELECT empID, FirstName, MiddleName, LastName, Sex, Email, Contact, Position, Role, Status FROM dict_employee ORDER BY empID";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <title>ADMIN DTR</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>

<style>
    @media (max-width: 991.98px) {
    .slide-nav .sidebar {
        margin-left: 0;
    }
    .sidebar {
        margin-left: -225px;
        width: 225px;
        transition: all 0.4s ease;
        z-index: 1041;
    }
    .slide-nav .sidebar-overlay {
        display: block;
    }
    .sidebar-overlay {
        background-color: rgba(0, 0, 0, 0.6);
        display: none;
        height: 100%;
        left: 0;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1040;
    }
}
</style>

<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="header-left">
                <a href="admin.php" class="logo">
                    <img src="assets/img/logo.png" width="35" height="35" alt=""> <span>DICT</span>
                </a>
            </div>
            
            <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
            <a id="mobile_btn" class="mobile_btn float-left" href="javascript:void(0);"><i class="fa fa-bars"></i></a>            
            <ul class="nav user-menu float-right">
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle" src="assets/img/user.jpg" width="24" alt="Admin">
                            <span class="status online"></span>
                        </span>
                        <span><?php echo htmlspecialchars($FullName); ?></span>
                    </a>
                    <div class="dropdown-menu">
                    <a class="dropdown-item" href="editprofile.php">Edit Profile</a>
                        <a class="dropdown-item" href="login.php">Logout</a>
                        <?php
                        if (isset($_GET['Logout'])) {
                            session_destroy();
                        }
                        ?>
                    </div>
                </li>
            </ul>
            <div class="dropdown mobile-user-menu float-right">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="editprofile.php">Edit Profile</a>
                    <a class="dropdown-item" href="login.php">Logout</a>
                    <?php
                    if (isset($_GET['Logout'])) {
                        session_destroy();
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>
                        <li class="active">
                            <a href="admin.php"><i class="fa fa-calendar"></i> <span>Account</span></a>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fa fa-user"></i> <span> Employees </span> <span class="menu-arrow"></span></a>
                            <ul style="display: none;">
                                <li><a class="active" href="accounts.php">View Account</a></li>
                                <li><a href="accountrecords.php">View Records</a></li>
                                <li><a href="addemployee.php">Add Account</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="page-wrapper">
            <div class="content">
                <div class="row filter-row">
                    <div class="col-sm-8 col-md-6 col-lg-4 col-xl-3 col-12">
                        <div class="form-group form-focus">
                            <label class="focus-label">Search Employee</label>
                            <input type="text" class="form-control floating" id="search-input" name="search" placeholder="Name, Email, or ID" value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped custom-table mb-0 datatable">
                                <thead>
                                    <tr>
                                        <th>Employee ID</th>
                                        <th>Full Name</th>
                                        <th>Sex</th>
                                        <th>Email</th>
                                        <th>Contact</th>
                                        <th>Position</th>
                                        <th>Role</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
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
                                                    <a class="dropdown-item status-change" href="#" data-status="1" data-empid="<?php echo htmlspecialchars($row['empID']); ?>">Activate</a>
                                                    <a class="dropdown-item status-change" href="#" data-status="0" data-empid="<?php echo htmlspecialchars($row['empID']); ?>">Deactivate</a>
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
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
<script>
    $(document).ready(function() {
        $('.status-change').click(function(e) {
            e.preventDefault();
            var status = $(this).data('status');
            var empID = $(this).data('empid');
            
             $.ajax({
                url: 'update_status.php',
                method: 'POST',
                data: { status: status, empID: empID },
                success: function(response) {
                    if(response == 'success') {
                        var statusText = status == 1 ? 'Active' : 'Inactive';
                        $('#status-' + empID).text(statusText);
                        $('#status-' + empID).removeClass('status-green status-red')
                            .addClass(status == 1 ? 'status-green' : 'status-red');
                    } else {
                        alert('Failed to update status');
                    }
                },
                error: function() {
                    alert('An error occurred');
                }
            });
        });

        // Add this new function for live search
        $('#search-input').on('input', function() {
            var searchTerm = $(this).val();
            $.ajax({
                url: 'search_employees.php',
                method: 'GET',
                data: { search: searchTerm },
                success: function(response) {
                    $('table tbody').html(response);
                },
                error: function() {
                    alert('An error occurred during the search');
                }
            });
        });
    });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
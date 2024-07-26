<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <title>UPDATE STATUS</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <?php
    include 'dbconn/dbconn.php';
    
    session_start();
    $email = $_SESSION['email'];
    $FullName = $_SESSION['FullName'];
    $Role = $_SESSION['Role'];
   $empID = $_GET['empID'];


   if (!isset($_SESSION['Email']) || $_SESSION['Role'] != 'Admin' && $_SESSION['Role'] != 'Sadmin') {
    header('Location: login.php');
    exit();
}


    $sql = "SELECT FirstName, MiddleName, LastName, Email, Contact, Position,Sig_ID, Role FROM dict_employee WHERE empID = '$empID'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $fn = $row['FirstName'];
            $mn = $row['MiddleName'];
            $ln = $row['LastName'];
            $signatory = $row['Sig_ID'];
            $email = $row['Email'];
            $contact = $row['Contact'];
            $position = $row['Position'];
            $role = $row['Role'];

            // Your code to display or process the retrieved data goes here
        }
    } else {
        echo "No records found.";
    }
    ?>
   
    <div class="main-wrapper">
        <div class="header">
            <div class="header-left">
                <a href="superadmin.html" class="logo">
                    <img src="assets/img/logo.png" width="35" height="35" alt=""> <span>DICT</span>
                </a>
            </div>
            <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
            <a id="mobile_btn" class="mobile_btn float-left" href="#sidebar"><i class="fa fa-bars"></i></a>
            <ul class="nav user-menu float-right">
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle" src="assets/img/user.jpg" width="24" alt="Admin">
                            <span class="status online"></span>
                        </span>
                        <span><?php echo $FullName; ?></span>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="editprofile.php">Edit Profile</a>
                        <a class="dropdown-item" href="login.php">Logout</a>
                        <?php
                            if (isset($_GET['logout'])) {
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
                        if (isset($_GET['logout'])) {
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
                            <a href="admin.php"><i class="fa fa-calendar "></i> <span>Account</span></a>
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
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <h4 class="page-title">Update Account</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                    <form method="POST" action="">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>First Name <span class="text-danger">*</span></label>
                                        <input class="form-control" name="FirstName" value="<?php echo htmlspecialchars($fn); ?>" required>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Middle Name <span class="text-danger">*</span></label>
                                        <input class="form-control" name="MiddleName" value="<?php echo htmlspecialchars($mn); ?>" required>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Last Name <span class="text-danger">*</span></label>
                                        <input class="form-control" name="LastName" value="<?php echo htmlspecialchars($ln); ?>" required>
                                    </div>
                                </div>

                                

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input class="form-control" name="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Contact <span class="text-danger">*</span></label>
                                        <input class="form-control" name="Contact" value="<?php echo htmlspecialchars($contact); ?>" required>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Position <span class="text-danger">*</span></label>
                                        <input class="form-control" name="Position" value="<?php echo htmlspecialchars($position); ?>" required>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Role <span class="text-danger">*</span></label>
                                        <select class="form-control" name="Role" required>
                                            <option value="User" <?php echo ($role == 'User') ? 'selected' : ''; ?>>User</option>
                                            <option value="Admin" <?php echo ($role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Signatory <span class="text-danger">*</span></label>
                                        <select class="form-control" name="signatory" required>
                                                <?php
                                                include 'dbconn/dbconn.php';
                                                $sql = "SELECT EmpID, CONCAT(FirstName, ' ', COALESCE(MiddleName, ''), ' ', LastName) AS FullName FROM dict_employee ";

                                                $result = $conn->query($sql);

                                                if ($result && $result->num_rows > 0) {
                                                    echo "<option value=''>     </option>";
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option value='" . htmlspecialchars($row['EmpID']) . "'>" . htmlspecialchars($row['FullName']) . "</option>";
                                                    }
                                                } else {
                                                    echo "<option value=''>No options available</option>";
                                                }

                                                $conn->close();
                                                ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="m-t-20 text-center">
                                <button class="btn btn-primary submit-btn" type="submit">Update Account</button>


                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


   
<?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $firstName = $_POST['first_name'];
                        $middleName = $_POST['middle_name'];
                        $lastName = $_POST['last_name'];
                        $email = $_POST['email'];
                        $contact = $_POST['contact'];
                        $sex = $_POST['sex'];
                        $position = $_POST['position'];
                        $role = $_POST['role'];
                        $status = 1;
                        $psswd = "12345";
                        $signatory = $_POST['signatory'];

                        include 'dbconn/dbconn.php';

                        // First, check if the email already exists
                        $check_email_sql = "SELECT * FROM dict_employee WHERE Email = '$email'";
                        $result = $conn->query($check_email_sql);

                        if ($result->num_rows > 0) {
                            // Email already exists
                            echo "<p class='text-danger'>Error: An employee with this email address already exists.</p>";
                        } else {
                            // Email doesn't exist, proceed with insertion
                            $sql = "INSERT INTO dict_employee (FirstName, MiddleName, LastName, Sex, Email, Contact, Password, Position, Role, Status, Sig_ID)
                                    VALUES ('$firstName', '$middleName', '$lastName', '$sex', '$email', '$contact', '$psswd', '$position', '$role', '$status', '$signatory')";

                            if ($conn->query($sql) === TRUE) {
                                echo "<p class='text-success'>New employee created successfully</p>";
                            } else {
                                echo "<p class='text-danger'>Error: " . $sql . "<br>" . $conn->error . "</p>";
                            }
                        }

                        $conn->close();
                    }
                    ?>



    <div class="sidebar-overlay" data-reff=""></div>

    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/Chart.bundle.js"></script>
    <script src="assets/js/chart.js"></script>
    <script src="assets/js/app.js"></script>

</body>
</html>
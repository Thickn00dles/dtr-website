<?php
    include 'dbconn/dbconn.php';

    session_start();
    $email = $_SESSION['Email'] ?? '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstName = $_POST['Fname'] ?? '';
        $middleName = $_POST['Mname'] ?? '';
        $lastName = $_POST['Lname'] ?? '';
        $newEmail = $_POST['email'] ?? '';
        $contact = $_POST['contact'] ?? '';
        $sigID = $_POST['Sig_ID'] ?? '';
        $newPassword = $_POST['Npsswd'] ?? '';
        $confirmPassword = $_POST['Cpsswd'] ?? '';

        if (!empty($newPassword) && $newPassword != $confirmPassword) {
            echo "<p class='text-danger'>New password and confirm password do not match</p>";
        } else {
            $sql = "UPDATE dict_employee SET 
                    FirstName = ?,
                    MiddleName = ?,
                    LastName = ?,
                    Email = ?,
                    Contact = ?,
                    Sig_ID = ?";
            $params = [$firstName, $middleName, $lastName, $newEmail, $contact, $sigID];
            $types = "ssssss";

            if (!empty($newPassword)) {
                $sql .= ", Password = ?";
                $params[] = $newPassword;
                $types .= "s";
            }

            $sql .= " WHERE Email = ?";
            $params[] = $email;
            $types .= "s";

            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param($types, ...$params);
                if ($stmt->execute()) {
                    echo "<p class='text-success'>Profile updated successfully</p>";
                    if ($email != $newEmail) {
                        $_SESSION['Email'] = $newEmail;
                        $email = $newEmail;
                    }
                } else {
                    echo "Error updating profile: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        }
    }

    // Fetch user data
    $stmt = $conn->prepare("SELECT * FROM dict_employee WHERE Email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Fetch signatory data
    $signatoryQuery = "SELECT EmpID, CONCAT(FirstName, ' ', LastName) AS FullName FROM dict_employee";
    $signatoryResult = $conn->query($signatoryQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <title>Edit Profile</title>
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
   
    <div class="main-wrapper">
        <div class="header">
			<div class="header-left">
				<a href="admin.php" class="logo">
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
                        <span><?php echo $email; ?></span>
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
                    <h4 class="page-title">Edit Profile</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>First Name <span class="text-danger">*</span></label>
                                    <input class="form-control" name="Fname" value="<?php echo htmlspecialchars($userData['FirstName']); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input class="form-control" name="Mname" value="<?php echo htmlspecialchars($userData['MiddleName']); ?>">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Last Name <span class="text-danger">*</span></label>
                                    <input class="form-control" name="Lname" value="<?php echo htmlspecialchars($userData['LastName']); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input class="form-control" name="email" value="<?php echo htmlspecialchars($userData['Email']); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Contact <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" name="contact" value="<?php echo htmlspecialchars($userData['Contact']); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                            <div class="form-group">
                                <label>Signatory</label>
                                <select class="form-control" name="Sig_ID">
                                    <option value="">Select Signatory</option>
                                    <?php
                                    if ($signatoryResult && $signatoryResult->num_rows > 0) {
                                        while ($row = $signatoryResult->fetch_assoc()) {
                                            $selected = ($row['EmpID'] == $userData['Sig_ID']) ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($row['EmpID']) . "' $selected>" . htmlspecialchars($row['FullName']) . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input class="form-control" type="password" name="Npsswd">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input class="form-control" type="password" name="Cpsswd">
                                </div>
                            </div>
                        </div>
                        <div class="m-t-20 text-center">
                            <button class="btn btn-primary submit-btn" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar-overlay" data-reff=""></div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slimscroll.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
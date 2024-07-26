<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $Email = $_POST['Email'];
    $Pssd = $_POST['PS'];

    include 'dbconn/dbconn.php';

    $sql = "SELECT empID, Email, Password, Role, FirstName, MiddleName, LastName, Status FROM dict_employee WHERE Email = ? AND Password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $Email, $Pssd);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $Role = $row['Role'];
        $ID = $row['empID'];
        $FirstName = $row['FirstName'];
        $MiddleName = $row['MiddleName'];
        $LastName = $row['LastName'];
        $FullName = $FirstName . ' ' . $MiddleName . ' ' . $LastName;
        $Status = $row['Status'];

        if($Status == '0'){
            echo json_encode(['success' => false, 'message' => 'Account is Deactivated']);
        } else {
            $_SESSION['Email'] = $Email; 
            $_SESSION['Role'] = $Role;
            $_SESSION['ID'] = $ID;
            $_SESSION['FullName'] = $FullName;

            if($Role == 'Admin' || $Role == 'Sadmin') {
                echo json_encode(['success' => true, 'redirect' => 'admin.php']);
            } elseif($Role == 'User') {
                echo json_encode(['success' => true, 'redirect' => 'index.php']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid user role']);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid Username or Password']);
    }
    
    $stmt->close();
    $conn->close();
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <title>DTR LOGIN</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <div class="main-wrapper account-wrapper">
        <div class="account-page">
            <div class="account-center">
                <div class="account-box">
                    <form id="loginForm" class="form-signin">
                        <div class="account-logo">
                            <a href="login.php"><img src="assets/img/favicon.png" alt=""></a>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" autofocus="" class="form-control" name="Email" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="PS" required>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary account-btn">Login</button>
                        </div>
                        <div id="loginMessage" class="alert" style="display: none;"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>  

    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/app.js"></script>

    <script>
    $(document).ready(function() {
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: 'login.php',
                type: 'post',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#loginMessage').removeClass('alert-danger').addClass('alert-success').text('Login successful. Redirecting...').show();
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 2000);
                    } else {
                        $('#loginMessage').removeClass('alert-success').addClass('alert-danger').text(response.message).show();
                    }
                },
                error: function() {
                    $('#loginMessage').removeClass('alert-success').addClass('alert-danger').text('An error occurred. Please try again.').show();
                }
            });
        });
    });
    </script>
</body>
</html>

<?php
session_start();

if($Role == 'Admin' || $Role == 'Sadmin') {
    header('Location: admin.php');
    exit;
} elseif($Role == 'User') {
    header('Location: index.php');
    exit;
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
    <title>DTR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="fence.js"></script>
    <script></script>
</head>
<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="header-left">
                <a href="index.php" class="logo">
                    <img src="assets/img/logo.png" width="35" height="35" alt=""> <span>DICT</span>
                </a>
            </div>
           <a id="toggle_btn" href="javascript:void(0);"><i class="fa fa-bars"></i></a>
            <ul class="nav user-menu float-right">
                <li class="nav-item dropdown has-arrow">
                    <a href="#" class="dropdown-toggle nav-link user-link" data-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle" src="assets/img/user.jpg" width="24" alt="Admin">
                            <span class="status online"></span>
                        </span>
                        <?php
                        $email = $_SESSION['Email'];
                        echo "<span>$email</span>";
                        ?>
                    </a>
                    <div class="dropdown-menu">
                    <a class="dropdown-item" href="editprofile-user.php">Edit Profile</a>
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
                    <a class="dropdown-item" href="editprofile-user.php">Edit Profile</a>
                                            <a class="dropdown-item" href="logout.php">Logout</a>

                </div>
            </div>
        </div>
        <div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>
                        <li class="active">
                            <a href="index.php"><i class="fa fa-calendar "></i> <span>Account</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="row">
                    <div class="col-md-12">
                    <div id="map" style="height: 400px; width: 100%;"></div>
                      <p><span id="distance"></span></p>
                      <p><span id="message"></span></p>
                      <div id="deviceInfo" style="margin: 10px 0;"></div>
                    </div>
                <div class="button-container">
    <div class="top-row">
        <button id="timeInBtn" class="btn btn-primary">Time In</button>
        <button id="timeOutBtn" class="btn btn-danger">Time Out</button>
    </div>
    <button id="viewRecordsBtn" class="btn btn-secondary btn-block" onclick="openRecordsWindow()">View Records</button>
</div>

<style>
    .button-container {
        width: 100%;
        max-width: 300px; /* Adjust as needed */
    }
    .top-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .btn {
        padding: 15px;
        font-size: 16px;
        border-radius: 5px;
        border: none;
        color: white;
        text-align: center;
    }
    #timeInBtn, #timeOutBtn {
        width: 48%; /* Adjust to control spacing between buttons */
    }
    #viewRecordsBtn {
        width: 48%; /* Same width as Time In button */
        margin-left: 0; /* Align with Time In button */
    }
    .btn-primary {
        background-color: #3498db;
    }
    .btn-danger {
        background-color: #ff5252;
    }
    .btn-secondary {
        background-color: #95a5a6;
    }
</style>
                        <table id="infoTable" class="table table-bordered">
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
                                <tr id="todayRow">
                                    <td id="currentDate"></td>
                                    <td id="timeInAM"></td>
                                    <td id="timeOutAM"></td>
                                    <td id="timeInPM"></td>
                                    <td id="timeOutPM"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const timeInBtn = document.getElementById('timeInBtn');
      const timeOutBtn = document.getElementById('timeOutBtn');

      function handleTimeAction(action) {
        if ($("#message").text() !== "You are in the DICT area.") {
          alert('You are not within the allowed office area.');
          return;
        }

        fetchWithErrorHandling('time_in_out.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `action=${action}`
        })
        .then(data => {
          console.log('Response:', data);
          if (data.success) {
            alert(data.message);
            updateElement('currentDate', data.date);
            updateElement(data.field, data.time);
            return fetchWithErrorHandling('get_today_record.php');
          } else {
            throw new Error(data.message || 'Unknown error occurred');
          }
        })
        .then(data => {
          if (data.success) {
            updateElement('currentDate', data.date);
            updateElement('timeInAM', data.timeInAM || '');
            updateElement('timeOutAM', data.timeOutAM || '');
            updateElement('timeInPM', data.timeInPM || '');
            updateElement('timeOutPM', data.timeOutPM || '');
          } else {
            console.error('Error fetching today\'s record:', data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred: ' + error.message);
        });
      }

      timeInBtn.addEventListener('click', () => handleTimeAction('timeIn'));
      timeOutBtn.addEventListener('click', () => handleTimeAction('timeOut'));

      // Function to update today's record
      function updateTodayRecord() {
        fetchWithErrorHandling('get_today_record.php')
          .then(data => {
            if (data.success) {
              updateElement('currentDate', data.date);
              updateElement('timeInAM', data.timeInAM || '');
              updateElement('timeOutAM', data.timeOutAM || '');
              updateElement('timeInPM', data.timeInPM || '');
              updateElement('timeOutPM', data.timeOutPM || '');
            } else {
              console.error('Error fetching today\'s record:', data.message);
            }
          })
          .catch(error => {
            console.error('Error updating today\'s record:', error);
          });
      }

      // Update today's record when the page loads
      updateTodayRecord();

      // Set up an interval to update the record every minute
      setInterval(updateTodayRecord, 60000);
    });

    function updateElement(id, value) {
      var element = document.getElementById(id);
      if (element) {
        element.textContent = value;
      } else {
        console.error('Element with id ' + id + ' not found.');
      }
    }

    // Helper function to handle fetch requests with error handling
    function fetchWithErrorHandling(url, options = {}) {
      return fetch(url, options)
        .then(response => response.text())
        .then(text => {
          try {
            return JSON.parse(text);
          } catch (e) {
            console.error('Error parsing JSON:', text);
            throw new Error('Invalid JSON response from server');
          }
        })
        .then(data => {
          if (!data.success) {
            throw new Error(data.message || 'Unknown error occurred');
          }
          return data;
        })
        .catch(error => {
          console.error('Fetch error:', error);
          throw error;
        });
    }

    function openRecordsWindow() {
        window.open('view_records.php', 'RecordsWindow', 'width=800,height=600');
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
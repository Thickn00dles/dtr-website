<?php
   $servername = "127.0.0.1"; // or 'localhost'
   $username = "u835965540_root"; // default MySQL username
   $password = "Attendancemonitoring1."; // assuming no password is set for root, else use the correct password
   $dbname = "u835965540_dictdb";
   
   // Create connection
   $conn = new mysqli($servername, $username, $password, $dbname);
   
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
   ?>
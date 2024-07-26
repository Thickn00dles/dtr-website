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

// Function to get employee details
function getEmployeeDetails($conn, $empID) {
    $stmt = $conn->prepare("SELECT CONCAT(FirstName, ' ', IFNULL(MiddleName, ''), ' ', LastName) AS FullName, Sig_ID, Position 
                            FROM dict_employee 
                            WHERE empID = ?");
    $stmt->bind_param("i", $empID);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to get signatory details
function getSignatoryDetails($conn, $sigID) {
    if (!$sigID) return null;
    $stmt = $conn->prepare("SELECT CONCAT(FirstName, ' ', IFNULL(MiddleName, ''), ' ', LastName) AS FullName, Position 
                            FROM dict_employee 
                            WHERE empID = ?");
    $stmt->bind_param("i", $sigID);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Fetch all employees for the dropdown
$empSql = "SELECT e.empID, CONCAT(e.FirstName, ' ', IFNULL(e.MiddleName, ''), ' ', e.LastName) AS FullName,
           e.Sig_ID, e.Position,
           CONCAT(s.FirstName, ' ', IFNULL(s.MiddleName, ''), ' ', s.LastName) AS SignatoryName,
           s.Position AS SignatoryPosition
           FROM dict_employee e
           LEFT JOIN dict_employee s ON e.Sig_ID = s.empID
           ORDER BY e.LastName, e.FirstName";
$empResult = $conn->query($empSql);

$empID = isset($_GET['empID']) ? $_GET['empID'] : null;
$employeeDetails = $empID ? getEmployeeDetails($conn, $empID) : null;
$signatoryDetails = $employeeDetails && $employeeDetails['Sig_ID'] ? getSignatoryDetails($conn, $employeeDetails['Sig_ID']) : null;

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
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">
    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
    <style>
        .filter-row .form-group {
            margin-bottom: 15px;
        }
        .filter-row label {
            margin-bottom: 5px;
        }
        .filter-row .form-control,
        .filter-row .select2-container .select2-selection--single {
            height: 40px;
            line-height: 40px;
        }
        .filter-row .btn {
            height: 40px;
            line-height: 38px;
            padding: 0 15px;
        }
        .filter-row .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }
        .filter-row .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }
        .filter-row .btn-group {
            display: flex;
            justify-content: space-between;
        }
        .filter-row .btn-group .btn {
            flex: 1;
            margin-right: 5px;
        }
        .filter-row .btn-group .btn:last-child {
            margin-right: 0;
        }
        .employee-name {
            background-color: #f0f0f0;
            padding: 10px;
            margin-bottom: 0;
            cursor: pointer;
        }
        .employee-name:hover {
            background-color: #e0e0e0;
        }
        .employee-section {
            margin-bottom: 20px;
        }
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
            }
            .print-table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px auto;
            }
            .print-table th, .print-table td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            .print-table th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
            .employee-name {
                background-color: #e6e6e6;
                font-weight: bold;
            }
            .print-table .col-record-id { width: 10%; }
            .print-table .col-date { width: 15%; }
            .print-table .col-time { width: 15%; }
            .print-table .col-total-hours { width: 15%; }
        }
    </style>
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
                                <li><a href="accounts.php">View Account</a></li>
                                <li><a class="active" href="accountrecords.php">View Records</a></li>
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
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label>Select Employee</label>
                            <select class="select floating" name="empID" id="empID">
                                <option value="">All Employees</option>
                                <?php while($emp = $empResult->fetch_assoc()): 
                                    $signatoryDetails = getSignatoryDetails($conn, $emp['Sig_ID']);
                                    $signatoryName = $signatoryDetails ? htmlspecialchars($signatoryDetails['FullName']) : 'N/A';
                                    $signatoryPosition = $signatoryDetails ? htmlspecialchars($signatoryDetails['Position']) : 'N/A';
                                ?>
                                    <option value="<?php echo htmlspecialchars($emp['empID']); ?>" 
                                            data-employee-name="<?php echo htmlspecialchars($emp['FullName']); ?>"
                                            data-employee-position="<?php echo htmlspecialchars($emp['Position']); ?>"
                                            data-signatory-name="<?php echo $signatoryName; ?>"
                                            data-signatory-position="<?php echo $signatoryPosition; ?>">
                                        <?php echo htmlspecialchars($emp['FullName']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label>Start Date</label>
                            <input class="form-control" type="date" name="startDate" id="startDate">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label>End Date</label>
                            <input class="form-control" type="date" name="endDate" id="endDate">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary" onclick="exportToPDF()">Print</button>
                                <div class="dropdown">
                                    <button class="btn btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Export
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                                        <a class="dropdown-item" href="#" onclick="exportToPDF()">PDF</a>
                                        <a class="dropdown-item" href="#" onclick="exportToExcel()">Excel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive" id="recordsTable">
                            <!-- Table content will be loaded here -->
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
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
    
   
   
    <script>


        $(document).ready(function() {
    $('#exportExcelBtn').on('click', exportToExcel);
});

$(document).ready(function() {
        $('#empID').select2({
            minimumResultsForSearch: 1,
            width: '100%'
        });

        $('#empID, #startDate, #endDate').on('change', function() {
            updateRecords();
        });

        function updateRecords() {
            var empID = $('#empID').val();
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();

            $.ajax({
                url: 'get_records.php',
                method: 'GET',
                data: {
                    empID: empID,
                    startDate: startDate,
                    endDate: endDate
                },
                success: function(response) {
                    $('#recordsTable').html(response);
                    setupCollapsibleSections();
                },
                error: function() {
                    alert('An error occurred while fetching records.');
                }
            });
        }

        function setupCollapsibleSections() {
            $('.employee-name').click(function() {
                $(this).next('table').slideToggle();
            });
        }

        // Initial load of records
        updateRecords();
    });

    $(document).on('click', '.show-more', function() {
        var employeeSection = $(this).closest('.employee-section');
        var hiddenRecords = employeeSection.find('.hidden-record');
        
        if (hiddenRecords.is(':visible')) {
            hiddenRecords.hide();
            $(this).text('Show More');
        } else {
            hiddenRecords.show();
            $(this).text('Show Less');
        }
    });

    function getTitle() {
        const employeeName = $('#empID option:selected').text().trim();
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();

        let title = 'Employee Records';
        if (employeeName !== 'Select Employee') {
            title = `Records for ${employeeName}`;
        }
        if (startDate && endDate) {
            title += ` (${startDate} to ${endDate})`;
        } else if (startDate) {
            title += ` (From ${startDate})`;
        } else if (endDate) {
            title += ` (Until ${endDate})`;
        }
        return title;
    }

    function calculateTotalHours(timeInAM, timeOutAM, timeInPM, timeOutPM) {
        const parseTime = (time) => {
            if (!time) return null;
            const [hours, minutes] = time.split(':').map(Number);
            return hours * 60 + minutes;
        };

        const calculateDuration = (start, end) => {
            if (!start || !end) return 0;
            let duration = end - start;
            if (duration < 0) duration += 24 * 60; // Handle overnight shifts
            return duration;
        };

        const morningStart = parseTime(timeInAM);
        const morningEnd = parseTime(timeOutAM);
        const afternoonStart = parseTime(timeInPM);
        const afternoonEnd = parseTime(timeOutPM);

        let totalMinutes = 0;

        if (morningStart && morningEnd) {
            totalMinutes += calculateDuration(morningStart, morningEnd);
        }

        if (afternoonStart && afternoonEnd) {
            totalMinutes += calculateDuration(afternoonStart, afternoonEnd);
        }
        const totalHours = Math.floor(totalMinutes / 60);
        const remainingMinutes = totalMinutes % 60;
        return { hours: totalHours, minutes: remainingMinutes };
    }

    //<!-- Print Records In excell -->

    function exportToExcel() {
    console.log('Starting Excel export');
    const wb = new ExcelJS.Workbook();
    const selectedOption = $('#empID option:selected');
    const isAllEmployees = selectedOption.val() === '';

    $.ajax({
        url: 'get_records.php',
        method: 'GET',
        data: {
            empID: selectedOption.val(),
            startDate: $('#startDate').val(),
            endDate: $('#endDate').val()
        },
        success: function(response) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = response;
            
            if (isAllEmployees) {
                $(tempDiv).find('.employee-section').each(function() {
                    const empName = $(this).find('.employee-name').text().trim();
                    const empID = $(this).data('empid');
                    const empOption = $(`#empID option[value="${empID}"]`);
                    const empPosition = empOption.data('employee-position') || '';
                    const sigName = empOption.data('signatory-name') || '';
                    const sigPosition = empOption.data('signatory-position') || '';
                    exportEmployeeSheet(wb, empName, empID, $(this), empPosition, sigName, sigPosition);
                });
            } else {
                const empSection = $(tempDiv).find('.employee-section');
                const empName = selectedOption.data('employee-name') || selectedOption.text();
                const empID = selectedOption.val();
                const empPosition = selectedOption.data('employee-position') || '';
                const sigName = selectedOption.data('signatory-name') || '';
                const sigPosition = selectedOption.data('signatory-position') || '';
                exportEmployeeSheet(wb, empName, empID, empSection, empPosition, sigName, sigPosition);
            }

            wb.xlsx.writeBuffer().then(buffer => {
                const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = isAllEmployees ? 'all_employees_daily_time_record.xlsx' : `${selectedOption.text().trim()}_daily_time_record.xlsx`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                console.log('Download initiated');
            }).catch(error => {
                console.error('Error writing workbook:', error);
            });
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
}

function exportEmployeeSheet(wb, employeeName, empID, employeeSection, employeePosition, signatoryName, signatoryPosition) {
    const ws = wb.addWorksheet(employeeName);

    // Set column widths (significantly reduced)
    ws.columns = [
        { width: 2 }, { width: 6 }, { width: 4 }, { width: 4 }, { width: 4 },
        { width: 4 }, { width: 3 }, { width: 3 }, { width: 3 }, { width: 3 }
    ];

    // Add header
    addHeaderToSheet(ws, employeeName, employeePosition);

    // Pre-populate rows for all 31 days
    for (let day = 1; day <= 31; day++) {
        const rowData = [day, '', '', '', '', '', '', '', '', ''];
        const wsRow = ws.addRow(rowData);
        styleDataRow(wsRow);
        wsRow.height = 8; // Significantly reduce row height
    }

    // Add data rows
    employeeSection.find('table tbody tr').each(function() {
        addDataRowToSheet(ws, $(this));
    });

    // Add summary rows
    addSummaryRowsToSheet(ws);

    // Add certification
    addCertificationToSheet(ws, employeeName, employeePosition, signatoryName, signatoryPosition);

    // Add border around the entire template
    const lastRow = ws.lastRow.number;
    const borderStyle = { style: 'thin', color: { argb: '000000' } };
    ws.views = [{ state: 'frozen', xSplit: 0, ySplit: 8 }];
    for (let col = 1; col <= 10; col++) {
        ws.getCell(1, col).border = { ...ws.getCell(1, col).border, top: borderStyle };
        ws.getCell(lastRow, col).border = { ...ws.getCell(lastRow, col).border, bottom: borderStyle };
    }
    for (let row = 1; row <= lastRow; row++) {
        ws.getCell(row, 1).border = { ...ws.getCell(row, 1).border, left: borderStyle };
        ws.getCell(row, 10).border = { ...ws.getCell(row, 10).border, right: borderStyle };
    }
}

function addHeaderToSheet(ws, employeeName, employeePosition) {
    addMergedCell(ws, 'A1:J1', 'CS FORM 48', { bold: true, size: 3 }, { vertical: 'middle', horizontal: 'right' });
    addMergedCell(ws, 'A2:J2', 'DAILY TIME RECORD', { bold: true, size: 6 }, { vertical: 'middle', horizontal: 'center' });
    addMergedCell(ws, 'A3:J3', `${employeeName}`, { bold: true, size: 5 }, { vertical: 'middle', horizontal: 'center' });
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    let dateRange = '';
    if (startDate && endDate) {
        dateRange = ` (${startDate} to ${endDate})`;
    } else if (startDate) {
        dateRange = ` (From ${startDate})`;
    } else if (endDate) {
        dateRange = ` (Until ${endDate})`;
    }
    addMergedCell(ws, 'A4:J4', `for the month of ${new Date().toLocaleString('default', { month: 'long', year: 'numeric' })}${dateRange}`, { size: 5 }, { vertical: 'left', horizontal: 'left' });
    addMergedCell(ws, 'A5:J5', 'Official hours for ARRIVAL and DEPARTURES', { size: 4 }, { vertical: 'left', horizontal: 'left' });
    addMergedCell(ws, 'A6:J6', 'REGULAR DAYS: Mon (8-5) Tuesday-Friday (Flexi Time)', { size: 4 }, { vertical: 'left', horizontal: 'left' });
    
    const headers = ['Day', 'DATE', 'AM', '', 'PM', '', 'TOTAL', '', 'UNDER', ''];
    const subHeaders = ['', '', 'IN', 'OUT', 'IN', 'OUT', 'HRS', 'MIN', 'HRS', 'MIN'];
    
    ws.addRow(headers);
    ws.addRow(subHeaders);

    // Adjust row heights to fit content (further reduced)
    ws.getRow(1).height = 8;
    ws.getRow(2).height = 10;
    ws.getRow(3).height = 9;
    ws.getRow(4).height = 8;
    ws.getRow(5).height = 8;
    ws.getRow(6).height = 9;
    ws.getRow(7).height = 9;
    ws.getRow(8).height = 9;

    [7, 8].forEach(rowNumber => {
        const row = ws.getRow(rowNumber);
        row.eachCell((cell) => {
            cell.font = { bold: true, size: 4 };
            cell.alignment = { vertical: 'middle', horizontal: 'center' };
            cell.border = {
                top: {style:'thin'}, left: {style:'thin'},
                bottom: {style:'thin'}, right: {style:'thin'}
            };
        });
    });

    ws.mergeCells('C7:D7');
    ws.mergeCells('E7:F7');
    ws.mergeCells('G7:H7');
    ws.mergeCells('I7:J7');
}

function addDataRowToSheet(ws, row) {
    const cells = row.find('td');
    const dateStr = cells.eq(1).text().trim();
    
    const dateParts = dateStr.split('-');
    if (dateParts.length !== 3) {
        console.error('Invalid date format:', dateStr);
        return;
    }
    const dayOfMonth = parseInt(dateParts[2], 10);

    if (isNaN(dayOfMonth) || dayOfMonth < 1 || dayOfMonth > 31) {
        console.error('Invalid day of month:', dayOfMonth);
        return;
    }

    const wsRow = ws.getRow(dayOfMonth + 8);

    wsRow.getCell(2).value = dateStr; // DATE
    wsRow.getCell(3).value = cells.eq(2).text().trim(); // AM IN
    wsRow.getCell(4).value = cells.eq(3).text().trim(); // AM OUT
    wsRow.getCell(5).value = cells.eq(4).text().trim(); // PM IN
    wsRow.getCell(6).value = cells.eq(5).text().trim(); // PM OUT

    const totalHours = cells.eq(6).text().trim();
    let [hours, minutes] = totalHours.split(':').map(Number);
    hours = hours || 0;
    minutes = minutes || 0;

    wsRow.getCell(7).value = hours; // TOTAL HRS
    wsRow.getCell(8).value = minutes; // TOTAL MIN

    styleDataRow(wsRow);
}

function styleDataRow(wsRow) {
    wsRow.eachCell((cell) => {
        cell.font = { size: 5 };
        cell.alignment = { vertical: 'middle', horizontal: 'center' };
        cell.border = {
            top: {style:'thin'}, left: {style:'thin'},
            bottom: {style:'thin'}, right: {style:'thin'}
        };
    });
}

function addMergedCell(ws, cell, value, font, alignment) {
    ws.getCell(cell).value = value;
    ws.getCell(cell).font = font;
    ws.getCell(cell).alignment = alignment;
    ws.mergeCells(cell);
}

function addSummaryRowsToSheet(ws) {
    ['VL', 'T/UT', 'PL/SPL', 'SL'].forEach(label => {
        const rowData = ['', '', '', '', '', label, '', '', '', ''];
        const wsRow = ws.addRow(rowData);
        wsRow.height = 8; // Further reduce row height
        wsRow.eachCell((cell) => {
            cell.font = { size: 5 };
            cell.alignment = { vertical: 'middle', horizontal: 'center' };
            cell.border = {
                top: {style:'thin'}, left: {style:'thin'},
                bottom: {style:'thin'}, right: {style:'thin'}
            };
        });
        ws.mergeCells(`G${wsRow.number}:J${wsRow.number}`);
    });
}

function addCertificationToSheet(ws, employeeName, employeePosition, signatoryName, signatoryPosition) {
    const lastRow = ws.lastRow.number;
    const certifyText = "I certify on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.";

    addMergedCell(ws, `A${lastRow + 1}:J${lastRow + 2}`, certifyText, { size: 4 }, { wrapText: true, vertical: 'top', horizontal: 'left' });
    ws.getRow(lastRow + 1).height = 20; // Adjust height for certification text

    // Employee signature
    addMergedCell(ws, `A${lastRow + 3}:E${lastRow + 3}`, employeeName, { bold: true, size: 5 }, { horizontal: 'center' });
    addMergedCell(ws, `A${lastRow + 4}:E${lastRow + 4}`, employeePosition, { italic: true, size: 4 }, { horizontal: 'center' });

    // Signatory (supervisor) signature
    addMergedCell(ws, `F${lastRow + 3}:J${lastRow + 3}`, signatoryName, { bold: true, size: 5 }, { horizontal: 'center' });
    addMergedCell(ws, `F${lastRow + 4}:J${lastRow + 4}`, signatoryPosition, { italic: true, size: 4 }, { horizontal: 'center' });

    // Adjust row heights for signature lines
    ws.getRow(lastRow + 3).height = 9;
    ws.getRow(lastRow + 4).height = 9;
}

 //<!-- ENd of Print Records In excell -->

 //<!-- Print Records In PDF -->

 function exportToPDF() {
    console.log('Starting compact PDF export');
    const { jsPDF } = window.jspdf;
    const selectedOption = $('#empID option:selected');
    const isAllEmployees = selectedOption.val() === '';

    $.ajax({
        url: 'get_records.php',
        method: 'GET',
        data: {
            empID: selectedOption.val(),
            startDate: $('#startDate').val(),
            endDate: $('#endDate').val()
        },
        success: function(response) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = response;
            
            if (isAllEmployees) {
                const pdf = new jsPDF('p', 'mm', 'a4'); // Use A4 size
                $(tempDiv).find('.employee-section').each(function(index) {
                    if (index > 0) pdf.addPage();
                    const empName = $(this).find('.employee-name').text().trim();
                    const empID = $(this).data('empid');
                    const empOption = $(`#empID option[value="${empID}"]`);
                    const empPosition = empOption.data('employee-position') || '';
                    const sigName = empOption.data('signatory-name') || '';
                    const sigPosition = empOption.data('signatory-position') || '';
                    exportEmployeePDF(pdf, empName, empID, $(this), empPosition, sigName, sigPosition);
                });
                pdf.save('all_employees_compact_dtr.pdf');
            } else {
                const pdf = new jsPDF('p', 'mm', 'a4'); // Use A4 size
                const empSection = $(tempDiv).find('.employee-section');
                const empName = selectedOption.data('employee-name') || selectedOption.text();
                const empID = selectedOption.val();
                const empPosition = selectedOption.data('employee-position') || '';
                const sigName = selectedOption.data('signatory-name') || '';
                const sigPosition = selectedOption.data('signatory-position') || '';
                exportEmployeePDF(pdf, empName, empID, empSection, empPosition, sigName, sigPosition);
                pdf.save(`${empName}_compact_dtr.pdf`);
            }
            console.log('Compact PDF download initiated');
        },
        error: function(error) {
            console.error('Error fetching data:', error);
        }
    });
}

function exportEmployeePDF(pdf, employeeName, empID, employeeSection, employeePosition, signatoryName, signatoryPosition) {
    const pageWidth = pdf.internal.pageSize.width;
    const pageHeight = pdf.internal.pageSize.height;
    const margin = 10;
    const contentWidth = pageWidth - 2 * margin;
    const contentHeight = pageHeight - 2 * margin;

    pdf.setFontSize(12);
    pdf.text('DAILY TIME RECORD', pageWidth / 2, margin + 5, { align: 'center' });
    
    pdf.setFontSize(10);
    pdf.text(`For the month of ${new Date().toLocaleString('default', { month: 'long', year: 'numeric' })}`, pageWidth / 2, margin + 10, { align: 'center' });
    
    pdf.setFontSize(8);
    pdf.text('Official hours for arrival and departure', pageWidth / 2, margin + 15, { align: 'center' });
    pdf.text('Regular days:', pageWidth / 2, margin + 20, { align: 'center' });

    pdf.setFontSize(10);
    pdf.text(employeeName, pageWidth / 2, margin + 25, { align: 'center' });

    // Table header
    const headers = [['Day', 'AM In', 'AM Out', 'PM In', 'PM Out', 'Hours', 'Minutes']];
    const data = [];

    for (let i = 1; i <= 31; i++) {
        data.push([i, '', '', '', '', '', '']);
    }

    pdf.autoTable({
        head: headers,
        body: data,
        startY: margin + 30,
        theme: 'grid',
        styles: { fontSize: 6, cellPadding: 1 },
        columnStyles: {
            0: { cellWidth: 10 },
            1: { cellWidth: 20 },
            2: { cellWidth: 20 },
            3: { cellWidth: 20 },
            4: { cellWidth: 20 },
            5: { cellWidth: 15 },
            6: { cellWidth: 15 }
        },
        headStyles: { fillColor: [255, 255, 255], textColor: 0, fontSize: 6, fontStyle: 'bold' }
    });

    // Summary rows
    const summaryData = [
        ['', '', '', '', 'Total', '', ''],
        ['', '', '', '', 'Tardy', '', ''],
        ['', '', '', '', 'Undertime', '', '']
    ];

    pdf.autoTable({
        body: summaryData,
        startY: pdf.lastAutoTable.finalY,
        theme: 'grid',
        styles: { fontSize: 6, cellPadding: 1 },
        columnStyles: {
            0: { cellWidth: 10 },
            1: { cellWidth: 20 },
            2: { cellWidth: 20 },
            3: { cellWidth: 20 },
            4: { cellWidth: 20 },
            5: { cellWidth: 15 },
            6: { cellWidth: 15 }
        }
    });

    // Certification
    pdf.setFontSize(6);
    const certText = "I certify on my honor that the above is a true and correct report of the hours of work performed, record of which was made daily at the time of arrival and departure from office.";
    pdf.text(certText, margin, pdf.lastAutoTable.finalY + 10, { maxWidth: contentWidth, align: 'justify' });

    // Signatures
    const signatureY = pageHeight - margin - 10;
    pdf.line(margin, signatureY, margin + 70, signatureY);
    pdf.line(pageWidth - margin - 70, signatureY, pageWidth - margin, signatureY);

    pdf.setFontSize(8);
    pdf.text(employeeName, margin + 35, signatureY + 5, { align: 'center' });
    pdf.text(signatoryName, pageWidth - margin - 35, signatureY + 5, { align: 'center' });

    pdf.setFontSize(6);
    pdf.text(employeePosition, margin + 35, signatureY + 10, { align: 'center' });
    pdf.text(signatoryPosition, pageWidth - margin - 35, signatureY + 10, { align: 'center' });
}


//<!-- End of Print Records In PDF -->

    </script>
</body>
</html>

<?php
$conn->close();
?>
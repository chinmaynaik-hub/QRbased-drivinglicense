<?php
session_start();
if (!isset($_SESSION['aadhar'])) {
    header("Location: newDL.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Submitted DL Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="/RTO_Bheemanna/assets/css/style.css">
    <style>
        body {
            background-color: rgba(96, 157, 219, 0.36);
        }
        .card-custom {
            background-color: #ffffff;
            border: none;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <?php require_once('includes/header.php'); ?>
    
    <div class="container my-5">
        <div class="col-lg-8 mx-auto">
            <div class="card-custom">
                <h3 class="text-center bg-warning text-white p-3 rounded mb-4">DL APPLICATION SUBMITTED</h3>
                
                <?php
                if (isset($_SESSION['aadhar'])) {
                    require_once('config/Connection.php');
                    $aadhar = $_SESSION['aadhar'];
                    $rto = isset($_SESSION['rto']) ? $_SESSION['rto'] : 'N/A';
                    
                    $obj = new Connection();
                    $db = $obj->getNewConnection();
                    
                    // Query the licenses table with normalized schema
                    $sql = "SELECT l.license_id, l.licenseNumber, l.examDate, l.status, l.validityDate,
                            r.rtoName, r.rtoCode,
                            vc.classCode, vc.classDescription
                            FROM licenses l
                            JOIN person p ON l.person_id = p.person_id
                            JOIN rtooffices r ON l.rto_id = r.rto_id
                            JOIN vehicleclasses vc ON l.class_id = vc.class_id
                            WHERE p.aadhar = ? AND l.licenseType = 'DL'
                            ORDER BY l.license_id DESC
                            LIMIT 1";
                    
                    $stmt = $db->prepare($sql);
                    
                    if ($stmt === false) {
                        echo "<div class='alert alert-danger'>Error preparing statement: " . $db->error . "</div>";
                    } else {
                        $stmt->bind_param("s", $aadhar);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();

                        if (!$row) {
                            echo "<p class='alert alert-danger text-center'>No DL application found for the provided Aadhar.</p>";
                        } else {
                            $status = $row['status'];
                            $license_id = $row['license_id'];
                            $licenseNumber = $row['licenseNumber'];
                            $examDate = $row['examDate'];
                            $validityDate = $row['validityDate'];
                            $rtoDisplay = $row['rtoName'] . ' (' . $row['rtoCode'] . ')';
                            $vehicleClass = $row['classCode'] . ' - ' . $row['classDescription'];

                            // Convert status to human-readable format
                            $statusText = $status == 1 ? 'Approved' : 'Pending';
                            $statusClass = $status == 1 ? 'status-approved' : 'status-pending';
                            
                            echo "<div class='row'>
                                    <div class='col-lg-12'>
                                        <ul class='list-group list-group-flush'>
                                            <li class='list-group-item bg-warning text-white text-center font-weight-bold'>
                                                DL APPLICATION STATUS
                                            </li>
                                            
                                            <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                                                <span class='font-weight-bold text-dark'>DL Number:</span>
                                                <span class='text-dark'>$licenseNumber</span>
                                            </li>
                                            
                                            <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                                                <span class='font-weight-bold text-dark'>Status:</span>
                                                <span class='status-badge $statusClass'>$statusText</span>
                                            </li>
                                            
                                            <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                                                <span class='font-weight-bold text-dark'>Test Date:</span>
                                                <span class='text-dark'>" . date('d-M-Y', strtotime($examDate)) . "</span>
                                            </li>
                                            
                                            <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                                                <span class='font-weight-bold text-dark'>Validity:</span>
                                                <span class='text-dark'>" . date('d-M-Y', strtotime($validityDate)) . "</span>
                                            </li>
                                            
                                            <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                                                <span class='font-weight-bold text-dark'>Vehicle Class:</span>
                                                <span class='text-dark'>$vehicleClass</span>
                                            </li>
                                            
                                            <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                                                <span class='font-weight-bold text-dark'>RTO Office:</span>
                                                <span class='text-dark'>$rtoDisplay</span>
                                            </li>
                                            
                                            <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                                                <span class='font-weight-bold text-dark'>Application ID:</span>
                                                <span class='text-dark'>$license_id</span>
                                            </li>
                                        </ul>
                                    </div>
                                  </div>";
                        }
                        $stmt->close();
                    }
                    $db->close();
                }
                ?>
                
                <div class="alert alert-success text-center mt-4" role="alert">
                    <i class="fa fa-check-circle mr-2"></i>
                    <strong>Form Submitted Successfully!</strong>
                </div>
                
                <div class="text-center">
                    <a href="/RTO_Bheemanna/index.php" class="btn btn-success btn-lg px-5 py-2 rounded-pill">
                        <i class="fa fa-home mr-2"></i> Go to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once('includes/footer.php'); ?>
    
    <!-- ##### All Javascript Script ##### -->
    <script src="/RTO_Bheemanna/assets/js/jquery/jquery-2.2.4.min.js"></script>
    <script src="/RTO_Bheemanna/assets/js/bootstrap/popper.min.js"></script>
    <script src="/RTO_Bheemanna/assets/js/bootstrap/bootstrap.min.js"></script>
    <script src="/RTO_Bheemanna/assets/js/plugins/plugins.js"></script>
    <script src="/RTO_Bheemanna/assets/js/active.js"></script>
</body>
</html>

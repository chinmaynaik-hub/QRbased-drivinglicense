<?php
ob_start(); // Start output buffering
session_start();
require_once('includes/header.php');

// Redirect if session is not set
if (!isset($_SESSION['aadhar']) || !isset($_SESSION['person_id']) || !isset($_SESSION['licenseNumber'])) {
    header("Location: index.php");
    exit();
}

require_once('config/Connection.php');
$aadhar = $_SESSION['aadhar'];
$person_id = $_SESSION['person_id'];
$rto_id = $_SESSION['rto_id'];
$rto_name = $_SESSION['rto_name'];
$licenseNumber = $_SESSION['licenseNumber']; // Get exact license number from session

$obj = new Connection();
$db = $obj->getNewConnection();

// Fetch LL details from normalized tables using EXACT license number
$sql = "SELECT l.licenseNumber, l.issueDate, l.validityDate, l.class_id,
        p.name, p.fatherName, p.dob, p.bloodGroup, p.address, p.aadhar, p.gender, p.mobileNumber, p.email,
        vc.classCode, vc.classDescription,
        r.rtoName, r.rtoCode
        FROM licenses l
        JOIN person p ON l.person_id = p.person_id
        JOIN vehicleclasses vc ON l.class_id = vc.class_id
        JOIN rtooffices r ON l.rto_id = r.rto_id
        WHERE l.licenseNumber = ? AND p.aadhar = ? AND l.licenseType = 'LL'
        LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $licenseNumber, $aadhar);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "No records found.";
    exit();
}

// Sanitize output
$name = htmlspecialchars($row['name']);
$fatherName = htmlspecialchars($row['fatherName']);
$dob = htmlspecialchars($row['dob']);
$bloodGroup = htmlspecialchars($row['bloodGroup']);
$address = htmlspecialchars($row['address']);
$validity = htmlspecialchars($row['validityDate']);
$issueDate = htmlspecialchars($row['issueDate']);
$gender = htmlspecialchars($row['gender']);
$mobileno = htmlspecialchars($row['mobileNumber']);
$email = htmlspecialchars($row['email']);
$rto = htmlspecialchars($row['rtoName'] . ' (' . $row['rtoCode'] . ')');
$vehicleType = htmlspecialchars($row['classCode'] . ' - ' . $row['classDescription']);
$displayLicenseNumber = htmlspecialchars($row['licenseNumber']);
$class_id = $row['class_id'];
?>

<div class="container my-5">
    <!-- Page Title -->
    <div class="bg-warning text-white text-center rounded shadow-lg p-4 mb-4">
        <h1 class="display-4 font-weight-bold mb-2 text-white">CONFIRM DL APPLICATION</h1>
        <!-- <p class="lead mb-0 text-white">Please review your details before applying for Driving License</p> -->
    </div> 
    
    <div class="row">
        <div class="col-lg-9 mx-auto">
            <!-- Card -->
            <div class="card shadow-lg border-0 rounded">
                <!-- Card Header -->
                <div class="card-header bg-warning text-white text-center py-3">
                    <h3 class="mb-0 font-weight-bold text-white">LEARNER LICENSE DETAILS</h3>
                </div>
                
                <!-- Info Alert -->
                <div class="alert alert-info border-left border-info m-3 mb-0" role="alert">
                    <i class="fa fa-info-circle mr-2"></i>
                    <strong>Note:</strong> Please verify all details carefully. Once confirmed, your DL application will be submitted for processing.
                </div>
                
                <!-- Card Body -->
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">LL Number:</span>
                            <span class="text-dark"><?php echo $licenseNumber; ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">Name:</span>
                            <span class="text-dark"><?php echo $name; ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">Last Name:</span>
                            <span class="text-dark"><?php echo $fatherName; ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">Aadhar Number:</span>
                            <span class="text-dark"><?php echo $aadhar; ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">Date of Birth:</span>
                            <span class="text-dark"><?php echo date('d-M-Y', strtotime($dob)); ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">Blood Group:</span>
                            <span class="text-dark"><?php echo $bloodGroup; ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">Gender:</span>
                            <span class="text-dark"><?php echo $gender; ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">Mobile Number:</span>
                            <span class="text-dark"><?php echo $mobileno; ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">Email:</span>
                            <span class="text-dark"><?php echo $email; ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">Address:</span>
                            <span class="text-dark"><?php echo $address; ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">RTO Office:</span>
                            <span class="text-dark"><?php echo $rto; ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">LL Issue Date:</span>
                            <span class="text-dark"><?php echo date('d-M-Y', strtotime($issueDate)); ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">LL Validity:</span>
                            <span class="text-dark"><?php echo date('d-M-Y', strtotime($validity)); ?></span>
                        </li>
                        <li class='list-group-item d-flex justify-content-between align-items-center py-3'>
                            <span class="font-weight-bold text-dark">Vehicle Class:</span>
                            <span class="text-dark"><?php echo $vehicleType; ?></span>
                        </li>
                    </ul>
                </div>

                <!-- Card Footer -->
                <div class="card-footer bg-light text-center py-4">
                    <form method="POST">
                        <button class="btn btn-success btn-lg px-5 py-3 rounded-pill font-weight-bold text-uppercase shadow" type="submit" name="confirm">
                            <i class="fa fa-check-circle mr-2"></i> Confirm & Apply For DL
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if (isset($_POST['confirm'])) {
    $obj = new Connection();
    $db = $obj->getNewConnection();

    // Check if person already has a DL for this vehicle class
    $checkStmt = $db->prepare("SELECT * FROM licenses WHERE person_id = ? AND class_id = ? AND licenseType = 'DL'");
    $checkStmt->bind_param("ii", $person_id, $class_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $checkStmt->close();
        $db->close();
        echo "<script>
                alert('You already have a DL for this vehicle class.');
                window.location.href = 'displayalldl.php';
              </script>";
        exit();
    } else {
        $Date = date("Y-m-d");
        $examDate = date('Y-m-d', strtotime($Date . ' + 15 days'));
        $validityDate = date("Y-m-d", strtotime($Date . ' + 20 years'));
        
        // Update ONLY the specific LL license to DL using exact license number
        $updateStmt = $db->prepare("UPDATE licenses 
        SET licenseType = 'DL', examDate = ?, validityDate = ?, status = 'pending' 
        WHERE licenseNumber = ? AND licenseType = 'LL'");
        $updateStmt->bind_param("sss", $examDate, $validityDate, $licenseNumber);
        $updateStmt->execute();

        if ($updateStmt->affected_rows > 0) {
            // Update license number from LL to DL for this specific license only
            $newLicenseNumber = str_replace("LL_", "DL_", $licenseNumber);
            $updateLicenseNum = $db->prepare("UPDATE licenses SET licenseNumber = ? WHERE licenseNumber = ?");
            $updateLicenseNum->bind_param("ss", $newLicenseNumber, $licenseNumber);
            $updateLicenseNum->execute();
            
            $updateLicenseNum->close();
            $updateStmt->close();
            $checkStmt->close();
            
            $_SESSION['dlNumber'] = $newLicenseNumber;
            $_SESSION['examDate'] = $examDate;
            $_SESSION['rto'] = $rto_name;
            
            $db->close();
            header("Location: displayDL.php");
            exit();
        } else {
            $updateStmt->close();
            $checkStmt->close();
            $db->close();
            error_log("Update Error: " . $db->error);
            echo "<script>
                    alert('Error updating record. Please try again.');
                    window.location.href = 'displayalldl.php';
                  </script>";
            exit();
        }
    }
} else {
    $db->close();
}
?>

<?php require_once('includes/footer.php'); ?>

<!-- ##### All Javascript Script ##### -->
<script src="assets/js/jquery/jquery-2.2.4.min.js"></script>
<script src="assets/js/bootstrap/popper.min.js"></script>
<script src="assets/js/bootstrap/bootstrap.min.js"></script>
<script src="assets/js/plugins/plugins.js"></script>
<script src="assets/js/active.js"></script>
</body>
</html>

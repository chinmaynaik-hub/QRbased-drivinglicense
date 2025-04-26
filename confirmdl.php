<?php
ob_start(); // Start output buffering

session_start();
require_once('header.php');

if (!isset($_SESSION['llno']) || !isset($_SESSION['aadhar'])) {
    header("Location: index.php");
    exit();
}

require_once('Connection.php');
$llno = $_SESSION['llno'];
$aadhar = $_SESSION['aadhar'];
$obj = new Connection();
$db = $obj->getNewConnection();

// Prepared statement to prevent SQL injection
$stmt = $db->prepare("SELECT * FROM ll WHERE llno = ? AND aadhar = ?");
$stmt->bind_param("ii", $llno, $aadhar);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$db->close();

if (!$row) {
    echo "No records found.";
    exit();
}

$name = htmlspecialchars($row['name']);
$fatherName = htmlspecialchars($row['fatherName']);
$dob = htmlspecialchars($row['dob']);
$bloodGroup = htmlspecialchars($row['bloodGroup']);
$address = htmlspecialchars($row['address']);
$validity = htmlspecialchars($row['validity']);
$issueDate = htmlspecialchars($row['issueDate']);
$gender = htmlspecialchars($row['gender']);
$mobileno = htmlspecialchars($row['mobileNumber']);
$email = htmlspecialchars($row['email']);
$rto = htmlspecialchars($row['rto']);
$vehicleType = htmlspecialchars($row['licenseType']);
?>
<div class="row">
    <div class="col-lg-6 m-auto d-block">
        <ul class="list-group">
            <li class='list-group-item text-muted' contenteditable='false'>LL</li>
            <li class='list-group-item text-right'><span class='pull-left'><strong class=''>LL Number:</strong></span><?php echo $llno; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong class=''>Name:</strong></span><?php echo $name; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong class=''>Aadhar Number:</strong></span><?php echo $aadhar; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong class=''>Father's Name:</strong></span><?php echo $fatherName; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong class=''>DOB:</strong></span><?php echo $dob; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong class=''>Blood Group:</strong></span><?php echo $bloodGroup; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong class=''>Address:</strong></span><?php echo $address; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong class=''>Issue Date:</strong></span><?php echo $issueDate; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong class=''>Validity:</strong></span><?php echo $validity; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong class=''>License Type :</strong></span><?php echo $vehicleType; ?></li>

        </ul>
        <form method="post">
        <form method="POST" action="displayDL.php">
    <center>
        <div class="btn-container">
        <input class="btn btn-success" type="submit" value="Confirm Details" name="confirm">
    </center>
    </div>
</form>

    </div>
</div>
<?php
if (isset($_POST['confirm'])) {
    require_once('Connection.php');
    $obj = new Connection();
    $db = $obj->getNewConnection();

    $Date = date("Y-m-d");
    $examDate = date('Y-m-d', strtotime($Date . ' + 15 days'));

    // Insert into dl table
    $stmt = $db->prepare("INSERT INTO dl (name, fatherName, dob, bloodGroup, address, aadhar, gender, mobileNumber, email, rto, examDate, licenseType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $name, $fatherName, $dob, $bloodGroup, $address, $aadhar, $gender, $mobileno, $email, $rto, $examDate, $vehicleType);
    $stmt->execute();

    // Check if insert was successful
    if ($stmt->affected_rows > 0) {
        $_SESSION['aadhar'] = $aadhar;
        $_SESSION['rto'] = $rto;
        header("Location: displayDL.php");
        exit();
    } else {
        // Log error
        error_log("Error: " . $db->error);
        echo "Error: " . $db->error;
    }

    $db->close();
}
?>
<?php require_once('footer.php'); ?>
<!-- ##### All Javascript Script ##### -->
<!-- jQuery-2.2.4 js -->
<script src="js/jquery/jquery-2.2.4.min.js"></script>
<!-- Popper js -->
<script src="js/bootstrap/popper.min.js"></script>
<!-- Bootstrap js -->
<script src="js/bootstrap/bootstrap.min.js"></script>
<!-- All Plugins js -->
<script src="js/plugins/plugins.js"></script>
<!-- Active js -->
<script src="js/active.js"></script>
</body>
</html>

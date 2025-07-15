<?php
ob_start(); // Start output buffering
session_start();
require_once('header.php');

// Redirect if session is not set
if (!isset($_SESSION['llno']) || !isset($_SESSION['aadhar'])) {
    header("Location: index.php");
    exit();
}

require_once('Connection.php');
$llno = $_SESSION['llno'];
$aadhar = $_SESSION['aadhar'];

$obj = new Connection();
$db = $obj->getNewConnection();

// Fetch LL details
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

// Sanitize output
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
            <li class='list-group-item text-center' style='color: black;'><strong>LL DETAILS</strong></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong>LL Number:</strong></span><?php echo $llno; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong>Name:</strong></span><?php echo $name; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong>Last Name:</strong></span><?php echo $fatherName; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong>Aadhar Number:</strong></span><?php echo $aadhar; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong>DOB:</strong></span><?php echo $dob; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong>Blood Group:</strong></span><?php echo $bloodGroup; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong>Address:</strong></span><?php echo $address; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong>Issue Date:</strong></span><?php echo $issueDate; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong>Validity:</strong></span><?php echo $validity; ?></li>
            <li class='list-group-item text-right'><span class='pull-left'><strong>License Type:</strong></span><?php echo $vehicleType; ?></li>
        </ul>

        <!-- Confirm Form -->
        <form method="POST">
            <center class="mt-3">
                <input class="btn btn-success" type="submit" value="Confirm Details" name="confirm">
            </center>
        </form>
    </div>
</div>

<?php
if (isset($_POST['confirm'])) {
    $obj = new Connection();
    $db = $obj->getNewConnection();

    // Check if Aadhar already exists in dl table
    $checkStmt = $db->prepare("SELECT * FROM dl WHERE aadhar = ?");
    $checkStmt->bind_param("s", $aadhar);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Aadhar number already exists in DL records.');</script>";
    } else {
        $Date = date("Y-m-d");
        $examDate = date('Y-m-d', strtotime($Date . ' + 15 days'));

        // Insert into dl table
        $stmt = $db->prepare("INSERT INTO dl (name, fatherName, dob, bloodGroup, address, aadhar, gender, mobileNumber, email, rto, examDate, licenseType) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssss", $name, $fatherName, $dob, $bloodGroup, $address, $aadhar, $gender, $mobileno, $email, $rto, $examDate, $vehicleType);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['aadhar'] = $aadhar;
            $_SESSION['rto'] = $rto;
            header("Location: displayDL.php");
            exit();
        } else {
            error_log("Insert Error: " . $db->error);
            echo "<script>alert('Error inserting record. Please try again.');</script>";
        }
    }

    $db->close();
}
?>

<?php require_once('footer.php'); ?>

<!-- ##### All Javascript Script ##### -->
<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/popper.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/plugins/plugins.js"></script>
<script src="js/active.js"></script>
</body>
</html>

<?php
session_start();

// Check if aadhar is set in session
if (!isset($_SESSION['aadhar'])) {
    header("Location: newDL.php");
    exit();
}

$aadhar = $_SESSION['aadhar'];

require_once('config/Connection.php');
$obj = new Connection();
$db = $obj->getNewConnection();

// Query to get all LL licenses for this aadhar
$sql = "SELECT 
            l.license_id,
            l.licenseNumber,
            l.status,
            l.issueDate,
            l.examDate,
            l.validityDate,
            p.person_id,
            p.aadhar,
            p.name,
            p.fatherName,
            p.dob,
            p.bloodGroup,
            p.gender,
            p.address,
            p.mobileNumber,
            p.email,
            r.rto_id,
            r.rtoName,
            r.rtoCode,
            vc.class_id,
            vc.classCode,
            vc.classDescription
        FROM licenses l
        JOIN person p ON l.person_id = p.person_id
        JOIN rtooffices r ON l.rto_id = r.rto_id
        JOIN vehicleclasses vc ON l.class_id = vc.class_id
        WHERE p.aadhar = ? AND l.licenseType = 'LL'
        ORDER BY l.issueDate DESC";

$stmt = $db->prepare($sql);
$stmt->bind_param("s", $aadhar);
$stmt->execute();
$res = $stmt->get_result();

// Handle Apply button click
if (isset($_POST['action']) && $_POST['action'] == 'Apply' && isset($_POST['license_id'])) {
    $license_id = $_POST['license_id'];
    
    // Get the license details
    $getLicenseSql = "SELECT l.licenseNumber, l.person_id, l.class_id, r.rto_id, r.rtoName, l.status
                      FROM licenses l
                      JOIN rtooffices r ON l.rto_id = r.rto_id
                      WHERE l.license_id = ? AND l.licenseType = 'LL'";
    
    $stmtLicense = $db->prepare($getLicenseSql);
    $stmtLicense->bind_param("i", $license_id);
    $stmtLicense->execute();
    $licenseResult = $stmtLicense->get_result();
    $licenseRow = $licenseResult->fetch_assoc();
    
    if ($licenseRow) {
        // Check if status is approved
        if ($licenseRow['status'] == 1 || $licenseRow['status'] == 'approved') {
            // Set session variables including the exact license number
            $_SESSION['licenseNumber'] = $licenseRow['licenseNumber']; // Pass exact license number
            $_SESSION['aadhar'] = $aadhar;
            $_SESSION['person_id'] = $licenseRow['person_id'];
            $_SESSION['rto_id'] = $licenseRow['rto_id'];
            $_SESSION['rto_name'] = $licenseRow['rtoName'];
            
            header("Location: confirmdl.php");
            exit();
        } else {
            $error_message = "This LL is not approved yet. Cannot apply for DL.";
        }
    }
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Apply for DL - Select License</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="/RTO_Bheemanna/assets/css/style.css">
    <style>
        body {
            background-color: rgba(96, 157, 219, 0.36);
        }
    </style>
</head>
<body>
    <?php require_once('includes/header.php'); ?>
    
    <div class="container my-5">
        <div class="bg-warning text-white text-center rounded shadow-lg p-4 mb-4">
            <h1 class="display-4 font-weight-bold mb-2 text-white">SELECT LICENSE TO APPLY FOR DL</h1>
            <p class="lead mb-0 text-white">Choose the learner license you want to upgrade to a driving license</p>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> <?php echo $error_message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <?php if ($res->num_rows > 0): ?>
            <div class="card shadow-lg border-0 rounded">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead class="bg-warning text-white thead-dark">
                                <tr class="text-center">
                                    <th scope="col">License Number</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Aadhar</th>
                                    <th scope="col">DOB</th>
                                    <th scope="col">Vehicle Class</th>
                                    <th scope="col">RTO</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $res->fetch_assoc()) : ?>
                                <tr>
                                    <td class="font-weight-bold"><?php echo $row['licenseNumber'] ?></td>
                                    <td><?php echo $row['name'] ?></td>
                                    <td><?php echo $row['fatherName'] ?></td>
                                    <td><?php echo $row['aadhar'] ?></td>
                                    <td><?php echo date('d-M-Y', strtotime($row['dob'])) ?></td>
                                    <td><?php echo $row['classCode'] . ' - ' . $row['classDescription'] ?></td>
                                    <td><?php echo $row['rtoName'] . ' (' . $row['rtoCode'] . ')' ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?php 
                                            echo ($row['status'] == 1 || $row['status'] == 'approved') ? 'success' : 
                                                (($row['status'] == 0 || $row['status'] == 'pending') ? 'warning' : 'danger'); 
                                        ?>">
                                            <?php echo ($row['status'] == 1 || $row['status'] == 'approved') ? 'Approved' : (($row['status'] == 0 || $row['status'] == 'pending') ? 'Pending' : 'Rejected'); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['status'] == 1 || $row['status'] == 'approved'): ?>
                                            <form method="post" style="display: inline;">
                                                <button type="submit" name="action" value="Apply" class="btn btn-success btn-sm">
                                                    <i class="fa fa-check-circle"></i> Apply for DL
                                                </button>
                                                <input type="hidden" name="license_id" value="<?php echo $row['license_id']; ?>"/>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="fa fa-ban"></i> Not Eligible
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center" role="alert">
                <i class="fa fa-exclamation-triangle fa-2x mb-3"></i>
                <h4>No Learner Licenses Found</h4>
                <p>You don't have any learner licenses registered with this Aadhar number.</p>
                <a href="/RTO_Bheemanna/newLL.php" class="btn btn-warning mt-2">
                    <i class="fa fa-plus-circle"></i> Apply for New LL
                </a>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="/RTO_Bheemanna/index.php" class="btn btn-secondary btn-lg px-5 py-2 rounded-pill">
                <i class="fa fa-home mr-2"></i> Go to Home
            </a>
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

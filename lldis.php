<?php
session_start();
if (!isset($_SESSION['aadhar']) || !isset($_SESSION['status'])) {
    header('Location: index.php');
    exit();
}

$aadhar = $_SESSION['aadhar'];
$status = $_SESSION['status'];

// Fetch license details from database
require_once('config/Connection.php');
$obj = new Connection();
$db = $obj->getNewConnection();

$sql = "SELECT l.license_id, l.licenseNumber, p.name, p.fatherName, p.aadhar, p.dob, 
        vc.classCode, vc.classDescription, r.rtoName, r.rtoCode, l.status
        FROM licenses l
        JOIN person p ON l.person_id = p.person_id
        JOIN vehicleclasses vc ON l.class_id = vc.class_id
        JOIN rtooffices r ON l.rto_id = r.rto_id
        WHERE p.aadhar = ? AND l.licenseType = 'LL'";

$stmt = $db->prepare($sql);
$stmt->bind_param("s", $aadhar);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>LL Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        .table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Learning License Status</h1>
        
        <?php if ($res->num_rows > 0): ?>
            <form method="post">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th scope="col">License Number</th>
                            <th scope="col">Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Aadhar</th>
                            <th scope="col">DOB</th>
                            <th scope="col">Vehicle Class</th>
                            <th scope="col">RTO</th>
                            <th scope="col">Learner Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $res->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row['licenseNumber'] ?></td>
                                <td><?php echo $row['name'] ?></td>
                                <td><?php echo $row['fatherName'] ?></td>
                                <td><?php echo $row['aadhar'] ?></td>
                                <td><?php echo $row['dob'] ?></td>
                                <td><?php echo $row['classCode'] . ' - ' . $row['classDescription'] ?></td>
                                <td><?php echo $row['rtoName'] . ' (' . $row['rtoCode'] . ')' ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $row['status'] == 1 ? 'success' : ($row['status'] == 0 ? 'warning' : 'danger'); ?>">
                                        <?php echo $row['status'] == 1 ? 'Approved' : ($row['status'] == 0 ? 'Pending' : 'Rejected'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </form>
        <?php else: ?>
            <div class="alert alert-warning text-center">No license records found.</div>
        <?php endif; ?>
        
        <div class="btn-container">
            <a href="index.php" class="btn btn-success">Go to Home</a>
        </div>
    </div>
    
    <script src="assets/js/jquery/jquery-2.2.4.min.js"></script>
    <script src="assets/js/bootstrap/bootstrap.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$db->close();
?>

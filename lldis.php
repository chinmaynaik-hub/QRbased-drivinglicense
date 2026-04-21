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
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>LL Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: rgba(199, 219, 96, 0.36);
        }
        .content-wrapper {
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .page-title {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; */
            background-color : #ffc107;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.2);
        }
        .page-title h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: bold;
            color : white;
        } 
        .table-card {
            background-color: #ffffff;
            border: none;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            overflow-x: auto;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 15px;
            vertical-align: middle;
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-color: #e9ecef;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(102, 126, 234, 0.05);
        }
        .badge {
            padding: 8px 15px;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 20px;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        .badge-success {
            background-color: #28a745;
            color: #fff;
        }
        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }
        .btn-home {
            background: linear-gradient(135deg, #28a745 0%, #4ba26bab 100%);
            color: white;
            padding: 12px 40px;
            border: none;
            border-radius: 25px;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.3s ease;
            box-shadow: 0px 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0px 8px 20px rgba(102, 126, 234, 0.6);
            color: white;
            text-decoration: none;
        }
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        .alert-custom {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            font-size: 1.1rem;
            color: #856404;
        }
        @media (max-width: 768px) {
            .page-title h1 {
                font-size: 1.8rem;
            }
            .table {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <?php require_once('includes/header.php'); ?>
    
    <div class="container content-wrapper">
        <div class="page-title">
            <h1>Learning License Status</h1>
        </div>
        
        <?php if ($res->num_rows > 0): ?>
            <div class="table-card">
                <table class="table table-striped table-bordered">
                    <thead class="text-center thead-dark">
                        <tr>
                            <th scope="col">License Number</th>
                            <th scope="col">Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Aadhar</th>
                            <th scope="col">DOB</th>
                            <th scope="col">Vehicle Class</th>
                            <th scope="col">RTO</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $res->fetch_assoc()) : ?>
                            <tr>
                                <td><strong><?php echo $row['licenseNumber'] ?></strong></td>
                                <td><?php echo $row['name'] ?></td>
                                <td><?php echo $row['fatherName'] ?></td>
                                <td><?php echo $row['aadhar'] ?></td>
                                <td><?php echo date('d-M-Y', strtotime($row['dob'])) ?></td>
                                <td><?php echo $row['classCode'] . ' - ' . $row['classDescription'] ?></td>
                                <td><?php echo $row['rtoName'] . ' (' . $row['rtoCode'] . ')' ?></td>
                                <td class="text-center">
                                    <span class="badge badge-<?php echo $row['status'] == 1 ? 'success' : ($row['status'] == 0 ? 'warning' : 'danger'); ?>">
                                        <?php echo $row['status'] == 1 ? 'Approved' : ($row['status'] == 0 ? 'Pending' : 'Rejected'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert-custom">
                <i class="fa fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                <p><strong>No license records found.</strong></p>
            </div>
        <?php endif; ?>
        
        <div class="btn-container">
            <a href="index.php" class="btn-home">Go to Home</a>
        </div>
    </div>
    
    <?php require_once('includes/footer.php'); ?>
    
    <!-- ##### All Javascript Script ##### -->
    <!-- jQuery-2.2.4 js -->
    <script src="assets/js/jquery/jquery-2.2.4.min.js"></script>
    <!-- Popper js -->
    <script src="assets/js/bootstrap/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="assets/js/bootstrap/bootstrap.min.js"></script>
    <!-- All Plugins js -->
    <script src="assets/js/plugins/plugins.js"></script>
    <!-- Active js -->
    <script src="assets/js/active.js"></script>
</body>
</html>

<?php
$stmt->close();
$db->close();
?>

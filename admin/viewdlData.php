<?php
    session_start();
    //$loggedin = $_SESSION['loggedin'];
    if (empty($_SESSION['loggedin'])) 
    {
        session_destroy();
        header("Location: adminLogin.php");
        die();
    }
    if (isset($_POST['adminPanel']))
    {
        header("Location: adminPanel.php");
        die();
    }
    require_once('../config/Connection.php');
    $obj = new Connection();
    $db = $obj->getNewConnection();
    //$sql = "select * from dl";
    $sql = "SELECT l.license_id,
        l.licenseNumber,
        l.licenseType,
        l.issueDate,
        l.examDate,
        l.status,
        l.validityDate,
        l.createdAt,
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
        r.rtoCode,
        r.rtoName,
        v.class_id,
        v.classCode,
        v.classDescription
        FROM licenses l
        JOIN person p ON l.person_id = p.person_id
        JOIN vehicleclasses v ON l.class_id = v.class_id
        JOIN rtooffices r ON l.rto_id = r.rto_id
        WHERE l.licenseType = 'LL' && l.status = 'approved'
        ORDER BY l.issueDate DESC";

    $res = $db->query($sql);
    
    if (!$res) {
        die("Query failed: no result found " . $db->error);
    }
    
    if ($res->num_rows == 0) {
        // No DL records found, redirect to admin panel
        echo "<div class='alert alert-info'>No DL records found.</div>";
        exit();
    }
    if (isset($_POST['action']) && isset($_POST['id'])) {
        if ($_POST['action'] == 'Edit') {
            $_SESSION['license_id'] = $_POST['id'];
            header('Location: editdldata.php');
            die();
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>View DL Data</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php require_once('../includes/header.php'); ?>
    <h1 class="text-white text-center font-weight-bold bg-warning" style="font-size: 55px;"> View DL Data </h1>
    <div class="container-fluid">
        <div class="table-responsive">
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
                        <th scope="col">Actions</th>
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
                            <span class="badge badge-<?php 
                                echo $row['status'] == 'approved' ? 'success' : 
                                    ($row['status'] == 'pending' ? 'warning' : 
                                    ($row['status'] == 'rejected' ? 'danger' : 'secondary')); 
                            ?>">
                                <?php echo ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td>
                            <form method="post">
                                <input type="submit" name="action" value="Edit" class="btn btn-sm btn-primary"/>
                                <input type="hidden" name="id" value="<?php echo $row['license_id']; ?>"/>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <br><br>
    <form method="post">
        <center><input type="submit" value="Admin Panel" name="adminPanel" class="btn btn-danger"></center>
    </form>
    <br>
    <?php require_once('../includes/footer.php'); ?>
    <!-- ##### All Javascript Script ##### -->
    <!-- jQuery-2.2.4 js -->
    <script src="../assets/js/jquery/jquery-2.2.4.min.js"></script>
    <!-- Popper js -->
    <script src="../assets/js/bootstrap/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
    <!-- All Plugins js -->
    <script src="../assets/js/plugins/plugins.js"></script>
    <!-- Active js -->
    <script src="../assets/js/active.js"></script>
</body>
</html>
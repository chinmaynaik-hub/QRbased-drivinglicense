<?php
session_start();
if (!isset($_SESSION['aadhar'])) {
    header("Location: newDL.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once('Connection.php');
    
    $llno = $_POST['llno'];
    $aadhar = $_POST['aadhar'];
    $obj = new Connection();
    $db = $obj->getNewConnection();
    
    $llnoerr = '';
    $aadharerr = '';
    
    $sql = "SELECT * FROM ll WHERE llno=? AND aadhar=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $llno, $aadhar);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if (!$row) {
        $llnoerr = "Invalid LL no or Aadhar no";
    } else {
        $rto = $row['rto'];
        if ($row['status'] == 0) {
            $aadharerr = "Status Pending";
        } elseif ($row['llno'] === $llno && $row['aadhar'] === $aadhar) {
            $_SESSION['llno'] = $llno;
            $_SESSION['aadhar'] = $aadhar;
            $_SESSION['rto'] = $rto;
            
            header("Location: confirmll.php");
            die();
        }
    }
    
    $db->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Submitted DL Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            background-color: #ffffff;
            border: none;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .btn-success {
            background-color: #28a745;
            border: none;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: 500;
        }
        .alert-link {
            color: #155724;
        }
        .list-group-item {
            border: none;
            padding: 15px;
        }
        .list-group-item.text-right {
            text-align: right;
            font-weight: 500;
        }
        .list-group-item.text-muted {
            color: #6c757d;
            font-weight: 700;
        }
        .pull-left {
            float: left;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-success:focus {
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5);
        }
        .alert {
            border-radius: 5px;
            padding: 15px;
            font-size: 1rem;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="col-lg-6 m-auto d-block">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center">Submitted Data</h3>
                    <?php
                    if (isset($_SESSION['aadhar'])) {
                        require_once('Connection.php');
                        $aadhar = $_SESSION['aadhar'];
                        $rto = $_SESSION['rto'];
                        $obj = new Connection();
                        $db = $obj->getNewConnection();
                        $sql = "SELECT * FROM dl WHERE aadhar=?";
                        $stmt = $db->prepare($sql);
                        $stmt->bind_param("s", $aadhar);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();

                        if (!$row) {
                            echo "<p class='alert alert-danger text-center'>No data found for the provided Aadhar.</p>";
                        } else {
                            $status = $row['status'];
                            $id = $row['id'];
                            $examDate = $row['examDate'];

                            echo "<div class='row'>
                                    <div class='col-lg-6 m-auto d-block'>
                                    <ul class='list-group'>
                                    <li class='list-group-item text-muted' contenteditable='false'>DL Status</li>
                                    <li class='list-group-item text-right'><span class='pull-left'><strong>Status:</strong></span>$status</li>
                                    <li class='list-group-item text-right'><span class='pull-left'><strong>Test Date:</strong></span>$examDate</li>
                                    <li class='list-group-item text-right'><span class='pull-left'><strong>RTO Office:</strong></span>$rto</li>
                                    <li class='list-group-item text-right'><span class='pull-left'><strong>Unique ID:</strong></span>$id</li>
                                    </ul>
                                    </div>
                                  </div>";
                        }
                    }
                    ?>
                    <h3 class="text-success text-center">Form Submitted Successfully</h3>
                    <div class="text-center">
                        <a href="index.php" class="btn btn-success mt-3">Go to Home</a>
                    </div>
                </div>
            </div>
            <br>
        </div>
    </div>
    <!-- ##### All Javascript Script ##### -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <!-- Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>

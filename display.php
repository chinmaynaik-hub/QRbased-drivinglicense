<?php
    session_start();
    if (!isset($_SESSION['aadhar'])) {
        header("Location: newLL.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Submitted LL Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="col-lg-6 m-auto d-block">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center">Submitted Data</h3>
                    <p><strong>Name:</strong> <?php echo $_SESSION['name']; ?></p>
                    <p><strong>Father's Name:</strong> <?php echo $_SESSION['fatherName']; ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo $_SESSION['dob']; ?></p>
                    <p><strong>Blood Group:</strong> <?php echo $_SESSION['bloodGroup']; ?></p>
                    <p><strong>Address:</strong> <?php echo $_SESSION['address']; ?></p>
                    <p><strong>Aadhar Number:</strong> <?php echo $_SESSION['aadhar']; ?></p>
                    <p><strong>Gender:</strong> <?php echo $_SESSION['gender']; ?></p>
                    <p><strong>Mobile Number:</strong> <?php echo $_SESSION['mobileNumber']; ?></p>
                    <p><strong>Email ID:</strong> <?php echo $_SESSION['email']; ?></p>
                    <p><strong>RTO Office:</strong> <?php echo $_SESSION['rto']; ?></p>
                    <!--<p><strong>Unique ID:</strong> <?php echo $_SESSION['id']; ?></p>-->
                    <p><strong>Exam date : </strong> <?php echo $_SESSION['examDate']; ?></p>
                    <p><strong>License Type:</strong> <?php echo $_SESSION['licenseType']; ?></p>
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

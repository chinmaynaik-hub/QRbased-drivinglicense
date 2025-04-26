<?php
session_start();
if (!isset($_SESSION['aadhar']) || !isset($_SESSION['status'])) {
    header('Location: index.php');
    exit();
}

$aadhar = $_SESSION['aadhar'];
$status = $_SESSION['status'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>LL Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .status {
            font-size: 20px;
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background-color: #e0f7fa;
            border: 2px solid #00897b;
            border-radius: 8px;
            color: #00695c;
        }
        .status.pending {
            background-color: #ffebee;
            border: 2px solid #d32f2f;
            color: #c62828;
        }
        .btn-success {
            display: inline-block;
            font-size: 16px;
            padding: 10px 20px;
            color: #fff;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Learning License Status</h1>
        <?php if ($status == 0): ?>
            <div class="status pending">Pending</div>
        <?php else: ?>
            <div class="status">You can now apply for the DL with your LL number</div>
        <?php endif; ?>
        <div class="btn-container">
            <a href="index.php" class="btn-success">Go to Home</a>
        </div>
    </div>
</body>
</html>

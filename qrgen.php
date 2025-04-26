<?php
require 'vendor/autoload.php'; // Ensure you have installed Endroid QR Code via Composer

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rto";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user input
$aadhar = $_POST['aadhar'];
$dob = $_POST['dob'];

// Prepare and execute SQL statement
$sql = "SELECT * FROM ll WHERE aadhar = ? AND dob = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $aadhar, $dob);
$stmt->execute();
$result = $stmt->get_result();

// Check if record exists and generate QR code
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $data = json_encode($row);

    // Create QR code
    $qrCode = new QrCode($data);
    $qrCode->setEncoding(new Encoding('UTF-8'))
           ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
           ->setSize(300)
           ->setMargin(10)
           ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin());

    // Write QR code to PNG
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // Save QR code image to server
    $fileName = 'qr_codes/' . uniqid() . '.png';
    $result->saveToFile($fileName);

    // Output QR code image and download link with styling
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QR Code</title>
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 70vh;
                margin: 0;
                background: url("background copy.jpg") no-repeat center center fixed;
                background-size: cover;
            }
            .container {
                text-align: center;
                background: rgba(200, 255, 255, 0.8);
                padding: 20px;
                border-radius: 10px;
            }
            .download-button {
                background: linear-gradient(45deg, skyblue, purple);
                border: none;
                color: white;
                padding: 10px 20px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                margin-top: 20px;
                cursor: pointer;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <img src="' . $fileName . '" alt="QR Code"><br>
            <a href="' . $fileName . '" download="qr_code.png"><button class="download-button">Download QR Code</button></a>
        </div>
    </body>
    </html>';
} else {
    echo "No matching records found.";
}

// Close connection
$conn->close();
?>

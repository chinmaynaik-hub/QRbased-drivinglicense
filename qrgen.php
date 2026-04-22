<?php
require 'vendor/autoload.php'; // Ensure you have installed Endroid QR Code via Composer

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;

require_once('config/Connection.php');

$obj = new Connection();
$conn = $obj->getNewConnection();

// Get user input
$aadhar = $_POST['aadhar'];
$dob = $_POST['dob'];

// Prepare and execute SQL statement with JOIN to get all DL license details
$sql = "SELECT 
            l.licenseNumber,
            l.licenseType,
            l.status,
            l.issueDate,
            l.examDate,
            l.validityDate,
            p.name,
            p.fatherName,
            p.dob,
            p.bloodGroup,
            p.gender,
            p.address,
            p.mobileNumber,
            p.email,
            p.aadhar,
            r.rtoName,
            r.rtoCode,
            vc.classCode,
            vc.classDescription
        FROM licenses l
        JOIN person p ON l.person_id = p.person_id
        JOIN rtooffices r ON l.rto_id = r.rto_id
        JOIN vehicleclasses vc ON l.class_id = vc.class_id
        WHERE p.aadhar = ? AND p.dob = ? AND l.licenseType = 'DL'
        ORDER BY l.issueDate DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $aadhar, $dob);
$stmt->execute();
$result = $stmt->get_result();

// Check if record exists and generate QR code
if ($result->num_rows > 0) {
    $vehicleClasses = [];
    $licenseNumbers = [];
    $mainData = null;
    
    // Fetch all DL licenses and collect vehicle classes
    while ($row = $result->fetch_assoc()) {
        // Store the first row's data as main data
        if ($mainData === null) {
            $mainData = $row;
        }
        
        // Collect all vehicle classes
        $vehicleClasses[] = $row['classCode'] . ' - ' . $row['classDescription'];
        $licenseNumbers[] = $row['licenseNumber'];
    }
    
    // Format the data for QR code with combined vehicle classes
    $qrData = [
        'License Numbers' => implode(', ', $licenseNumbers),
        'License Type' => 'DL',
        'Name' => $mainData['name'],
        'Father Name' => $mainData['fatherName'],
        'DOB' => date('d-M-Y', strtotime($mainData['dob'])),
        'Blood Group' => $mainData['bloodGroup'],
        'Gender' => $mainData['gender'],
        'Address' => $mainData['address'],
        'Mobile' => $mainData['mobileNumber'],
        'Email' => $mainData['email'],
        'Aadhar' => $mainData['aadhar'],
        'Vehicle Classes' => implode(', ', $vehicleClasses),
        'RTO' => $mainData['rtoName'] . ' (' . $mainData['rtoCode'] . ')',
        'Issue Date' => date('d-M-Y', strtotime($mainData['issueDate'])),
        'Validity' => date('d-M-Y', strtotime($mainData['validityDate'])),
        'Status' => ($mainData['status'] == 1 || $mainData['status'] == 'approved') ? 'Approved' : (($mainData['status'] == 0 || $mainData['status'] == 'pending') ? 'Pending' : 'Rejected')
    ];
    
    $data = json_encode($qrData);

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

    // Convert QR code to base64 string (NO SERVER STORAGE)
    $qrCodeBase64 = base64_encode($result->getString());
    $qrCodeDataUri = 'data:image/png;base64,' . $qrCodeBase64;

    // Output QR code image with client-side storage
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>QR Code Generated</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            html {
                height: 100%;
                width: 100%;
            }
            
            body {
                min-height: 100vh;
                width: 100vw;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0;
                padding: 20px;
                background: url("assets/img/bg-img/qrbgimg.png") no-repeat center center fixed;
                background-size: cover;
                font-family: Arial, sans-serif;
                position: relative;
            }
            
            .container {
                text-align: center;
                background: rgba(255, 255, 255, 0.95);
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                max-width: 500px;
                width: 90%;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
            
            .qr-title {
                color: #333;
                font-size: 2rem;
                font-weight: bold;
                margin-bottom: 10px;
            }
            
            .qr-subtitle {
                color: #666;
                margin-bottom: 30px;
            }
            
            .qr-image {
                border: 5px solid #ffc107;
                border-radius: 10px;
                padding: 10px;
                background: white;
                margin: 20px auto;
                display: inline-block;
            }
            
            .qr-image img {
                display: block;
                max-width: 100%;
                height: auto;
            }
            
            .download-button {
                background: #28a745;
                border: none;
                color: white;
                padding: 15px 40px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 16px;
                font-weight: bold;
                margin-top: 20px;
                cursor: pointer;
                border-radius: 25px;
                transition: all 0.3s ease;
                box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            }
            
            .download-button:hover {
                background: #218838;
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(40, 167, 69, 0.5);
            }
            
            .home-button {
                background: #ffc107;
                border: none;
                color: #333;
                padding: 12px 30px;
                text-align: center;
                text-decoration: none;
                display: inline-block;
                font-size: 14px;
                font-weight: bold;
                margin-top: 15px;
                cursor: pointer;
                border-radius: 25px;
                transition: all 0.3s ease;
            }
            
            .home-button:hover {
                background: #e0a800;
                text-decoration: none;
                color: #333;
            }
            
            .storage-info {
                background: #e7f3ff;
                padding: 10px;
                border-radius: 8px;
                margin-top: 15px;
                font-size: 12px;
                color: #0066cc;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1 class="qr-title">QR Code Generated!</h1>
            <p class="qr-subtitle">Scan this QR code to view license details</p>
            
            <div class="qr-image">
                <img id="qrCodeImage" src="' . $qrCodeDataUri . '" alt="QR Code">
            </div>
            
            <button class="download-button" onclick="downloadQRCode()">
                <i class="fa fa-download"></i> Download QR Code
            </button>
            <br>
            <a href="index.php" class="home-button">
                <i class="fa fa-home"></i> Go to Home
            </a>
            
            <div class="storage-info">
                ✓ QR Code stored in browser (not on server)
            </div>
        </div>

        <script>
            // Store QR code in localStorage
            const qrCodeData = "' . $qrCodeDataUri . '";
            const licenseNumber = "' . htmlspecialchars(implode('_', $licenseNumbers)) . '";
            const aadhar = "' . htmlspecialchars($mainData['aadhar']) . '";
            
            // Save to localStorage with unique key
            const storageKey = "qr_code_" + aadhar;
            localStorage.setItem(storageKey, qrCodeData);
            localStorage.setItem(storageKey + "_timestamp", Date.now());
            localStorage.setItem(storageKey + "_license", licenseNumber);
            
            console.log("QR Code saved to localStorage with key:", storageKey);
            
            // Function to download QR code
            function downloadQRCode() {
                const link = document.createElement("a");
                link.href = qrCodeData;
                link.download = "qr_code_DL_" + aadhar + ".png";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
            
            // Optional: Clean up old QR codes (older than 30 days)
            function cleanupOldQRCodes() {
                const thirtyDaysAgo = Date.now() - (30 * 24 * 60 * 60 * 1000);
                
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key && key.startsWith("qr_code_") && key.endsWith("_timestamp")) {
                        const timestamp = parseInt(localStorage.getItem(key));
                        if (timestamp < thirtyDaysAgo) {
                            const baseKey = key.replace("_timestamp", "");
                            localStorage.removeItem(baseKey);
                            localStorage.removeItem(key);
                            localStorage.removeItem(baseKey + "_license");
                            console.log("Cleaned up old QR code:", baseKey);
                        }
                    }
                }
            }
            
            cleanupOldQRCodes();
        </script>
    </body>
    </html>';
} else {
    echo "<script>
        alert('Invalid Credentials. No license found with the provided Aadhar and DOB.');
        window.location.href = 'qrcode.php';
    </script>";
    exit();
}

// Close connection
$stmt->close();
$conn->close();
?>

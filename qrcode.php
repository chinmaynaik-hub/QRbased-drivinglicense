<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aadhaar and DOB Input</title>
    <link rel="stylesheet" href="qrcode.css">
</head>
<body>
    <div class="container">
        <h1>Enter Aadhaar and Date of Birth</h1>
        <form action="qrgen.php" method="post">
            <label for="aadhar">Aadhaar:</label>
            <input type="text" id="aadhar" name="aadhar" required>
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>
            <button type="submit">Generate QR Code</button>
        </form>
    </div>
</body>
</html>
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
            margin: 0;
            background: url("rto.png") no-repeat center center fixed;
            background-size: contain;
        }
        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.8); /* Transparent white */
            padding: 20px;
            border-radius: 10px;
        }
        .download-button {
            background: (45deg, white);
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

</html>';
?>

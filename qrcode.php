<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aadhaar and DOB Input</title>
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
            background: url("assets/img/bg-img/rto-copy.png") no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            position: relative;
        }
        
        /* Add overlay to reduce background opacity */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.5); /* White overlay with 50% opacity */
            z-index: 0;
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
            z-index: 1; /* Ensure container is above the overlay */
        }
        
        h1 {
            color: #333;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 30px;
        }
        
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        label {
            color: #333;
            font-weight: bold;
            text-align: left;
            margin-bottom: 5px;
        }
        
        input[type="text"],
        input[type="date"] {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="date"]:focus {
            outline: none;
            border-color: #ffc107;
        }
        
        button[type="submit"] {
            background: #28a745;
            border: none;
            color: white;
            padding: 15px 40px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 25px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        button[type="submit"]:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.5);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Enter Aadhaar and Date of Birth</h1>
        <form action="qrgen.php" method="post">
            <label for="aadhar">Aadhaar:</label>
            <input type="text" id="aadhar" name="aadhar" required maxlength="12" pattern="\d{12}" placeholder="Enter 12-digit Aadhaar">
            
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>
            
            <button type="submit">Generate QR Code</button>
        </form>
    </div>
</body>
</html>

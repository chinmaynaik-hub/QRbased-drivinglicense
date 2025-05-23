<?php
    // Initialize variables
    $name = '';
    $fatherName = '';
    $dob = '';
    $bloodGroup = '';
    $address = '';
    $aadhar = '';
    $gender = '';
    $mobileNumber = '';
    $email = '';
    $rto = '';
    $aadharerr = '';
    $licenseType = '';

    if (isset($_POST['submit'])) {
        session_start();
        require_once('Connection.php');

        // Retrieve form data
        $name = $_POST['name'];
        $fatherName = $_POST['fatherName'];
        $dob = $_POST['dob'];
        $bloodGroup = $_POST['bloodGroup'];
        $address = $_POST['address'];
        $aadhar = $_POST['aadhar'];
        $gender = $_POST['gender'];
        $mobileNumber = $_POST['mobileNumber'];
        $email = $_POST['email'];
        $rto = $_POST['rto'];
        $licenseType = isset($_POST['licenseType']) ? implode(",", $_POST['licenseType']) : ''; // Handle checkbox values

        $obj = new Connection();
        $db = $obj->getNewConnection();

        // Check if Aadhar number is already registered
        $q = "SELECT * FROM ll WHERE aadhar='$aadhar'";
        $r = $db->query($q);

        if ($r->num_rows > 0) {
            $aadharerr = "Aadhar Number already registered";
        } else {
            $Date = date("Y-m-d");
            $examDate = date('Y-m-d', strtotime($Date . ' + 15 days'));
            $sql = "INSERT INTO ll(name, fatherName, dob, bloodGroup, address, aadhar, gender, mobileNumber, email, rto, status, examDate, licenseType) 
                    VALUES('$name', '$fatherName', '$dob', '$bloodGroup', '$address', '$aadhar', '$gender', '$mobileNumber', '$email', '$rto', 0, '$examDate', '$licenseType')";
            $res = $db->query($sql);

            if ($res) {
                $_SESSION['name'] = $name;
                $_SESSION['fatherName'] = $fatherName;
                $_SESSION['dob'] = $dob;
                $_SESSION['bloodGroup'] = $bloodGroup;
                $_SESSION['address'] = $address;
                $_SESSION['aadhar'] = $aadhar;
                $_SESSION['gender'] = $gender;
                $_SESSION['mobileNumber'] = $mobileNumber;
                $_SESSION['email'] = $email;
                $_SESSION['rto'] = $rto;
                $_SESSION['examDate'] = $examDate;
                $_SESSION['licenseType'] = $licenseType;
                $db->close();
                header("Location: display.php");
                exit();
            } else {
                // Handle insert failure
                echo "Error: " . $db->error;
            }
        }
    }
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Apply for New Learner License</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <?php require_once('header.php'); ?>
    <br>
    <h1 class="text-white text-center font-weight-bold bg-warning" style="font-size: 55px;"> New LL Registration </h1>
    <div class="container"><br>
        <div class="col-lg-6 m-auto d-block">
            <form method="POST" onsubmit="return validation()" class="bg-light">
                <div class="form-group">
                    <label for="name" class="font-weight-bold"> Enter Name: </label>
                    <input type="text" name="name" class="form-control" id="name" value="<?php echo $name; ?>">
                    <span id="nameerr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="fatherName" class="font-weight-bold"> Enter Father's Name: </label>
                    <input type="text" name="fatherName" class="form-control" id="fatherName" value="<?php echo $fatherName; ?>">
                    <span id="fatherNameerr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="dob" class="font-weight-bold"> Enter DOB: </label>
                    <input type="date" name="dob" class="form-control" id="dob" value="<?php echo $dob; ?>">
                    <span id="doberr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="bloodGroup" class="font-weight-bold"> Enter Blood Group: </label>
                    <input type="text" name="bloodGroup" class="form-control" id="bloodGroup" value="<?php echo $bloodGroup; ?>">
                    <span id="bloodGrouperr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="address" class="font-weight-bold"> Enter Address: </label>
                    <input type="text" name="address" class="form-control" id="address" value="<?php echo $address; ?>">
                    <span id="addresserr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="aadhar" class="font-weight-bold"> Enter Aadhar Number: </label>
                    <input type="text" name="aadhar" class="form-control" id="aadhar" value="<?php echo $aadhar; ?>">
                    <span id="aadharerr" class="text-danger font-weight-bold"> <?php echo $aadharerr; ?> </span>
                </div>
                <div class="form-group">
                    <label for="gender" class="font-weight-bold"> Enter Gender: </label>
                    <input type="text" name="gender" class="form-control" id="gender" value="<?php echo $gender; ?>">
                    <span id="gendererr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="mobileNumber" class="font-weight-bold"> Enter Mobile Number: </label>
                    <input type="number" name="mobileNumber" class="form-control" id="mobileNumber" value="<?php echo $mobileNumber; ?>">
                    <span id="mobileNumbererr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="email" class="font-weight-bold"> Enter Email ID: </label>
                    <input type="email" name="email" class="form-control" id="email" value="<?php echo $email; ?>">
                    <span id="emailerr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="rto" class="font-weight-bold"> Enter RTO Office: </label>
                    <input type="text" name="rto" class="form-control" id="rto" value="<?php echo $rto; ?>">
                    <span id="rtoerr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold"> Select License Type: </label><br>
                    <input type="checkbox" name="licenseType[]" value="MCWOG" id="mcwog">
                    <label for="mcwog"> MCWOG </label><br>
                    <input type="checkbox" name="licenseType[]" value="MCWG" id="mcwg">
                    <label for="mcwg"> MCWG </label><br>
                    <input type="checkbox" name="licenseType[]" value="LMV" id="lmv">
                    <label for="lmv"> LMV </label><br>
                    <input type="checkbox" name="licenseType[]" value="HMV" id="hmv">
                    <label for="hmv"> HMV </label><br>
                    <span id="licenseTypeerr" class="text-danger font-weight-bold"> </span>
                </div>
                <center><input type="submit" name="submit" value="SUBMIT" class="btn btn-success"><center>
            </form>
            <br>
        </div>
    </div>
    <script type="text/javascript">
        function validation() {
            var name = document.getElementById('name').value;
            var fatherName = document.getElementById('fatherName').value;
            var dob = document.getElementById('dob').value;
            var bloodGroup = document.getElementById('bloodGroup').value;
            var address = document.getElementById('address').value;
            var aadhar = document.getElementById('aadhar').value;
            var gender = document.getElementById('gender').value;
            var mobileNumber = document.getElementById('mobileNumber').value;
            var email = document.getElementById('email').value;
            var rto = document.getElementById('rto').value;
            var licenseTypeChecked = document.querySelectorAll('input[name="licenseType[]"]:checked').length > 0;
            if (name == "") {
                document.getElementById('nameerr').innerHTML =" ** Please fill the name field";
                return false;
            }
            if (fatherName == "") {
                document.getElementById('fatherNameerr').innerHTML =" ** Please fill the fatherName field";
                return false;
            }
            if (dob == "") {
                document.getElementById('doberr').innerHTML =" ** Please fill the dob field";
                return false;
            }
            if (bloodGroup == "") {
                document.getElementById('bloodGrouperr').innerHTML =" ** Please fill the bloodGroup field";
                return false;
            }
            if (address == "") {
                document.getElementById('addresserr').innerHTML =" ** Please fill the address field";
                return false;
            }
            if (aadhar == "") {
                document.getElementById('aadharerr').innerHTML =" ** Please fill the aadhar field";
                return false;
            }
            else if(aadhar.toString().length != 12) {
                document.getElementById('aadharerr').innerHTML =" ** Aadhar No should be of 12 digits";
                return false;    
            }
            if (gender == "") {
                document.getElementById('gendererr').innerHTML =" ** Please fill the gender field";
                return false;
            }
            if (mobileNumber == "") {
                document.getElementById('mobileNumbererr').innerHTML =" ** Please fill the mobileNumber field";
                return false;
            }
            if (email == "") {
                document.getElementById('emailerr').innerHTML =" ** Please fill the email field";
                return false;
            }
            if (rto == "") {
                document.getElementById('rtoerr').innerHTML =" ** Please fill the rto field";
                return false;
            }
            if (!licenseTypeChecked) {
                document.getElementById('licenseTypeerr').innerHTML =" ** Please select at least one license type";
                return false;
        }
    </script>
    <?php require_once('footer.php'); ?>
    <!-- ##### All Javascript Script ##### -->
    <!-- jQuery-2.2.4 js -->
    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <!-- Popper js -->
    <script src="js/bootstrap/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <!-- All Plugins js -->
    <script src="js/plugins/plugins.js"></script>
    <!-- Active js -->
    <script src="js/active.js"></script>
</body>
</html>

<?php
    session_start();
    require_once('Connection.php');
    $aadhar = $_SESSION['aadhar'];
    $obj = new Connection();
    $db = $obj->getNewConnection();
    
    // First get the existing DL data
    $sql = "SELECT * FROM dl WHERE aadhar=$aadhar";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();
    $vehicleType = $row['licenseType'];
    $vtype = explode(',', $vehicleType);
    
    if (isset($_POST['submit'])) {
        // First fetch the llno from ll table
        $llQuery = "SELECT llno FROM ll WHERE aadhar=$aadhar";
        $llResult = $db->query($llQuery);
        $llData = $llResult->fetch_assoc();
        $llno = $llData['llno'];
        
        $name = $_POST['name'];
        $fatherName = $_POST['fatherName'];
        $dob = $_POST['dob'];
        $bloodGroup = $_POST['bloodGroup'];
        $address = $_POST['address'];
        $gender = $_POST['gender'];
        $mobileNumber = $_POST['mobileNumber'];
        $email = $_POST['email'];
        $rto = $_POST['rto'];
        $status = $_POST['status'];
        $validity = $_POST['validity'];
        $issueDate = $_POST['issueDate'];
        $selectedLicenseTypes = isset($_POST['licenseType']) ? implode(',', $_POST['licenseType']) : '';

        $q = "UPDATE dl 
              SET name='$name', dlno=$llno, fatherName='$fatherName', 
              dob='$dob', bloodGroup='$bloodGroup', address='$address', gender='$gender', 
              mobileNumber=$mobileNumber, email='$email', rto='$rto', status=$status, 
              validity='$validity', issueDate='$issueDate', licenseType='$selectedLicenseTypes' 
              WHERE aadhar=$aadhar"; 
        
        $res1 = $db->query($q);
        if (!$res1) {
            die($db->error);
        }
        $db->close();
        header("Location: viewdlData.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Edit DL Data</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require_once('header.php'); ?>
</head>
<body>
    <br>
    <h1 class="text-white text-center font-weight-bold bg-warning" style="font-size: 55px;"> Edit DL Data </h1>
    <div class="container"><br>
        <div class="col-lg-6 m-auto d-block">
            <form method="POST" onsubmit="return validation()" class="bg-light">
                <div class="form-group">
                    <label for="name" class="font-weight-bold"> Name: </label>
                    <input type="text" name="name" class="form-control" id="name" value="<?php echo htmlspecialchars($row['name']); ?>">
                    <span id="nameerr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="dlno" class="font-weight-bold"> DL No: </label>
                    <?php 
                        // Fetch llno from ll table to display (readonly)
                        $llQuery = "SELECT llno FROM ll WHERE aadhar=$aadhar";
                        $llResult = $db->query($llQuery);
                        $llData = $llResult->fetch_assoc();
                        $llno = $llData['llno'];
                    ?>
                    <input type="number" name="dlno" class="form-control" id="dlno" value="<?php echo htmlspecialchars($llno); ?>" readonly>
                    <span id="dlnoerr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="fatherName" class="font-weight-bold"> Last Name: </label>
                    <input type="text" name="fatherName" class="form-control" id="fatherName" value="<?php echo htmlspecialchars($row['fatherName']); ?>">
                    <span id="fatherNameerr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="dob" class="font-weight-bold"> DOB: </label>
                    <input type="text" name="dob" class="form-control" id="dob" value="<?php echo htmlspecialchars($row['dob']); ?>">
                    <span id="doberr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="bloodGroup" class="font-weight-bold"> Blood Group: </label>
                    <input type="text" name="bloodGroup" class="form-control" id="bloodGroup" value="<?php echo htmlspecialchars($row['bloodGroup']); ?>">
                    <span id="bloodGrouperr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="address" class="font-weight-bold"> Address: </label>
                    <input type="text" name="address" class="form-control" id="address" value="<?php echo htmlspecialchars($row['address']); ?>">
                    <span id="addresserr" class="text-danger font-weight-bold"> </span>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold d-block">Select Gender:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="genderMale" value="Male" 
                            <?php echo ($row['gender'] == 'Male') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="genderMale">Male</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="Female"
                            <?php echo ($row['gender'] == 'Female') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="genderFemale">Female</label>
                    </div>
                    <span id="gendererr" class="text-danger font-weight-bold"></span>
                </div>
                
                <div class="form-group">
                    <label for="mobileNumber" class="font-weight-bold"> Mobile Number: </label>
                    <input type="number" name="mobileNumber" class="form-control" id="mobileNumber" value="<?php echo htmlspecialchars($row['mobileNumber']); ?>">
                    <span id="mobileNumbererr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="email" class="font-weight-bold"> Email: </label>
                    <input type="email" name="email" class="form-control" id="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                    <span id="emailerr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="rto" class="font-weight-bold"> RTO: </label>
                    <input type="text" name="rto" class="form-control" id="rto" value="<?php echo htmlspecialchars($row['rto']); ?>">
                    <span id="rtoerr" class="text-danger font-weight-bold"> </span>
                </div>
                
                <div class="form-group">
                    <label class="font-weight-bold"> Select License Type: </label><br>
                    <input type="checkbox" name="licenseType[]" value="MCWOG" id="mcwog" 
                        <?php echo in_array('MCWOG', $vtype) ? 'checked' : ''; ?>>
                    <label for="mcwog" style="margin-left: 5px;"> MCWOG </label><br>
                    <input type="checkbox" name="licenseType[]" value="MCWG" id="mcwg"
                        <?php echo in_array('MCWG', $vtype) ? 'checked' : ''; ?>>
                    <label for="mcwg" style="margin-left: 5px;"> MCWG </label><br>
                    <input type="checkbox" name="licenseType[]" value="LMV" id="lmv"
                        <?php echo in_array('LMV', $vtype) ? 'checked' : ''; ?>>
                    <label for="lmv" style="margin-left: 5px;"> LMV </label><br>
                    <input type="checkbox" name="licenseType[]" value="HMV" id="hmv"
                        <?php echo in_array('HMV', $vtype) ? 'checked' : ''; ?>>
                    <label for="hmv" style="margin-left: 5px;"> HMV </label><br>
                    <span id="licenseTypeerr" class="text-danger font-weight-bold"> </span>
                </div>
                
                <div class="form-group">
                    <label for="status" class="font-weight-bold">Status:</label>
                    <select name="status" class="form-control" id="status">
                        <option value="0" <?php echo ($row['status'] == 0) ? 'selected' : ''; ?>>Not Approved</option>
                        <option value="1" <?php echo ($row['status'] == 1) ? 'selected' : ''; ?>>Approved</option>
                    </select>
                    <span id="statuserr" class="text-danger font-weight-bold"></span>
                </div>
                <div class="form-group">
                    <label for="validity" class="font-weight-bold"> Validity </label>
                    <input type="date" name="validity" class="form-control" id="validity" value="<?php echo htmlspecialchars($row['validity']); ?>">
                    <span id="validityerr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="issueDate" class="font-weight-bold"> Issue Date </label>
                    <input type="date" name="issueDate" class="form-control" id="issueDate" value="<?php echo htmlspecialchars($row['issueDate']); ?>">
                    <span id="issueDateerr" class="text-danger font-weight-bold"> </span>
                </div>
                <center><input type="submit" name="submit" value="SUBMIT" class="btn btn-success"></center>
            </form>
            <br>
        </div>
    </div>

    <script type="text/javascript">
        function validation() {
            // Reset error messages
            document.querySelectorAll('.text-danger').forEach(function(el) {
                el.innerHTML = '';
            });

            var isValid = true;
            
            // Validate each field
            var name = document.getElementById('name').value;
            if (name.trim() === "") {
                document.getElementById('nameerr').innerHTML = " ** Please fill the name field";
                isValid = false;
            }

            var fatherName = document.getElementById('fatherName').value;
            if (fatherName.trim() === "") {
                document.getElementById('fatherNameerr').innerHTML = " ** Please fill the lastName field";
                isValid = false;
            }

            var dob = document.getElementById('dob').value;
            if (dob.trim() === "") {
                document.getElementById('doberr').innerHTML = " ** Please fill the date of birth field";
                isValid = false;
            }

            var bloodGroup = document.getElementById('bloodGroup').value;
            if (bloodGroup.trim() === "") {
                document.getElementById('bloodGrouperr').innerHTML = " ** Please fill the blood group field";
                isValid = false;
            }

            var address = document.getElementById('address').value;
            if (address.trim() === "") {
                document.getElementById('addresserr').innerHTML = " ** Please fill the address field";
                isValid = false;
            }

            var gender = document.querySelector('input[name="gender"]:checked');
            if (!gender) {
                document.getElementById('gendererr').innerHTML = " ** Please select your gender";
                isValid = false;
            }

            var mobileNumber = document.getElementById('mobileNumber').value;
            if (mobileNumber.trim() === "") {
                document.getElementById('mobileNumbererr').innerHTML = " ** Please fill the mobile number field";
                isValid = false;
            }

            var email = document.getElementById('email').value;
            if (email.trim() === "") {
                document.getElementById('emailerr').innerHTML = " ** Please fill the email field";
                isValid = false;
            }

            var rto = document.getElementById('rto').value;
            if (rto.trim() === "") {
                document.getElementById('rtoerr').innerHTML = " ** Please fill the RTO field";
                isValid = false;
            }

            var licenseTypeChecked = document.querySelectorAll('input[name="licenseType[]"]:checked').length > 0;
            if (!licenseTypeChecked) {
                document.getElementById('licenseTypeerr').innerHTML = " ** Please select at least one license type";
                isValid = false;
            }

            var status = document.getElementById('status').value;
            if (status === "") {
                document.getElementById('statuserr').innerHTML = " ** Please select a status";
                isValid = false;
            }

            var validity = document.getElementById('validity').value;
            if (validity.trim() === "") {
                document.getElementById('validityerr').innerHTML = " ** Please fill the validity field";
                isValid = false;
            }

            var issueDate = document.getElementById('issueDate').value;
            if (issueDate.trim() === "") {
                document.getElementById('issueDateerr').innerHTML = " ** Please fill the issue date field";
                isValid = false;
            }

            return isValid;
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
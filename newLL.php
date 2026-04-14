<?php
    // Start session at the top
    session_start();
    
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
        require_once('config/Connection.php');

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
        $licenseType = isset($_POST['licenseType']) ? implode(",", $_POST['licenseType']) : '';

        $obj = new Connection();
        $db = $obj->getNewConnection();

        // Check if Aadhar number is already registered
        $q = "SELECT * FROM person WHERE aadhar='$aadhar'";
        $r = $db->query($q);

        if ($r->num_rows > 0) {
            $aadharerr = "Aadhar Number already registered";
        } else {
            // Generate unique 4-digit LL number
            $llnoGenerated = false;
            $llno = 0;
            $maxAttempts = 50; // Prevent infinite loop
            $attempts = 0;
            
            while (!$llnoGenerated && $attempts < $maxAttempts) {
                $llno = mt_rand(1000, 999999); // Generate 6-digit number
                $checkSql = "SELECT * FROM licenses WHERE licenseNumber='LL_$llno'";
                $checkResult = $db->query($checkSql);
                
                if ($checkResult->num_rows === 0) {
                    $llnoGenerated = true;
                }
                $attempts++;
            }

            if (!$llnoGenerated) {
                die("Could not generate unique LL number. Please try again.");
            }

            $Date = date("Y-m-d");
            $examDate = date('Y-m-d', strtotime($Date . ' + 15 days'));
            
            // Insert with generated llno
            // $sql = "INSERT INTO ll(name, LastName, dob, bloodGroup, address, aadhar, gender, mobileNumber, email, rto, status, examDate, licenseType, llno) 
            //         VALUES('$name', '$fatherName', '$dob', '$bloodGroup', '$address', '$aadhar', '$gender', '$mobileNumber', '$email', '$rto', 0, '$examDate', '$licenseType', '$llno')";
                    
            $sql_person = "INSERT INTO PERSON (aadhar, name, fatherName, dob, bloodGroup, gender, address, mobileNumber, email) VALUES ('$aadhar', '$name', '$fatherName', '$dob', '$bloodGroup', '$gender', '$address', '$mobileNumber', '$email')";

            $res_person = $db->query($sql_person);

            if($res_person){

                //gets autoincrement number of last inserted data
                $person_id = $db->insert_id;

                // we should have all the details before
                // we should only fetch from here
                $sql_rto = "SELECT rto_id FROM rtooffices where rtoName = '$rto' limit 1";
                $res_rto = $db->query($sql_rto);

                if($res_rto->num_rows > 0){
                    $row_rto = $res_rto->fetch_assoc();
                    $rto_id = $row_rto["rto_id"];
                } else {
                    //show error
                    die("RTO does not exist");
                }

                // Process ALL selected license types and insert separate records
                $licenseTypeArray = explode(",", $licenseType);
                $allInserted = true;
                
                foreach($licenseTypeArray as $classCode) {
                    $classCode = trim($classCode);
                    
                    // Get class_id for this vehicle class
                    $sql_class = "SELECT class_id FROM vehicleclasses WHERE classCode = '$classCode' LIMIT 1";
                    $resClass = $db->query($sql_class);
                    
                    if ($resClass->num_rows > 0) {
                        $rowClass = $resClass->fetch_assoc();
                        $class_id = $rowClass['class_id'];
                        
                        // Generate unique license number for this class
                        $licenseNumber = "LL_" . $llno . "_" . $classCode;
                        
                        // Calculate validity (6 months from issue date)
                        $issueDate = date("Y-m-d");
                        $validityDate = date("Y-m-d", strtotime($issueDate . ' + 6 months'));
                        
                        // Insert license record for this class
                        $sql_license = "INSERT INTO licenses 
                                       (licenseNumber, person_id, licenseType, class_id, rto_id, issueDate, examDate, validityDate, status) 
                                       VALUES 
                                       ('$licenseNumber', $person_id, 'LL', $class_id, $rto_id, '$issueDate', '$examDate', '$validityDate', 'pending')";
                        
                        $res_license = $db->query($sql_license);
                        
                        if (!$res_license) {
                            echo "Error inserting license for $classCode: " . $db->error;
                            $allInserted = false;
                            break;
                        }
                    } else {
                        echo "Vehicle class '$classCode' not found in database";
                        $allInserted = false;
                        break;
                    }
                }
                
                // Set $res based on whether all licenses were inserted
                $res = $allInserted;

            } else {
                $res = false;
                echo "Error inserting person: " . $db->error;
            }

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
                $_SESSION['llno'] = $llno; // Store generated LL number
                
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
    <?php require_once('includes/header.php'); ?>
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
                    <label for="fatherName" class="font-weight-bold"> Enter Last Name: </label>
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
                    <label class="font-weight-bold d-block">Select Gender:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="genderMale" value="Male" 
                            <?php echo ($gender == 'Male') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="genderMale">Male</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="Female"
                            <?php echo ($gender == 'Female') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="genderFemale">Female</label>
                    </div>
                    <span id="gendererr" class="text-danger font-weight-bold"></span>
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
                    <input type="checkbox" name="licenseType[]" value="MCWOG" id="mcwog" <?php echo (strpos($licenseType, 'MCWOG') !== false) ? 'checked' : ''; ?>>
                    <label for="mcwog"> MCWOG </label><br>
                    <input type="checkbox" name="licenseType[]" value="MCWG" id="mcwg" <?php echo (strpos($licenseType, 'MCWG') !== false) ? 'checked' : ''; ?>>
                    <label for="mcwg"> MCWG </label><br>
                    <input type="checkbox" name="licenseType[]" value="LMV" id="lmv" <?php echo (strpos($licenseType, 'LMV') !== false) ? 'checked' : ''; ?>>
                    <label for="lmv"> LMV </label><br>
                    <input type="checkbox" name="licenseType[]" value="HMV" id="hmv" <?php echo (strpos($licenseType, 'HMV') !== false) ? 'checked' : ''; ?>>
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
            var gender = document.querySelector('input[name="gender"]:checked');
            var mobileNumber = document.getElementById('mobileNumber').value;
            var email = document.getElementById('email').value;
            var rto = document.getElementById('rto').value;
            var licenseTypeChecked = document.querySelectorAll('input[name="licenseType[]"]:checked').length > 0;
            
            // Reset previous errors
            document.getElementById('nameerr').innerHTML = "";
            document.getElementById('fatherNameerr').innerHTML = "";
            document.getElementById('doberr').innerHTML = "";
            document.getElementById('bloodGrouperr').innerHTML = "";
            document.getElementById('addresserr').innerHTML = "";
            document.getElementById('aadharerr').innerHTML = "";
            document.getElementById('gendererr').innerHTML = "";
            document.getElementById('mobileNumbererr').innerHTML = "";
            document.getElementById('emailerr').innerHTML = "";
            document.getElementById('rtoerr').innerHTML = "";
            document.getElementById('licenseTypeerr').innerHTML = "";
            
            var isValid = true;
            
            if (name == "") {
                document.getElementById('nameerr').innerHTML = " ** Please fill the name field";
                isValid = false;
            }
            if (fatherName == "") {
                document.getElementById('fatherNameerr').innerHTML = " ** Please fill the Last Name field";
                isValid = false;
            }
            if (dob == "") {
                document.getElementById('doberr').innerHTML = " ** Please fill the dob field";
                isValid = false;
            }
            if (bloodGroup == "") {
                document.getElementById('bloodGrouperr').innerHTML = " ** Please fill the bloodGroup field";
                isValid = false;
            }
            if (address == "") {
                document.getElementById('addresserr').innerHTML = " ** Please fill the address field";
                isValid = false;
            }
            if (aadhar == "") {
                document.getElementById('aadharerr').innerHTML = " ** Please fill the aadhar field";
                isValid = false;
            }
            else if(aadhar.toString().length != 12) {
                document.getElementById('aadharerr').innerHTML = " ** Aadhar No should be of 12 digits";
                isValid = false;    
            }
            if (!gender) {
                document.getElementById('gendererr').innerHTML = " ** Please select your gender";
                isValid = false;
            }
            if (mobileNumber == "") {
                document.getElementById('mobileNumbererr').innerHTML = " ** Please fill the mobileNumber field";
                isValid = false;
            }
            if (email == "") {
                document.getElementById('emailerr').innerHTML = " ** Please fill the email field";
                isValid = false;
            }
            if (rto == "") {
                document.getElementById('rtoerr').innerHTML = " ** Please fill the rto field";
                isValid = false;
            }
            if (!licenseTypeChecked) {
                document.getElementById('licenseTypeerr').innerHTML = " ** Please select at least one license type";
                isValid = false;
            }
            
            return isValid;
        }
    </script>
    <?php require_once('includes/footer.php'); ?>
    <!-- ##### All Javascript Script ##### -->
    <!-- jQuery-2.2.4 js -->
    <script src="assets/js/jquery/jquery-2.2.4.min.js"></script>
    <!-- Popper js -->
    <script src="assets/js/bootstrap/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="assets/js/bootstrap/bootstrap.min.js"></script>
    <!-- All Plugins js -->
    <script src="assets/js/plugins/plugins.js"></script>
    <!-- Active js -->
    <script src="assets/js/active.js"></script>
</body>
</html>
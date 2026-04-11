<?php
    session_start();
    require_once('../config/Connection.php');
    
    // Get license_id from session (set in viewdlData.php)
    if (!isset($_SESSION['license_id'])) {
        echo "Debug: Session license_id not set<br>";
        echo "Available session variables: ";
        print_r($_SESSION);
        die("<br>Error: No license selected. Please go back to <a href='viewdlData.php'>View DL Data</a>");
    }
    
    $license_id = $_SESSION['license_id'];
    echo "Debug: license_id = $license_id<br>";
    
    $obj = new Connection();
    $db = $obj->getNewConnection();
    
    // Get existing DL data with JOINs
    $sql = "SELECT 
                l.license_id,
                l.licenseNumber,
                l.status,
                l.issueDate,
                l.examDate,
                l.validityDate,
                p.person_id,
                p.aadhar,
                p.name,
                p.fatherName,
                p.dob,
                p.bloodGroup,
                p.gender,
                p.address,
                p.mobileNumber,
                p.email,
                r.rto_id,
                r.rtoName,
                r.rtoCode,
                vc.class_id,
                vc.classCode,
                vc.classDescription
            FROM licenses l 
            JOIN person p ON l.person_id = p.person_id
            JOIN rtooffices r ON l.rto_id = r.rto_id
            JOIN vehicleclasses vc ON l.class_id = vc.class_id
            WHERE l.license_id = $license_id && status = 'approved'";
    
    $res = $db->query($sql);
    
    if (!$res) {
        die("Query error: " . $db->error);
    }
    
    if ($res->num_rows == 0) {
        die("License not found. Please go back to <a href='viewdlData.php'>View DL Data</a>");
    }
    
    $row = $res->fetch_assoc();
    
    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $fatherName = $_POST['fatherName'];
        $dob = $_POST['dob'];
        $bloodGroup = $_POST['bloodGroup'];
        $address = $_POST['address'];
        $gender = $_POST['gender'];
        $mobileNumber = $_POST['mobileNumber'];
        $email = $_POST['email'];
        $rto_id = $_POST['rto_id'];
        $status = $_POST['status'];
        $validityDate = $_POST['validity'];
        $issueDate = $_POST['issueDate'];
        
        $person_id = $row['person_id'];
        
        // Update person table
        $query_person = "UPDATE person 
                        SET name='$name', 
                            fatherName='$fatherName', 
                            dob='$dob', 
                            bloodGroup='$bloodGroup', 
                            address='$address', 
                            gender='$gender', 
                            mobileNumber='$mobileNumber', 
                            email='$email'
                        WHERE person_id=$person_id";
        
        $result_person = $db->query($query_person);
        
        // Update licenses table
        $query_licenses = "UPDATE licenses 
                          SET rto_id=$rto_id,
                              status='$status',
                              validityDate='$validityDate',
                              issueDate='$issueDate'
                          WHERE license_id=$license_id";
        
        $result_licenses = $db->query($query_licenses);
        
        if (!$result_licenses || !$result_person) {
            die("Update failed: " . $db->error);
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
</head>
<body>
    <?php require_once('../includes/header.php'); ?>
    <br>
    <h1 class="text-white text-center font-weight-bold bg-warning" style="font-size: 55px;"> Edit DL Data </h1>
    <div class="container"><br>
        <div class="col-lg-6 m-auto d-block">
            <form method="POST" onsubmit="return validation()" class="bg-light p-3">
                <div class="form-group">
                    <label for="name" class="font-weight-bold"> Name: </label>
                    <input type="text" name="name" class="form-control" id="name" value="<?php echo htmlspecialchars($row['name']); ?>">
                    <span id="nameerr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="licenseNumber" class="font-weight-bold"> DL Number: </label>
                    <input type="text" name="licenseNumber" class="form-control" value="<?php echo htmlspecialchars($row['licenseNumber']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="fatherName" class="font-weight-bold"> Father Name: </label>
                    <input type="text" name="fatherName" class="form-control" id="fatherName" value="<?php echo htmlspecialchars($row['fatherName']); ?>">
                    <span id="fatherNameerr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="dob" class="font-weight-bold"> DOB: </label>
                    <input type="date" name="dob" class="form-control" id="dob" value="<?php echo htmlspecialchars($row['dob']); ?>">
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
                            <?php echo ($row['gender'] == 'Male' || $row['gender'] == 'Ma' || $row['gender'] == 'M') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="genderMale">Male</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="Female"
                            <?php echo ($row['gender'] == 'Female' || $row['gender'] == 'Fe' || $row['gender'] == 'F') ? 'checked' : ''; ?>>
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
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['rtoName'] . ' (' . $row['rtoCode'] . ')'); ?>" readonly>
                    <input type="hidden" name="rto_id" value="<?php echo $row['rto_id']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="classCode" class="font-weight-bold"> Vehicle Class: </label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['classCode'] . ' - ' . $row['classDescription']); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="status" class="font-weight-bold">Status:</label>
                    <select name="status" class="form-control" id="status">
                        <option value="pending" <?php echo ($row['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo ($row['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo ($row['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                        <option value="expired" <?php echo ($row['status'] == 'expired') ? 'selected' : ''; ?>>Expired</option>
                    </select>
                    <span id="statuserr" class="text-danger font-weight-bold"></span>
                </div>
                <div class="form-group">
                    <label for="validity" class="font-weight-bold"> Validity: </label>
                    <input type="date" name="validity" class="form-control" id="validity" value="<?php echo htmlspecialchars($row['validityDate']); ?>">
                    <span id="validityerr" class="text-danger font-weight-bold"> </span>
                </div>
                <div class="form-group">
                    <label for="issueDate" class="font-weight-bold"> Issue Date: </label>
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
            document.querySelectorAll('.text-danger').forEach(function(el) {
                el.innerHTML = '';
            });

            var isValid = true;
            
            var name = document.getElementById('name').value;
            if (name.trim() === "") {
                document.getElementById('nameerr').innerHTML = " ** Please fill the name field";
                isValid = false;
            }

            var fatherName = document.getElementById('fatherName').value;
            if (fatherName.trim() === "") {
                document.getElementById('fatherNameerr').innerHTML = " ** Please fill the father name field";
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

    <?php require_once('../includes/footer.php'); ?>
    <script src="../assets/js/jquery/jquery-2.2.4.min.js"></script>
    <script src="../assets/js/bootstrap/popper.min.js"></script>
    <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/plugins.js"></script>
    <script src="../assets/js/active.js"></script>
</body>
</html>

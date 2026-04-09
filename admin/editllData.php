<?php
    session_start();
    require_once('../config/Connection.php');
    
    // Get license_id from session (set in viewllData.php)
    $license_id = $_SESSION['license_id'];
    
    $obj = new Connection();
    $db = $obj->getNewConnection();
    
    // Query to get license data with JOINs
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
            WHERE l.license_id = $license_id";

    $res = $db->query($sql);
    $row = $res->fetch_assoc();
    
    if (!$row) {
        die("License not found");
    }
    
    if (isset($_POST['submit']))
    {
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

        // Update person table
        $person_id = $row['person_id'];
        $q_person = "UPDATE person 
                    SET name='$name', fatherName='$fatherName', 
                        dob='$dob', bloodGroup='$bloodGroup', 
                        address='$address', gender='$gender', 
                        mobileNumber='$mobileNumber', email='$email' 
                    WHERE person_id=$person_id";
        
        $res_person = $db->query($q_person);
        
        // Update license table
        $q_license = "UPDATE licenses 
                     SET rto_id=$rto_id, status='$status', 
                         validityDate='$validityDate', issueDate='$issueDate' 
                     WHERE license_id=$license_id";
        
        $res_license = $db->query($q_license);
        
        if (!$res_person || !$res_license) {
            die($db->error);
        }
        
        $db->close();
        header("Location: viewllData.php");
        die();
    }
?>

<html>
<?php require_once('../includes/header.php'); ?>
    <br>
    <h1 class="text-white text-center font-weight-bold bg-warning" style="font-size: 55px;"> Edit LL Data </h1>
    <div class="container"><br>
        <div class="col-lg-6 m-auto d-block">
            <form method="POST" onsubmit="return validation()" class="bg-light">
                <div class="form-group">
					<label for="name" class="font-weight-bold"> Name: </label>
					<input type="text" name="name" class="form-control" id="name" value="<?php echo $row['name'] ?>">
					<span id="nameerr" class="text-danger font-weight-bold"> </span>
				</div>
                <div class="form-group">
					<label for="licenseNumber" class="font-weight-bold"> License Number: </label>
					<input type="text" name="licenseNumber" class="form-control" id="licenseNumber" value="<?php echo $row['licenseNumber'] ?>" readonly>
				</div>
                <div class="form-group">
					<label for="fatherName" class="font-weight-bold"> Last Name: </label>
					<input type="text" name="fatherName" class="form-control" id="fatherName" value="<?php echo $row['fatherName'] ?>">
					<span id="fatherNameerr" class="text-danger font-weight-bold"> </span>
				</div>
                <div class="form-group">
					<label for="dob" class="font-weight-bold"> DOB: </label>
					<input type="date" name="dob" class="form-control" id="dob" value="<?php echo $row['dob'] ?>">
					<span id="doberr" class="text-danger font-weight-bold"> </span>
				</div>
                <div class="form-group">
					<label for="bloodGroup" class="font-weight-bold"> Blood Group: </label>
					<input type="text" name="bloodGroup" class="form-control" id="bloodGroup" value="<?php echo $row['bloodGroup'] ?>">
					<span id="bloodGrouperr" class="text-danger font-weight-bold"> </span>
				</div>
                <div class="form-group">
					<label for="address" class="font-weight-bold"> Address: </label>
					<input type="text" name="address" class="form-control" id="address" value="<?php echo $row['address'] ?>">
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
				</div>
                <div class="form-group">
					<label for="mobileNumber" class="font-weight-bold"> Mobile Number: </label>
					<input type="number" name="mobileNumber" class="form-control" id="mobileNumber" value="<?php echo $row['mobileNumber'] ?>">
					<span id="mobileNumbererr" class="text-danger font-weight-bold"> </span>
				</div>
                <div class="form-group">
					<label for="email" class="font-weight-bold"> Email: </label>
					<input type="email" name="email" class="form-control" id="email" value="<?php echo $row['email'] ?>">
					<span id="emailerr" class="text-danger font-weight-bold"> </span>
				</div>
                <div class="form-group">
					<label for="rto_id" class="font-weight-bold"> RTO: </label>
					<input type="text" name="rto_display" class="form-control" value="<?php echo $row['rtoName'] . ' (' . $row['rtoCode'] . ')' ?>" readonly>
                    <input type="hidden" name="rto_id" value="<?php echo $row['rto_id'] ?>">
					<span id="rtoerr" class="text-danger font-weight-bold"> </span>
				</div>
                <div class="form-group">
                    <label for="classCode" class="font-weight-bold"> Vehicle Class: </label>
                    <input type="text" name="classCode" class="form-control" value="<?php echo $row['classCode'] . ' - ' . $row['classDescription'] ?>" readonly>
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
					<input type="date" name="validity" class="form-control" id="validity" value="<?php echo $row['validityDate'] ?>">
					<span id="validityerr" class="text-danger font-weight-bold"> </span>
				</div>
                <div class="form-group">
					<label for="issueDate" class="font-weight-bold"> Issue Date: </label>
					<input type="date" name="issueDate" class="form-control" id="issueDate" value="<?php echo $row['issueDate'] ?>">
					<span id="issueDateerr" class="text-danger font-weight-bold"> </span>
				</div>
                <center><input type="submit" name="submit" value="SUBMIT" class="btn btn-success"><center>
            </form>
            <br>
        </div>
    </div>
    <script type="text/javascript">
        function validation() {
            var name = document.getElementById('name').value;
            if (name == "") {
                document.getElementById('nameerr').innerHTML =" ** Please fill the name field";
                return false;
            }
            var llno = document.getElementById('llno').value;
            if (llno == "") {
                document.getElementById('llnoerr').innerHTML =" ** Please fill the llno field";
                return false;
            }
            var fatherName = document.getElementById('fatherName').value;
            if (fatherName == "") {
                document.getElementById('fatherNameerr').innerHTML =" ** Please fill the lastName field";
                return false;
            }
            var dob = document.getElementById('dob').value;
            if (dob == "") {
                document.getElementById('doberr').innerHTML =" ** Please fill the dob field";
                return false;
            }
            var bloodGroup = document.getElementById('bloodGroup').value;
            if (bloodGroup == "") {
                document.getElementById('bloodGrouperr').innerHTML =" ** Please fill the bloodGroup field";
                return false;
            }
            var address = document.getElementById('address').value;
            if (address == "") {
                document.getElementById('addresserr').innerHTML =" ** Please fill the address field";
                return false;
            }
            var gender = document.getElementById('gender').value;
            if (gender == "") {
                document.getElementById('gendererr').innerHTML =" ** Please fill the gender field";
                return false;
            }
            var mobileNumber = document.getElementById('mobileNumber').value;
            if (mobileNumber == "") {
                document.getElementById('mobileNumbererr').innerHTML =" ** Please fill the mobileNumber field";
                return false;
            }
            var email = document.getElementById('email').value;
            if (email == "") {
                document.getElementById('emailerr').innerHTML =" ** Please fill the email field";
                return false;
            }
            var rto = document.getElementById('rto').value;
            if (rto == "") {
                document.getElementById('rtoerr').innerHTML =" ** Please fill the rto field";
                return false;
            }
            var status = document.getElementById('status').value;
            if (status == "") {
                document.getElementById('statuserr').innerHTML =" ** Please fill the status field";
                return false;
            }
            var validity = document.getElementById('validity').value;
            if (validity == "") {
                document.getElementById('validityerr').innerHTML =" ** Please fill the validity field";
                return false;
            }
            var issueDate = document.getElementById('issueDate').value;
            if (issueDate == "") {
                document.getElementById('issueDateerr').innerHTML =" ** Please fill the issueDate field";
                return false;
            }
            return true;
        }
    </script>
    <?php require_once('../includes/footer.php'); ?>
    <!-- ##### All Javascript Script ##### -->
    <!-- jQuery-2.2.4 js -->
    <script src="../assets/js/jquery/jquery-2.2.4.min.js"></script>
    <!-- Popper js -->
    <script src="../assets/js/bootstrap/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="../assets/js/bootstrap/bootstrap.min.js"></script>
    <!-- All Plugins js -->
    <script src="../assets/js/plugins/plugins.js"></script>
    <!-- Active js -->
    <script src="../assets/js/active.js"></script>
</html>


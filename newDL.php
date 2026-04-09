<?php
    $llno = '';
    $aadhar ='';
    $llnoerr = '';
    $aadharerr ='';
    

    if (isset($_POST['submit'])) {
        require_once('config/Connection.php');
        session_start();
        $llno = $_POST['llno'];
        $aadhar = $_POST['aadhar'];
        
        // Input validation
        if (empty($llno)) {
            $llnoerr = "LL No is required";
        }
        if (empty($aadhar)) {
            $aadharerr = "Aadhar No is required";
        }

        if (empty($llnoerr) && empty($aadharerr)) {
            $obj = new Connection();
            $db = $obj->getNewConnection();

            // Query normalized structure - check if person has approved LL
            $sql = "SELECT l.*, p.aadhar, r.rto_id, r.rtoName
            FROM licenses l 
            JOIN person p ON l.person_id = p.person_id
            JOIN rtooffices r ON l.rto_id = r.rto_id
            WHERE l.licenseNumber LIKE ?
            AND p.aadhar = ?
            AND l.licenseType = 'LL'
            LIMIT 1";

            $llnoPattern = "LL_".$llno."%";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ss', $llnoPattern, $aadhar);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();

            if ($row) {
                $rto_id = $row['rto_id'];
                $rto_name = $row['rtoName'];
                $person_id = $row['person_id'];
                
                // Check status (ENUM: 'pending', 'approved', 'rejected', 'expired')
                if ($row['status'] == 'pending') {
                    $aadharerr = "LL Status Pending - Cannot apply for DL yet";
                } else if ($row['status'] == 'rejected') {
                    $aadharerr = "LL was rejected - Cannot apply for DL";
                } else if ($row['status'] == 'expired') {
                    $aadharerr = "LL has expired - Cannot apply for DL";
                } else if ($row['status'] == 'approved') {
                    // LL is approved, allow DL application
                    $_SESSION['llno'] = $llno;
                    $_SESSION['aadhar'] = $aadhar;
                    $_SESSION['rto_id'] = $rto_id;
                    $_SESSION['rto_name'] = $rto_name;
                    $_SESSION['person_id'] = $person_id;
                    header("Location: confirmdl.php");
                    die();
                } else {
                    $aadharerr = "LL status is invalid: " . $row['status'];
                }
            } else {
                $llnoerr = "Invalid LL No or Aadhar No, or no LL found";
            }

            $db->close();
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Apply For New DL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <?php require_once('includes/header.php'); ?>
    <br>
    <h1 class="text-white text-center font-weight-bold bg-warning" style="font-size: 55px;"> Apply For New DL </h1>
    <div class="container"><br>
        <div class="col-lg-6 m-auto d-block">
            <form method="POST" onsubmit="return validation()" class="bg-light">
                <div class="form-group">
					<label for="llno" class="font-weight-bold"> Enter LL No: </label>
					<input type="number" name="llno" class="form-control" id="llno" value="<?php echo $llno; ?>">
					<span id="llnoerr" class="text-danger font-weight-bold"> <?php echo $llnoerr; ?> </span>
				</div>
                <div class="form-group">
					<label for="aadhar" class="font-weight-bold"> Enter Aadhar No: </label>
					<input type="number" name="aadhar" class="form-control" id="aadhar" value="<?php echo $aadhar; ?>">
					<span id="aadharerr" class="text-danger font-weight-bold"> <?php echo $aadharerr; ?> </span>
				</div>
                <center><input type="submit" name="submit" value="SUBMIT" class="btn btn-success"><center>
                    
            </form>
            <br>
        </div>
    </div>
    <script type="text/javascript">
        function validation() {
            var llno = document.getElementById('llno').value;
            var aadhar = document.getElementById('aadhar').value;
            if (llno == "") {
                document.getElementById('llnoerr').innerHTML =" ** Please fill the llno field";
                return false;
            }
            if (aadhar == "") {
                document.getElementById('aadharerr').innerHTML =" ** Please fill the aadhar field";
                return false;
            }
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
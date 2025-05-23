<?php
    $llno = '';
    $aadhar ='';
    $llnoerr = '';
    $aadharerr ='';

    if (isset($_POST['submit'])) {
        require_once('Connection.php');
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

            // Prepared statement to prevent SQL injection
            $sql = "SELECT * FROM ll WHERE llno = ? AND aadhar = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ii', $llno, $aadhar); // 'ii' means two integers
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();

            if ($row) {
                $rto = $row['rto'];
                if ($row['status'] == 0) {
                    $aadharerr = "Status Pending";
                } else {
                    $_SESSION['llno'] = $llno;
                    $_SESSION['aadhar'] = $aadhar;
                    $_SESSION['rto'] = $rto;
                    header("Location: confirmdl.php");
                    die();
                }
            } else {
                if ($res->num_rows == 0) {
                    $llnoerr = "Invalid LL No or Aadhar No";
                }
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
    <?php require_once('header.php'); ?>
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
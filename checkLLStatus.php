<?php
session_start();
$aadhar = '';
$llnumber = '';
$aadharerr = '';
$llnumbererr = '';
$status = '';

if (isset($_POST['submit'])) {
    require_once('Connection.php');
    
    $aadhar = $_POST['aadhar'];
    $llnumber = $_POST['id'];  // Changed from 'llno' to 'id'
    
    $obj = new Connection();
    $db = $obj->getNewConnection();
    $sql = "SELECT aadhar, llno, status FROM ll WHERE aadhar= ? AND llno=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $aadhar, $llnumber);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
    $db->close();

    if ($row) {
        $_SESSION['aadhar'] = $aadhar;
        $_SESSION['llno'] = $llnumber;
        $_SESSION['status'] = $row['status'];
        header('Location: lldis.php');
        exit();
    } else {
        // Error handling when no rows are found
        $aadharerr = "Invalid Aadhar Number or LL Number";
        $llnumbererr = "Invalid Aadhar Number or LL Number";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Check LL Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <?php require_once('header.php'); ?>
    <br>
    <h1 class="text-white text-center font-weight-bold bg-warning" style="font-size: 55px;"> Check LL Status </h1>
    <div class="container"><br>
        <div class="col-lg-6 m-auto d-block">
            <form method="POST" onsubmit="return validation()" class="bg-light">
                <div class="form-group">
                    <label for="aadhar" class="font-weight-bold"> Enter Aadhar Number: </label>
                    <input type="number" name="aadhar" class="form-control" id="aadhar" value="<?php echo $aadhar; ?>">
                    <span id="aadharerr" class="text-danger font-weight-bold"> <?php echo $aadharerr; ?> </span>
                </div>
                <div class="form-group">
                    <label for="id" class="font-weight-bold"> Enter LL Number: </label>
                    <input type="text" name="id" class="form-control" id="id" value="<?php echo $llnumber; ?>">
                    <span id="iderr" class="text-danger font-weight-bold"> <?php echo $llnumbererr; ?> </span>
                </div>
                <center><input type="submit" name="submit" value="SUBMIT" class="btn btn-success"></center>
            </form>
            <br>
        </div>
    </div>
    <script type="text/javascript">
        function validation() {
            var aadhar = document.getElementById('aadhar').value;
            if (aadhar == "") {
                document.getElementById('aadharerr').innerHTML = " ** Please fill the aadhar field";
                return false;
            }
            var id = document.getElementById('id').value;  // Changed from 'llno' to 'id'
            if (id == "") {
                document.getElementById('iderr').innerHTML = " ** Please fill the id field";
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

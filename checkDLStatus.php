<?php
error_reporting(0); // Suppress all warnings and notices

$aadhar = '';
$aadharerr = '';

if (isset($_POST['submit'])) {
    require_once('Connection.php');
    session_start();

    $aadhar = trim($_POST['aadhar']);

    // Server-side validation
    if (!preg_match('/^\d{12}$/', $aadhar)) {
        $aadharerr = "Invalid Aadhar Number. It must be exactly 12 digits.";
    } else {
        $obj = new Connection();
        $db = $obj->getNewConnection();

        // Secure query using prepared statement
        $stmt = $db->prepare("SELECT aadhar, rto FROM dl WHERE aadhar = ?");
        $stmt->bind_param("s", $aadhar);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();
        $db->close();

        if ($row) {
            $_SESSION['aadhar'] = $row['aadhar'];
            $_SESSION['rto'] = $row['rto'];
            header("Location: checkdlqr.php");
            exit();
        } else {
            $aadharerr = "Aadhar number not found in DL records.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Check DL Status</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php require_once('header.php'); ?>
<br>
<h1 class="text-white text-center font-weight-bold bg-warning" style="font-size: 55px;">Check DL Status</h1>

<div class="container"><br>
    <div class="col-lg-6 m-auto d-block">
        <form method="POST" onsubmit="return validation()" class="bg-light p-4 rounded">
            <div class="form-group">
                <label for="aadhar" class="font-weight-bold">Enter Aadhar Number:</label>
                <input type="number" name="aadhar" class="form-control" id="aadhar" value="<?php echo htmlspecialchars($aadhar); ?>">
                <span id="aadharerr" class="text-danger font-weight-bold"><?php echo $aadharerr; ?></span>
            </div>
            <center><input type="submit" name="submit" value="SUBMIT" class="btn btn-success"></center>
        </form>
        <br>
    </div>
</div>

<script type="text/javascript">
function validation() {
    var aadhar = document.getElementById('aadhar').value;
    var err = document.getElementById('aadharerr');
    err.innerHTML = "";

    if (aadhar === "") {
        err.innerHTML = " ** Please enter the Aadhar number.";
        return false;
    }
    if (aadhar.length !== 12) {
        err.innerHTML = " ** Aadhar No should be exactly 12 digits.";
        return false;
    }
    return true;
}
</script>

<?php require_once('footer.php'); ?>

<!-- ##### All Javascript Script ##### -->
<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/popper.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/plugins/plugins.js"></script>
<script src="js/active.js"></script>
</body>
</html>

<?php
error_reporting(E_PARSE);
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    header("Location: adminPanel.php");
    exit();
}

$loginError = false;

if (isset($_POST['submit'])) {
    require_once('Connection.php');
    $username = $_POST['username'];
    $pass = $_POST['password'];

    $obj = new Connection();
    $db = $obj->getNewConnection();

    // Use prepared statements to prevent SQL injection
    $stmt = $db->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $pass);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $db->close();

    if ($row && $row['username'] === $username && $row['password'] === $pass) {
        $_SESSION['loggedin'] = 1;
        header("Location: adminPanel.php");
        exit();
    } else {
        $loginError = true; // Trigger alert on page
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<?php require_once('header.php'); ?>
<br>
<h1 class="text-white text-center font-weight-bold bg-warning" style="font-size: 55px;">Admin Login</h1>
<div class="container"><br>
    <div class="col-lg-6 m-auto d-block">
        <form method="POST" onsubmit="return validation()" class="bg-light p-3 rounded">
            <div class="form-group">
                <label for="username" class="font-weight-bold">Username:</label>
                <input type="text" name="username" class="form-control" id="username">
                <span id="usernameerr" class="text-danger font-weight-bold"></span>
            </div>
            <div class="form-group">
                <label for="password" class="font-weight-bold">Password:</label>
                <input type="password" name="password" class="form-control" id="password">
                <span id="passworderr" class="text-danger font-weight-bold"></span>
            </div>
            <center><input type="submit" name="submit" value="LOGIN" class="btn btn-success"></center>
        </form>
        <br>
    </div>
</div>

<script type="text/javascript">
    function validation() {
        var username = document.getElementById('username').value.trim();
        var password = document.getElementById('password').value.trim();
        var valid = true;

        document.getElementById('usernameerr').innerHTML = "";
        document.getElementById('passworderr').innerHTML = "";

        if (username === "") {
            document.getElementById('usernameerr').innerHTML = " ** Please fill the username field";
            valid = false;
        }
        if (password === "") {
            document.getElementById('passworderr').innerHTML = " ** Please fill the password field";
            valid = false;
        }

        return valid;
    }

    // Show login error alert if credentials were invalid
    <?php if ($loginError): ?>
        alert("Invalid username or password.");
    <?php endif; ?>
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

<?php
    $aadhar = '';
    $aadharerr = '';
    
    if (isset($_POST['submit'])) {
        require_once('config/Connection.php');
        session_start();
        $aadhar = $_POST['aadhar'];
        
        // Input validation
        if (empty($aadhar)) {
            $aadharerr = "Aadhar No is required";
        } elseif (strlen($aadhar) != 12) {
            $aadharerr = "Aadhar number must be 12 digits";
        }

        if (empty($aadharerr)) {
            $obj = new Connection();
            $db = $obj->getNewConnection();

            // Check if person exists with this aadhar and has LL licenses
            $sql = "SELECT COUNT(*) as ll_count
                    FROM licenses l 
                    JOIN person p ON l.person_id = p.person_id
                    WHERE p.aadhar = ? AND l.licenseType = 'LL'";

            $stmt = $db->prepare($sql);
            $stmt->bind_param('s', $aadhar);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();

            if ($row && $row['ll_count'] > 0) {
                // Person has LL licenses, redirect to display all
                $_SESSION['aadhar'] = $aadhar;
                header("Location: displayalldl.php");
                die();
            } else {
                $aadharerr = "No Learner License found for this Aadhar number";
            }

            $db->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Apply For New DL</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: rgba(96, 157, 219, 0.36);
        }
    </style>
</head>
<body>
    <?php require_once('includes/header.php'); ?>
    
    <div class="container my-5">
        <!-- Page Title -->
        <div class="bg-warning text-white text-center rounded shadow-lg p-4 mb-4">
            <h1 class="display-4 font-weight-bold mb-2 text-white">APPLY FOR NEW DL</h1>
            <p class="lead mb-0 text-white">Enter your Aadhar number to view your learner licenses</p>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="card shadow-lg border-0 rounded">
                    <div class="card-header bg-warning text-white text-center py-3">
                        <h4 class="mb-0 font-weight-bold text-white">ENTER DETAILS</h4>
                    </div>
                    
                    <div class="card-body p-4">
                        <form method="POST" onsubmit="return validation()">
                            <div class="form-group">
                                <label for="aadhar" class="font-weight-bold text-dark">
                                    <i class="fa fa-id-card mr-2"></i>Aadhar Number:
                                </label>
                                <input 
                                    type="text" 
                                    name="aadhar" 
                                    class="form-control form-control-lg" 
                                    id="aadhar" 
                                    value="<?php echo htmlspecialchars($aadhar); ?>" 
                                    placeholder="Enter your 12-digit Aadhar number"
                                    maxlength="12"
                                    pattern="\d{12}"
                                >
                                <small class="form-text text-muted">Please enter your 12-digit Aadhar number</small>
                                <span id="aadharerr" class="text-danger font-weight-bold d-block mt-2">
                                    <?php echo $aadharerr; ?>
                                </span>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" name="submit" class="btn btn-success btn-lg px-5 py-3 rounded-pill font-weight-bold text-uppercase shadow">
                                    <i class="fa fa-search mr-2"></i> Find My Licenses
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-footer bg-light text-center py-3">
                        <small class="text-muted">
                            <i class="fa fa-info-circle mr-1"></i>
                            Don't have a Learner License? 
                            <a href="newLL.php" class="font-weight-bold">Apply for LL first</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
        function validation() {
            var aadhar = document.getElementById('aadhar').value;
            var aadharerr = document.getElementById('aadharerr');
            
            // Clear previous errors
            aadharerr.innerHTML = "";
            
            if (aadhar == "") {
                aadharerr.innerHTML = "** Please fill the Aadhar field";
                return false;
            }
            
            if (aadhar.length != 12) {
                aadharerr.innerHTML = "** Aadhar number must be exactly 12 digits";
                return false;
            }
            
            if (!/^\d+$/.test(aadhar)) {
                aadharerr.innerHTML = "** Aadhar number must contain only digits";
                return false;
            }
            
            return true;
        }
        
        // Allow only numbers in Aadhar field
        document.getElementById('aadhar').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
    
    <?php require_once('includes/footer.php'); ?>
    
    <!-- ##### All Javascript Script ##### -->
    <script src="assets/js/jquery/jquery-2.2.4.min.js"></script>
    <script src="assets/js/bootstrap/popper.min.js"></script>
    <script src="assets/js/bootstrap/bootstrap.min.js"></script>
    <script src="assets/js/plugins/plugins.js"></script>
    <script src="assets/js/active.js"></script>
</body>
</html>

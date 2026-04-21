<footer class="footer-area">
        <!-- Main Footer Area -->
        <div class="main-footer-area section-padding-100-0 bg-img bg-overlay" >
            <div class="container">
                <div class="row">

                    <!-- Footer Widget Area -->
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="footer-widget mb-100">
                            <div class="widget-title">
                                <?php
                                // Detect if we're in admin folder
                                $inAdmin = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false);
                                $basePath = $inAdmin ? '../' : '';
                                ?>
                                <a href="#"><img src="<?php echo $basePath; ?>assets/img/core-img/logo.png" alt=""></a>
                            </div>
                            <p>This website is made to perform all the RTO related services online to make it easily available for all and for faster registration than the old school method.</p>
                            <div class="footer-social-info">
                                <a href="#"><i class="fa fa-facebook"></i></a>
                                <a href="#"><i class="fa fa-twitter"></i></a>
                                <a href="#"><i class="fa fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <!-- Footer Widget Area -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="footer-widget mb-100">
                            <div class="widget-title">
                                <h6>Quick Links</h6>
                            </div>
                            <nav>
                                <ul class="useful-links d-flex justify-content-between flex-wrap">
                                    <li><a href="<?php echo $basePath; ?>index.php">Home</a></li>
                                    <li><a href="<?php echo $basePath; ?>qrcode.php">QR Code</a></li>
                                    
                                    <li><a href="<?php echo $basePath; ?>newLL.php">Apply For New LL</a></li>
                                    <li><a href="<?php echo $basePath; ?>checkLLStatus.php">Check Ll Status</a></li>
                                    <li><a href="<?php echo $basePath; ?>newDL.php">Apply For New DL</a></li>
                                    <li><a href="<?php echo $basePath; ?>checkDLStatus.php">Check DL Status</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </footer>
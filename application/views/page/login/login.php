<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title; ?></title>
    <!-- plugins:css -->
    <link rel="stylesheet"
        href="<?= base_url('template/'); ?>assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/vendors/iconfonts/ionicons/css/ionicons.css">
    <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/vendors/iconfonts/typicons/src/font/typicons.css">
    <link rel="stylesheet"
        href="<?= base_url('template/'); ?>assets/vendors/iconfonts/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/vendors/css/vendor.bundle.addons.css">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/css/shared/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="<?= base_url('template/'); ?>assets/images/favicon.png" />
</head>

<body>
    <div class="container-scroller">

        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth auth-bg-1 theme-one"
                style="background-image:url(template/img/bg_login2.webp)">
                <div class="row <?php echo $this->input->get('mobile') ? '' : 'w-100'; ?>">
                    <div class="col-lg-4" style="margin-left:10%">

                        <div class="auto-form-wrapper" style="padding-top:3rem;">
                            <?php
                            if ($this->session->flashdata('error')) {
                                ?>
                                <h3 class="" id="wrapNotifError" style="margin-bottom: 3rem;">
                                    <?php echo $this->session->flashdata('error'); ?>
                                </h3>
                            <?php } ?>
                            <h2 style="margin-bottom:2rem" class=" text-center">Centro Informação Telkomcel
                            </h2>
                            <form action="<?= base_url('Login/prosesLogin') ?>" method="post">
                                <input type="hidden" name="mobile"
                                    value="<?php echo $this->input->get('mobile') ? 'Y' : 'N'; ?>">
                                <div class="form-group">
                                    <label class="label">Username</label>
                                    <div class="input-group">
                                        <input type="text" name="username" style="font-size: 1rem;" class="form-control"
                                            placeholder="Username">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="label">Password</label>
                                    <div class="input-group">
                                        <input type="password" name="password" style="font-size: 1rem;"
                                            class="form-control" placeholder="*********">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="mdi mdi-check-circle-outline"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary submit-btn btn-block">Login</button>
                                </div>
                                <div class="form-group d-flex justify-content-between">
                                    <!-- <div class="form-check form-check-flat mt-0">
                                          <label class="form-check-label"> -->
                                    <!-- <input type="checkbox" class="form-check-input" checked> Keep me signed in </label> -->
                                    <!-- </div> -->
                                    <!-- <a href="#" class="text-small forgot-password text-black">Forgot Password</a> -->
                                </div>
                                <!-- <div class="form-group">
                                      <button class="btn btn-block g-login">
                                        <img class="mr-3" src="<?= base_url('template/'); ?>assets/images/file-icons/icon-google.svg" alt="">Log in with Google</button>
                                    </div>
                                    <div class="text-block text-center my-3">
                                      <span class="text-small font-weight-semibold">Not a member ?</span>
                                      <a href="register.html" class="text-black text-small">Create new account</a>
                                    </div> -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="<?= base_url('template/'); ?>assets/vendors/js/vendor.bundle.base.js"></script>
    <script src="<?= base_url('template/'); ?>assets/vendors/js/vendor.bundle.addons.js"></script>
    <!-- endinject -->
    <!-- inject:js -->
    <script src="<?= base_url('template/'); ?>assets/js/shared/off-canvas.js"></script>
    <script src="<?= base_url('template/'); ?>assets/js/shared/misc.js"></script>
    <!-- endinject -->

    <script>
        localStorage['logged'] = 'Y';

        $(document).ready(function () {
            if ($('#wrapNotifError').html() != '') {
                $('#wrapNotifError').addClass('alert-danger').addClass('alert');
            }
        });
    </script>
</body>

</html>
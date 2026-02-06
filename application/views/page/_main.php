<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title><?= @$title == '' ? 'Helpdesk' : $title ?></title>
        <!-- plugins:css -->
        <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/vendors/iconfonts/mdi/css/materialdesignicons.min.css">
        <!-- <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/vendors/iconfonts/ionicons/css/ionicons.css">
        <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/vendors/iconfonts/typicons/src/font/typicons.css"> -->
        <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/vendors/iconfonts/flag-icon-css/css/flag-icon.min.css">
        <!-- <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/vendors/css/vendor.bundle.base.css">
        <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/vendors/css/vendor.bundle.addons.css"> -->
        <!-- endinject -->
        <!-- plugin css for this page -->
        <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/vendors/iconfonts/font-awesome/css/font-awesome.min.css" />
        <!-- End plugin css for this page -->
        <!-- inject:css -->
        <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/css/shared/style.css">
        <!-- endinject -->
        <!-- Layout styles -->
        <link rel="stylesheet" href="<?= base_url('template/'); ?>assets/css/demo_1/style.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
        <!-- End Layout styles -->
        <link rel="shortcut icon" href="<?= base_url('template/'); ?>assets/images/favicon_new.png" />
        <link rel="stylesheet" href="<?= base_url('dist/sweetalert2.css'); ?>">
        <script src="<?php echo base_url('dist/sweetalert2.all.js'); ?>"></script>
        <!-- <script src="https://code.jquery.com/jquery-3.3.1.js"></script> -->
        <script src="<?php echo base_url('dist/jquery-3.3.1.js'); ?>"></script>
        <script>
//            if (navigator.userAgent.toString().indexOf('Chrome') < 0) {
//                alert('Please use browser Google Chrome to get best result');
//                window.location = 'https://www.google.com/intl/id/chrome/';
//            }
        </script>
    </head>
    <style>
        @import url('https://fonts.googleapis.com/css?family=IBM+Plex+Sans&display=swap');

        :root, body{
            color:#525252;
        }

        .sidebar > .nav:not(.sub-menu) > .nav-item:hover:not(.nav-profile):not(.hover-open) > .nav-link:not([aria-expanded="true"]) {
            background: #cc0c0c;
        }

        .sidebar {
            background: linear-gradient(45deg, #8e24aa, #ff6e40) !important;
        }

        .sidebar > .nav .nav-item:not(.hover-open) .collapse .sub-menu .nav-item .nav-link:before, .sidebar > .nav .nav-item:not(.hover-open) .collapsing .sub-menu .nav-item .nav-link:before {
            background: #ad0d0d;
        }

        .helpdesk{
            font-family: 'IBM Plex Sans', sans-serif;
            font-size: 22px;
        }

        .version{
            font-size: 10px;
            background: black;
            position: relative;
            color: #FFF;
            top: 2px;
            left: 2px;
            padding: 2px;
            border-radius: 10px;
        }
        select{
            min-width:3.5rem;
        }
        .card-title.head-module-action{
            margin-bottom:0px;
            text-align:center;
            padding: 0.8rem;
            border-bottom: solid 1px #DDD;
            /*            background-color: #d4536c;*/
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            background: linear-gradient(20deg, #9c2f9e, #ff6e40) !important;
        }
        .card-title.head-module-action a{
            color: white!important;
            padding: 0.5rem;
            border-radius: 1rem;
        }
        .card-title.head-module-action a:hover{
            background-color: #f87045;
            /*            background: linear-gradient(120deg, #8e24aa, #ff6e40) !important;*/
            text-decoration:none!important;
            color:white!important;
        }
        .title-module{
            font-size: 1.6rem;
            padding: 0rem;
            color: #796b6b;
        }
        .subtitle-module{
            font-size:1.1rem;
            padding: 0rem 0rem 2rem;
            color:#7f7f7f;
        }
        .main-panel{
            padding: 0.5rem 1rem;
            min-height: 58rem;
        }
        .content-wrapper{
            background: none!important;
        }
        .footer{
            margin-top:5rem;
            background: none!important;
        }
        .card-body.body-module-action{
            padding:0px!important;
        }
        .card-body.body-module-action .card.mt-2{
            margin: 0px!important;
        }
        .dataTables_paginate.paging_simple_numbers, .dataTables_info{
            margin-top:2rem!important;
        }
        div.dataTables_info{
            padding-top:0px!important;
        }
        .body-module-action .table tbody tr td,
        .body-module-action .table thead tr th {
            font-size: 0.8rem;
            padding:0.2rem 0.3rem;
        }
        .body-module-action .table thead th{
            vertical-align:middle;
        }
        .body-module-action .table td{
            height:30px;
        }
        .body-module-action .table tbody tr:hover{
            background-color: #ddd;
        }
        .body-module-action .table tbody tr td:first-child{
            text-align: center;
        }
        .navbar.default-layout .navbar-menu-wrapper{
            box-shadow: 12px 4px 16px 0 rgba(167, 175, 183, 0.33);
        }
    </style>
    <body>
        <div class="container-scroller">
            <!-- partial:../../partials/_navbar.html -->
            <?php
            if ($this->session->userdata('mobile') == 'N') {
                $this->load->view('incl/navbar');
            } else {
                ?>
                <div style="position:fixed;top:1rem;right:2rem">
                    <b><a href="javascript:void(0)" class="logoutLink">Logout</a></b>
                </div>
                <?php
            }
            ?>
            <!-- partial -->
            <div class="container-fluid page-body-wrapper">
                <!-- partial:../../partials/_sidebar.html -->
                <?php
                if ($this->session->userdata('mobile') == 'N') {
                    $this->load->view('incl/sidebar');
                }
                ?>
                <!-- partial -->
                <div class="main-panel">
                    <div class="content-wrapper" style="padding: 25px 15px" >
                        <?php $this->load->view($linkView); ?>
                    </div>
                    <!-- content-wrapper ends -->
                    <!-- partial:../../partials/_footer.html -->
                    <?php
                    if ($this->session->userdata('mobile') == 'N') {
                        $this->load->view('incl/footer');
                    }
                    ?>
                    <!-- partial -->
                </div>
                <!-- main-panel ends -->
            </div>
            <!-- page-body-wrapper ends -->
        </div>
        <!-- container-scroller -->
        <!-- plugins:js -->
        <script src="<?= base_url('template/'); ?>assets/vendors/js/vendor.bundle.base.js"></script>
        <script src="<?= base_url('template/'); ?>assets/vendors/js/vendor.bundle.addons.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

        <?php
        if ($this->uri->uri_string() == 'ComplainHandling/group_inbox') {
            ?>  
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
            <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css">

            <script type="text/javascript" src= "https://code.jquery.com/jquery-1.12.4.js"></script>
            <script type="text/javascript" src= "https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
            <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
            <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
            <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>
            <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.4.2/js/buttons.print.min.js"></script>  
            <?php
        } else {
            ?>
            <script src="<?= base_url('template/bootboxjs/'); ?>bootbox.min.js"></script>
            <script src="<?= base_url('template/bootboxjs/'); ?>bootbox.locales.min.js"></script>
            <?php
        }
        ?>
        <!-- endinject -->
        <!-- Plugin js for this page-->
        <!-- End plugin js for this page-->
        <!-- inject:js -->
        <!-- <script src="<?= base_url('template/'); ?>assets/js/shared/off-canvas.js"></script> -->
        <script src="<?= base_url('template/'); ?>assets/js/shared/misc.js?u=123"></script>
        <!-- endinject -->
        <!-- Custom js for this page-->
        <!-- End custom js for this page--> 


        <!-- Modal edit user -->

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Profile</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo base_url('ManageSystem/updateProfil'); ?>" id="formprofile" enctype="multipart/form-data"  class="form-horizontal">
                            <input type="hidden" value="<?php echo $this->session->userdata('id'); ?>" name="id"/> 
                            <div class="form-group" id="photo-preview">
                                <label class="control-label col-md-3">Photo</label>
                                <div class="col-md-9">
                                    <!-- (No photo) -->

                                    <img id="imagepreview"  src="<?php echo base_url('upload/' . $this->session->userdata('photo')); ?>" alt="" style="width: 44%;">
                                    <span class="help-block"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3" id="label-photo">Upload Photo </label>
                                <div class="col-md-9">
                                    <input type="file" name="photo" accept="images/*" id="photo" >
                                    <span class="help-block"></span>
                                </div>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>


        <script>
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#imagepreview').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#photo").change(function () {
                readURL(this);
            });


            $(document).ready(function () {

                $('#formprofile').submit(function (e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    $.ajax({
                        url: $('#formprofile').attr('action'),
                        type: "POST",
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function (data) {
                            window.location.reload();
                        },
                        done: function () {
                            window.location.reload();
                        }
                    });

                    setTimeout(function () {
                        window.location.reload();
                    }, 4000);
                });
            });
        </script>

    </body>
</html>
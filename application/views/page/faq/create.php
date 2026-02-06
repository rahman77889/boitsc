<!-- The Modal Add User -->
<div class='title-module'>Create FAQ</div>
<div class="subtitle-module">FAQ Content &raquo; Create FAQ</div>

<style>
    #huruf {
        font-size: 14px;
        padding: 7px;
        /* margin:20px; */
    }

    .jarak {
        margin: 5px;
    }
</style>

<div class="row flex-grow">

    <?php
    if ($this->session->flashdata("delete")) {
        ?>
        <script>
            Swal.fire('Success Delete Data!', 'You clicked the button!', 'success')
        </script>
    <?php }
    ;
    ?>
    <div class="col-12">
        <div class="card" style="border-radius: 10px;">
            <div class="card-title head-module-action">
                <div class="row">
                    <?php if ($this->session->userdata('privilege') != '9') { ?>
                        <?php
                        if ($this->session->userdata('privilege') != '5') {
                            ?>
                            <div class="col-sm-2">
                                <a href="#" style="color: white">
                                    <i class="fa fa-check-circle-o"></i>
                                    Enable</a>
                            </div>
                            <div class="col-sm-2">
                                <a href="#" style="color: white">
                                    <i class="fa fa-times-circle-o"></i>
                                    Disable</a>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="col-sm-3">
                            <a href="#" style="color: white" data-toggle="collapse" data-target="#demo">
                                <i class="fa fa-plus-circle"></i>
                                Add FAQ Information</a>
                        </div>
                        <?php
                        if ($this->session->userdata('privilege') != '5') {
                            ?>
                            <div class="col-sm-3">
                                <a href="#" id="btn-delete" style="color: white">
                                    <i class="fa fa-times"></i>
                                    Delete FAQ Information</a>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="card-body body-module-action">
                <div id="demo" class="collapse">
                    <div class="card">
                        <form id="submit" method="POST" action="javascript:void(0);" class="smart-form"
                            novalidate="novalidate">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label id="huruf" for="">*Category Id</label>
                                    </div>
                                    <div class="col-md-8">
                                        <span id="cloneID"></span>
                                        <select name="categoryId" id="categoryId" class="form-control jarak">
                                            <option value="">--</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label id="huruf" for="">*Faq File (*.pdf and *.jpg)</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="file" name="faqFile" accept=".pdf, .jpg" id="faqFile"
                                            class="jarak">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <h4>OR</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label id="huruf" for="">*Video File (less than 50 MB, format *.mp4)</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="file" name="videoFile" accept=".mp4" id="videoFile" class="jarak">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <h4>OR</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label id="huruf" for="">*Faq Video Url Embed</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="videoUrl" id="videoUrl" class="form-control jarak">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label id="huruf" for="">*Faq Title</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select name="id_title" id="id_title" class="form-control jarak" required>
                                            <option value="">--</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label id="huruf" for="">*Status Active</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select name="status" id="status" class="form-control jarak">
                                            <option value="N" selected="">NO</option>
                                            <option value="Y">Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary" id="btn_upload" type="submit">Save</button>
                                        <button class="btn btn-default" id="cancel" type="reset">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

                <div class="card mt-2">
                    <div class="card-body">

                        <table class="table table-bordered display" id="contoh" style="width:100%">
                            <thead>
                                <th style="width: 15px; text-align: center">
                                    #
                                </th>
                                <th style="width: 50px; text-align: center">
                                    No
                                </th>
                                <th style="text-align: center">
                                    Category
                                </th>
                                <!--                            <th style="text-align: center">Sub Category</th>-->
                                <th style="width:10px;text-align: center;">
                                    FAQ Title</th>
                                <!--                            <th style="text-align: center">
                                FAQ File</th>-->
                                <th style="text-align: center">
                                    Upload Date</th>
                                <th style="text-align: center">
                                    Upload by User</th>
                                <th style="text-align: center">
                                    Status
                                </th>

                            </thead>
                            <tbody>


                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // alert("hello world");
        getTitle();
        getCategoryName();
        getSubCategoryName();
        showdata();
        btnCari();

        $('#klikedit').click(function () {
            //  e.preventDefault();
            $('#demo').removeClass("collapse");


        });
        $('#cancel').click(function (e) {
            e.preventDefault();
            $('.collapse').collapse('hide');

        });
        $("#btn-delete").click(function () { // Ketika user mengklik tombol delete
            // // var r = confirm("Apakah anda yakin ingin menghapus data ini ?");
            // if (confirm("Apakah anda yakin ingin menghapus data ini ?")) {
            //     $("#form-delete").submit(); // Submit form d 
            // }
            confirm("Are you sure you want to submit this form?", function (result) {
                if (result) {
                    $("#form-delete").submit();
                }
            });
        });

    });


    function showdata() {


        // body...
        $('#contoh').DataTable({
            // Processing indicator
            "destroy": true,
            "searching": true,
            "processing": true,
            // DataTables server-side processing mode
            "serverSide": true,
            "scrollX": true,
            // Initial no order.
            "order": [],
            // Load data from an Ajax source
            "ajax": {
                "url": "<?= base_url("Faq/dtshowcreatefaq"); ?>",
                "type": "POST"
            },
            //Set column definition initialisation properties
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false
                }
            ]
        });


        // insert uploud file
        $('#submit').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url('Faq/do_upload'); ?>',
                type: "post",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                async: false,
                success: function (data) {
                    clearInput();
                    Swal.fire(
                        'Sucess Insert Data!',
                        'You clicked the button!',
                        'success'
                    );

                }
            });
        });
    }




    // fungsi link delete data
    function hapus(faqId) {
        $("#btn-delete").attr("href", "<?php echo base_url(); ?>Faq/deletefaq?faqId=" + faqId);
    }




    //fungsi add dan edit

    function proses() {
        var categoryId = $('input[name=categoryId]').val();
        if (categoryId != undefined) {
            prosesUpCategory(categoryId);
        } else {
            prosesInFaq();
        }
    }

    function prosesInFaq() {
        event.preventDefault();
        $('#submit').submit(function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url('Faq/do_upload'); ?>',
                type: "post",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                async: false,
                success: function (data) {
                    alert("Upload Image Berhasil.");
                }
            });
        });
    }

    function prosesUpCategory() {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url("CategoryController/upCategory"); ?>',
            type: 'POST',
            dataType: 'JSON',
            data: $('form:visible').serialize()
        })
            .done(function (data) {

                Swal.fire(
                    'Sukses!',
                    data.msg,
                    'success'
                );
                showdata();

            })
            .fail(function () {
                console.log("error");
            })
            .always(function () {
                console.log("complete");
            });
    }

    function edit(categoryId = '') {
        ubahText(categoryId, 'edit');
        hapus(categoryId);

        // $('#demo').removeClass("collapse");

        if (categoryId != '') {

            $.ajax({
                url: '<?= base_url("CategoryController/getCategory?categoryId="); ?>' + categoryId,
                type: 'GET',
                dataType: 'JSON'
            })
                .done(function (data) {
                    // debugger;
                    $('input[name=categoryName]').val(data.categoryName);
                    $('select[name=categoryType]').val(data.categoryType);
                    $('select[name=groupInboxTehnicalCco]').val(data.groupInboxTehnicalCco);
                    $('select[name=groupInboxTehnicalVas]').val(data.groupInboxTehnicalVas);
                    $('select[name=statusActive]').val(data.statusActive);


                    $('input[name=review]').removeAttr('readonly');
                    $('#txtAction*').removeAttribute('disabled');
                })
                .fail(function () {
                    console.log("error");
                })
                .always(function () {
                    console.log("complete");
                });

        }
    }

    function ubahText(categoryId = '', val = '') {

        var r;
        if (val == 'add') {
            r = 'Add';
            $('#txtArea').html(" ");
            $('#txtArea').text(" ");
            $('#txtAction*').text(r);
            $('#upForm').attr('categoryId', '');
            $('#cloneID').html('');
            $('.review').input("disabled", "disabled");
            $(this).attr('disabled', 'disabled');

        } else if (val == 'edit') {
            r = 'Edit';
            $('#txtAction*').text(r);
            $('#txtAction*').removeAttr('disabled');

            $('#inForm').attr('categoryId', '');

            $('#cloneID').html("<input type='hidden' name='categoryId' value='" + categoryId + "'>");
        }

        return r;
    }


    function btnCari() {
        $(document).ready(function () {
            $('#btn-cari').click(function (e) {
                e.preventDefault();

                showdata();

            });
        });
    }


    function getCategoryName() {
        $.ajax({
            type: "GET",
            url: "<?= base_url("CategoryController/getCategoryName"); ?>",
            dataType: "JSON",
            success: function (response) {
                // debugger;
                for (const x in response) {
                    $('select[name="categoryId"]').append('<option value="' + response[x].categoryId + '">' + response[x].categoryName + '</option>');
                }
            }
        });
    }

    function getTitle() {
        $.ajax({
            type: "GET",
            url: "<?= base_url("Faq/getTitle"); ?>",
            dataType: "JSON",
            success: function (response) {
                // debugger;
                for (const x in response) {
                    $('select[name="id_title"]').append('<option value="' + response[x].id_title + '">' + response[x].name_title + '</option>');
                }
            }
        });
    }


    function getSubCategoryName() {
        $.ajax({
            type: "GET",
            url: "<?= base_url("SubCategoryController/getSubCategoryName"); ?>",
            dataType: "JSON",
            success: function (response) {
                // debugger;
                for (const x in response) {
                    $('select[name="subCategoryId"]').append('<option value="' + response[x].subCategoryId + '">' + response[x].subCategory + '</option>');
                }
            }
        });
    }


    function clearInput() {
        $('select[name="categoryId"]').val('');
        $('select[name="subCategoryId"]').val('');
        $('input[name="faqFile"]').val('');
        $('input[name="faqTitle"]').val('');
        $('select[name="status"]').val('');

    }

</script>
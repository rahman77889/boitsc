<!-- The Modal Add User -->
<div class='title-module'>FAQ</div>
<div class="subtitle-module">FAQ Content &raquo; FAQ</div>

<style>
    .table tbody tr td,
    .table thead tr th {
        font-size: 11px;
    }

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
    <?php };
    ?>
    <div class="col-12">
        <div class="card" style="border-radius: 10px;">
            <div class="card-title head-module-action" style="text-align: left">
                <a href="javascript:void(0)"><i class="fa fa-search"></i> Find FAQ</a>
            </div>
            <div class="card-body body-module-action">
                <div class="card mt-2">
                    <div class="card-body">

                        <table class="table table-bordered display" id="contoh" style="width:100%" data-page-length='25'>
                            <thead>
                                <!-- <th style="width: 15px; text-align: center">
                                    #
                                </th> -->
                            <th style="width: 50px; text-align: center">
                                No
                            </th>
                            <th style="text-align: center">
                                Category
                            </th>
<!--                            <th style="text-align: center">
                                Sub Category</th>-->
                            <th style="text-align: center;">
                                FAQ Title</th>
<!--                            <th style="text-align: center">
                                FAQ File</th>-->
                            <th style="text-align: center">
                                Read More</th>
                            <!-- <th style="text-align: center">
                                Upload by User</th> -->
                            <!-- <th style="text-align: center">
                                Status
                            </th> -->

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
        getCategoryName();
        getSubCategoryName();
        showdata();
        btnCari();

        $('#klikedit').click(function () {
            //  e.preventDefault();
            $('#demo').removeClass("collapse");


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
                "url": "<?= base_url("Faq/dtshowfaq"); ?>",
                "type": "POST"
            },
            //Set column definition initialisation properties
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false
                },
                {
                    "targets": 3,
                    "className": "text-center",
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


//     function edit(categoryId) {
//     $("#edit").attr("href", "<?php echo base_url(); ?>CategoryController/editcategorymulti?categoryId=" + categoryId);
//     hapus(categoryId);
// }

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
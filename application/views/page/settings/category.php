<!-- The Modal Add User -->
<div class='title-module'>Category Setting</div>
<div class="subtitle-module">Module Settings &raquo; Category Setting</div>

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


    <div class="row flex-grow">

        <?php
        if ($this->session->flashdata("delete")) {
            ?>
            <script>
                Swal.fire(
                    'Success Delete Data!',
                    'You clicked the button!',
                    'success'
                )
            </script>
        <?php }
        ;
        ?>
        <div class="col-12">
            <div class="card" style="border-radius: 10px;">
                <div class="card-title head-module-action">
                    <div class="row">
                        <?php if ($this->session->userdata('privilege') != '9') { ?>
                            <div class="col-sm-2">
                                <a href="#" style="color: white" onclick="clearInput();" class="muncul"
                                    data-toggle="collapse" data-target="#demo"><i class="fa fa-cog"></i> Add Category</a>
                            </div>
                            <div class="col-sm-2">
                                <a href="#" style="color: white" onclick="edit()" id="klikedit"><i
                                        class="fa fa-table muncul"></i> Edit Category</a>
                            </div>
                            <div class="col-sm-3">
                                <a href="#" id="btn-delete" style="color: white"><i class="fa fa-times"></i> Remove
                                    Category</a>
                            </div>
                            <div class="col-sm-3">
                                <a href="<?= base_url("CategoryController/download"); ?>" target="_blank"
                                    style="color: white"><i class="fa fa-download"></i> Export Category</a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="card-body body-module-action">

                    <div id="demo" class="collapse">
                        <div class="card" id="test">
                            <form id="inForm" method="POST" action="javascript:void(0);" class="smart-form"
                                novalidate="novalidate">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label id="huruf" for="">*Category Name</label>
                                            <label id="huruf" for="">*Category Tehnical Flag</label>
                                            <label id="huruf" for="">*Group Inbox Tehnical CCO</label>
                                            <label id="huruf" for="">*Group Inbox Tehnical VAS</label>
                                            <label id="huruf" for="">*Status Active</label>
                                        </div>
                                        <div class="col-md-3">
                                            <span id="cloneID"></span>

                                            <input type="text" name="categoryName" id="categoryName"
                                                class="form-control jarak" placholder="Category Name" required>
                                            <select name="categoryType" id="categoryType" class="form-control jarak"
                                                required>
                                                <option value="">--</option>
                                                <option value="Techinical">Techinical</option>
                                                <option value="Information">Information</option>
                                                <option value="Registration">Registration</option>
                                                <option value="Sales">Sales</option>
                                            </select>
                                            <select name="groupInboxTehnicalCco" id="groupInboxTehnicalCco"
                                                class="form-control jarak" required>
                                                <option value="N">NO</option>
                                                <option value="Y">Yes</option>
                                            </select>

                                            <select name="groupInboxTehnicalVas" id="groupInboxTehnicalVas"
                                                class="form-control jarak" required>
                                                <option value="N">NO</option>
                                                <option value="Y">Yes</option>
                                            </select>

                                            <select name="statusActive" id="statusActive" class="form-control jarak"
                                                required>
                                                <option value="N">NO</option>
                                                <option value="Y">Yes</option>
                                            </select>

                                        </div>

                                    </div>
                                    <div class="clearfix">
                                        <button type="submit" class="btn btn-primary simpan" id="txtAction"
                                            onclick="proses();">
                                            Add
                                        </button>
                                        <button type="reset" id="cancel" class="btn btn-default"
                                            onclick="ubahText('', 'add')">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>


                    <div class="card mt-2">
                        <div class="card-body">
                            <form method="GET" action="<?php echo base_url('CategoryController/deletecategory') ?>"
                                id="form-delete">
                                <table class="table table-bordered display" id="contoh" style="width:100%">
                                    <thead>
                                        <tr>
                                            <!-- <th style="width: 15px; text-align: center"> # </th> -->
                                            <th style="width: 50px; text-align: center"> No </th>
                                            <!--<th style="text-align: center"> Category ID </th>-->
                                            <th style="text-align: center"> Category Name </th>
                                            <th style="text-align: center"> Category Type </th>
                                            <th style="text-align: center"> Group Inbox Tehnical CCO </th>
                                            <th style="text-align: center"> Group Inbox Tehnical VAS</th>
                                            <th style="text-align: center"> Status Active </th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                        </tr>

                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>



                </div>
            </div>
        </div>

    </div>

    <script>
        $(document).ready(function () {

            showdata();


            $('#cancel').click(function (e) {
                e.preventDefault();
                $('.collapse').collapse('hide');

            });

            $('.muncul').click(function () {
                $('.collapse').collapse('show');
            });
            // $('#klikedit').click(function () {
            // $('.collapse').collapse('show');
            // });
            $("#btn-delete").click(function () { // Ketika user mengklik tombol delete
                var r = confirm("Apakah anda yakin ingin menghapus data ini ?");
                if (r == true) {
                    $("#form-delete").submit(); // Submit form d 
                } else {
                    return false;
                }
            });

            // confirmation();
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
                    "url": "<?= base_url("CategoryController/showdtcategory "); ?>",
                    "type": "POST"
                },
                //Set column definition initialisation properties
                "columnDefs": [{
                    "targets": [0],
                    "orderable": false
                }]
            });
        }



        // fungsi link delete data
        function hapus(categoryId) {
            $("#btn-delete").attr("href", "<?php echo base_url(); ?>CategoryController/deletecategory?categoryId=" + categoryId);
        }





        function proses() {
            var categoryId = $('input[name=categoryId]').val();
            if (categoryId != '' && categoryId != undefined) {
                prosesUpCategory(categoryId);
            } else {
                prosesInCategory();
            }
        }

        function prosesInCategory() {
            event.preventDefault();
            $.ajax({
                url: '<?= base_url("CategoryController/inCategory"); ?>',
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


                    $('.collapse').collapse('hide');

                    clearInput();
                    showdata();
                })
                .fail(function () {
                    console.log("error");
                })
                .always(function () {
                    console.log("complete");
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
                    $('.collapse').collapse('hide');

                    // clearInput();
                    clearInputUpdate();
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

            if (categoryId == '') {
                alert("Not Selected Data");
                $('.collapse').collapse('hide');
            } else {
                $('.collapse').collapse('show');

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

                            // $('input[name=review]').removeAttr('readonly');
                            // $('#txtAction*').removeAttribute('disabled');
                        })
                        .fail(function () {
                            console.log("error");
                        })
                        .always(function () {
                            console.log("complete");
                        });

                }
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

        function clearInput() {
            var r;
            $('input[name=categoryId]').val('');
            $('input[name="categoryName"]').val('');
            $('select[name="categoryType"]').val('');
            $('select[name="groupInboxTehnicalCco"]').val('');
            $('select[name="groupInboxTehnicalVas"]').val('');
            $('select[name="statusActive"]').val('');
            // $('#cloneID*').hide();
            // $('#txtAction*').text('add');

            //$('#cloneID').remove("<input type='hidden' name='categoryId' value='" + categoryId + "'>");            


        }

        function clearInputUpdate() {
            var r;
            $('input[name=categoryId]').val('');
            $('input[name="categoryName"]').val('');
            $('select[name="categoryType"]').val('');
            $('select[name="groupInboxTehnicalCco"]').val('');
            $('select[name="groupInboxTehnicalVas"]').val('');
            $('select[name="statusActive"]').val('');
            $('#cloneID*').hide();
            $('#txtAction*').text('add');
            //$('#cloneID').remove("<input type='hidden' name='categoryId' value='" + categoryId + "'>");            


        }
    </script>
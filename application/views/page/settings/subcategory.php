<!-- The Modal Add User -->
<div class='title-module'>Sub Category Setting</div>
<div class="subtitle-module">Module Settings &raquo; Sub Category Setting</div>

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
    <div class="col-12">
        <div class="card" style="border-radius: 10px;">
            <div class="card-title head-module-action">
                <div class="row">
                    <?php if ($this->session->userdata('privilege') != '9') { ?>
                        <div class="col-sm-2">
                            <a href="#" class="muncul" onclick="clearInput();" data-toggle="collapse" data-target="#demo"
                                style="color: white"><i class="fa fa-cog"></i> Add Data</a>
                        </div>
                        <div class="col-sm-2">
                            <a href="#" id="klikedit" style="color: white"><i class="fa fa-table"></i> Edit Data</a>
                        </div>
                        <div class="col-sm-2">
                            <a href="#" style="color: white" id="btn-delete"><i class="fa fa-times"></i> Remove Data</a>
                        </div>
                        <div class="col-sm-3">
                            <a href="<?= base_url("SubCategoryController/download"); ?>" target="_blank"
                                style="color: white"><i class="fa fa-download"></i> Export Sub Category</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body body-module-action">
                <div id="demo" class="collapse">
                    <div class="card">
                        <form id="inForm" method="POST" action="javascript:void(0);" class="smart-form"
                            novalidate="novalidate">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label id="huruf" for="">*Category Id</label><br>
                                        <label id="huruf" for="">*Sub Category Name</label><br>
                                        <label id="huruf" for="">*Sub Technical Flag</label><br>
                                        <!--                                        <label id="huruf" for="">*Escalation</label> <br>-->
                                        <label id="huruf" for="">*Normal SLA</label><br>
                                        <label id="huruf" for="">*Visit SLA</label><br>
                                        <label id="huruf" for="">*Status Active</label>
                                    </div>
                                    <div class="col-md-3">
                                        <span id="cloneID"></span>

                                        <select name="categoryId" id="categoryId" class="form-control jarak" required>
                                            <option value="">--</option>

                                        </select>

                                        <input type="text" name="subCategory" id="subCategory"
                                            class="form-control jarak" placholder="Category Name" required>

                                        <select name="sub_category_type" id="sub_category_type"
                                            class="form-control jarak" required>
                                            <option value="">--</option>
                                            <option value="Techinical">Techinical</option>
                                            <option value="Information">Information</option>
                                            <option value="Registration">Registration</option>
                                            <option value="Sales">Sales</option>
                                        </select>

                                        <!--                                        <select name="escalation" id="escalation" class="form-control jarak" required>
                                            <option value="N">NO</option>
                                            <option value="Y">Yes</option>
                                        </select>-->

                                        <input type="text" name="sla" id="sla" class="form-control jarak"
                                            placholder="Category Name" required>

                                        <input type="text" name="if_sla" id="if_sla" class="form-control jarak"
                                            placholder="Category Name" required>

                                        <select name="statusActive" id="statusActive" class="form-control jarak"
                                            required>
                                            <option value="N">Not Active</option>
                                            <option value="Y">Active</option>
                                        </select>

                                    </div>

                                </div>
                                <div class="clearfix">
                                    <button type="submit" class="btn btn-primary" id="txtAction" onclick="proses()">
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
                        <!-- <form method="GET" action="<?php echo base_url('SubCategoryController/deletesubcategory') ?>" id="form-delete"> -->
                        <table class="table table-bordered display" id="contoh" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 15px; text-align: center"> # </th>
                                    <th style="width: 50px; text-align: center"> No </th>
                                    <th style="text-align: center">Sub Category Name</th>
                                    <th style="text-align: center">SLA ( m" ) </th>
                                    <th style="text-align: center">If Needed Visit ? Y : SLA ( m" ) </th>
                                    <th style="text-align: center">Sub Category Type </th>
                                    <!--<th style="text-align: center">Escalation </th>-->
                                    <th style="text-align: center">Category </th>
                                    <th style="text-align: center">Status Active </th>


                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                </tr>

                            </tbody>
                        </table>
                        <!-- </form> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        showdata();
        getCategoryName();
        $('.muncul').click(function () {
            $('.collapse').collapse('show');
        });
        $('#klikedit').click(function () {
            $('.collapse').collapse('show');
        });

        $('#cancel').click(function (e) {
            e.preventDefault();
            $('.collapse').collapse('hide');

        });

        $("#btn-delete").click(function () { // Ketika user mengklik tombol delete
            var r = confirm("Apakah anda yakin ingin menghapus data ini ?");
            if (r == true) {
                //  $("#form-delete").submit(); // Submit form d 
                hapus(subCategoryId);
            } else {
                return false;
            }
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
                "url": "<?= base_url("SubCategoryController/showdtsubcategory"); ?>",
                "type": "POST"
            },
            //Set column definition initialisation properties
            "columnDefs": [{
                "targets": [0],
                "orderable": false
            }]
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

    //  function edit(subCategoryId) {
    //     $("#edit").attr("href", "<?php echo base_url(); ?>SubCategoryController/editcategorymulti?subCategoryId=" + subCategoryId);
    //     hapus(subCategoryId);
    //  }

    // fungsi link delete data
    function hapus(subCategoryId) {
        $("#btn-delete").attr("href", "<?php echo base_url(); ?>SubCategoryController/deletesubcategory?subCategoryId=" + subCategoryId);
    }

    //fungsi add dan edit

    function proses() {
        var subCategoryId = $('input[name=subCategoryId]').val();
        if (subCategoryId != '' && subCategoryId != undefined) {
            prosesUpSubCategory(subCategoryId);
        } else {
            prosesInSubCategory();
        }
    }

    function prosesInSubCategory() {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url("SubCategoryController/inSubCategory"); ?>',
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

    function prosesUpSubCategory() {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url("SubCategoryController/upSubCategory"); ?>',
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

    function edit(subCategoryId = '') {
        ubahText(subCategoryId, 'edit');
        hapus(subCategoryId);

        // $('#demo').removeClass("collapse");

        if (subCategoryId != '') {

            $.ajax({
                url: '<?= base_url("SubCategoryController/getSubCategory?subCategoryId="); ?>' + subCategoryId,
                type: 'GET',
                dataType: 'JSON'
            })
                .done(function (data) {


                    // debugger;
                    $('select[name=categoryId]').val(data.categoryId);
                    $('input[name=subCategory]').val(data.subCategory);
                    $('input[name=sla]').val(data.sla);
                    $('input[name=if_sla]').val(data.if_sla);
                    $('select[name=sub_category_type]').val(data.sub_category_type);
                    //                        $('select[name=escalation]').val(data.escalation);
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

    function ubahText(subCategoryId = '', val = '') {

        var r;
        if (val == 'add') {
            r = 'Add';
            $('#txtArea').html(" ");
            $('#txtArea').text(" ");
            $('#txtAction*').text(r);
            $('#upForm').attr('subCategoryId', '');
            $('#cloneID').html('');
            $('.review').input("disabled", "disabled");
            $(this).attr('disabled', 'disabled');

        } else if (val == 'edit') {
            r = 'Edit';
            $('#txtAction*').text(r);
            $('#txtAction*').removeAttr('disabled');

            $('#inForm').attr('subCategoryId', '');

            $('#cloneID').html("<input type='hidden' name='subCategoryId' value='" + subCategoryId + "'>");
        }

        return r;
    }


    function clearInput() {
        $('select[name="categoryId"]').val('');
        $('input[name="subCategoryId"]').val('');
        $('input[name="subCategory"]').val('');
        $('select[name="sub_category_type"]').val('');
        //        $('select[name="escalation"]').val('');
        $('input[name="sla"]').val('');
        $('input[name="if_sla"]').val('');
        $('select[name="statusActive"]').val('');
        //  $('#cloneID*').hide();
        //  $('#txtAction*').text('add');
    }

    function clearInputUpdate() {
        $('select[name="categoryId"]').val('');
        $('input[name="subCategoryId"]').val('');
        $('input[name="subCategory"]').val('');
        $('select[name="sub_category_type"]').val('');
        //        $('select[name="escalation"]').val('');
        $('input[name="sla"]').val('');
        $('input[name="if_sla"]').val('');
        $('select[name="statusActive"]').val('');
        $('#cloneID*').hide();
        $('#txtAction*').text('add');
    }
</script>
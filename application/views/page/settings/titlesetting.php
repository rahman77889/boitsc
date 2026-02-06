<!-- The Modal Add User -->
<div class='title-module'>FAQ Name Setting </div>
<div class="subtitle-module">Module Settings &raquo; FAQ Name Setting</div>

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
                            <a href="#" id="klikedit" style="color: white"><i class="fa fa-table"></i>Edit Data</a>
                        </div>
                        <div class="col-sm-2">
                            <a href="#" style="color: white" id="btn-delete"><i class="fa fa-times"></i>Remove Data</a>
                        </div>
                    <?php } ?>
                    <!-- <div class="col-sm-2"><a href="#" style="color: white"> #Sub Category Name :</a></div> -->
                    <!-- <input class="form-control" type="text" placeholder="Search" aria-label="Search" style="width: 150px; height: 21px; position: relative; right: 27px;"> -->
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
                                        <label id="huruf" for="">*Name Title</label>

                                    </div>
                                    <div class="col-md-3">
                                        <span id="cloneID"></span>



                                        <input type="text" name="name_title" id="name_title" class="form-control jarak"
                                            placeholder="Name Title" required>


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
                                    <th style="text-align: center">Title Name</th>



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
        //   hapus(id_title);
        // getCategoryName();

        // alert("hello world");
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
                hapus(id_title);
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
                "url": "<?= base_url("TitleController/showdttitle"); ?>",
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
    }


    // function getCategoryName() {
    //     $.ajax({
    //         type: "GET",
    //         url: "<?= base_url("CategoryController/getCategoryName"); ?>",
    //         dataType: "JSON",
    //         success: function (response) {
    //             // debugger;
    //             for (const x in response) {
    //                 $('select[name="categoryId"]').append('<option value="' + response[x].categoryId + '">' + response[x].categoryName + '</option>');
    //             }
    //         }
    //     });
    // }

    //  function edit(id_title) {
    //     $("#edit").attr("href", "<?php echo base_url(); ?>SubCategoryController/editcategorymulti?id_title=" + id_title);
    //     hapus(id_title);
    //  }

    // fungsi link delete data
    function hapus(id_title) {
        $("#btn-delete").attr("href", "<?php echo base_url(); ?>TitleController/deletetitle?id_title=" + id_title);
    }

    //fungsi add dan edit

    function proses() {
        var id_title = $('input[name=id_title]').val();
        if (id_title != '' && id_title != undefined) {
            prosesUpTitle(id_title);
        } else {
            prosesInTitle();
        }
    }

    function prosesInTitle() {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url("TitleController/inTitle"); ?>',
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

    function prosesUpTitle() {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url("TitleController/upTitle"); ?>',
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

    function edit(id_title = '') {
        ubahText(id_title, 'edit');
        hapus(id_title);

        // $('#demo').removeClass("collapse");

        if (id_title != '') {

            $.ajax({
                url: '<?= base_url("TitleController/getTitle?id_title="); ?>' + id_title,
                type: 'GET',
                dataType: 'JSON'
            })
                .done(function (data) {


                    // debugger;
                    // $('select[name=categoryId]').val(data.categoryId);
                    $('input[name=name_title]').val(data.name_title);


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

    function ubahText(id_title = '', val = '') {

        var r;
        if (val == 'add') {
            r = 'Add';
            $('#txtArea').html(" ");
            $('#txtArea').text(" ");
            $('#txtAction*').text(r);
            $('#upForm').attr('id_title', '');
            $('#cloneID').html('');
            $('.review').input("disabled", "disabled");
            $(this).attr('disabled', 'disabled');

        } else if (val == 'edit') {
            r = 'Edit';
            $('#txtAction*').text(r);
            $('#txtAction*').removeAttr('disabled');

            $('#inForm').attr('id_title', '');

            $('#cloneID').html("<input type='hidden' name='id_title' value='" + id_title + "'>");
        }

        return r;
    }


    function clearInput() {
        // $('select[name="categoryId"]').val('');
        $('input[name="name_title"]').val('');
        // $('input[name="subCategory"]').val('');
        // $('select[name="sub_category_type"]').val('');
        // $('select[name="escalation"]').val('');
        // $('input[name="sla"]').val('');
        // $('input[name="if_sla"]').val('');
        // $('select[name="statusActive"]').val('');
        //  $('#cloneID*').hide();
        //  $('#txtAction*').text('add');
    }

    function clearInputUpdate() {
        // $('select[name="categoryId"]').val('');
        $('input[name="name_title"]').val('');
        // $('input[name="subCategory"]').val('');
        // $('select[name="sub_category_type"]').val('');
        // $('select[name="escalation"]').val('');
        // $('input[name="sla"]').val('');
        // $('input[name="if_sla"]').val('');
        // $('select[name="statusActive"]').val('');
        $('#cloneID*').hide();
        $('#txtAction*').text('add');
    }
</script>
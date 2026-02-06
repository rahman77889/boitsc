<!-- The Modal Add User -->
<div class='title-module'>Counter Setting </div>
<div class="subtitle-module">Manage System &raquo; Counter Place Setting </div>



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
            Swal.fire(
                'Success Delete Unit!',
                'You clicked the button!',
                'success'
            )
        </script>
    <?php }
    ;
    ?>
    <div class="col-12">
        <div class="card" style="border-radius: 10px">
            <div class="card-title head-module-action">
                <div class="row">
                    <?php if ($this->session->userdata('privilege') != '9') { ?>
                        <div class="col-sm-2">
                            <a href="#" style="color: rgb(255, 255, 255)" class="muncul" data-toggle="collapse"
                                onclick="clearInput();" data-target="#demo"><i class="fa fa-cog"></i>
                                Add Counter</a>
                        </div>
                        <?php if ($this->session->userdata('privilege') != '5') { ?>
                            <div class="col-sm-2">
                                <a href="#" id="klikedit" style="color: rgb(255, 255, 255)"><i class="fa fa-table"></i> Edit
                                    Counter</a>
                            </div>
                            <div class="col-sm-3">
                                <a href="#" id="btn-delete" style="color: rgb(255, 255, 255)"><i class="fa fa-times"></i> Remove
                                    Counter</a>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>


            <div class="card-body body-module-action">
                <div id="demo" class="collapse">
                    <div class="card">
                        <form id="inForm" method="POST" action="javascript:void(0);" class="smart-form"
                            enctype="multipart/form-data" novalidate="novalidate">
                            <div class="card-body">
                                <span id="cloneID"></span>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label id="huruf" for="">*Counter Name</label><br>
                                        <label id="huruf" for="">*Counter Video Display</label><br>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="judul" id="name" class="form-control jarak"
                                            placholder="Counter Title">
                                        <input type="file" name="video" class="jarak">
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
                        <!-- <form method="GET" action="<?php echo base_url('Unit/deleteunit') ?>" id="form-delete"> -->

                        <table class="table table-bordered display" id="contoh" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 15px; text-align: center"> # </th>
                                    <th style="width: 50px; text-align: center"> No </th>
                                    <th style="text-align: left"> Counter Number </th>
                                    <th style="text-align: left"> Counter Queuing Number </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="checkbox" name="id[]" id="id"></td>
                                    <td> 7 </td>
                                    <td> 2 </td>
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
                hapus(id);
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
                "url": "<?= base_url("Unit/showdtqueuePlace"); ?>",
                "type": "POST"
            },
            //Set column definition initialisation properties
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false
                },
                { "className": "dt-center", "targets": "_all" }
            ]
        });
    }


    //     function edit(id) {
    //     $("#edit").attr("href", "<?php echo base_url(); ?>Unit/editunitmulti?id=" + id);
    //     hapus(id);
    // }

    // fungsi link delete data
    function hapus(id) {
        $("#btn-delete").attr("href", "<?php echo base_url(); ?>Unit/deleteCounterPlace/?id=" + id);
    }




    //fungsi add dan edit

    function proses() {
        var id = $('input[name=id]').val();
        if (id != '' && id != undefined) {
            prosesUpUnitInformation(id);
        } else {
            inUnitInformation();
        }
    }

    function inUnitInformation() {
        event.preventDefault();

        var formData = new FormData();
        formData.append('video', $('[name="video"]')[0].files[0]);
        formData.append('judul', $('[name="judul"]').val());

        $.ajax({
            url: '<?= base_url("Unit/inCounterPlace"); ?>',
            type: 'POST',
            data: formData,
            processData: false, // tell jQuery not to process the data
            contentType: false,
            //            dataType: 'JSON',
            //            data: $('form:visible').serialize()
        }).done(function (data) {

            Swal.fire(
                'Sukses!',
                data.msg,
                'success'
            );
            $('.collapse').collapse('hide');
            clearInput();
            showdata();
        }).fail(function () {
            console.log("error");
        }).always(function () {
            console.log("complete");
        });
    }

    function prosesUpUnitInformation() {
        event.preventDefault();

        var formData = new FormData();
        formData.append('video', $('[name="video"]')[0].files[0]);
        formData.append('judul', $('[name="judul"]').val());
        formData.append('id', $('[name="id"]').val());

        $.ajax({
            url: '<?= base_url("Unit/upCounterPlace"); ?>',
            type: 'POST',
            data: formData,
            processData: false, // tell jQuery not to process the data
            contentType: false,
            //            dataType: 'JSON',
            //            data: $('form:visible').serialize()
        }).done(function (data) {

            Swal.fire(
                'Sukses!',
                data.msg,
                'success'
            );
            $('.collapse').collapse('hide');
            clearInputUpdate();
            showdata();

        }).fail(function () {
            console.log("error");
        }).always(function () {
            console.log("complete");
        });
    }
    function clearInput() {
        $('input[name="id"]').val('');
        $('input[name="counter"]').val('');
        $('input[name="nomor"]').val('');
    }

    function clearInputUpdate() {
        $('input[name="counter"]').val('');
        $('input[name="nomor"]').val('');
        $('#cloneID*').hide();
        $('#txtAction*').text('add');
    }

    function edit(id = '') {
        ubahText(id, 'edit');
        hapus(id);

        if (id != '') {

            $.ajax({
                url: '<?= base_url("Unit/getCounterPlace?id="); ?>' + id,
                type: 'GET',
                dataType: 'JSON'
            }).done(function (data) {
                $('input[name=judul]').val(data.judul);
                $('input[name=id]').val(data.id);
            }).fail(function () {
                console.log("error");
            }).always(function () {
                console.log("complete");
            });

        }
    }

    function ubahText(id = '', val = '') {

        var r;
        if (val == 'add') {
            r = 'Add';
            $('#txtArea').html(" ");
            $('#txtArea').text(" ");
            $('#txtAction*').text(r);
            $('#upForm').attr('id', '');
            $('#cloneID').html('');
            $('.review').input("disabled", "disabled");
            $(this).attr('disabled', 'disabled');

        } else if (val == 'edit') {
            r = 'Edit';
            $('#txtAction*').text(r);
            $('#txtAction*').removeAttr('disabled');

            $('#inForm').attr('id', '');

            $('#cloneID').html("<input type='hidden' name='id' value='" + id + "'>");
        }

        return r;
    }
</script>
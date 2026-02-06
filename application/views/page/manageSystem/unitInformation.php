<!-- The Modal Add User -->
<div class='title-module'>Unit / Vendor Information</div>
<div class="subtitle-module">Manage System &raquo; Unit / Vendor Information</div>



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
                                onclick="clearInput();" data-target="#demo"><i class="fa fa-cog"></i> Add Unit</a>
                        </div>
                        <?php if ($this->session->userdata('privilege') != '5') { ?>
                            <div class="col-sm-2">
                                <a href="#" id="klikedit" style="color: rgb(255, 255, 255)"><i class="fa fa-table"></i> Edit
                                    Unit</a>
                            </div>
                            <div class="col-sm-2">
                                <a href="#" id="btn-delete" style="color: rgb(255, 255, 255)"><i class="fa fa-times"></i> Remove
                                    Unit</a>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>


            <div class="card-body body-module-action">
                <div id="demo" class="collapse">
                    <div class="card">
                        <form id="inForm" method="POST" action="javascript:void(0);" class="smart-form"
                            novalidate="novalidate">
                            <div class="card-body">
                                <span id="cloneID"></span>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label id="huruf" for="">*Unit Name</label><br>
                                        <label id="huruf" for="">*Unit PIC</label><br>
                                        <label id="huruf" for="">* Unit Email Address</label><br>
                                        <label id="huruf" for="">* Unit Phone No</label><br>
                                        <label id="huruf" for="">*status Active</label>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="unit" id="unit" class="form-control jarak">
                                            <!-- <option value="">--</option> -->
                                        </select>
                                        <input type="text" name="name" id="name" class="form-control jarak"
                                            placholder="Category Name">
                                        <input type="text" name="email" id="email" class="form-control jarak"
                                            placholder="Category Name">
                                        <input type="text" name="phone" id="phone" class="form-control jarak"
                                            placholder="Category Name">


                                        <select name="status" id="status" class="form-control jarak">
                                            <option value="">--</option>
                                            <option value="0">NO</option>
                                            <option value="1">Yes</option>
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
                        <!-- <form method="GET" action="<?php echo base_url('Unit/deleteunit') ?>" id="form-delete"> -->

                        <table class="table table-bordered display" id="contoh" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 15px; text-align: center"> # </th>
                                    <th style="width: 50px; text-align: center"> No </th>
                                    <th style="text-align: center"> Name </th>
                                    <th style="text-align: center"> Email </th>
                                    <th style="text-align: center"> Phohe </th>
                                    <th style="text-align: center"> Unit - Vendor </th>
                                    <th style="text-align: center"> Status </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="checkbox" name="id[]" id="id"></td>
                                    <td> 7 </td>
                                    <td>Aliyudin</td>
                                    <td> Aliyudin Bim </td>
                                    <td> System Adminsitrator </td>
                                    <td> System Adminsitrator </td>
                                    <td> System Adminsitrator </td>
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
        getUnit();
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
                "url": "<?= base_url("Unit/showdtunit "); ?>",
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


    //     function edit(id) {
    //     $("#edit").attr("href", "<?php echo base_url(); ?>Unit/editunitmulti?id=" + id);
    //     hapus(id);
    // }

    // fungsi link delete data
    function hapus(id) {
        $("#btn-delete").attr("href", "<?php echo base_url(); ?>Unit/deleteunit?id=" + id);
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
        $.ajax({
            url: '<?= base_url("Unit/inUnitInformation"); ?>',
            type: 'POST',
            dataType: 'JSON',
            data: $('form:visible').serialize()
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
        $.ajax({
            url: '<?= base_url("Unit/upUnitInformation"); ?>',
            type: 'POST',
            dataType: 'JSON',
            data: $('form:visible').serialize()
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
        $('input[name="name"]').val('');
        $('input[name="email"]').val('');
        $('input[name="phone"]').val('');
        $('select[name="status"]').val('');
        $('select[name="unit"]').val('');
        // $('#cloneID*').hide();
        //     $('#txtAction*').text('add');
    }

    function clearInputUpdate() {
        $('input[name="name"]').val('');
        $('input[name="email"]').val('');
        $('input[name="phone"]').val('');
        $('select[name="status"]').val('');
        $('select[name="unit"]').val('');
        $('#cloneID*').hide();
        $('#txtAction*').text('add');
    }

    function edit(id = '') {
        ubahText(id, 'edit');
        hapus(id);

        if (id != '') {

            $.ajax({
                url: '<?= base_url("Unit/getUnitInformation?id="); ?>' + id,
                type: 'GET',
                dataType: 'JSON'
            }).done(function (data) {
                $('input[name=name]').val(data.name);
                $('input[name=email]').val(data.email);
                $('input[name=phone]').val(data.phone);
                $('select[name=unit]').val(data.unit_id);
                $('select[name=status]').val(data.status);
                // $('input[name=review]').removeAttr('readonly');
                // $('#txtAction*').removeAttribute('disabled');
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


    function getUnit() {
        $.ajax({
            type: "GET",
            url: "<?= base_url("Unit/getUnit"); ?>",
            dataType: "JSON",
            success: function (response) {
                // debugger;
                for (const x in response) {
                    $('select[name="unit"]').append('<option value="' + response[x].id + '">' + response[x].unitName + '</option>');
                }
            }
        });
    }
</script>
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
<!-- The Modal Add User -->
<div class='title-module'>Manage System</div>
<div class="subtitle-module">Manage System &raquo; Account Information</div>

<div class="modal" id="modalAddUser">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Add User</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form action="javascript.location(0);" id="formAddUser">
                <input type="hidden" name="id">
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p>foto</p>
                            <div class="input-group input-group-sm mb-3">
                                <input name="photo" type="file" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>Extent Number</p>
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" class="form-control" id="extend_number" name="extend_number"
                                    placeholder="extend number" aria-label="Sizing example input"
                                    aria-describedby="inputGroup-sizing-sm">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>Username *</p>
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" class="form-control" required name="userid" placeholder="User ID"
                                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>Password</p>
                            <div class="input-group input-group-sm mb-3">
                                <input type="password" class="form-control" name="password"
                                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>Retype Password</p>
                            <div class="input-group input-group-sm mb-3">
                                <input type="password" class="form-control" name="password2"
                                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>Name</p>
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" class="form-control" name="username" placeholder="UserName"
                                    aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>User Manager *</p>
                            <div class="input-group input-group-sm mb-3">
                                <select name="usermanager" required class="form-control form-control-sm">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>User Spv *</p>
                            <div class="input-group input-group-sm mb-3">
                                <select name="userspv" required class="form-control form-control-sm">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>Previleges *</p>
                            <div class="input-group input-group-sm mb-3">
                                <select name="previleges" required class="form-control form-control-sm">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>Unit/Vendor</p>
                            <div class="input-group input-group-sm mb-3">
                                <select name="unit" class="form-control form-control-sm">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>Location *</p>
                            <div class="input-group input-group-sm mb-3">
                                <select name="location" required class="form-control form-control-sm" required>
                                    <option></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>Agent Type *</p>
                            <div class="input-group input-group-sm mb-3">
                                <select name="tipe" required class="form-control form-control-sm" required>
                                    <option value="123">123</option>
                                    <option value="147">147</option>
                                    <option value="888">888</option>
                                    <option value="147123888">147, 123 and 888</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>Counter Place (with counter only)</p>
                            <div class="input-group input-group-sm mb-3">
                                <select name="id_counter_setting" onchange="getCounterList(this.value)"
                                    class="form-control form-control-sm">
                                    <option value="">No Counter Place</option>
                                    <?php
                                    $listCounterSetting = $this->db->select('*')->from('queue_setting')->get()->result();

                                    foreach ($listCounterSetting as $lc) {
                                        echo '<option value="' . $lc->id . '">' . $lc->judul . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p>Counter Type (with counter only)</p>
                            <div class="input-group input-group-sm mb-3">
                                <select name="id_counter" class="form-control form-control-sm">
                                    <option value="">No Counter</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" onclick="save()" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
    <?php
    if ($this->session->flashdata("disable")) {
        ?>
        <script>
            Swal.fire('Success Disable User!', 'You clicked the button!', 'success')
        </script>
    <?php }
    ;
    ?>
    <?php
    if ($this->session->flashdata("enable")) {
        ?>
        <script>
            Swal.fire('Success Enable User!', 'You clicked the button!', 'success')
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
                            <a href="#" style="color: white" data-toggle="modal" data-target="#modalAddUser"><i
                                    class="fa fa-plus-circle"></i> Add User</a>
                        </div>
                        <?php if ($this->session->userdata('privilege') != '5') { ?>
                            <div class="col-sm-2">
                                <a href="javascript:void(0)" onclick="editUser()" style="color: white"><i
                                        class="fa fa-table"></i> Edit Data</a>
                            </div>
                            <div class="col-sm-2">
                                <a href="javascript:void(0)" id="btn-delete" style="color: white"><i class="fa fa-times"></i>
                                    Remove User</a>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <!-- collpase -->

            <!-- akhir collpase -->

            <div class="card-body body-module-action">

                <!-- awal collaspse -->
                <div id="wrapForm" class="collapse">
                    <div class="card">
                        <form id="inForm" method="POST" action="javascript:void(0);" class="smart-form"
                            novalidate="novalidate">
                            <input type="hidden" name="id">
                            <div class="card-body">


                                <div class="form-group">
                                    <span id="cloneID"></span>
                                    <label id="huruf" for="">*Extend Number</label>
                                    <input type="text" class="form-control jarak" id="extend_number"
                                        name="extend_number" placeholder="extent number"
                                        aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>
                                <div class="form-group">
                                    <label id="huruf" for="">*Username</label>
                                    <input type="text" class="form-control jarak" id="userid" name="userid"
                                        placeholder="User ID" aria-label="Sizing example input"
                                        aria-describedby="inputGroup-sizing-sm">
                                </div>
                                <div class="form-group">
                                    <label id="huruf" for="">*Password</label>
                                    <input type="password" class="form-control jarak" name="password"
                                        aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>
                                <div class="form-group">
                                    <label id="huruf" for="">*Name</label>
                                    <input type="text" class="form-control jarak" name="username" placeholder="UserName"
                                        aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                                </div>
                                <div class="form-group">
                                    <label id="huruf" for="">*User Manager</label>
                                    <select name="usermanager" class="form-control jarak">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label id="huruf" for="">*User Spv</label>
                                    <select name="userspv" class="form-control jarak">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label id="huruf" for="">*Previleges</label>
                                    <select name="previleges" class="form-control jarak">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label id="huruf" for="">*Unit/Vendor</label>
                                    <select name="unit" class="form-control jarak">
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label id="huruf" for="">Location</label>
                                    <select name="location" class="form-control jarak" required>
                                        <option></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label id="huruf" for="">Agent Type</label>
                                    <select name="tipe" required class="form-control jarak" required>
                                        <option value="123">123</option>
                                        <option value="147">147</option>
                                        <option value="888">888</option>
                                        <option value="147123888">147, 123 and 888</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label id="huruf" for="">Counter Plaza (with counter only)</label>
                                    <select name="id_counter_setting" onchange="getCounterList(this.value)"
                                        class="form-control jarak">
                                        <option value="">No Counter Place</option>
                                        <?php
                                        foreach ($listCounterSetting as $lc) {
                                            echo '<option value="' . $lc->id . '">' . $lc->judul . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label id="huruf" for="">Counter Type (with counter only)</label>

                                    <select name="id_counter" class="form-control jarak">
                                        <option value="">No Counter</option>
                                    </select>
                                </div>

                                <div class="clearfix">
                                    <button type="submit" class="btn btn-primary" id="txtAction" onclick="proses()">
                                        Add
                                    </button>
                                    <button type="reset" id="cancel" class="btn btn-default"
                                        onclick="$('#wrapForm').slideToggle()">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

                <!-- akhir collpase -->
                <div class="card mt-2">
                    <div class="card-body">
                        <table id="tabel" class="table table-bordered display" style="width:100%;">
                            <thead>
                                <th style="width: 15px; text-align: center"> # </th>
                                <th style="width: 50px; text-align: center"> No </th>
                                <th style="text-align: center"> Users ID </th>
                                <th style="text-align: center"> Name</th>
                                <th style="width:10px;text-align: center;"> Extend Number</th>
                                <th style="width:10px;text-align: center;"> Previleges</th>
                                <th style="text-align: center"> Supervisor</th>
                                <th style="text-align: center"> Manager</th>
                                <th style="text-align: center"> Location</th>
                                <th style="text-align: center"> Unit</th>
                                <th style="text-align: center"> User Status - IP Location</th>
                                <th style="text-align: center"> Status</th>
                                <th style="text-align: center"> Action</th>
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

<style>
    <?php
    if ($this->session->userdata('mobile') == 'Y') {
        ?>
        .pagination {
            justify-content: center !important;
        }

        label {
            margin-bottom: 0px !important;
        }

        .form-group {
            margin-bottom: 0.5rem !important;
        }

    <?php } ?>
</style>

<script>
    $(document).ready(function () {
        showTable();
        //        getUserManager();
        getUserSupervisor();
        getPrivilege();
        getUnit();
        getLocation();
    });

    function editUser() {
        var id_user = $('.userCheck:checked').val();
        if (!id_user) {
            alert('Please select user first');
        } else {
            $('#wrapForm').slideToggle();

            edit(id_user);
        }
    }

    function showTable() {
        $('#tabel').DataTable({
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
                "url": "<?= base_url("ManageSystem/dtUser"); ?>",
                "type": "POST"
            },
            //Set column definition initialisation properties
            "columnDefs": [{
                "targets": [0, 10],
                "orderable": false
            }],
            "drawCallback": function (settings) {
                <?php
                if ($this->session->userdata('mobile') == 'Y') {
                    ?>
                    $('#tabel_previous a').text('<');
                    $('#tabel_next a').text('>');
                    <?php
                }
                ?>
            }
        });
    }
    <?php
    if ($this->session->userdata('mobile') == 'Y') {
        ?>

        $('.logoutLink').click(function () {
            if (confirm('Are you sure want to logout?')) {
                window.location = '<?= site_url('Login/logout'); ?>';
            }
        });

        <?php
    }
    ?>

    //    function getUserManager() {
    //        $.ajax({
    //            type: "GET",
    //            url: "<?= base_url("ManageSystem/getUserSupervisor"); //getUserManager                                                                                                                                                                                 
    ?>",
    //            dataType: "JSON",
    //            success: function (response) {
    //                for (const x in response) {
    //                    $('select[name="usermanager"]').append('<option value="' + response[x].id + '">' + response[x].username + '</option>');
    //                }
    //            }
    //        });
    //    }

    function getUserSupervisor() {
        $.ajax({
            type: "GET",
            url: "<?= base_url("ManageSystem/getUserSupervisor"); ?>",
            dataType: "JSON",
            success: function (response) {
                for (const x in response) {
                    $('select[name="usermanager"]').append('<option value="' + response[x].id + '">' + response[x].username + '</option>');
                    $('select[name="userspv"]').append('<option value="' + response[x].id + '">' + response[x].username + '</option>');
                }
            }
        });
    }

    function getPrivilege() {
        $.ajax({
            type: "GET",
            url: "<?= base_url("Privileges/getPrivilege"); ?>",
            dataType: "JSON",
            success: function (response) {
                for (const x in response) {
                    //                    if (response[x].id > 0) {
                    $('select[name="previleges"]').append('<option value="' + response[x].id + '">' + response[x].privilegeName + '</option>');
                    //                    }
                }
            }
        });
    }

    function getUnit() {
        $.ajax({
            type: "GET",
            url: "<?= base_url("Unit/getUnit"); ?>",
            dataType: "JSON",
            success: function (response) {
                for (const x in response) {
                    $('select[name="unit"]').append('<option value="' + response[x].id + '">' + response[x].unitName + '</option>');
                }
            }
        });
    }

    function getLocation() {
        $.ajax({
            type: "GET",
            url: "<?= base_url("Location/getLocation"); ?>",
            dataType: "JSON",
            success: function (response) {
                for (const x in response) {
                    $('select[name="location"]').append('<option value="' + response[x].id + '">' + response[x].locationName + '</option>');
                }
            }
        });
    }

    function save() {
        $('#btnSave').text('saving...'); //change button text
        $('#btnSave').attr('disabled', true); //set button disable 

        var formData = new FormData($('#formAddUser')[0]);
        $.ajax({
            url: "<?php echo base_url("ManageSystem/addUser") ?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function (data) {

                Swal.fire('Success Add Data!', 'You clicked the button!', 'success')
                $('#modalAddUser').modal('hide');

                if (data.status == 1) {
                    showTable();
                    $('#modalAddUser').modal('hide');
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnSave').text('save'); //change button text
                $('#btnSave').attr('disabled', false); //set button enable 

            }
        });
    }


    function hapus(id) {
        $("#btn-delete").attr("href", "<?php echo base_url(); ?>ManageSystem/deleteAccout?id=" + id);
        disable(id);
        enable(id);
    }

    function prosesUpUser() {
        event.preventDefault();
        $.ajax({
            url: '<?= base_url("ManageSystem/upSubUser"); ?>',
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

            $('#wrapForm').slideToggle();

            showTable();

        }).fail(function () {
            console.log("error");
        }).always(function () {
            console.log("complete");
        });
    }

    function edit(id = '') {
        ubahText(id, 'edit');
        hapus(id);

        $('[name="id"]').val(id);

        $('.userCheck').each(function () {
            if ($(this).val() != id) {
                $(this).prop('checked', false);
            }
        });

        $('#wrapForm').removeClass("collapse");

        if (id != '') {
            $.ajax({
                url: '<?= base_url("ManageSystem/getSubUser?id="); ?>' + id,
                type: 'GET',
                dataType: 'JSON'
            }).done(function (data) {
                // debugger;
                $('input[name="extend_number"]').val(data.extend_number);
                $('input[name="userid"]').val(data.username);
                //                $('input[name="password"]').val(data.password);
                $('input[name="username"]').val(data.fullName);
                $('select[name="usermanager"]').val(data.userManagerId);
                $('select[name="userspv"]').val(data.userSpvId);
                $('select[name="previleges"]').val(data.privilegeId);
                $('select[name="unit"]').val(data.unitId);
                $('select[name="location"]').val(data.locationId);
                $('select[name="tipe"]').val(data.tipe);
                $('select[name="id_counter_setting"]').val(data.id_counter_setting);
                getCounterList(data.id_counter_setting);
                $('select[name="id_counter"]').data('val', data.id_counter);
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
        }

        return r;
    }


    function disable(id) {
        $('#btn-disable').attr("href", "<?php echo base_url(); ?>ManageSystem/disableuser?id=" + id);
    }

    function enable(id) {
        $('#btn-enable').attr("href", "<?php echo base_url(); ?>ManageSystem/enableuser?id=" + id);
    }

    function proses() {
        var id = $('input[name=id]').val();
        if (id != '' && id != undefined) {
            prosesUpUser(id);
        } else {
            // prosesInSubCategory();
            console.log("ke lempar di proses insert");
        }
    }

    function clearInputUpdate() {
        $('#wrapForm :input').val('');
    }

    function forceLogout(id) {
        $.get('<?= base_url("ManageSystem/forceLogout/"); ?>' + id, function (res) {
            Swal.fire(
                'Sukses!',
                'User has been force logout',
                'success'
            );
            $('.collapse').collapse('hide');

            showTable();
        });
    }

    function getCounterList(val) {
        $.get('<?= base_url("ManageSystem/getQueueList/"); ?>' + val, function (res) {
            $('[name="id_counter"]').html('<option value="">-- Choose Counter --</option>');

            for (var r in res) {
                $('[name="id_counter"]').append('<option value="' + res[r].id + '">' + res[r].counter + '</option>');
            }

            $('select[name="id_counter"]').val($('select[name="id_counter"]').data('val'));
        });
    }
</script>
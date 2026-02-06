<div class='title-module'>Search Recording</div>
<div class="subtitle-module">User Monitoring &raquo; Search Recording</div>


<style>
    .table thead tr th,
    .table tbody tr td {
        font-size: 11px;
    }
</style>
<div class="card" style="margin-bottom: 40px;">
    <div class="card-body" id="wrapForm" style="position: relative; left: 30px;">


        <div class="row">
            <label for="UserID" class="col-md-3" style="font-size: 15px">User ID</label>
            <select class="form-control col-md-4" id="id" name="id" style="width: 200px; position: relative; bottom: 10px; left: 10px;">

            </select>
        </div>

        <!-- <div class="row" style="margin-top:0.5rem">
            <label for="call_center_number" class="col-md-3" style="font-size: 15px">Call Center Number</label>
            <?php
            if ($this->session->userdata('tipe') == '123') {
            ?>
                <select class="form-control col-md-4" id="call_center_number" name="call_center_number" style="width: 200px; position: relative; bottom: 10px; left: 10px;">
                    <option value="">-- All Number --</option>
                    <option value="123">+123</option>
                    <option value="other">Other</option>
                </select>
            <?php
            } else if ($this->session->userdata('tipe') == '147') {
            ?>
                <select class="form-control col-md-4" id="call_center_number" name="call_center_number" style="width: 200px; position: relative; bottom: 10px; left: 10px;">
                    <option value="">-- All Number --</option>
                    <option value="147">+147</option>
                    <option value="other">Other</option>
                </select>
            <?php
            } else if ($this->session->userdata('tipe') == '888') {
            ?>
                <select class="form-control col-md-4" id="call_center_number" name="call_center_number" style="width: 200px; position: relative; bottom: 10px; left: 10px;">
                    <option value="">-- All Number --</option>
                    <option value="888">+888</option>
                    <option value="other">Other</option>
                </select>
            <?php
            } else {
            ?>
                <select class="form-control col-md-4" id="call_center_number" name="call_center_number" style="width: 200px; position: relative; bottom: 10px; left: 10px;">
                    <option value="">-- All Number --</option>
                    <option value="123">+123</option>
                    <option value="147">+147</option>
                    <option value="other">Other</option>
                </select>
            <?php
            }
            ?>
        </div> -->

        <div class="row" style="margin-top:0.5rem">
            <label for="Destination" class="col-md-3" style="font-size: 15px">Destination</label>
            <input class="form-control form-control-sm col-md-4" name="msisdn" id="msisdn" type="text" style="width: 200px;position: relative; bottom: 5px; left: 7px;">
        </div>

        <div class="row" style="margin-top:0.5rem">
            <label for="Interval" class="col-md-3" style="font-size: 15px">Interval</label>
            <div class="col-md-6">
                <input type="date" name="startdate" class="form-control" id="startdate" style="display: inline;width: 40%;"> Until
                <input type="date" name="enddate" class="form-control" id="enddate" style="display: inline;width: 40%;">
            </div>
        </div>
        <div class="row" style="margin-top:1rem">
            <button type="button" class="btn btn-primary" id="btn-cari" style="height:30px;">Submit Filter</button>
        </div>



    </div>
</div>

<div class="row flex-grow">
    <div class="col-12">
        <div class="card" style="border-radius: 10px;">
            <div class="card-title head-module-action">
                <div class="row">
                    <div class="col-sm-2"><a href="javascript:showdata()" style="color: white"><i class="fa fa-check-circle-o"></i> Search</a></div>
                    <div class="col-sm-2"><a href="javascript:clear()" style="color: white"><i class="fa fa-times-circle-o"></i> Clear</a></div>
                    <div class="col-sm-2"><a href="javascript:play()" style="color: white"><i class="fa fa-plus-circle"></i> Play</a></div>
                    <div class="col-sm-2"><a href="javascript:stop()" style="color: white"><i class="fa fa-times"></i> Stop</a></div>
                    <div class="col-sm-2"><a href="javascript:download()" style="color: white"><i class="fa fa-key"></i> Download</a></div>
                </div>
            </div>
            <div class="card-body body-module-action">
                <div class="card mt-2">
                    <div class="card-body">
                        <table class="table table-bordered display" id="contoh" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 15px; text-align: center"> # </th>
                                    <th style="width: 50px; text-align: center"> No </th>
                                    <th style="text-align: center"> Rec ID </th>
                                    <!-- <th style="text-align: center"> Agent ID</th>
                                    <th style="width:10px;text-align: center;"> Agent Name</th> -->
                                    <th style="text-align: center"> Ext Number</th>
                                    <th style="text-align: center"> Msisdn</th>
                                    <th style="text-align: center"> File Name</th>
                                    <th style="text-align: center"> File Size</th>
                                    <th style="text-align: center"> Date </th>
                                    <th style="text-align: center"> Duration </th>
                                    <!-- <th style="text-align: center"> Call Center Number </th> -->
                                </tr>
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

<div style="display: none" id="soundWrap"></div>
<script>
    $(document).ready(function() {
        showdata();
        btnCari();
        getUser();
        $('#btn-cari').unbind('click').click(function(e) {
            e.preventDefault();

            showdata();
        });
    });

    function clear() {
        $('#wrapForm :input').val('');
        showdata();
    }
    var curVal = '';

    function edit(id) {
        curVal = id;
        $('.oke').each(function() {
            if ($(this).val() != curVal) {
                $(this).prop('checked', false);
            }
        });
    }

    function showdata() {
        // body...
        id = $('select[name=id]').val();
        msisdn = $('input[name=msisdn]').val();
        // call_center_number = $('#call_center_number').val();
        startdate = $('input[name=startdate]').val();
        enddate = $('input[name=enddate]').val();
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
                "url": "<?= base_url("RecordingsController/showdtrecordings"); ?>?id=" + id + "&msisdn=" + msisdn + "&startdate=" + startdate + "&enddate=" + enddate, // + "&call_center_number=" + call_center_number,
                "type": "POST"
            },
            //Set column definition initialisation properties
            "columnDefs": [{
                "targets": [0],
                "orderable": false
            }]
        });
    }

    function getUser() {
        $.ajax({
            type: "GET",
            url: "<?= base_url("ManageSystem/getUser"); ?>",
            dataType: "JSON",
            success: function(response) {
                $('select[name="id"]').append('<option value="">-- -- All Agent -- --</option>');
                for (const x in response) {
                    $('select[name="id"]').append('<option value="' + response[x].id + '">' + response[x].username + '</option>');
                }
            }
        });
    }

    function btnCari() {
        $(document).ready(function() {
            $('#btn-cari').unbind('click').click(function(e) {
                e.preventDefault();

                showdata();
            });
        });
    }

    function download() {
        if ($('[name="id[]"]:checked').length == 1) {
            var filename = $('[name="id[]"]:checked').data('filename');
            window.location = '<?php echo base_url('ari/downloadRecording/?file_name='); ?>' + filename;
        } else {
            alert('Please choose one record');
        }
    }

    function play() {
        if ($('[name="id[]"]:checked').length == 1) {
            var filename = $('[name="id[]"]:checked').data('filename');
            $('#soundWrap').html('<audio controls="controls" id="embed_player" autoplay="autoplay">\n\
                                    <source src="<?php echo base_url('ari/downloadRecording/?play=true&file_name='); ?>' + filename + '" type="audio/mpeg">\n\
                                    Your browser does not support the audio element.\n\
                                  </audio>');
        } else {
            alert('Please choose one record');
        }
    }

    function stop() {
        if ($('[name="id[]"]:checked').length == 1) {
            $('#embed_player')[0].pause();
            $('#soundWrap').html('');
        } else {
            alert('Please choose one record');
        }
    }
</script>
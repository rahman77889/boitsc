<div class='title-module'>Call Center Report </div>
<div class="subtitle-module">Reports &raquo; Call Center Report</div>
<style>
    #button {
        top: -12px;
        position: relative;
    }

    /* th,td{
        border: 1px solid #b1b2b3 !important;
    } */
    .table {
        border: 1px solid #b1b2b3 !important;
    }
</style>

<form id="inForm" method="GET" action="" class="smart-form" novalidate="novalidate">


    <div class="card" style="border-radius: 15px">
        <div class="card-body">
            <div class="row mt-1">
                <div class="col-md-2">
                    <label for="label-control" style="font-size: 15px">Report Type</label>
                </div>
                <div class="col-md-2">
                    <select name="report_type" id="report_type" class="form-control kanan" style="width:15rem">
                        <option value="inbound">Call Inbound Report</option>
                        <option value="outbound">Call Outbount Report</option>
                        <option value="agent">Agent</option>
                    </select>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-2">
                    <label for="label-control" style="font-size: 15px">Group By</label>
                </div>
                <div class="col-md-2">
                    <select name="group_by" id="group_by" class="form-control kanan" style="width:15rem">
                        <?php
                        if ($report_type != 'agent') {
                            echo '
                            <option value="daily">Daily</option>
                            <option value="hourly">Hourly</option>
                            <option value="agent">Agent</option>
                            ';
                        } else {
                            echo '<option value="ALL">-- All Agent --</option>';
                            foreach ($listAgent as $kg => $ag) {
                                echo '<option value="' . $kg . '">' . $ag . '</option>';
                            }
                        }
                        ?>

                    </select>
                </div>
            </div>

            <div class="row mt-1">
                <div class="col-md-2">
                    <label for="label-control" style="font-size: 15px">Interval</label>
                </div>
                <div class="col-md-3">
                    <input type="date" name="tgl1" id="tgl1" class="form-control kanan ">
                </div>
                <p style="position: relative;top: 7px;"> - </p>
                <div class="col-md-3">
                    <input type="date" name="tgl2" id="tgl2" class="form-control kanan ">
                </div>
            </div>

            <?php
            if ($this->session->userdata('tipe') == '147123888') {
            ?>

                <div class="row mt-1">
                    <div class="col-md-2">
                        <label for="call_center_number" style="font-size: 15px;text-align: left">Call Center Number</label>
                    </div>
                    <div class="col-md-6">
                        <select class="form-control" id="call_center_number" name="call_center_number">
                            <option value="">-- All Number --</option>
                            <option value="123">+123</option>
                            <option value="147">+147</option>
                            <option value="888">+888</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

            <?php
            }
            ?>
        </div>
    </div>
</form>

<div class="card" style="border-radius: 15px;margin-top:1rem;">
    <div class="card-body">
        <div class="row">
            <a href="<?= current_url() ?>?<?= http_build_query($this->input->get()) ?>&export=Y" target="_blank" id="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Export To Excel</a>
        </div>
        <div class="row mt-1">
            <?php
            switch ($report_type) {
                case 'inbound':
            ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="inbound">
                            <thead>
                                <tr>
                                    <th style="border: 1px solid #bdbcbc; ">Interval</th>
                                    <th style="border: 1px solid #bdbcbc; ">Total Call</th>
                                    <th style="border: 1px solid #bdbcbc; ">Total Call Answered</th>
                                    <th style="border: 1px solid #bdbcbc; ">Total Abandoned Calls</th>
                                    <th style="border: 1px solid #bdbcbc; ">Total Calls Answered In 20"</th>
                                    <th style="border: 1px solid #bdbcbc; ">Avg Handling Time"</th>
                                    <th style="border: 1px solid #bdbcbc; ">Duration Handling Time</th>
                                    <th style="border: 1px solid #bdbcbc; ">Abadon Rate</th>
                                    <th style="border: 1px solid #bdbcbc; ">SCR</th>
                                    <th style="border: 1px solid #bdbcbc; ">Avg ACD Time</th>
                                </tr>

                            </thead>
                            <tbody>
                                <?php
                                echo $content_report;
                                ?>
                                <?php
                                echo $total;
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                    break;
                case 'outbound':
                ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="outbound">
                            <thead>
                                <tr>
                                    <th>Interval</th>
                                    <th>Outgoing Calls</th>
                                    <th>Outgoing Calls Answered</th>
                                    <th>Outgoing Abandoned Calls</th>
                                    <th>AVG Talktime</th>
                                    <th>Abandon Rate</th>
                                    <th>Answered Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                echo $content_report;
                                ?>
                                <?php
                                echo $total;
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                    break;
                case 'agent':
                ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="agent">
                            <thead>
                                <tr>
                                    <!-- <th>Agent Name</th>
                                    <th>ACD Calls</th>
                                    <th>ACD Time (s)</th>
                                    <th>Ring to Answer/R2A (s)	</th>
                                    <th>Avg ACD Time (s)</th>
                                    <th>AVG R2A Time (s)</th>
                                    <th>AUX Time</th>
                                    <th>Avail Time</th>
                                    <th>Staffed Time</th>
                                    <th>Connected Calls</th>
                                    <th>Held Calls</th>
                                    <th>Abandon Calls</th>
                                    <th>AHT</th> -->
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Log In Time</th>
                                    <th>ACD Calls</th>
                                    <th>ACD Times</th>
                                    <th>R2A(Ring to Answer) </th>
                                    <th>AUX Time</th>
                                    <th>Avail Time</th>
                                    <th>Staffed Time</th>
                                    <th>Connected Calls</th>
                                    <th>Held Calls</th>
                                    <th>Abandon Calls</th>
                                    <th>AHT</th>
                                    <th>Logout Out Time</th>
                                    <th>WorkingTime</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                echo $content_report;
                                ?>
                                <?php
                                echo $total;
                                ?>
                            </tbody>
                        </table>
                    </div>
            <?php
                    break;
            }
            ?>
            <div style="text-align:center;font-size:1rem;margin-top:1.5rem;width:100%">
                Page :
                <select name="page" form="inForm" class="form-control" style="width:10rem">
                    <?php
                    for ($pi = 1; $pi <= ceil($total_count / 50); $pi++) {
                        echo '<option value="' . $pi . '" ' . ($pi == $page ? 'selected' : '') . '>' . $pi . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function() {
        $('#group_by').val('<?php echo $group_by; ?>');
        $('#report_type').val('<?php echo $report_type; ?>');
        $('#tgl1').val('<?php echo $tgl1; ?>');
        $('#tgl2').val('<?php echo $tgl2; ?>'); // exportToExcel('#inbound');
        $('#call_center_number').val('<?php echo $call_center_number; ?>'); // exportToExcel('#inbound');
    });
</script>
<script>
    $(document).ready(function() {
        $('#inForm :input').change(function() {
            $('[name="page"]').val('');
            $('#inForm').submit();
        });
        $('[name="page"]').change(function() {
            $('#inForm').submit();
        });
    });
</script>
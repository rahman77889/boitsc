<div class='title-module'>Dashboard History </div>
<div class="subtitle-module">Reports &raquo; Dashboard History</div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<style>
    .kecil_img {
        width: 2.7rem;
    }

    .kecil_kata {
        font-size: 12px;
        margin: 0px;
        margin-top: 7px !important;
    }

    .agentLogout {
        background-color: white;
    }

    .agentLogin {
        background-color: #c3ffc3;
    }

    .agentReady {
        background-color: #ffffc0;
    }

    .agentBusy {
        background-color: #ffcfcf;
    }

    .numberLog {
        font-size: 1.5rem;
    }

    .chart_wrap {}

    #tableAgent tr td {
        padding: 0.5rem 1rem;
    }
</style>

<!--<div class='title-module' style="margin-top:-15px">Dashboard Helpdesk - <b>Telkomcel &copy;</b></div>
<div class="subtitle-module">Below feature to could monitor call center performance dashboard report</div>-->
<div class="card" style="padding:1rem">
    <div class="row" style="color: #655d5d;">
        <div class="col-md-12">
            <form method="get" action="">
                <div class="row mt-1">
                    <div class="col-md-2">
                        Date Range
                    </div>
                    <div class="col-md-10">
                        <input type="date" name="tgl1" class="form-control" value="<?php echo $tgl1; ?>" style="width:30%"> Until <input type="date" name="tgl2" class="form-control" value="<?php echo $tgl2; ?>" style="width:30%">
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
                } else {
                    echo '<input type="hidden" name="call_center_number" id="call_center_number">';
                }
                ?>
                <button class="btn btn-success" type="submit">Submit</button>
            </form>
        </div>
    </div>
</div>
<div class="row" style="color: #655d5d;margin-top:2rem;">
    <div class="col-md-12">
        <table style="width:100%;">
            <tbody>
                <tr>
                    <td style="width:20%">
                        <img src="<?php echo base_url('template/img/total_call_today.svg'); ?>" alt="" class="kecil_img mx-auto d-block">
                        <h4 class="text-center kecil_kata mt-3 ">Total Call </h4>
                        <h6 class="text-center numberLog" id="totalCallToday">0</h6>
                    </td>
                    <td style="width:20%">
                        <img src="<?php echo base_url('template/img/asa.svg'); ?>" alt="" class="kecil_img mx-auto d-block">
                        <h4 class="text-center kecil_kata mt-3 ">ASA </h4>
                        <h6 class="text-center numberLog" id="asa">0</h6>
                    </td>
                    <td style="width:20%">
                        <img src="<?php echo base_url('template/img/average_handling_time.svg'); ?>" alt="" class="kecil_img mx-auto d-block">
                        <h4 class="text-center kecil_kata mt-3 ">Average Handling Time </h4>
                        <h6 class="text-center numberLog" id="handlingTime">0</h6>
                    </td>
                    <td style="width:20%">
                        <img src="<?php echo base_url('template/img/call_5minute.svg'); ?>" alt="" class="kecil_img mx-auto d-block">
                        <h4 class="text-center kecil_kata mt-3 ">Calls > 5 Min </h4>
                        <h6 class="text-center numberLog" id="call5minutes">0</h6>
                    </td>
                    <td style="width:20%">
                        <img src="<?php echo base_url('template/img/call_waiting.svg'); ?>" alt="" class="kecil_img mx-auto d-block">
                        <h4 class="text-center kecil_kata mt-3 ">Call waiting </h4>
                        <h6 class="text-center numberLog" id="callWaiting">0</h6>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row" style="margin-top:2rem;">
    <div class="col-md-4">
        <div id="call_Monitoring" class="chart_wrap"></div>
    </div>
    <div class="col-md-4">
        <div id="livegraph" class="chart_wrap"></div>
    </div>
    <div class="col-md-4">
        <div id="topAgent" class="chart_wrap"></div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#call_center_number').val('<?php echo $call_center_number; ?>');

        getDasboard();
    });

    function scrollDown(el) {
        el.animate({
            scrollTop: el[0].scrollHeight
        }, 5000, function() {
            scrollUp(el)
        });
    }

    function scrollUp(el) {
        el.animate({
            scrollTop: 0
        }, 5000, function() {
            scrollDown(el);
        });
    }

    function getDasboard() {
        $.ajax({
            type: "GET",
            url: "<?= base_url("CallController/getDashboard/" . $tgl1 . '/' . $tgl2 . '/' . $call_center_number); ?>",
            dataType: "JSON",
            success: function(json) {
                for (var it in json.item) {
                    if (json.item[it] != null && json.item[it] > 0) {
                        $('#' + it).text(json.item[it]);
                    } else {
                        $('#' + it).text(0);
                    }

                    if (it == 'asa' || it == 'handlingTime') {
                        $('#' + it).html($('#' + it).text() + ' <small>Second</small>');
                    }
                }

                Highcharts.chart('call_Monitoring', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: 'Incoming Call Monitoring'
                    },
                    tooltip: {
                        // pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                        pointFormat: '{series.name}: <b>{point.y}</b>'

                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false
                            },
                            showInLegend: true,
                            animation: false
                        }
                    },
                    series: [{
                        name: 'Total',
                        colorByPoint: true,
                        data: json.callMonitor
                    }]
                });

                Highcharts.chart('topAgent', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: 'Top Agent'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false
                            },
                            showInLegend: true,
                            animation: false
                        }
                    },
                    series: [{
                        name: 'total',
                        colorByPoint: true,
                        data: json.topAgent
                    }]
                });

                Highcharts.chart('livegraph', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie',

                    },
                    title: {
                        text: 'Call Monitoring'
                    },
                    tooltip: {
                        // pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                        pointFormat: '{series.name}: <b>{point.y}</b>'
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: false
                            },
                            showInLegend: true,
                            animation: false
                        }
                    },
                    series: [{
                        name: 'total',
                        colorByPoint: true,
                        data: json.totalCallInOut
                    }]
                });

                $('#tableAgent').html('');

                for (var ig in json.tableAgent) {
                    $('#tableAgent').append('<tr class="' + json.tableAgent[ig].status + '"><td style="width:2rem"><img src="<?php echo base_url('template/img/users.svg'); ?>" class="card-img-top" alt="..."></td><td>' + json.tableAgent[ig].nama + '</td></tr>');
                }
            }
        });
    }
</script>
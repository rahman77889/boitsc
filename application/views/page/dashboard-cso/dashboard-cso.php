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

    .csoStanby {
        background-color: #c3ffc3;
    }

    .totalCustomer {
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

    .tab-area {
        background-color: #f3db33;
        padding: 0.5rem;
        cursor: pointer;
    }

    .tab-area.tab-active {
        background: linear-gradient(45deg, #8e24aa, #ff6e40) !important;
        padding: 0.5rem;
        color: white;
        cursor: pointer;
    }
</style>

<!--<div class='title-module' style="margin-top:-15px">Dashboard Helpdesk - <b>Telkomcel &copy;</b></div>
    <div class="subtitle-module">Below feature to could monitor call center performance dashboard report</div>-->

<div class="row" style="color: #655d5d;">
    <div class="col-md-5" style="height: 16rem;overflow-x: hidden">
        <table class="table table-hover" id="tableAgent"></table>
    </div>

    <div class="col-md-7">
        <?php
        if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2) {
            ?>
            <div class="row" style="font-weight: bold">
                <div class="col-md-4 text-center tab-area tab-active" data-val="">
                    All Area
                </div>
                <div class="col-md-4 text-center tab-area" data-val="inner">
                    Inner
                </div>
                <div class="col-md-4 text-center tab-area" data-val="outer">
                    Outer
                </div>
            </div>
            <?php
        }
        ?>
        <table style="width:100%;margin-top:1.5rem">
            <tbody>
                <tr>
                    <td>
                        <img src="<?php echo base_url('template/img/agent_login.svg'); ?>" alt=""
                            class="kecil_img mx-auto d-block">
                        <h4 class="text-center kecil_kata mt-3 ">CSO Stanby</h4>
                        <h6 class="text-center numberLog" id="csoStanby">0</h6>
                    </td>
                    <td>
                        <img src="<?php echo base_url('template/img/agent_ready.svg'); ?>" alt=""
                            class="kecil_img mx-auto d-block">
                        <h4 class="text-center kecil_kata mt-3 ">Total Customer Today</h4>
                        <h6 class="text-center numberLog" id="totalCustomer">0</h6>
                    </td>
                    <td onclick="window.location = '<?php echo base_url('Reports/call_log'); ?>';"
                        style="cursor: pointer">
                        <img src="<?php echo base_url('template/img/total_call_today.svg'); ?>" alt=""
                            class="kecil_img mx-auto d-block">
                        <h4 class="text-center kecil_kata mt-3 ">Average Handling Time </h4>
                        <h6 class="text-center numberLog" id="averageHandling">0</h6>
                    </td>
                    <td>
                        <img src="<?php echo base_url('template/img/asa.svg'); ?>" alt=""
                            class="kecil_img mx-auto d-block">
                        <h4 class="text-center kecil_kata mt-3 ">Hourly Service </h4>
                        <h6 class="text-center numberLog" id="hourlyService">0</h6>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: center;padding-top:0.5rem;">
                        <table style="width: 100%">
                            <tr>
                                <td style="width:25%">
                                    <img src="<?php echo base_url('template/img/average_handling_time.svg'); ?>" alt=""
                                        class="kecil_img mx-auto d-block">
                                    <h4 class="text-center kecil_kata mt-3 ">Total Activity Today </h4>
                                    <h6 class="text-center numberLog" id="totalActivityToday">0</h6>
                                </td>
                                <td style="width:25%">
                                    <img src="<?php echo base_url('template/img/call_5minute.svg'); ?>" alt=""
                                        class="kecil_img mx-auto d-block">
                                    <h4 class="text-center kecil_kata mt-3 ">Handling Time > 5 Min </h4>
                                    <h6 class="text-center numberLog" id="handlingTime5Min">0</h6>
                                </td>
                                <td style="width:25%">
                                    <img src="<?php echo base_url('template/img/call_waiting.svg'); ?>" alt=""
                                        class="kecil_img mx-auto d-block">
                                    <h4 class="text-center kecil_kata mt-3 ">Queue waiting </h4>
                                    <h6 class="text-center numberLog" id="queueWaiting">0</h6>
                                </td>
                                <td style="width:25%">
                                    <img src="<?php echo base_url('template/assets/images/call-back.svg'); ?>" alt=""
                                        class="kecil_img mx-auto d-block">
                                    <h4 class="text-center kecil_kata mt-3 ">Average Waiting Time </h4>
                                    <h6 class="text-center numberLog" id="averageWaiting">0</h6>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row" style="margin-top:2rem;">
    <?php
    if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 || $this->session->userdata('privilege') == 4 || $this->session->userdata('privilege') == 5) {
        ?>
        <div class="col-md-4">
            <div id="plaza_chart" class="chart_wrap"></div>
        </div>
        <div class="col-md-4">
            <div id="rating_cso" class="chart_wrap"></div>
        </div>
        <?php
    }
    if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 || $this->session->userdata('privilege') == 4) {
        ?>
        <div class="col-md-4">
            <div id="topCso" class="chart_wrap"></div>
        </div>
        <?php
    }
    if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 || $this->session->userdata('privilege') == 4 || $this->session->userdata('privilege') == 5) {
        ?>
        <div class="col-md-4">
            <div id="total_customer" class="chart_wrap"></div>
        </div>
        <div class="col-md-4">
            <div id="trend_data_package" class="chart_wrap"></div>
        </div>
        <div class="col-md-4">
            <div id="best_sales" class="chart_wrap"></div>
        </div>
        <?php
    }
    ?>
</div>


<script>
    var currentType = '<?php echo $this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 ? '' : $this->session->userdata('area'); ?>';
    var tglFilter = '';

    $(document).ready(function () {

        getDasboard();

        setInterval(function () {
            getDasboard();
        }, 10000);

        $('.navbar-nav .nav-item:first').html('<div class="title-module">Centro Informação</b></div>');
        $('.navbar-nav .nav-item:first').removeClass('font-weight-semibold');

        setTimeout(function () {
            scrollDown($('#tableAgent').parent());
        }, 2000);

        $('#tableAgent').parent().hover(function () {
            $(this).stop(true);
        }, function () {
            scrollDown($('#tableAgent').parent());
        });


        $('.tab-area').click(function () {
            var val = $(this).data('val');

            currentType = val;

            getDasboard();

            $('.tab-area.tab-active').removeClass('tab-active');
            $(this).addClass('tab-active');
        });
    });

    function scrollDown(el) {
        el.animate({
            scrollTop: el[0].scrollHeight
        }, 10000, function () {
            scrollUp(el)
        });
    }

    function scrollUp(el) {
        el.animate({
            scrollTop: 0
        }, 10000, function () {
            scrollDown(el);
        });
    }

    function getDasboard() {
        var val = currentType;
        var add = '';
        var dt = new Date();
        var dd = dt.getFullYear() + "-" + ((dt.getMonth() + 1) < 10 ? '0' : '') + (dt.getMonth() + 1) + "-" + dt.getDate();

        if (val) {
            tglFilter = dd;

            add = '/' + dd + '/' + dd + '/' + val;
        } else {
            tglFilter = dd;
        }
        $.ajax({
            type: "GET",
            url: "<?= base_url("CallController/getDashboardCso"); ?>" + add,
            dataType: "JSON",
            success: function (json) {
                for (var it in json.item) {
                    if (json.item[it] != null && json.item[it] > 0) {
                        $('#' + it).text(json.item[it]);
                    } else {
                        $('#' + it).text(0);
                    }

                    if (it == 'averageHandling' || it == 'averageWaiting') {
                        $('#' + it).html($('#' + it).text() + ' <small>Second</small>');
                    }
                }

                <?php
                if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 || $this->session->userdata('privilege') == 4 || $this->session->userdata('privilege') == 5) {
                    ?>
                    Highcharts.chart('plaza_chart', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: 'Plaza Activity'
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
                                animation: false,
                                point: {
                                    events: {
                                        click: function (event) {
                                            if (event.point.name == 'Purchase') {
                                                window.location.href = '<?= base_url() ?>Reports/detail_cso_purchase_area?tgl1=' + tglFilter + '&tgl2=' + tglFilter + '&area=' + currentType;
                                            } else {
                                                window.location.href = '<?= base_url() ?>Reports/cso_performance?tgl1=' + tglFilter + '&tgl2=' + tglFilter + '&id_plaza=';
                                            }
                                        }
                                    }
                                }
                            },
                        },
                        series: [{
                            name: 'Total',
                            colorByPoint: true,
                            data: json.plazaActivity
                        }]
                    });
                    Highcharts.chart('total_customer', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: 'Total Customer Queue'
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
                            data: json.totalCustomer
                        }]
                    });
                    Highcharts.chart('trend_data_package', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: 'Trend Data Package Today'
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
                            data: json.trendDataPackage
                        }]
                    });
                    Highcharts.chart('best_sales', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: 'Best Sales Today'
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
                            data: json.bestSales
                        }]
                    });
                    <?php
                }
                if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 || $this->session->userdata('privilege') == 4) {
                    ?>
                    Highcharts.chart('topCso', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: 'Top CSO'
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
                            name: 'CSO',
                            colorByPoint: true,
                            data: json.topCso
                        }]
                    });
                    <?php
                }


                if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 || $this->session->userdata('privilege') == 4 || $this->session->userdata('privilege') == 5) {
                    ?>

                    Highcharts.chart('rating_cso', {
                        chart: {
                            plotBackgroundColor: null,
                            plotBorderWidth: null,
                            plotShadow: false,
                            type: 'pie'
                        },
                        title: {
                            text: 'Average Rating Satisfaction',
                        },
                        subtitle: {
                            text: 'Total Voters : ' + json.total_voters
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
                            name: 'Indicator',
                            colorByPoint: true,
                            data: json.rating_cso
                        }]
                    });

                <?php } ?>

                $('#tableAgent').html('');

                for (var ig in json.tableAgent) {
                    $('#tableAgent').append('<tr class="' + json.tableAgent[ig].status + '"><td style="width:2rem"><img src="<?php echo base_url('template/img/users.svg'); ?>" class="card-img-top" alt="..."></td><td>' + json.tableAgent[ig].nama + '</td></tr>');
                }
            }
        });
    }
</script>
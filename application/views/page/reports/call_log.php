<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<div class='title-module'>Total Call Center Log</div>
<div class="subtitle-module">Reports &raquo; Total Call Center Log</div>

<div class="card" style="padding:1rem">
    <div class="row" style="color: #655d5d;">
        <div class="col-md-12">
            <form method="get" action="" id="formFilter">
                <div class="row mt-1">
                    <div class="col-md-2">
                        <label for="call_center_number" style="font-size: 15px;text-align: left">Period</label>
                    </div>
                    <div class="col-md-6">
                        <input type="date" name="tgl1" class="form-control" value="<?php echo $tgl1; ?>" style="width:40%"> Until <input type="date" name="tgl2" class="form-control" value="<?php echo $tgl2; ?>" style="width:40%">
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

<div id="chartData" style="margin-top:2rem;"></div>

<div class="card mt-2 " style="border-radius: 10px;">
    <div class="card-title head-module-action">
        <table>
            <thead>
                <tr>
                    <th><span style="color:white"><i class="fa fa-list"></i> List Call Center Log</span>
                        <a href="javascript:exportToExcel('#tableReport');" class="pelebaran"><i class="fa fa-file-excel-o" style=""></i> Export To Excell</a>
                    </th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered display table-hover table-striped" id="tableReport" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Datetime</th>
                            <th>MSISDN</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $reportData; ?>
                    </tbody>
                </table>

                <div style="text-align: center;margin-top: 2rem;">
                    Page :
                    <select name="page" onchange="window.location = '?' + $('#formFilter :input').serialize() + '&page=' + this.value;">
                        <?php
                        for ($p = 1; $p <= $totalPage; $p++) {
                            echo '<option value="' . $p . '">' . $p . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('[name="page"]').val('<?php echo $page + 1; ?>');
        $('#call_center_number').val('<?php echo $call_center_number; ?>');
    });
    Highcharts.chart('chartData', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Total Call Log <?php echo date('d F Y', strtotime($tgl1)) . ' until ' . date('d F Y', strtotime($tgl2)); ?>'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Total Activation'
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y}'
                }
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span><br>Total : <b>{point.y}</b><br/>'
        },
        series: [{
            name: "Package Name",
            colorByPoint: true,
            data: <?php echo json_encode($reportChart); ?>
        }]
    });
</script>

<script>
    function exportToExcel(table) {
        var htmls = "";
        var uri = 'data:application/vnd.ms-excel;base64,';
        var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
        var base64 = function(s) {
            return window.btoa(unescape(encodeURIComponent(s)))
        };
        var format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) {
                return c[p];
            })
        };

        htmls = $(table)[0].outerHTML;

        var ctx = {
            worksheet: 'Worksheet',
            table: htmls
        }


        var link = document.createElement("a");
        link.download = "export.xls";
        link.href = uri + base64(format(template, ctx));
        link.click();
    }
</script>
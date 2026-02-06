<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<div class='title-module'>Total Smart Call Auto Call IVR Log</div>
<div class="subtitle-module">Reports &raquo; Total Call Center Log</div>

<div class="card" style="padding:1rem">
    <div class="row" style="color: #655d5d;">
        <div class="col-md-12">
            <form method="get" action="" id="formFilter">
                <div class="row">
                    <div class="col-md-3">Date Filter : </div>
                    <div class="col-md-9"><input type="date" name="tgl1" class="form-control" value="<?php echo $tgl1; ?>" style="width:30%"> Until <input type="date" name="tgl2" class="form-control" value="<?php echo $tgl2; ?>" style="width:30%"></div>
                </div>
                <div class="row" style="margin-top: 0.5rem">
                    <div class="col-md-3">Result Status : </div>
                    <div class="col-md-9">
                        <select class="form-control" name="status" style="width:30%">
                            <option value="">-- All Status --</option>
                            <option value="success">Success</option>
                            <!--<option value="reject">Reject</option>-->
                            <option value="no-answer">No Answer</option>
                            <option value="out-of-coverage">Out Of Coverage</option>
                            <option value="busy">Busy</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-success" type="submit" style="margin-top: 1rem">Submit</button>
            </form>
        </div>
    </div>
</div>

<div id="chartData" style="margin-top:2rem;"></div>

<div class="card mt-2 " style="border-radius: 10px;" >
    <div class="card-title head-module-action">
        <table>
            <thead>
                <tr>
                    <th><span style="color:white"><i class="fa fa-list"></i>  List Auto Call IVR Log</span>
                        <a href="javascript:exportToExcel('#tableReport');" class="pelebaran"><i class="fa fa-file-excel-o" style=""></i> Export To Excell</a>
                    </th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered display table-hover table-striped"  id="tableReport"  style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Datetime</th>
                            <th>MSISDN</th>
                            <th>Language</th>
                            <th>Status</th>
                            <th>Result</th>
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

    $(document).ready(function () {
    $('[name="page"]').val('<?php echo $page + 1; ?>');
    $('[name="status"]').val('<?php echo $status; ?>');
    });
    Highcharts.chart('chartData', {
    chart: {
    type: 'column'
    },
            title: {
            text: 'Total Auto Call Log IVR - <?php echo date('d F Y', strtotime($tgl1)) . ' until ' . date('d F Y', strtotime($tgl2)); ?>'
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
            series: [
            {
            name: "Result Type",
                    colorByPoint: true,
                    data: <?php echo json_encode($reportChart); ?>
            }
            ]
    });</script>

<script>
            function exportToExcel(table) {
    var htmls = ""; var uri = 'data:application/vnd.ms-excel;base64,';
            var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
                        var base64 = function (s) {
            return window.btoa(unescape(encodeURIComponent(s)))
        };
        var format = function (s, c) {
            return s.replace(/{(\w+)}/g, function (m, p) {
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

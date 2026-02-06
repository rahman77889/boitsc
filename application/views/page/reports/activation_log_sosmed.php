<?php
if ($all) {
    ob_start();
}
?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<div class='title-module'>Activation Log Chatbot</div>
<div class="subtitle-module">Reports &raquo; Activation Log Chatbot</div>

<div class="card" style="padding:1rem">
    <div class="row" style="color: #655d5d;">
        <div class="col-md-12">
            <form method="get" action="" id="formFilter">
                <input type="date" name="tgl1" class="form-control" value="<?php echo $tgl1; ?>" style="width:30%"> Until <input type="date" name="tgl2" class="form-control" value="<?php echo $tgl2; ?>" style="width:30%">
                <button class="btn btn-success" type="submit">Submit</button>
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
                    <th><span style="color:white"><i class="fa fa-list"></i>  List Activation Log</span>
                        <a href="javascript:exportToExcel('#tableReport');" class="pelebaran"><i class="fa fa-file-excel-o" style=""></i> Export To Excell</a>
                    </th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="table-responsive">
                <?php
                if ($all) {
                    ob_end_clean();

                    ob_start();
                    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>worksheet</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body>';
                }
                ?>
                <table class="table table-bordered display table-hover table-striped"  id="tableReport"  style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Datetime</th>
                            <th>Refil Id</th>
                            <th>Package Name</th>
                            <th>MSISDN</th>
                            <th>Channel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $reportData; ?>
                    </tbody>
                </table>

                <?php
                if ($all) {
                    echo '</body></html>';
                    $data = ob_get_clean();

                    header("Content-type: application/vnd-ms-excel");
                    header("Content-Disposition: attachment; filename=Data Activation Call.xls");

                    echo $data;

                    exit();
                }
                ?>

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
    });
    Highcharts.chart('chartData', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Total Activation Log <?php echo date('d F Y', strtotime($tgl1)) . ' until ' . date('d F Y', strtotime($tgl2)); ?>'
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
                name: "Package Name",
                colorByPoint: true,
                data: <?php echo json_encode($reportChart); ?>
            }
        ]
    });</script>

<script>
function exportToExcel(table) {
window.location = '?tgl1=<?php echo $tgl1; ?>&tgl2=<?php echo $tgl2; ?>&all=true';
//var htmls = ""; var uri = 'data:application/vnd.ms-excel;base64,';
//var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
//            var base64 = function (s) {
//return window.btoa(unescape(encodeURIComponent(s)))
//};
//var format = function (s, c) {
//return s.replace(/{(\w+)}/g, function (m, p) {
//    return c[p];
//})
//};
//
//htmls = $(table)[0].outerHTML;
//
//var ctx = {
//worksheet: 'Worksheet',
//table: htmls
//}
//
//
//var link = document.createElement("a");
//link.download = "export.xls";
//link.href = uri + base64(format(template, ctx));
//link.click();
}
</script>
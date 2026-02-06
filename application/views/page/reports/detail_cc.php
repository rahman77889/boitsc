<div class="card" style="border-radius: 15px;margin-top:1rem;">
    <div class="card-body">
        <div class="row mt-1">
            <!--<a href="javascript:void" onclick="history.go(-1)" class="btn btn-primary float-right mt-4" style="top: -2rem;position: relative;"><i class="fa fa-arrow-circle-left "></i>Back To Report</a>-->

            <div class="row" style="margin-left: 1px;margin-top:-0.5rem">
                <button onclick="printDiv($('#inbound'))" class="btn btn-success" style="margin-left:10px;height: 2rem;"><i class="fa fa-print"></i> Cetak</button>&nbsp;
                <button onclick="exportToExcel('#inbound');" id="button" class="btn btn-success" style="margin-left:10px;height: 2rem;"><i class="fa fa-file-excel-o"></i> Export  </button>
            </div><!-- comment -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="inbound" >
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date Time</th>
                            <th>MSISDN</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = ($page == 1 ? 1 : (($page - 1) * 50) + 1);
                        foreach ($data as $row) {
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row->tgl; ?></td>
                                <td><?php echo $row->msisdn; ?></td>
                            </tr>
                        <?php }; ?>
                    </tbody>
                </table>

                <div style="text-align: right;padding:1rem;">
                    Page : 
                    <select onchange="window.location = '?<?php echo $query; ?>&page=' + this.value" class="form-control" style="width:5rem">
                        <?php
                        $max_page = ceil($count[0]->jumlah / 50);

                        for ($p = 1; $p <= $max_page; $p++) {
                            echo '<option value="' . $p . '" ' . ($p == $page ? 'selected' : '') . '>' . $p . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function printDiv(element) {
        var divToPrint = element[0].outerHTML;
        var newWin = window.open('', 'Print-Window');
        newWin.document.open();
        newWin.document.write('<html><body onload="window.print()">' + divToPrint + '</body></html>');
        newWin.document.close();
        setTimeout(function () {
            newWin.close();
        }, 10);
    }
    function cetak(inbound, outbound) {

        if (inbound == "inbound") {
            exportToExcel('#inbound');
        } else if (outbound == "outbound") {
            exportToExcel('#outbound');
        }
    }

    function exportToExcel(table) {
        var htmls = "";
        var uri = 'data:application/vnd.ms-excel;base64,';
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
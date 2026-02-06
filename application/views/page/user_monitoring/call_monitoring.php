<div class='title-module'>Call Monitoring</div>
<div class="subtitle-module">User Monitoring &raquo; Call Monitoring</div>

<style>
    .table thead tr th,.table tbody tr td{
        font-size: 11px;
    }
</style>

<div class="row flex-grow">
    <div class="col-12">
        <div class="card" style="border-radius: 10px;" >
            <div class="card-title head-module-action" style="text-align: left">
                <a href="javascript:void(0)"><i class="fa fa-search"></i> List Call Monitoring</a>
            </div>
            <div class="card-body body-module-action">
                <div class="card mt-2">
                    <div class="card-body">
                        <table class="table table-bordered display"  id="contoh"  style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center"> No </th>
                                    <th style="text-align: center"> Agent </th>
                                    <th style="text-align: center"> Ext</th>
                                    <th style="width:10px;text-align: center;" > Status</th>
                                    <th style="text-align: center" > Status Time</th>
                                    <th style="text-align: center" > Ext Status</th>
                                    <th style="text-align: center" > Spy</th>
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
<style>
    #contoh_processing{
        display: none!important;
    }
</style>
<script>

    $(document).ready(function () {
        showdata();

        //refresh every 3 second
        setInterval(showdata, 3000);
    });

    function showdata() {
        // body...
        $('#contoh').DataTable({
            // Processing indicator
            "pageLength": 1000,
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
                "url": "<?= base_url("CallMonitoring/showdtcallmonitoring "); ?>",
                "type": "POST"
            },
            //Set column definition initialisation properties
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false
                }
            ],
            "createdRow": function (row, data, dataIndex) {
                if (data[3] == `Logout`) {
                    $(row).css('background-color', '#ffcfcf');
                }
            }
        });
    }

    function spy(exten) {
//        $.getJSON("http://localhost:8083/call/*11" + exten, function (result) {
////                        console.log(result);
//        });
        placeCall('*11' + exten, true);
    }

    function whisper(exten) {
//        $.getJSON("http://localhost:8083/call/*12" + exten, function (result) {
////                        console.log(result);
//        });
        placeCall('*12' + exten, true);
    }
</script>
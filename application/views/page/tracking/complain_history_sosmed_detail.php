<div class='title-module'>Detail Complain History Social Media</div>
<div class="subtitle-module">Tracking &raquo; Detail Complain History Social Media</div>

<style>
    .bo{
        border-radius: 8px !important;

    }

    .kiri{
        margin-left:4px;
        /* padding: 10px; */
    }

    .kanan{
        margin-left:4px;

    }

    .pelebaran{
        padding: 6px;
    }

</style>

<div class="card bo" id="form-filter">
    <div class="card-body">
        <div class="row">
            <div class="col-md-10">

                <div class="row">
                    <div class="col-md-4">
                        <label for="label-control">Ticket Number</label>
                    </div>
                    <div class="col-md-6 bold" id="AUticketnumber">

                    </div>
                </div>
                <div class="row" style="margin-top:1rem;">
                    <div class="col-md-4">
                        <label for="label-control">Channel</label>
                    </div>
                    <div class="col-md-6 bold" id="AUchannel">

                    </div>
                </div>
                <div class="row" style="margin-top:1rem;">
                    <div class="col-md-4">
                        <label for="label-control">Customer Name</label>
                    </div>
                    <div class="col-md-6 bold" id="AUname">

                    </div>
                </div>
                <div class="row" style="margin-top:1rem;">
                    <div class="col-md-4">
                        <label for="label-control">Customer Phone</label>
                    </div>
                    <div class="col-md-6 bold" id="AUphone">

                    </div>
                </div>
                <div class="row" style="margin-top:1rem;">
                    <div class="col-md-4">
                        <label for="label-control">Customer Email</label>
                    </div>
                    <div class="col-md-6 bold" id="AUemail">

                    </div>
                </div>
                <div class="row" style="margin-top:1rem;">
                    <div class="col-md-4">
                        <label for="label-control">Permalink</label>
                    </div>
                    <div class="col-md-6 bold" id="AUlink">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<br>
<div class="card mt-2 " style="border-radius: 10px;" >
    <div class="card-title head-module-action">
        <table>
            <thead>
                <tr>
                    <th>
                        <a href="javascript:void(0)"  class="pelebaran"><i class="fa fa-search"></i> List Detail Chat</a> 
                    </th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered display"  id="contoh"  style="width:100%">
                    <thead>
                        <tr>
                            <!--<th>#</th>-->
                            <th>No</th>
                            <th>DateTime</th>
                            <th>Message</th>
                            <th>Attachment</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
    var getVar;
    $(document).ready(function () {
        getVar = getQueryParams(document.location.search);

        showdata();

//        $.get("https://localhost/sociolution/handle.act?integration_helpdesk_detail_chat=true&a=true&id=" + getVar.id, function (res) {
        $.get("https://hakbesik.telkomcel.tl/handle.act?integration_helpdesk_detail_chat=true&a=true&id=" + getVar.id, function (res) {
            var json = $.parseJSON(res);

            $('#AUticketnumber').text(json.no);
            $('#AUchannel').text(json.channel);
            $('#AUname').text(json.customer_name);
            $('#AUphone').text(json.customer_phone);
            $('#AUemail').text(json.customer_email);
            if (json.permalink) {
                $('#AUlink').html('<a href="' + json.permalink + '" class="btn btn-sm btn-success" target="_blank">Source Link</a>');
            } else {
                $('#AUlink').hide();
            }
        });
    });

    function getQueryParams(qs) {
        qs = qs.split('+').join(' ');

        var params = {},
                tokens,
                re = /[?&]?([^=]+)=([^&]*)/g;

        while (tokens = re.exec(qs)) {
            params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
        }

        return params;
    }



    function showdata() {
        $('#contoh').DataTable({
            // Processing indicator
            "destroy": true,
            "searching": false,
            "processing": true,
            // DataTables server-side processing mode
            "serverSide": true,
            "scrollX": true,
            // Initial no order.
            "order": [],
            // Load data from an Ajax source
            "ajax": {
                "url": "https://hakbesik.telkomcel.tl/handle.act?integration_helpdesk_detail=true&a=true&id=" + getVar.id,
//                "url": "https://localhost/sociolution/handle.act?integration_helpdesk_detail=true&a=true&id=" + getVar.id,
                "type": "GET"
            },
            //Set column definition initialisation properties
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false
                }
            ]
        });
    }
</script>
<div class='title-module'>Complain History Social Media</div>
<div class="subtitle-module">Tracking &raquo; Complain History Social Media</div>

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
                    <div class="col-md-6">
                        <input type="text" name="ticket_no" class="form-control j" >	
                    </div>
                </div>
                <div class="row" style="margin-top:1rem;">
                    <div class="col-md-4">
                        <label for="label-control">Channel</label>
                    </div>
                    <div class="col-md-6">
                        <select name="channel" id="categoryId" class="form-control j">
                            <?php echo $listChannel; ?>
                        </select>
                    </div>
                </div>
                <div class="row" style="margin-top:1rem;">
                    <div class="col-md-4">
                        <label for="label-control">Complain Date</label>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="date" name="startdate" class="form-control j" style="width: 153px;"> 
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="enddate" class="form-control j" style="width: 153px;  margin-left: 2rem;"> 

                            </div>
                        </div>

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
                        <a href="javascript:runSearch()"  class="pelebaran"><i class="fa fa-search"></i> Search</a> 
                        <a href="javascript:openDetail()" class="pelebaran"><i class="fa fa-file" style=""></i> Show Detail Ticket</a> 
                        <a href="javascript:clearForm()" class="pelebaran"><i class="fa fa-close" style="color:red;"></i> Clear</a>
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
                            <th>#</th>
                            <th>No</th>
                            <th>Ticket Number</th>
                            <th>DateTime</th>
                            <th>Channel</th>
                            <th>Cust Name</th>
                            <th>Cust Phone</th>
                            <th>Cust Email</th>
                            <th>Complaint Message</th>
                            <th>Attachment</th>
                            <th>Permalink</th>
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
    var curVal;
    function edit(id) {
        curVal = id;
        $('[name="id"]').each(function () {
            if ($(this).val() != curVal) {
                $(this).prop('checked', false);
            }
        });
    }
    function runSearch() {
        showdata();
    }

    function openDetail() {
        if (curVal) {
            window.location = '<?php echo base_url('Tracking/complain_history_sosmed_detail?id='); ?>' + curVal;
        } else {
            alert('Please select data first');
        }
    }

    function clearForm() {
        $('#form-filter :input').val('');
    }


    $(document).ready(function () {
        showdata();
    });

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
                "url": "https://hakbesik.telkomcel.tl/handle.act?integration_helpdesk=true&a=true&" + $('#form-filter :input').serialize(),
//                "url": "https://localhost/sociolution/handle.act?integration_helpdesk=true&a=true&" + $('#form-filter :input').serialize(),
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
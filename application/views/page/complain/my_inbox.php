<div class='title-module'>My Inbox</div>
<div class="subtitle-module">Complaint Handling &raquo; My Inbox</div>
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
            <div class="col-md-2">
                <label for="label-control">Transaction Code</label>
            </div>
            <div class="col-md-3">
                <input type="text" name="transactionCode" class="form-control kiri" >	
            </div>
            <div class="col-md-2">
                <label for="label-control">Category</label>
            </div>
            <div class="col-md-3">
                 <!-- <input type="text" class="form-control kiri" >	 -->
                <select name="categoryId" id="categoryId" class="form-control kiri">
                    <?php echo $listCategory; ?>
                </select>

            </div>
        </div>
        <div class="row mt-1">
            <div class="col-md-2">
                <label for="label-control">Customer Name </label>
            </div>
            <div class="col-md-3">
                <input type="text" name="customerName" class="form-control kiri" >	
            </div>
            <div class="col-md-2">
                <label for="label-control">MDN Number</label>
            </div>
            <div class="col-md-3">
                <input type="text" name="mdnProblem" class="form-control kiri" >	
            </div>
        </div>

        <div class="row mt-1">
            <div class="col-md-2">
                <label for="label-control">Location</label>
            </div>
            <div class="col-md-3">
                <input type="text" name="btsLocation" class="form-control kanan">
            </div>
            <div class="col-md-2">
                <label for="label-control">Unit</label>
            </div>
            <div class="col-md-3">
                <select name="unitId" id="unitId" class="form-control kiri">
                    <?php echo $listUnit; ?>
                </select>
            </div>
        </div>

        <div class="row mt-1">
            <div class="col-md-2">
                <label for="label-control">Complain Date</label>
            </div>
            <div class="col-md-3">
                <input type="date" name="complainDate" class="form-control kanan ">

            </div>
            <div class="col-md-2">
                <label for="label-control">User Create</label>
            </div>
            <div class="col-md-3">
                <select name="userId" id="" class="form-control kiri">
                    <?php echo $listUser; ?>
                </select>
            </div>
        </div>     
    </div>
</div>

<br>
<div class="card mt-2 " style="border-radius: 10px 10px;">
    <div class="card-title head-module-action" style="text-align: left">
        <table>
            <thead>
                <tr>
                    <th>
                        <a href="javascript:runSearch()"  class="pelebaran"><i class="fa fa-search"></i> Search</a> 
                        <a href="javascript:openDetail()" class="pelebaran"><i class="fa fa-file" style=""></i> Show Complain Ticket</a> 
                        <a href="javascript:clearForm()" class="pelebaran"><i class="fa fa-close" style="color:red;"></i> Clear</a>
                    </th> 
                </tr>
            </thead>

        </table>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered" id="complaint">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Transaction Code</th>
                            <th>Cust Name</th>
                            <th>MDN Number</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Complaint Status</th>
                            <th>Complaint Detail</th>
                            <th>Complaint Date</th>
                            <th>Last Update</th>
                            <th>SLG</th>
                        </tr>
                    </thead>

                </table>
            </div>
        </div>
    </div>
</div>

<script>
    var curVal;
    function showTable() {

        $('#complaint').DataTable({
            // Processing indicator
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
                "url": "<?= base_url("ComplainHandling/showdtcomplaint/E"); ?>?" + $('#form-filter :input').serialize(),
                "type": "POST"
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

    function edit(id) {
        curVal = id;
        $('[name="id"]').each(function () {
            if ($(this).val() != curVal) {
                $(this).prop('checked', false);
            }
        });
    }

    function runSearch() {
        showTable();
    }

    function openDetail() {
        if (curVal) {
            window.location = '<?php echo base_url('ComplainHandling/detail_complain/'); ?>' + curVal;
        } else {
            alert('Please select data first');
        }
    }

    function clearForm() {
        $('#form-filter :input').val('');
    }

    $(document).ready(function () {
        showTable();
    });
</script>
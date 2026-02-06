<div class='title-module'>Inbox Caring</div>
<div class="subtitle-module">Complaint Handling &raquo; Inbox Caring</div>
<style>
    .bo {
        border-radius: 8px !important;

    }

    .kiri {
        margin-left: 4px;
        /* padding: 10px; */
    }

    .kanan {
        margin-left: 4px;

    }

    .pelebaran {
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
                <input type="text" name="transactionCode" class="form-control kiri">
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
                <input type="text" name="customerName" class="form-control kiri">
            </div>
            <div class="col-md-2">
                <label for="label-control">MDN Number</label>
            </div>
            <div class="col-md-3">
                <input type="text" name="mdnProblem" class="form-control kiri">
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
        <div class="row mt-1">
            <div class="col-md-2">
                <label for="label-control">Status</label>
            </div>
            <div class="col-md-3">
                <select name="status" id="" class="form-control j kiri">
                    <option value="">-- All Status --</option>
                    <option value="P">Progress</option>
                    <option value="V">Visit</option>
                    <option value="C">Closed</option>
                    <option value="E">Escalation</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="label-control">Channel</label>
            </div>
            <div class="col-md-3">
                <?php
                if ($this->session->userdata('tipe') == '123') {
                ?>
                    <select class="form-control j kanan" name="channel" required="true">
                        <option value="">--All Channel--</option>
                        <option value="Via Call 123">Via Call 123</option>
                        <option value="Via Call 888">Via Call 888</option>
                        <option value="Corporate Customer">Corporate Customer</option>
                        <option value="Facebook">Media Social Facebook</option>
                        <option value="Twitter">Media Social Twitter</option>
                        <option value="Instagram">Media Social Instagram</option>
                        <option value="Telegram">Media Social Telegram</option>
                        <option value="Whatsapp">Media Social Whatsapp</option>
                        <option value="SMS">Media Social SMS</option>
                        <option value="Email">Media Social Email</option>
                        <option value="Webchat">Media Social Webchat</option>
                        <option value="Plaza Telkomcel">Plaza Telkomcel</option>
                        <option value="Other">Other</option>
                    </select>
                <?php
                } else if ($this->session->userdata('tipe') == '147') {
                ?>
                    <select class="form-control j kanan" name="channel" required="true">
                        <option value="">--All Channel--</option>
                        <option value="Via Call 147">Via Call 147</option>
                        <option value="Via Call 888">Via Call 888</option>
                        <option value="WhatsApp 147">WhatsApp 147</option>
                        <option value="Other">Other</option>
                    </select>
                <?php
                } else if ($this->session->userdata('tipe') == '888') {
                ?>
                    <select class="form-control j kanan" name="channel" required="true">
                        <option value="">--All Channel--</option>
                        <option value="Via Call 888">Via Call 888</option>
                        <option value="Other">Other</option>
                    </select>
                <?php
                } else {
                ?>
                    <select class="form-control j" name="channel" required="true">
                        <option value="">--All Channel--</option>
                        <option value="Via Call 123">Via Call 123</option>
                        <option value="Via Call 147">Via Call 147</option>
                        <option value="Via Call 888">Via Call 888</option>
                        <option value="Corporate Customer">Corporate Customer</option>
                        <option value="Facebook">Media Social Facebook</option>
                        <option value="Twitter">Media Social Twitter</option>
                        <option value="Instagram">Media Social Instagram</option>
                        <option value="Telegram">Media Social Telegram</option>
                        <option value="Whatsapp">Media Social Whatsapp</option>
                        <option value="SMS">Media Social SMS</option>
                        <option value="Email">Media Social Email</option>
                        <option value="Webchat">Media Social Webchat</option>
                        <option value="Plaza Telkomcel">Plaza Telkomcel</option>
                        <option value="WhatsApp 147">WhatsApp 147</option>
                        <option value="Other">Other</option>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-md-2">
                <label for="label-control">Complain Type</label>
            </div>
            <div class="col-md-3">
                <select name="complaintType" id="" class="form-control kiri">
                    <?php echo $listComplainType; ?>
                </select>

            </div>
            <div class="col-md-2">

            </div>
            <div class="col-md-3">

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
                        <a href="javascript:runSearch()" class="pelebaran"><i class="fa fa-search"></i> Search</a>
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
                            <th>Channel</th>
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
                "url": "<?= base_url("ComplainHandling/showdtcomplaint/C"); ?>?" + $('#form-filter :input').serialize(),
                "type": "POST"
            },
            //Set column definition initialisation properties
            "columnDefs": [{
                "targets": [0],
                "orderable": false
            }]
        });
    }

    function edit(id) {
        curVal = id;
        $('[name="id"]').each(function() {
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

    $(document).ready(function() {
        showTable();
    });
</script>
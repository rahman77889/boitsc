<div class='title-module'>Complain History</div>
<div class="subtitle-module">Tracking &raquo; Complain History</div>

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
            <div class="col-md-6">

                <div class="row">
                    <div class="col-md-4">
                        <label for="label-control">Transaction Code</label>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="transactionCode" class="form-control j">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="label-control">Customer Name </label>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="customerName" class="form-control j">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="label-control">Location</label>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="btsLocation" class="form-control j">
                    </div>
                </div>

                <div class="row">
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
                <div class="row">
                    <div class="col-md-4">
                        <label for="label-control">Channel</label>
                    </div>
                    <div class="col-md-6">
                        <?php
                        if ($this->session->userdata('tipe') == '123') {
                        ?>
                            <select class="form-control j" name="channel" required="true">
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
                            <select class="form-control j" name="channel" required="true">
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
                                <option value="Via Call 888">Via Call 888</option>
                                <option value="Via Call 147">Via Call 147</option>
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
                <div class="row">
                    <div class="col-md-4">
                        <label for="label-control">Complain Type</label>
                    </div>
                    <div class="col-md-6">
                        <select name="complaintType" id="" class="form-control j">
                            <?php echo $listComplainType; ?>
                        </select>

                    </div>
                </div>
            </div>
            <div class="col-md-6">

                <div class="row">
                    <div class="col-md-4">
                        <label for="label-control">Category</label>
                    </div>
                    <div class="col-md-6">
                        <!-- <input type="text" class="form-control kiri" >	 -->
                        <select name="categoryId" id="categoryId" class="form-control j">
                            <?php echo $listCategory; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="label-control">MDN Number</label>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="mdnProblem" class="form-control j ">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="label-control">Unit</label>
                    </div>
                    <div class="col-md-6">
                        <select name="unitId" id="unitId" class="form-control j ">
                            <?php echo $listUnit; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label for="label-control">User Create</label>
                    </div>
                    <div class="col-md-6">
                        <select name="userId" id="" class="form-control j">
                            <?php echo $listUser; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<br>
<div class="card mt-2 " style="border-radius: 10px;">
    <div class="card-title head-module-action">
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
                <table class="table table-bordered display" id="contoh" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No</th>
                            <th>Transaction Code</th>
                            <th>Cust Name</th>
                            <th>MDN Number</th>
                            <th>Complaint Type</th>
                            <th>Detail Complaint</th>
                            <th>Detail Solution</th>
                            <th>Channel</th>
                            <th>Complaint Status</th>
                            <th>SLA</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                        </tr>

                </table>
            </div>

        </div>
    </div>
</div>

<script>
    var curVal;

    function edit(id, idh) {
        curVal = id + '/' + idh;
        $('.oke').each(function() {
            if ($(this).val() != idh) {
                $(this).prop('checked', false);
            }
        });
    }

    function runSearch() {
        showdata();
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
        showdata();
    });

    function showdata() {
        $('#contoh').DataTable({
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
                "url": "<?= base_url("Tracking/showdtcomplainthistory"); ?>?" + $('#form-filter :input').serialize(),
                "type": "POST"
            },
            //Set column definition initialisation properties
            "columnDefs": [{
                "targets": [0],
                "orderable": false
            }]
        });
    }
</script>
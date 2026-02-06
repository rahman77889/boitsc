<div class="card" style="border-radius: 15px;margin-top:1rem;">
    <div class="card-body">
        <div class="row mt-1">
            <a href="javascript:void" onclick="history.go(-1)" class="btn btn-primary float-right mt-4" style="top: -2rem;position: relative;"><i class="fa fa-arrow-circle-left "></i>Back To Report</a>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Ticket Id</th>
                            <th>Location</th>
                            <th>Open Time</th>
                            <th>Municipio</th>
                            <th>Complain Detail</th>
                            <th>Complain Status</th>
                            <th>Close Time</th>
                            <th>Complain Solution</th>
                            <th>Unit Vendor</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $no = 1;
                        foreach ($data as $row) {
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row->transactionCode; ?></td>
                                <td><?php echo $row->btsLocation; ?></td>
                                <td><?php echo $row->createDate; ?></td>
                                <td><?php echo $row->district; ?></td>
                                <td><?php echo $row->detailComplain; ?></td>
                                <td><?php echo $row->status; ?></td>
                                <td><?php echo $row->createDate; ?></td>
                                <td><?php echo $row->solution; ?></td>
                                <td><?php echo $row->unitName; ?></td>
                            </tr>
                        <?php }; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>
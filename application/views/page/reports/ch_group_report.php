<div class='title-module'>CH Group Report</div>
<div class="subtitle-module">Reports &raquo; CH Group Report</div>
<style>
	#button {
		top: -12px;
		position: relative;
	}

	/* th,td{
        border: 1px solid #b1b2b3 !important;
    } */
	.table {
		border: 1px solid #b1b2b3 !important;
	}

	.tableexport-caption {
		caption-side: top !important;
		margin-bottom: 1rem;
	}

	.tableexport-caption button {
		margin-right: 0.3rem;
		color: #ffffff;
		background-color: #19d895;
		border-color: #19d895;
	}
</style>
<form id="inForm" method="GET" action="" class="smart-form" novalidate="novalidate">


	<div class="card" style="border-radius: 15px">
		<div class="card-body">




			<div class="row mt-1">
				<div class="col-md-2">
					<label for="label-control" style="font-size: 15px">Report Type</label>
				</div>
				<div class="col-md-6">
					<select name="report_type" id="report_type" class="form-control kanan">
						<option value="by_escalation">By Escalation</option>
						<option value="by_category">By Category</option>
						<option value="by_location">By Location</option>
						<option value="by_date">By Date</option>
					</select>
				</div>
			</div>

			<div class="row mt-1">
				<div class="col-md-2">
					<label for="label-control" style="font-size: 15px">User Group</label>
				</div>
				<div class="col-md-6">
					<select name="user_group" id="user_group" class="form-control kanan">

					</select>
				</div>
			</div>

			<div class="row mt-1">
				<div class="col-md-2">
					<label for="label-control" style="font-size: 15px">UserId</label>
				</div>
				<div class="col-md-6">
					<select name="userId" id="userId" class="form-control kanan">
						<?php echo $userList; ?>
					</select>
				</div>
			</div>

			<div class="row mt-1">
				<div class="col-md-2">
					<label for="label-control" style="font-size: 15px">Status</label>
				</div>
				<div class="col-md-6">

					<select name="status" id="status" class="form-control kanan">
						<option value="P">Progress</option>
						<option value="V">Visit</option>
						<option value="C">Closed</option>
						<option value="E">Escalation</option>
					</select>
				</div>
			</div>

			<div class="row mt-1">
				<div class="col-md-2">
					<label for="label-control" style="font-size: 15px">interval</label>
				</div>
				<div class="col-md-3">
					<input type="date" name="tgl1" id="tgl1" class="form-control kanan ">
				</div>
				<p style="position: relative;top: 7px;"> - </p>
				<div class="col-md-3">
					<input type="date" name="tgl2" id="tgl2" class="form-control kanan ">

				</div>
			</div>
			<div class="row mt-1">
				<div class="col-md-2">
					<label for="label-control" style="font-size: 15px">Complaint Type</label>
				</div>
				<div class="col-md-6">
					<select name="complaintType" id="complaintType" class="form-control kanan">
						<?php echo $complaintTypeList; ?>
					</select>
				</div>
			</div>
			<div class="row mt-1">
				<div class="col-md-2">
					<label for="label-control" style="font-size: 15px">Category</label>
				</div>
				<div class="col-md-6">
					<select name="categoryId" id="categoryId" class="form-control kanan">
						<?php echo $listCategory; ?>
					</select>
				</div>
			</div>


		</div>
	</div>

</form>


<!-- content -->

<div class="card" style="border-radius: 15px;margin-top:1rem;">
	<div class="card-body">
		<div class="row">
			<a href="<?= current_url() ?>?<?= http_build_query($this->input->get()) ?>&export=Y" target="_blank" id="button" class="btn btn-success"><i class="fa fa-file-excel-o"></i> Export To Excel</a>
		</div>
		<div class="row mt-1">
			<?php
			switch ($report_type) {
				case 'by_escalation':
			?>

					<div class="table-responsive" style="max-height: 500px">
						<table class="table table-bordered table-hover table-striped" name="ch_group" id="by_escalation">
							<thead>
								<!-- <tr> -->
								<th colspan="16">
									<h6 class="text-center">Unit</h6>
								</th>
								<!-- </tr> -->
								<tr>
									<th style="border: 1px solid #bdbcbc; ">No</th>
									<th style="border: 1px solid #bdbcbc; ">Category</th>
									<?php echo $tableHead; ?>
								</tr>
							</thead>
							<tbody>
								<?php
								echo $content_report;
								?>
							</tbody>
						</table>
					</div>
				<?php
					break;
				case 'by_category':
				?>
					<div class="table-responsive" style="max-height: 500px">
						<table class="table table-bordered table-hover table-striped" name="ch_group" id="by_category">
							<thead>
								<th colspan="16">
									<h6 class="text-center">Complaint Status </h6>
								</th>

								<tr>
									<th>No</th>
									<th>Category</th>
									<?php echo $tableHead; ?>


								</tr>
							</thead>
							<tbody>
								<?php
								echo $content_report;
								?>

							</tbody>
						</table>
					</div>
				<?php
					break;
				case 'by_location':
				?>
					<div class="table-responsive" style="max-height: 500px">
						<table class="table table-bordered table-hover table-striped" name="ch_group" id="by_location">
							<thead>
								<th colspan="16">
									<h6 class="text-center">Complaint Location </h6>
								</th>

								<tr>
									<th>No</th>
									<th>Category</th>
									<?php echo $tableHead; ?>

								</tr>
							</thead>
							<tbody>
								<?php
								echo $content_report;
								?>
							</tbody>
						</table>
					</div>
				<?php
					break;
				case 'by_date':
				?>
					<div class="table-responsive" style="max-height: 500px">
						<table class="table table-bordered table-hover table-striped" name="ch_group" id="by_date">
							<thead>
								<!-- <th colspan="16"><h6 class="text-center">Complaint Location	</h6></th> -->

								<tr>
									<th>No</th>
									<th>Date</th>
									<th>Time</th>
									<th>Channel</th>
									<th>Transaction Code</th>
									<th>MSISDN</th>
									<th>Customer Name</th>
									<th>BTS loc</th>
									<th>Detail Loc</th>
									<th>Detail BTS Location</th>
									<th>Complain Type</th>
									<th>Category</th>
									<th>Sub Category</th>
									<th>Status</th>
									<!--<th>Unit</th>-->
									<th>Solved Date</th>
									<th>Solved Time</th>
									<th>Duration</th>
									<th>Friendliness</th>
									<th>Solution</th>
									<th>Price & Quality</th>
									<th>Network</th>
									<th>Facilities</th>
									<th>Agent</th>
								</tr>
							</thead>
							<tbody>
								<?php
								echo $content_report;
								?>
							</tbody>
						</table>
					</div>
			<?php }
			?>
			<div style="text-align:center;font-size:1rem;margin-top:1.5rem;width:100%">
				Page :
				<select name="page" form="inForm" class="form-control" style="width:4rem">
					<?php
					for ($pi = 1; $pi <= ceil($total_count / 50); $pi++) {
						echo '<option value="' . $pi . '" ' . ($pi == $page ? 'selected' : '') . '>' . $pi . '</option>';
					}
					?>
				</select>
			</div>
		</div>

	</div>
</div>

<script>
	$(document).ready(function() {
		getPrivilege();
		$('#inForm :input').change(function() {
			$('[name="page"]').val('');
			$('#inForm').submit();
		});
		$('[name="page"]').change(function() {
			$('#inForm').submit();
		});

		$('#report_type').val('<?php echo $report_type; ?>');

		$('#status').val('<?php echo $status; ?>');

		$('#tgl1').val('<?php echo $tgl1; ?>');

		$('#tgl2').val('<?php echo $tgl2; ?>');

		$('#complaintType').val('<?php echo $complaintType; ?>');

		$('#categoryId').val('<?php echo $categoryId; ?>');
	});

	function getPrivilege() {
		$.ajax({
			type: "GET",
			url: "<?= base_url("Privileges/getPrivilege"); ?>",
			dataType: "JSON",
			success: function(response) {
				for (const x in response) {
					$('select[name="user_group"]').append('<option value="' + response[x].id + '">' + response[x].privilegeName + '</option>');
				}

				$('select[name="user_group"]').val('<?php echo $user_group; ?>');
				$('select[name="userId"]').val('<?php echo $userId; ?>');
			}
		});
	}
</script>
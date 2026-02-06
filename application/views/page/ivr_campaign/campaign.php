<!-- The Modal Add User -->
<div class='title-module'>Campaign</div>
<div class="subtitle-module">Data Master &raquo; Campaign</div>

<style>
	#huruf {
		font-size: 14px;
		padding: 7px;
		/* margin:20px; */
	}

	.jarak {
		margin: 5px;
	}
</style>

<div class="row flex-grow">

	<?php
	if ($this->session->flashdata("delete")) {
		$this->session->unset_userdata('delete')
	?>
		<script>
			Swal.fire('Success Delete Data!', 'You clicked the button!', 'success')
		</script>
	<?php };
	?>
	<div class="col-12">
		<div class="card" style="border-radius: 10px;">
			<div class="card-title head-module-action">
				<div class="row">
					<div class="col-sm-3">
						<a href="#" style="color: white" data-toggle="collapse" data-target="#demo">
							<i class="fa fa-plus-circle"></i>
							Add Campaign</a>
					</div>
					<div class="col-sm-3">
						<a href="#" style="color: white" data-toggle="collapse" data-target="#demo">
							<i class=" fa fa-table"></i>
							Edit Campaign</a>
					</div>
					<?php
					if ($this->session->userdata('privilege') != '5') {
					?>

						<div class="col-sm-3">
							<a href="#" id="btn-delete" style="color: white">
								<i class="fa fa-times"></i>
								Delete Campaign</a>
						</div>
					<?php
					}
					?>
				</div>
			</div>
			<div class="card-body body-module-action">
				<div id="demo" class="collapse">
					<div class="card">
						<form id="form_submit" method="POST" onsubmit="return false" enctype="multipart/form-data" class="smart-form" novalidate="novalidate">
							<input type="hidden" name="id">
							<div class="card-body">
								<div class="row">
									<div class="col-md-4">
										<label id="huruf" for="">*IVR Slot Robot</label>
									</div>
									<div class="col-md-8">
										<select name="slot" class="form-control">
											<?php
											for ($is = 1; $is <= 50; $is++) {
												echo '<option value="' . $is . '">' . $is . '</option>';
											}
											?>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<label id="huruf" for="">*Target CSV (*.csv)</label>
									</div>
									<div class="col-md-8">
										<input type="file" name="csv" accept=".csv" id="csvFile" class="jarak ">
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<label id="huruf" for="">*Title</label>
									</div>
									<div class="col-md-8">
										<input type="text" name="title" id="title" class="jarak form-control">
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<label id="huruf" for="">*Description</label>
									</div>
									<div class="col-md-8">
										<textarea name="description" id="description" class="jarak form-control" rows="3"></textarea>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<label id="huruf" for="">*Sound IVR (*.mp3)</label>
									</div>
									<div class="col-md-8">
										<input type="file" name="sound" accept=".mp3" id="soundFile" class="jarak ">
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<label id="huruf" for="">*Status Active</label>
									</div>
									<div class="col-md-8">
										<select name="status" id="status" class="form-control jarak">
											<option value="N" selected="">NO</option>
											<option value="Y">Yes</option>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										<button class="btn btn-primary" id="btn_upload" type="button">Save</button>
										<button class="btn btn-default" id="cancel" type="reset">Cancel</button>
									</div>
								</div>
							</div>
						</form>
					</div>

				</div>

				<div class="card mt-2">
					<div class="card-body">
						<table class="table table-bordered display" id="contoh" style="width:100%">
							<thead>
								<th style="width: 15px; text-align: center">
									#
								</th>
								<th style="width: 50px; text-align: center">
									No
								</th>
								<th style="text-align: center">Slot</th>
								<th style="width:10px;text-align: center;">
									Title</th>
								<th style="text-align: center">
									Create Date</th>
								<th style="text-align: center">
									Create by</th>
								<th style="text-align: center">
									Status
								</th>
							</thead>
							<tbody>
						</table>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		showdata();
		btnCari();

		$('#klikedit').click(function() {
			//  e.preventDefault();
			$('#demo').removeClass("collapse");


		});
		$('#cancel').click(function(e) {
			e.preventDefault();
			$('.collapse').collapse('hide');

		});
		$("#btn-delete").click(function() {

			confirm("Are you sure you want to submit this form?", function(result) {
				if (result) {
					$("#form-delete").submit();
				}
			});
		});

	});


	function showdata() {
		// body...
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
				"url": "<?= base_url("IVRCampaign/dtshow"); ?>",
				"type": "POST"
			},
			//Set column definition initialisation properties
			"columnDefs": [{
				"targets": [0],
				"orderable": false
			}]
		});

		// insert uploud file
		$('#btn_upload').unbind('click').click(function(e) {
			e.preventDefault();
			$.ajax({
				url: '<?php echo base_url('IVRCampaign/submit'); ?>',
				type: "post",
				data: new FormData($('#form_submit')[0]),
				processData: false,
				contentType: false,
				cache: false,
				async: false,
				success: function(data) {
					clearInput();
					Swal.fire(
						'Sucess Insert Data!',
						'You clicked the button!',
						'success'
					);

					showdata();
					$('.collapse').collapse('hide');

					window.location = 'ivr_campaign/convert.php';
				}
			});
		});
	}

	// fungsi link delete data
	function hapus(id) {
		$("#btn-delete").attr("href", "<?php echo base_url(); ?>IVRCampaign/delete?id=" + id);
	}

	function edit(id = '') {
		ubahText(id, 'edit');
		hapus(id);

		if (id != '') {

			$.ajax({
					url: '<?= base_url("IVRCampaign/getById?id="); ?>' + id,
					type: 'GET',
					dataType: 'JSON'
				})
				.done(function(data) {
					// debugger;
					$('input[name=id]').val(data.id);
					$('select[name=slot]').val(data.slot);
					$('input[name=title]').val(data.title);
					$('textarea[name=description]').val(data.description);
					$('select[name=status]').val(data.status);
				})
				.fail(function() {
					console.log("error");
				})
				.always(function() {
					console.log("complete");
				});

		}
	}

	function ubahText(categoryId = '', val = '') {

		var r;
		if (val == 'add') {
			r = 'Add';
			$('#txtArea').html(" ");
			$('#txtArea').text(" ");
			$('#txtAction*').text(r);
			$('#upForm').attr('categoryId', '');
			$('#cloneID').html('');
			$('.review').input("disabled", "disabled");
			$(this).attr('disabled', 'disabled');

		} else if (val == 'edit') {
			r = 'Edit';
			$('#txtAction*').text(r);
			$('#txtAction*').removeAttr('disabled');

			$('#inForm').attr('categoryId', '');

			$('#cloneID').html("<input type='hidden' name='categoryId' value='" + categoryId + "'>");
		}

		return r;
	}


	function btnCari() {
		$(document).ready(function() {
			$('#btn-cari').click(function(e) {
				e.preventDefault();

				showdata();

			});
		});
	}


	function clearInput() {
		$('input[name="csv"]').val('');
		$('input[name="sound"]').val('');
		$('input[name="title"]').val('');
		$('[name="description"]').val('');
		$('select[name="status"]').val('');

	}
</script>
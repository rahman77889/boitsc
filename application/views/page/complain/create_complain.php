<div class='title-module'>Create New Ticket</div>
<div class="subtitle-module">Complaint Handling &raquo; Create New Ticket</div>

<div class="card" style="border-radius: 10px;margin-bottom: 27px;padding: 1rem;">
	<div class="row">
		<label for="exampleFormControlSelect1" style="align-self: center;position: relative;left: 20px; font-size: 15px;top:4px; left: 25px">FAQ Information :</label>
		<select class="form-control" id="listFAQ" style="width: 300px; align-self: center;position: relative;left: 30px;">
			<option value="">--Choose--</option>
			<?php
			echo $listFaq;
			?>
		</select>
	</div>
</div>

<form class="forms-sample" id="formComplaint" onsubmit="return false;">
	<input type="hidden" name="id" value="<?php echo $id; ?>">
	<div class="row">
		<div class="col-md-6">
			<div class="card" style="border-radius: 10px">
				<div class="card-body">
					<h4 class="card-title" style=" color: #616161;font-size: 19px; margin-bottom: 22px; ">Customer Information</h4>
					<div class="form-group">
						<label for="exampleInputName1">Transaction Code *</label>
						<input type="text" name="transactionCode" readonly="true" value="<?php echo $transactionCode; ?>" <?php echo $id ? 'disabled' : ''; ?> class="form-control" id="exampleInputName1">
					</div>
					<div class="form-group">
						<label for="exampleInputEmail3">MDN Problem *</label>
						<input type="number" name="mdnProblem" min="67000000000" required="true" value="<?php echo isset($mdnProblem) ? $mdnProblem : ''; ?>" <?php echo $id ? 'disabled' : ''; ?> class="form-control" id="exampleInputEmail3">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Customer Name *</label>
						<input type="text" name="customerName" required="true" value="<?php echo isset($customerName) ? $customerName : ''; ?>" <?php echo $id ? 'disabled' : ''; ?> class="form-control" id="exampleInputPassword4">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Complaint Type *</label>
						<select class="form-control" name="complaintType" required="true" <?php echo $id ? 'disabled' : ''; ?> id="exampleFormControlSelect1">
							<?php echo $listComplaintType; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Channel *</label>
						<select class="form-control" name="channel" required="true" <?php echo $id ? 'disabled' : ''; ?> id="exampleFormControlSelect1">
							<?php
							if ($id) {
								echo '<option value="">' . $channel . '</option>';
							} else {
							?>
								<option value="">--Chose--</option>
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
								<option <?php echo $channel == 'Plaza' ? 'selected' : ''; ?> value="Plaza Telkomcel">Plaza Telkomcel</option>
								<option value="WhatsApp 147">WhatsApp 147</option>
								<option value="Other">Other</option>
							<?php
							}
							?>
						</select>
					</div>

					<div class="form-group">
						<label for="exampleInputPassword4">Contact Person Customer *</label>
						<input type="text" name="contactPersonCustomer" required="true" value="<?php echo isset($contactPersonCustomer) ? $contactPersonCustomer : (isset($customerName) ? $customerName : ''); ?>" <?php echo $id ? 'disabled' : ''; ?> class="form-control" id="exampleInputPassword4">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Municipio</label>
						<input type="text" name="district" required="true" value="<?php echo isset($district) ? $district : ''; ?>" <?php echo $id ? 'disabled' : ''; ?> class="form-control" id="exampleInputPassword4">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">BTS Location *</label>
						<select class="form-control" required="true" <?php echo $id ? 'disabled' : ''; ?> name="btsLocation" id="exampleFormControlSelect1">
							<?php echo $listBts; ?>
						</select>
						<iframe src="" id="locationCustomerHelpdeskComplaint" style="display:none;height: 10rem;width:100%;border:0" frameborder="0" allowfullscreen></iframe>
					</div>
					<div class="form-group">
						<label for="exampleTextarea1">Detail Location Customer</label>
						<textarea class="form-control" required="true" name="detailLocationCustomer" <?php echo $id ? 'disabled' : ''; ?> id="exampleTextarea1" rows="2"><?php echo isset($detailLocationCustomer) ? $detailLocationCustomer : ''; ?></textarea>
					</div>
					<div class="form-group">
						<label for="exampleInputPasswsord4">Detail BTS Location</label>
						<select class="form-control" name="bts_detail" <?php echo $id ? 'disabled' : ''; ?> id="exampleInputPasswsord4">
							<option value="BTS Inner Dili">BTS Inner Dili</option>
							<option value="BTS Outer Dili">BTS Outer Dili</option>
						</select>
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Complain Date (yyyy/mm/dd) *</label>
						<input type="date" name="complainDate" required="true" value="<?php echo isset($complainDate) ? $complainDate : ''; ?>" <?php echo $id ? 'disabled' : ''; ?> class="form-control" id="exampleInputPassword4" placeholder="2019/06/20">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Complain Time (hh:mm) *</label>
						<input type="time" name="complainTime" required="true" value="<?php echo isset($complainTime) ? $complainTime : ''; ?>" <?php echo $id ? 'disabled' : ''; ?> class="form-control" id="exampleInputPassword4" placeholder="18:47">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Category *</label>
						<select class="form-control" required="true" <?php echo $id ? 'disabled' : ''; ?> name="categoryId" id="exampleFormControlSelect1">
							<?php echo $listCategory; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">* Sub Category *</label>
						<select class="form-control" required="true" name="subCategoryId" <?php echo $id ? 'disabled' : ''; ?> id="exampleFormControlSelect1">
							<?php echo $subCategory; ?>
						</select>
					</div>
					<!-- <div class="form-group">
						<label for="exampleInputPassword4">Queue No</label>
						<input type="number" name="queue_no" value="<?php echo isset($queue_no) ? $queue_no : ''; ?>" <?php echo $id ? 'disabled' : ''; ?> class="form-control" id="exampleInputPassword4" placeholder="10">
					</div> -->
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card" style="border-radius: 10px">
				<div class="card-body">
					<h4 class="card-title" style=" color: #616161;font-size: 19px; margin-bottom: 22px; ">Information Issue</h4>
					<div class="form-group">
						<label for="exampleInputName1">Detail Complaint *</label>
						<textarea class="form-control" name="detailComplain" required="true" <?php echo $id ? 'disabled' : ''; ?> id="exampleTextarea1" rows="2"><?php echo isset($detailComplain) ? $detailComplain : ''; ?></textarea>
					</div>
					<div class="form-group">
						<label for="exampleInputEmail3">Detail Solution *</label>
						<textarea class="form-control inputHistory" name="solution" required="true" <?php echo $idh || $close ? 'disabled' : ''; ?> id="exampleTextarea1" rows="2"><?php echo isset($solution) ? $solution : ''; ?></textarea>
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Notes</label>
						<textarea class="form-control inputHistory" name="notes" <?php echo $idh || $close ? 'disabled' : ''; ?> id="exampleTextarea1" rows="2"><?php echo isset($notes) ? $notes : ''; ?></textarea>
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Status *</label>
						<select class="form-control inputHistory" name="status" required="true" <?php echo $idh || $close ? 'disabled' : ''; ?> id="exampleFormControlSelect1">
							<?php echo $listComplainStatus; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Unit *</label>
						<select class="form-control inputHistory" name="unitId" required="true" <?php echo $idh || $close ? 'disabled' : ''; ?> id="exampleFormControlSelect1">
							<?php echo $listUnit; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Solved Date</label>
						<input type="date" name="solvedDate" min="<?php echo isset($complainDate) ? $complainDate : ''; ?>" value="<?php echo isset($solvedDate) && strtotime($solvedDate) > 0 ? $solvedDate : ''; ?>" <?php echo $idh || $close ? 'disabled' : ''; ?> class="form-control inputHistory" id="exampleInputPassword4">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Solved Time</label>
						<input type="time" name="solvedTime" value="<?php echo isset($solvedTime) && $solvedTime != '00:00:00' ? $solvedTime : ''; ?>" <?php echo $idh || $close ? 'disabled' : ''; ?> class="form-control inputHistory" id="exampleInputPassword4">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword4">Agent Front Liner</label>
						<input type="text" readonly="true" value="<?php echo isset($userId) ? $userId : $username; ?>" class="form-control" id="exampleInputPassword4">
					</div>
					<input type="hidden" name="survei">
					<button type="submit" id="btnSubmit" onmouseover="$('[name=survei]').val('')" <?php echo $idh || $close ? 'disabled' : ''; ?> class="btn btn-success mr-2">Save Only</button>
					&nbsp;&nbsp;
					<input type="submit" value="Save & Survei ID" onmouseover="$('[name=survei]').val('CSRATINGIdn')" <?php echo $idh || $close ? 'disabled' : ''; ?> class="btn btn-warning mr-2">
					&nbsp;&nbsp;
					<input type="submit" value="Save & Survei EN" onmouseover="$('[name=survei]').val('CSRATINGEng')" <?php echo $idh || $close ? 'disabled' : ''; ?> class="btn btn-danger mr-2">
					&nbsp;&nbsp;
					<input type="submit" value="Save & Survei TT" onmouseover="$('[name=survei]').val('CSRATINGTet')" <?php echo $idh || $close ? 'disabled' : ''; ?> class="btn btn-info mr-2">
					<!--                    <button class="btn btn-light">Exit</button>-->
				</div>
			</div>
		</div>
	</div>
</form>

<div class="row flex-grow" style="margin-top: 30px">
	<div class="col-12">
		<div class="card" style="border-radius: 10px;">

			<div class="card-body">

				<table class="table table-bordered" id="history" style="width:100%">
					<thead style="background-color: #e8e4e4">
						<tr>
							<th style="text-align: center"> Action </th>
							<th style="text-align: center"> Last Update </th>
							<th style="text-align: center"> Channel </th>
							<th style="text-align: center"> User Update </th>
							<th style="text-align: center"> Solution </th>
							<th style="text-align: center"> User Notes </th>
							<th style="text-align: center"> SLG ("m) </th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="row flex-grow" style="margin-top: 30px;display: none" id="historyTracking">
	<div class="col-12">
		<div class="card" style="border-radius: 10px;">
			<div class="card-body">
				<div class="row">
					<div class="table-responsive">
						<h4 style="margin-bottom:1.5rem">History Complaint On This Number : </h4>

						<button class="btn btn-success btn-sm" onclick="openDetail()" style="margin-bottom: 1rem">Show Detail History</button>
						<table class="table table-bordered display" id="tableHistoryTracking" style="width:100%;">
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
	</div>
</div>

<script>
	var curVal;

	function edit(id, idh) {
		curVal = id + '/' + idh;
		$('[name="id"]').each(function() {
			if ($(this).val() != curVal) {
				$(this).prop('checked', false);
			}
		});
	}

	function openDetail() {
		if (curVal) {
			window.location = '<?php echo base_url('ComplainHandling/detail_complain/'); ?>' + curVal;
		} else {
			alert('Please select data first');
		}
	}

	function getHistoryTracking() {
		$('#tableHistoryTracking').DataTable({
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
				"url": "<?= base_url("Tracking/showdtcomplainthistory"); ?>?transactionCode=&customerName=&btsLocation=&startdate=&enddate=&categoryId=&mdnProblem=" + $('[name="mdnProblem"]').val() + "&unitId=&userId=&channel=" + $('[name="channel"]').val(),
				"type": "POST"
			},
			//Set column definition initialisation properties
			"columnDefs": [{
				"targets": [0],
				"orderable": false
			}]
		});
	}

	$(document).ready(function() {

		$('[name="complainDate"]').change(function() {
			if ($(this).val()) {
				$('[name="solvedDate"]').attr('min', $(this).val());
			}
		});
		$('[name="solvedTime"], [name="solvedDate"]').change(function() {
			if ($('[name="solvedDate"]').val() && $('[name="solvedTime"]').val()) {
				var solved = new Date($('[name="solvedDate"]').val() + ' ' + $('[name="solvedTime"]').val());
				var created = new Date($('[name="complainDate"]').val() + ' ' + $('[name="complainTime"]').val());

				if (created.getTime() > solved.getTime()) {
					$('[name="solvedDate"]').val('');
					$('[name="solvedTime"]').val('');

					alert('Please fill correct solved time');
				}
			}
		});

		$('[name="mdnProblem"]').change(function() {
			if (parseInt($(this).val()) > 67000000000) {
				$.get('<?php echo base_url('CallEstablish/getBTSCode/'); ?>' + $(this).val(), function(res) {
					if (res != '4G') {
						var xr = res.toString().split('_');
						var btsId = xr[0];

						$('[name="btsLocation"]').val(btsId);
						if (btsId) {
							$.get('<?php echo base_url('CallEstablish/getBTSMap/'); ?>' + btsId, function(res) {
								$('#locationCustomerHelpdeskComplaint').show().attr('src', res);
							});
						} else {
							$('#locationCustomerHelpdeskComplaint').hide();
						}
					}
				});

				$.get('<?php echo base_url('CallEstablish/getInfoAnc/'); ?>' + $(this).val() + '/ok', function(res) {
					var res = JSON.parse(res);
					$('[name="customerName"]').val(res.customername);
				});

				$.get('<?php echo base_url('CallEstablish/getInfoSc/'); ?>' + $(this).val(), function(res) {
					$('[name="complaintType"]').val(res);
				});

				$('#historyTracking').show();

				$('[name="channel"]').change(function() {
					getHistoryTracking();
				});

				getHistoryTracking();
			} else {
				$('#historyTracking').hide();
				$('[name="btsLocation"]').val('');
				$('#locationCustomerHelpdeskComplaint').hide();
				$('[name="customerName"]').val('');
				$('[name="complaintType"]').val('');

				$('[name="channel"]').unbind('change');
			}
		});

		$('[name="status"]').change(function() {
			if ($(this).val() == 'C') {
				$('[name="solvedDate"]').val(new Date().toISOString().slice(0, 10));
				$('[name="solvedTime"]').val(new Date().toTimeString().slice(0, 5));
			} else {
				$('[name="solvedDate"]').val('');
				$('[name="solvedTime"]').val('');
			}
		});

		$('#listFAQ').change(function() {
			var faqId = $(this).val();
			var path = '';
			var opsi = $('#listFAQ').find('option[value="' + faqId + '"]');
			if (opsi.data('video') != '') {
				path = '<?php echo base_url(); ?>player/video/?file=' + opsi.data('video');
			} else if (opsi.data('pdf') != '') {
				path = opsi.data('pdf');
			} else {
				path = opsi.data('embed');
			}

			window.open(path, '_blank', 'location=yes,height=570,width=520,scrollbars=yes,status=yes');
		});

		$('[name="categoryId"]').change(function() {
			$.get('<?php echo base_url('ComplainHandling/getSubCategory/'); ?>' + $(this).val(), function(res) {
				$('[name="subCategoryId"]').html('<option value="">--Choose--</option>' + res);
			});
		});

		$('#formComplaint').submit(function(e) {
			e.preventDefault();

			$.ajax({
				'url': '<?php echo $id ? base_url('ComplainHandling/inComplaintHistory/') : base_url('ComplainHandling/inComplaint/'); ?>',
				'type': 'post',
				'dataType': 'json',
				data: $('#formComplaint').serialize()
			}).done(function(data) {

				Swal.fire(
					'Sukses!',
					data.msg,
					'success'
				);

				if ('<?php echo $id; ?>' == '' && data.id && data.idh) {
					window.location = '<?php echo base_url('ComplainHandling/create_complain/'); ?>' + data.id + '/' + data.idh;
				} else {
					if ($('[name="status"]').val() == 'C') { //close
						$('.inputHistory').attr('disabled');
					} else {
						$('.inputHistory').val('');
					}
					showdata();
				}

				if ($('[name="status"]').val() == 'E') {
					//run ajax sent telegram to group
					var formatKonten = '**Ticket Escalation Summary**';
					formatKonten += "\n\n";
					formatKonten += "Transaction Code : `" + $('[name="transactionCode"]').val() + "`";
					formatKonten += "\n";
					formatKonten += "MDN Problem : `" + $('[name="mdnProblem"]').val() + "`";
					formatKonten += "\n";
					formatKonten += "Customer Name : `" + $('[name="customerName"]').val() + "`";
					formatKonten += "\n";
					formatKonten += "Complaint Type : `" + $('[name="complaintType"] option[value="' + $('[name="complaintType"]').val() + '"]').text() + "`";
					formatKonten += "\n";
					//                    formatKonten += "Channel : `" + $('[name="channel"] option[value="' + $('[name="channel"]').val() + '"]').text() + "`";
					//                    formatKonten += "\n";
					//                    formatKonten += "Contact Person Customer : `" + $('[name="contactPersonCustomer"]').val() + "`";
					//                    formatKonten += "\n";
					//                    formatKonten += "Municipio : `" + $('[name="district"]').val() + "`";
					//                    formatKonten += "\n";
					formatKonten += "BTS Location : `" + ($('[name="btsLocation"] option[value="' + $('[name="btsLocation"]').val() + '"]').text() ? $('[name="btsLocation"] option[value="' + $('[name="btsLocation"]').val() + '"]').text() : '-') + "`";
					formatKonten += "\n";
					formatKonten += "Detail Location Customer : `" + $('[name="detailLocationCustomer"]').val() + "`";
					formatKonten += "\n";
					formatKonten += "Complain Date : `" + $('[name="complainDate"]').val() + "`";
					formatKonten += "\n";
					formatKonten += "Complain Time : `" + $('[name="complainTime"]').val() + "`";
					formatKonten += "\n";
					formatKonten += "Category : `" + $('[name="categoryId"] option[value="' + $('[name="categoryId"]').val() + '"]').text() + "`";
					formatKonten += "\n";
					formatKonten += "Sub Category : `" + $('[name="subCategoryId"] option[value="' + $('[name="subCategoryId"]').val() + '"]').text() + "`";
					formatKonten += "\n";
					formatKonten += "Detail Complaint : `" + ($('[name="detailComplain"]').val() ? $('[name="detailComplain"]').val() : '-') + "`";
					formatKonten += "\n";
					//                    formatKonten += "Detail Solution : `" + $('[name="solution"]').val() + "`";
					//                    formatKonten += "\n";
					//                    formatKonten += "Notes : `" + $('[name="notes"]').val() + "`";
					//                    formatKonten += "\n";
					//                    formatKonten += "Status : `" + $('[name="status"] option[value="' + $('[name="status"]').val() + '"]').text() + "`";
					//                    formatKonten += "\n";
					//                    formatKonten += "Unit : `" + $('[name="unitId"] option[value="' + $('[name="unitId"]').val() + '"]').text() + "`";
					//                    formatKonten += "\n";
					//                    formatKonten += "Solved Date : `" + $('[name="solvedDate"]').val() + "`";
					//                    formatKonten += "\n";
					//                    formatKonten += "Solved Time : `" + $('[name="solvedTime"]').val() + "`";
					//                    formatKonten += "\n";
					formatKonten += "Agent Front Liner : `" + $('form .form-control:last').val() + "`";
					formatKonten += "\n";

					$.post('https://hakbesik.telkomcel.tl/handle.act?channel=telegram&a=true&t=bypass_konten', {
						konten: formatKonten
					}, function(res) {
						console.log('result integrai telegram : ' + res);
					});
				}

			}).fail(function() {
				console.log("error");
			}).always(function() {
				console.log("complete");
			});

			return false;
		});

		var btsId = '<?php echo $btsLocation; ?>';
		$('[name="btsLocation"]').val(btsId);
		if (btsId) {
			$('#locationCustomerHelpdeskComplaint').show().attr('src', '<?php echo $btsUrlMap; ?>');
		} else {
			$('#locationCustomerHelpdeskComplaint').hide();
		}

		$('[name="btsLocation"]').change(function() {
			$('#locationCustomerHelpdeskComplaint').hide();
		});

		showdata();

		var complaintTypeVal = '<?php echo $complaintTypeVal; ?>';
		if (complaintTypeVal) {
			getHistoryTracking();
			$('[name="complaintType"]').val(complaintTypeVal);
			$('[name="complainDate"]').attr('readonly', true);
			$('[name="complainTime"]').attr('readonly', true);
			$('[name="complainTime"]').attr('readonly', true);
			<?php if (!$channel) { ?>
				$('[name="channel"]').val('Via Call<?php echo $call_center; ?>');
			<?php } else { ?>
				$('[name="channel"]').val('Plaza Telkomcel');
			<?php } ?>
		}
	});

	function showdata() {

		$('#history').DataTable({
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
				"url": "<?= base_url("ComplainHandling/showdthistory/") . $id; ?>",
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

<style>
	#helpdeskTools .container {
		width: 450px;
		height: auto;
		flex-wrap: wrap;
		-moz-flex-wrap: wrap;
		-webkit-flex-wrap: wrap;
		display: flex;
		display: -moz-flex;
		display: -webkit-flex;
		flex-direction: row;
		-moz-flex-direction: row;
		-webkit-flex-direction: row;
		justify-content: center;
		-moz-justify-content: center;
		-webkit-justify-content: center;
		margin: 0 auto;
		align-content: center;
	}

	#helpdeskTools .number {
		border: 1px solid #a2a2a2;
		width: 100px;
		height: 100px;
		border-radius: 50%;
		display: flex;
		display: -moz-flex;
		display: -webkit-flex;
		flex-direction: column;
		-moz-flex-direction: column;
		-webkit-flex-direction: column;
		justify-content: center;
		-moz-justify-content: center;
		-webkit-justify-content: center;
		justify-content: center;
		align-items: center;
		-moz-align-items: center;
		-webkit-align-items: center;
		margin: 15px;
		transition: ease-in .2s;
		cursor: pointer;
	}

	#helpdeskTools .number:hover {
		border: 1px solid #EEB111;
	}

	#helpdeskTools .number h1 {
		font-weight: 400;
	}

	#helpdeskTools .number .num1 {
		margin-top: -20px;
	}

	#helpdeskTools .number ul {
		list-style: none;
		display: flex;
		display: -moz-flex;
		display: -webkit-flex;
	}

	#helpdeskTools .number ul li {
		padding: 2px;
	}

	#helpdeskTools {
		position: fixed;
		bottom: 0px;
		width: 100%;
		z-index: 10000;
		left: 0px;
		padding: 0.5rem 1rem;
		background-color: white;
		box-shadow: 0px 3px 15px 12px rgba(167, 175, 183, 0.33);
		background-color: #f3db33;
		/*        font-weight:bold;*/
		font-size: 1.35rem;
		height: 3.7rem;
	}

	#helpdeskTools #extend_txt,
	#helpdeskTools #status_call,
	#helpdeskTools #type_call {
		font-weight: bold;
	}

	#helpdeskTools .highlight-text {
		color: #f86a47;
		background-color: white;
		padding: 0.2rem 1rem;
		border-radius: 3rem;
	}

	#helpdeskTools .imgTools {
		height: 4rem;
	}

	#helpdeskTools .toolsAction {
		cursor: pointer;
		margin-top: 0.5rem;
	}

	#helpdeskTools .toolsAction:hover {
		background-color: rgba(255, 255, 255, 0.8117647058823529);
		border-radius: 10rem;
		padding: 0.5rem;
	}

	.checkboxHelpdesk {
		opacity: 0;
	}

	.switchHelpdesk {
		position: relative;
		top: -2.2rem;
	}

	.switchHelpdesk>div {
		width: 80px;
		height: 40px;
		/*        background: linear-gradient(20deg, #9c2f9e, #ff6e40) !important;*/
		z-index: 0;
		cursor: pointer;
		position: relative;
		border-radius: 50px;
		line-height: 40px;
		text-align: right;
		padding: 0 10px;
		color: rgba(0, 0, 0, .5);
		transition: all 250ms;
		box-shadow: inset 0 3px 15px -3px
	}

	.switchHelpdesk>input:checked+div {
		background: #ff6e40;
		text-align: left;
		color: rgba(255, 255, 255, .75);
	}

	.switchHelpdesk>div:before {
		content: '';
		display: inline-block;
		position: absolute;
		left: 0;
		top: -2px;
		height: 44px;
		width: 44px;
		background: linear-gradient(#f9f9f9 30%, #CDCDCD);
		border-radius: 50%;
		transition: all 200ms;
		box-shadow: 0 15px 15px -3px rgba(0, 0, 0, .25), inset 0 -2px 2px -3px, 0 3px 0 0px #f9f9f9;
	}

	.switchHelpdesk>div:after {
		content: '';
		display: inline-block;
		position: absolute;
		left: 11px;
		top: 11px;
		height: 22px;
		width: 22px;
		background: linear-gradient(20deg, #9c2f9e, #ff6e40) !important;
		border-radius: 50%;
		transition: all 200ms;
	}

	.switchHelpdesk>input:checked+div:after {
		left: 52px;
	}

	.switchHelpdesk>input:checked+div:before {
		content: '';
		position: absolute;
		left: 40px;
		border-radius: 50%;
	}
</style>

<!-- Modal -->
<div class="modal fade" id="modalIncoming" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body" style="
				 background: linear-gradient(45deg, #8e24aa, #ff6e40) !important;
				 color: white;
				 padding:3rem;
				 ">
				<h4>
					<span id="callCenterCaller"
						style="font-weight: bold;text-align: center;display: block;font-size: 2rem;"></span>
					<img style="height: 2.6rem;"
						src="<?php echo base_url('template/assets/images/call/003-telephone.svg'); ?>">
					Call incoming from <span id="numberCaller">+670XXXXXXXX</span>
					<br>
					<span id="nameCaller" style="margin-left: 3rem;text-decoration: underline"></span>
				</h4>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger btn-secondary btn-lg" onclick="endCall()"
					data-dismiss="modal">Reject</button>
				<button type="button" class="btn btn-primary btn-lg" onclick="apiAnswerCall(currentCallId, false)"
					data-dismiss="modal">Answer</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalTransferCall" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
	aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body" style="
				 background: linear-gradient(45deg, #8e24aa, #ff6e40) !important;
				 color: white;
				 padding:3rem;
				 ">
				<h4>
					<span id="callCenterCaller"
						style="font-weight: bold;text-align: center;display: block;font-size: 2rem;"></span>
					<img style="height: 2.6rem;" src="<?php echo base_url('template/assets/images/call-back.svg'); ?>">
					Transfer Call To :
					<br>
					<select id="selUsExt">
					</select>
				</h4>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger btn-secondary btn-lg"
					onclick="apiTransferCall(currentCallId, '9' + $('#selUsExt').val())" data-dismiss="modal">Transfer
					Now</button>
				<button type="button" class="btn btn-primary btn-lg" data-dismiss="modal">Cancel Transfer</button>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo base_url('template/assets/bria/bria_api_constants.js'); ?>"></script>
<script src="<?php echo base_url('template/assets/bria/bria_api_js.js'); ?>?u=1z000dd3s"></script>
<?php
$activeCall = '';
$notActiveCall = '';
if ($this->session->userdata('tipe') == '123') {
	$activeCall = '*01';
	$notActiveCall = '*02';
} else if ($this->session->userdata('tipe') == '147') {
	$activeCall = '*03';
	$notActiveCall = '*04';
} else if ($this->session->userdata('tipe') == '888') {
	$activeCall = '*001';
	$notActiveCall = '*002';
}
?>
<script>
	var hideHD = true;
	var currentCallId = '';
	var urlRingtone = '<?php echo base_url('template/assets/ringtone.mp3'); ?>';
	jq(document).ready(function () {
		var activeCall = '<?php echo $activeCall; ?>';
		var notActiveCall = '<?php echo $notActiveCall; ?>';
		if (localStorage['logged'] == 'Y') {
			$.get('<?php echo base_url('CallEstablish/setStatusAdc/Y'); ?>', function (res) {
				if (res.success) {
					placeCall(activeCall + '<?php echo $this->session->userdata('extend_number'); ?>', true);
				}
			});
			localStorage['logged'] = 'N';
		}

		$('.checkboxHelpdesk').click(function () {
			if ($(this).is(':checked')) { //join group
				$.get('<?php echo base_url('CallEstablish/setStatusAdc/Y'); ?>', function (res) {
					if (res.success) {
						placeCall(activeCall + '<?php echo $this->session->userdata('extend_number'); ?>', true);
					}
				});
			} else { //left group
				//                                $.get('<?php echo base_url('CallEstablish/setStatusAdc/N'); ?>', function (res) {
				//                                    if (res.success) {
				//                                        placeCall(notActiveCall + '<?php echo $this->session->userdata('extend_number'); ?>', true);
				//                                    }
				//                                });
			}
		});
		$('.logoutLink').click(function () {
			if (confirm('Are you sure want to logout?')) {
				$.get('<?php echo base_url('CallEstablish/setStatusAdc/N'); ?>', function (res) {
					if (res.success) {
						try {
							placeCall(notActiveCall + '<?php echo $this->session->userdata('extend_number'); ?>', true);
						} catch (e) {

						}
						setTimeout(function () {
							window.location = '<?= site_url('Login/logout'); ?>';
						}, 1000);
					}
				});
			}
		});

		$('.btnSubmitActivity').click(function () {
			var type = $(this).data('type');

			$('#inputActivity').val(type);

			$('#btnsubmitForm').trigger('click');
		});

		var sc_history = '';
		var name_history = '';
		var msisdn_history = '';

		$(document).on('click', '#btnCreateActivity', function () {
			Swal.showLoading();

			$.get('<?= base_url('Display/startQueue'); ?>', function (res) {
				Swal.close();

				$('#date_history').val(res.date);
				$('#msisdn_history').val(res.msisdn);
				$('#name_history').val(res.name);
				$('#queue_no_history').val(res.queue_no);
				sc_history = res.sc;
				name_history = res.name;
				msisdn_history = res.msisdn;

				$('#modalPlazaActivity').modal('show');
			});
		});

		$('#formActivity').submit((e) => {
			e.preventDefault();

			$.ajax({
				type: "POST",
				data: $('#formActivity').serialize(),
				url: "<?= base_url("CallController/createActivity"); ?>",
				dataType: "JSON",
				success: function (json) {
					if (json.status) {
						Swal.fire(
							'Sukses!',
							json.msg,
							'success'
						);

						$('#modalPlazaActivity').modal('hide');
						var type = $('#inputActivity').val();

						if (type == 'complaint') {
							window.location = '<?= base_url('ComplainHandling/create_complain/'); ?>?msisdn=' + msisdn_history + '&name=' + name_history + '&sc=' + sc_history + '&call_center=&channel=Plaza'
						}
					} else {
						Swal.fire(
							'Failed!',
							json.msg,
							'error'
						);
					}
				}
			});

			return false;
		});
	});

	function transferCallEstablish() {
		//                        if (!currentCallId) {
		//                            alert('Your status is being idle');
		//                        } else {
		$.get('<?php echo base_url('CallEstablish/getListAgentActive'); ?>', function (res) {
			if (res.count > 0) {
				$('#selUsExt').html(res.list);
				$("#modalTransferCall").modal({
					backdrop: 'static',
					keyboard: false
				});
				$.get('<?php echo base_url('CallEstablish/setTransferNow'); ?>', function (res) {

				});
			} else {
				alert('There\'s no stand by agent');
			}
		});
		//                        }
	}

	function helpdeskToolsSlide() {
		if (hideHD) { //open
			hideHD = false;
			$('i.fa.fa-angle-up').removeClass('fa-angle-up').addClass('fa-angle-down');
			$('#helpdeskTools').animate({
				'height': '30rem'
			}, 200);
		} else { //close
			hideHD = true;
			$('i.fa.fa-angle-down').removeClass('fa-angle-down').addClass('fa-angle-up');
			$('#helpdeskTools').animate({
				'height': '3.7rem'
			}, 200);
		}
	}

	function helpdeskCekEstablish() {
		$.get('<?= base_url("CallEstablish/get"); ?>', function (res) {

			if (!res || !res.status) {
				return false;
			}

			localStorage['status'] = res.status.status;
			localStorage['status_adc'] = res.status.status_adc;
			localStorage['status_call_msisdn'] = res.status.status_call_msisdn;
			localStorage['status_call_start'] = res.status.status_call_start;
			localStorage['status_call_type'] = res.status.status_call_type;
			localStorage['status_call_language'] = res.status.status_call_language;
			if (res.status.status_adc == 'Y') {
				$('.checkboxHelpdesk').prop('checked', true);
			} else {
				$('.checkboxHelpdesk').prop('checked', false);
			}

			try {
				if (res.status.status == 'logout') {
					$('#helpdeskTools').hide();
				} else {
					$('#helpdeskTools').show();
					if (res.status.status == 'stand_by') {
						helpdeskReset();
					} else {

						$('#linkCreateComplaint').data('msisdn', res.status.status_call_msisdn.toString().split('+').join(''));
						$('#status_call').text(res.status.status_call_msisdn);
						//                                        $('#type_call').text(ucwords(res.status.status_call_type));
						$('#language_call').text(ucwords(res.status.status_call_language));
						var shortBahasa = '';
						switch (res.status.status_call_language) {
							case 'Indonesia':
								shortBahasa = ' (ID)';
								break;
							case 'Tetum':
								shortBahasa = ' (TL)';
								break;
							case 'English':
								shortBahasa = ' (EN)';
								break;
						}
						var callCenter = res.status.status_call_center_number != null ? res.status.status_call_center_number : '';
						$('#type_call').text(ucwords(res.status.status_call_type) + shortBahasa + ' ' + callCenter);
						$('#ivr_language').text(res.status.status_call_language);
						$('#cug_customer').text(res.cug);
						if ($('#providerInfo tbody').html() == '') {
							$.get('<?= base_url("CallEstablish/getDetail"); ?>', function (res) {

								$('#linkCreateComplaint').data('nama', res['customer'] != null ? res.customer.customername : '');
								$('#linkCreateComplaint').data('serviceClass', (res.provider != null ? res.provider.serviceClass : ''));
								$('#linkCreateComplaint').data('call_center', (res.call_center != null ? res.call_center : ''));
								$('#providerInfo tbody').html('');
								var customer = res.provider;
								try {
									appendCustomerInfo('MSIDN', customer.msisdn);
									appendCustomerInfo('Mytelkomcel Point', customer.point);
									appendCustomerInfo('Account Value', '$ ' + customer.accountValue);
									if (customer['accountValuePostpaid'] != null) {
										appendCustomerInfo('CL Postpaid', '$ ' + customer.accountValuePostpaid);
									}
									appendCustomerInfo('Service Class', customer.serviceClass);
									appendCustomerInfo('Expired Date', customer.supervisionExpiryDate);
									appendCustomerInfo('Package Data', customer.data);
									appendCustomerInfo('Package SMS', customer.sms);
									appendCustomerInfo('Package Voice', customer.voice);
									appendCustomerInfo('Package Monetary', customer.monetary);
									//                if (hideHD) {
									//                    helpdeskToolsSlide();
									//                }

									if (res['customer'] != null) {
										$('#anc_name').text(res.customer.customername);
										$('#anc_address').text(res.customer.customeraddress);
										$('#anc_card_number').text(res.customer.idcard);
										$('#anc_card_type').text(res.customer.idtype);
										$('#anc_reg_date').text(res.customer.registerdate);
									}

									if (res['device'] != null) {
										$('#device_marketing_name').text(res.device.marketing_name);
										$('#device_manufaktur').text(res.device.manufacturer_or_applicant);
										$('#device_model').text(res.device.mode_name);
										$('#device_band').text(res.device.band);
										$('#device_os').text(res.device.operating_system);
										$('#device_dev_type').text(res.device.device_type);
									}

									$('#anc_bts_info').text(res.bts != null ? res.bts.site_name : 'Location not available');
									if (res.bts != null) {
										if (!$('#locationCustomerHelpdesk').is(':visible')) {
											$('#locationCustomerHelpdesk').show().attr('src', res.bts.urlmap);
										}
									}
								} catch (e) {
									console.log(e);
								}

							});
						}

					}
				}
			} catch (e) { }
		}).fail(function () {

		});
	}

	function appendCustomerInfo(param, val) {
		if (val['size'] != null) {
			if (val['size'] > 0) {
				var collct = '<table>';
				collct += '<tr><th>Quota</th><th>Name</th><th>Expire Date</th></tr>';
				for (var p in val.packageList) {
					var quota = val.packageList[p]['quata'];
					var name = val.packageList[p]['name'];
					var expireDate = val.packageList[p]['expireDate'];
					if (quota == 'UNLIM') {
						quota = 'Unlimited';
					}

					collct += '<tr><td>' + quota + '</td><td>' + name + '</td><td>' + expireDate + '</td></tr>';
				}
				collct += '</table>';
				collct += '<b style="margin-top:0.5rem">Total : ' + (val.total == 'UNLIM' ? 'Unlimited' : val.total) + '</b>';
				val = collct;
			} else {
				val = 'No package active';
			}
		}
		$('#providerInfo tbody:first').append('<tr><td>' + ucwords(param) + '</td><td>' + val + '</td></tr>');
	}

	function helpdeskReset() {
		//        if (!hideHD) {
		//            helpdeskToolsSlide();
		//        }

		$('.itemHelpdesk, .anc_value').text('');
		$('.itemHelpdeskStatus').text('Idle');
		$('#linkCreateComplaint').data('msisdn', '');
		$('#linkCreateComplaint').data('nama', '');
		$('#linkCreateComplaint').data('call_center', '');
		$('#locationCustomerHelpdesk').html('').hide();
		$('#providerInfo tbody').html('');
	}

	function ucwords(str) {
		return (str + '')
			.replace(/^(.)|\s+(.)/g, function ($1) {
				return $1.toUpperCase()
			})
	}

	function goto360() {
		Swal.showLoading();
		$.get('<?= base_url('External/toLogin360'); ?>', function (res) {
			Swal.close();

			if (res.status) {
				// window.location = 'https://helpdesk360.telkomcel.tl/auth/sso-login/' + res.username + '/' + res.password + '/general-info'; //?msisdn=67073725096
				var url = '';
				if (res.msisdn) {
					// url = 'https://dashboard360.shiblysolution.id/auth/sso-login/' + res.username + '/' + res.password + '/customer-information?msisdn=' + res.msisdn
					url = 'https://helpdesk360.telkomcel.tl/auth/sso-login/' + res.username + '/' + res.password + '/customer-information?msisdn=' + res.msisdn
				} else {
					// url = 'https://dashboard360.shiblysolution.id/auth/sso-login/' + res.username + '/' + res.password + '/general-info'; //?msisdn=67073725096
					url = 'https://helpdesk360.telkomcel.tl/auth/sso-login/' + res.username + '/' + res.password + '/general-info'; //?msisdn=67073725096
				}

				window.open(url, '_blank').focus();
			} else {
				Swal.fire(
					'Failed!',
					'Failed generate link SSO, please ask administrator',
					'error'
				);
			}
		});
	}

	setInterval(helpdeskCekEstablish, 5000);
</script>
<div class="modal fade" id="modalPlazaActivity" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content" style="width: 130%!important;left: -15%;">
			<div class="modal-body" style="
				 background: linear-gradient(45deg, #8e24aa, #ff6e40) !important;
				 color: white;
				 padding:3rem;
				 ">
				<h4>
					<span id="callCenterCaller"
						style="font-weight: bold;text-align: center;display: block;font-size: 2rem;"></span>
					<!-- <img style="height: 2.6rem;" src="<?php echo base_url('template/assets/images/call/003-telephone.svg'); ?>"> -->
					Create Plaza Activity
					<br>
					<form class="forms-sample" id="formActivity" name="formActivity" style="margin-top:2rem">
						<input type="hidden" name="activity" id="inputActivity">
						<div class="row">
							<div class="col-12">
								<div class=" form-group">
									<label>Datetime</label>
									<input style="width:100%" class="form-control" type="datetime-local"
										id="date_history" required name="date_history">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<div class=" form-group">
									<label>MSISDN</label>
									<input style="width:100%" class="form-control" type="number" min="67073000000"
										id="msisdn_history" name="msisdn" placeholder="67073000000">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<div class=" form-group">
									<label>Name</label>
									<input style="width:100%" class="form-control" type="text" id="name_history"
										name="name" placeholder="">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Queue Number</label>
									<input style="width:100%" class="form-control" type="number" min="1"
										id="queue_no_history" required name="queue_no" placeholder="1">
								</div>
							</div>
						</div>
						<button type="submit" id="btnsubmitForm" style="display:none">submit</button>
					</form>
				</h4>
			</div>
			<div class="modal-footer">
				<button data-type="information" class="btnSubmitActivity btn btn-success btn-lg">Information</button>
				<button data-type="registration" class="btnSubmitActivity btn btn-primary btn-lg">Registration</button>
				<button data-type="complaint" class="btnSubmitActivity btn btn-info btn-lg">Complaint</button>
				<button data-type="penjualan" class="btnSubmitActivity btn btn-warning btn-lg">Purchase</button>
			</div>
		</div>
	</div>
</div>
<?php
if ($this->session->userdata('extend_number') && $this->session->userdata('extend_number') != '') {
	?>
	<div id="helpdeskTools">
		<div class="row" style="height: 4rem;">
			<div class="col-md-2" style="text-align: center;cursor: pointer" title="Click to call number"><img onclick="window.location = '<?php echo base_url('SmartCall/call_number'); ?>'
								;" style="height: 2.6rem;" src="<?= base_url('template/'); ?>assets/images/call/003-telephone.svg">
				<label class="switchHelpdesk">
					<input type="checkbox" disabled class="checkboxHelpdesk" />
					<div class=""></div>
				</label>
			</div>
			<div class="col-md-2" style="padding-top: 0.5rem;">Extend : <span id="extend_txt"
					class="highlight-text"><?php echo $this->session->userdata('extend_number'); ?></span></div>
			<div class="col-md-3" style="padding-top: 0.5rem;text-align: center">MSISDN : <span id="status_call"
					class="highlight-text itemHelpdeskStatus">Idle</span></div>
			<div class="col-md-3" style="padding-top: 0.5rem;text-align: center">Type : <span id="type_call"
					class="highlight-text itemHelpdeskStatus">Idle</span></div>
			<div class="col-md-2" style="text-align: right">
				<img onclick="transferCallEstablish()"
					style="height: 2.6rem;cursor:pointer;margin-top:-1rem;margin-right: 1rem;"
					src="<?= base_url('template/'); ?>assets/images/call-back.svg">
				<i onclick="helpdeskToolsSlide()" class="fa fa-angle-up highlight-text"
					style="font-size: 2.5rem;font-weight: bold;cursor: pointer;padding: 0rem 0.5rem;"></i>
			</div>
		</div>
		<div class="row" style="margin-top:0rem;padding:0.5rem 0rem;">
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-12">
						<div
							style="padding: 0.5rem 1.7rem;background-color: white;border-radius: 1rem;max-height: 15rem;overflow-x: auto">
							<table>
								<tr>
									<td>Language Selected</td>
									<td>:</td>
									<td><span id="ivr_language" class="anc_value"></span></td>
								</tr>
								<tr>
									<td>CUG</td>
									<td>:</td>
									<td><span id="cug_customer" class="anc_value"></span></td>
								</tr>
								<tr>
									<td>Name</td>
									<td>:</td>
									<td><span id="anc_name" class="anc_value"></span></td>
								</tr>
								<tr>
									<td>Address</td>
									<td>:</td>
									<td><span id="anc_address" class="anc_value"></span></td>
								</tr>
								<tr>
									<td>ID Card Number</td>
									<td>:</td>
									<td><span id="anc_card_number" class="anc_value"></span></td>
								</tr>
								<tr>
									<td>ID Card Type</td>
									<td>:</td>
									<td><span id="anc_card_type" class="anc_value"></span></td>
								</tr>
								<tr>
									<td>Register Date</td>
									<td>:</td>
									<td><span id="anc_reg_date" class="anc_value"></span></td>
								</tr>
								<tr>
									<td>BTS Info</td>
									<td>:</td>
									<td><span id="anc_bts_info" class="anc_value"></span></td>
								</tr>
								<tr>
									<td nowrap>Device Name</td>
									<td>:</td>
									<td><span id="device_marketing_name" class="anc_value"></span></td>
								</tr>
								<tr>
									<td nowrap>Device Manufacture</td>
									<td>:</td>
									<td><span id="device_manufaktur" class="anc_value"></span></td>
								</tr>
								<tr>
									<td>Device Model</td>
									<td>:</td>
									<td><span id="device_model" class="anc_value"></span></td>
								</tr>
								<tr>
									<td valign="top">Device Band</td>
									<td valign="top">:</td>
									<td><span id="device_band" class="anc_value"></span></td>
								</tr>
								<tr>
									<td>Device OS</td>
									<td>:</td>
									<td><span id="device_os" class="anc_value"></span></td>
								</tr>
								<tr>
									<td>Device Type</td>
									<td>:</td>
									<td><span id="device_dev_type" class="anc_value"></span></td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top:1rem">
					<div class="col-md-12">
						<iframe src="" id="locationCustomerHelpdesk" style="display:none;height: 10rem;width:100%;border:0"
							frameborder="0" allowfullscreen></iframe>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-12">
						<div style="overflow-x: auto;height: 15rem;background-color: white;border-radius: 1rem;">
							<table class="table" id="providerInfo">
								<thead>
									<tr>
										<th>Parameter</th>
										<th>Value</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="row" style="margin-top:1rem;padding-top:1rem;">
					<div style="max-width:19%!important;text-align:center" class="col-md-3 toolsAction" onclick="goto360();"
						style="text-align: center;"><img style="display:block;margin: 0 auto;" class="imgTools"
							src="<?= base_url('template/'); ?>img/agent_login.svg">CRM 360</div>
					<div style="max-width:19%!important;text-align:center" class="col-md-3 toolsAction" onclick="endCall();"
						style="text-align: center;"><img style="display:block;margin: 0 auto;" class="imgTools"
							src="<?= base_url('template/'); ?>assets/images/call/no-chatting.svg">Hangup</div>
					<div style="max-width:19%!important;text-align:center" class="col-md-3 toolsAction"
						onclick="window.location = '<?= base_url('faq'); ?>'" style="text-align: center;"><img
							style="display:block;margin: 0 auto;" class="imgTools"
							src="<?= base_url('template/'); ?>assets/images/call/004-faq.svg">Information</div>
					<div style="max-width:19%!important;text-align:center" class="col-md-3 toolsAction"
						onclick="window.open('http://103.30.115.1:6363/evoucher/main/main.zul?' + $(this).next().data('msisdn'), 'popUpWindow', 'height=400, width=600, left=1, top=1, resizable=yes, scrollbars=yes, toolbar=no, menubar=no, location=no, directories=no, status=no');"
						id="linkEvoucher" style="text-align: center"><img style="display:block;margin: 0 auto;"
							class="imgTools" src="<?= base_url('template/'); ?>assets/images/call/evoucher.svg">E-Voucher
					</div>
					<div style="max-width:19%!important;text-align:center" class="col-md-3 toolsAction"
						onclick="window.location = '<?= base_url('ComplainHandling/create_complain/'); ?>?msisdn=' + $(this).data('msisdn') + '&name=' + $(this).data('nama') + '&sc=' + $(this).data('serviceClass') + '&call_center=' + $(this).data('call_center');"
						id="linkCreateComplaint" style="text-align: center"><img style="display:block;margin: 0 auto;"
							class="imgTools" src="<?= base_url('template/'); ?>assets/images/call/009-sad.svg">Complaint
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}


if ($this->session->userdata('id_counter')) {
	$detCounter = $this->db->select('*')->from('queue')->where('id', $this->session->userdata('id_counter'))->get()->row_array();
	$detCounterSetting = $this->db->select('*')->from('queue_setting')->where('id', $this->session->userdata('id_counter_setting'))->get()->row_array();
	?>
	<div
		style="position: fixed;bottom:4.5rem;z-index: 10;right: 1rem;width: 30.5rem;text-align: center;background-color: white;padding: 0.5rem;border: 2px #d9566782 solid;border-radius: 2rem;">
		<div style="font-size:1rem;color:rgb(0 0 0 / 74%)">Counter <?php echo $detCounterSetting['judul']; ?></div>
		<div style="font-size:1.5rem;color:rgb(255 0 0 / 74%)">Counter <?php echo $detCounter['counter']; ?></div>
		<div style="font-size:1.3rem;color:blue;margin-top:0.3rem">No : <?php echo $detCounter['nomor']; ?></div>
		<button class="btn btn-success" style="margin-top:0.3rem"
			onclick="window.location = '<?php echo base_url('Display/nextQueue'); ?>'">Next Queue</button>
		<button class="btn btn-warning" style="margin-top:0.3rem"
			onclick="window.location = '<?php echo base_url('Display/nextQueue/N/Y'); ?>'">Repeat Call</button><br>
		<button class="btn btn-primary" style="margin-top:0.3rem" id="btnCreateActivity">Start Queue</button>
		<button class="btn btn-secondary" style="margin-top:0.3rem" id="btnFinishActivity"
			onclick="window.location = '<?php echo base_url('Display/finishLastQueue'); ?>'">Finish Queue</button>
		<!-- <button class="btn btn-primary" style="margin-top:0.3rem" onclick="window.location = '<?php echo base_url('Display/startQueue'); ?>'">Start Queue</button> -->
		<button class="btn btn-danger" style="margin-top:0.3rem"
			onclick="confirm('Are you sure want to reset queue ? ') ? window.location = '<?php echo base_url('Display/nextQueue/Y/N'); ?>' : ''">Reset
			Queue</button>
	</div>
	<?php
}
?>

<footer class="footer" style="margin-bottom: 3rem;margin-top:10rem">
	<div class="container-fluid clearfix">
		<span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © 2019 <a
				href="http://telkomcel.tl" target="_blank">Telkomcel</a>. All rights reserved.</span>
		<!-- <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="mdi mdi-heart text-danger"></i> -->
		<!--    </span>-->
	</div>
</footer>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<?php
if ($this->session->userdata('privilege') == 4) {
	?>
	<div class="row" style="margin-top:2rem;">
		<div class="col-md-4">
			<div id="plaza_chart" class="chart_wrap"></div>
		</div>
		<!-- <div class="col-md-4">
			<div style="text-align:center;border:5px solid #f81818ff;padding:1rem;border-radius:1rem;">
				<div>
					<h4 style="font-weight:bold">Plaza Activity</h4>
				</div>
				<br>
				<br>
				<div id="plaza_history" class="chart_wrap"><button id="btnCreateActivity" class="btn btn-info btn-lg" style="width: 100%;">Create Activity</button></div>
			</div>
		</div> -->
		<div class="col-md-4">
			<div id="topCso" class="chart_wrap"></div>
		</div>
		<div class="col-md-4">
			<div id="rating_cso" class="chart_wrap"></div>
		</div>
	</div>

	<script>
		$(document).ready(function () {

			currentCallCenter = '<?php echo $this->session->userdata('tipe'); ?>';

			getDasboard();

			setInterval(function () {
				currentCallCenter = '<?php echo $this->session->userdata('tipe'); ?>';
				getDasboard();
			}, 10000);
		});

		function getDasboard() {
			var val = currentCallCenter;
			var add = '';
			if (val) {
				var dt = new Date();

				var dd = dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate();

				add = '/' + dd + '/' + dd + '/' + val;
			}
			$.ajax({
				type: "GET",
				url: "<?= base_url("CallController/getDashboardPlaza"); ?>" + add,
				dataType: "JSON",
				success: function (json) {
					Highcharts.chart('plaza_chart', {
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							type: 'pie'
						},
						title: {
							text: 'Plaza Activity'
						},
						tooltip: {
							// pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
							pointFormat: '{series.name}: <b>{point.y}</b>'

						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: false
								},
								showInLegend: true,
								animation: false
							}
						},
						series: [{
							name: 'Total',
							colorByPoint: true,
							data: json.plazaActivity
						}]
					});
					Highcharts.chart('topCso', {
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							type: 'pie'
						},
						title: {
							text: 'Top CSO'
						},
						tooltip: {
							// pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
							pointFormat: '{series.name}: <b>{point.y}</b>'

						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: false
								},
								showInLegend: true,
								animation: false
							}
						},
						series: [{
							name: 'CSO',
							colorByPoint: true,
							data: json.topCso
						}]
					});
					Highcharts.chart('rating_cso', {
						chart: {
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false,
							type: 'pie'
						},
						title: {
							text: 'Average Rating Satisfaction',
						},
						subtitle: {
							text: 'Total Voters : ' + json.total_voters
						},
						tooltip: {
							// pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
							pointFormat: '{series.name}: <b>{point.y}</b>'

						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: false
								},
								showInLegend: true,
								animation: false
							}
						},
						series: [{
							name: 'Indicator',
							colorByPoint: true,
							data: json.rating_cso
						}]
					});
				}
			});
		}
	</script>
	<?php
} else {
	?>

	<style>
		.kecil_img {
			width: 2.7rem;
		}

		.kecil_kata {
			font-size: 12px;
			margin: 0px;
			margin-top: 7px !important;
		}

		.agentLogout {
			background-color: white;
		}

		.agentLogin {
			background-color: #c3ffc3;
		}

		.agentReady {
			background-color: #ffffc0;
		}

		.agentBusy {
			background-color: #ffcfcf;
		}

		.numberLog {
			font-size: 1.5rem;
		}

		.chart_wrap {}

		#tableAgent tr td {
			padding: 0.5rem 1rem;
		}

		.tab-call-center {
			background-color: #f3db33;
			padding: 0.5rem;
			cursor: pointer;
		}

		.tab-call-center.tab-active {
			background: linear-gradient(45deg, #8e24aa, #ff6e40) !important;
			padding: 0.5rem;
			color: white;
			cursor: pointer;
		}
	</style>

	<!--<div class='title-module' style="margin-top:-15px">Dashboard Helpdesk - <b>Telkomcel &copy;</b></div>
	<div class="subtitle-module">Below feature to could monitor call center performance dashboard report</div>-->

	<div class="row" style="color: #655d5d;">
		<div class="col-md-5" style="height: 16rem;overflow-x: hidden">
			<table class="table table-hover" id="tableAgent"></table>
		</div>

		<div class="col-md-7">
			<?php
			if ($this->session->userdata('tipe') != '123' && $this->session->userdata('tipe') != '147') {
				?>
				<div class="row" style="font-weight: bold">
					<div class="col-md-3 text-center tab-call-center tab-active" data-val="">
						All Number
					</div>
					<div class="col-md-3 text-center tab-call-center" data-val="123">
						+123
					</div>
					<div class="col-md-3 text-center tab-call-center" data-val="147">
						+147
					</div>
					<div class="col-md-3 text-center tab-call-center" data-val="888">
						+888
					</div>
				</div>
				<?php
			}
			?>
			<table style="width:100%;margin-top:1.5rem">
				<tbody>
					<tr>
						<td>
							<img src="<?php echo base_url('template/img/agent_login.svg'); ?>" alt=""
								class="kecil_img mx-auto d-block">
							<h4 class="text-center kecil_kata mt-3 ">Agent Log In </h4>
							<h6 class="text-center numberLog" id="agentLogin">0</h6>
						</td>
						<td>
							<img src="<?php echo base_url('template/img/agent_ready.svg'); ?>" alt=""
								class="kecil_img mx-auto d-block">
							<h4 class="text-center kecil_kata mt-3 ">Agent Ready </h4>
							<h6 class="text-center numberLog" id="agentReady">0</h6>
						</td>
						<td onclick="window.location = '<?php echo base_url('Reports/call_log'); ?>';"
							style="cursor: pointer">
							<img src="<?php echo base_url('template/img/total_call_today.svg'); ?>" alt=""
								class="kecil_img mx-auto d-block">
							<h4 class="text-center kecil_kata mt-3 ">Total Call Today </h4>
							<h6 class="text-center numberLog" id="totalCallToday">0</h6>
						</td>
						<td>
							<img src="<?php echo base_url('template/img/asa.svg'); ?>" alt=""
								class="kecil_img mx-auto d-block">
							<h4 class="text-center kecil_kata mt-3 ">ASA </h4>
							<h6 class="text-center numberLog" id="asa">0</h6>
						</td>
					</tr>
					<tr>
						<td colspan="5" style="text-align: center;padding-top:0.5rem;">
							<table style="width: 100%">
								<tr>
									<td style="width:33%">
										<img src="<?php echo base_url('template/img/average_handling_time.svg'); ?>" alt=""
											class="kecil_img mx-auto d-block">
										<h4 class="text-center kecil_kata mt-3 ">Average Handling Time </h4>
										<h6 class="text-center numberLog" id="handlingTime">0</h6>
									</td>
									<td style="width:33%">
										<img src="<?php echo base_url('template/img/call_5minute.svg'); ?>" alt=""
											class="kecil_img mx-auto d-block">
										<h4 class="text-center kecil_kata mt-3 ">Calls > 5 Min </h4>
										<h6 class="text-center numberLog" id="call5minutes">0</h6>
									</td>
									<td style="width:33%">
										<img src="<?php echo base_url('template/img/call_waiting.svg'); ?>" alt=""
											class="kecil_img mx-auto d-block">
										<h4 class="text-center kecil_kata mt-3 ">Call waiting </h4>
										<h6 class="text-center numberLog" id="callWaiting">0</h6>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div class="row" style="margin-top:2rem;">
		<?php
		if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 || $this->session->userdata('privilege') == 3 || $this->session->userdata('privilege') == 5) {
			?>
			<div class="col-md-4">
				<div id="call_Monitoring" class="chart_wrap"></div>
			</div>
			<div class="col-md-4">
				<div id="livegraph" class="chart_wrap"></div>
			</div>
			<?php
		}
		if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2) {
			?>
			<div class="col-md-4">
				<div id="topAgent" class="chart_wrap"></div>
			</div>
			<?php
		}
		if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 4) {
			?>
			<div class="col-md-4">
				<div id="plaza_chart" class="chart_wrap"></div>
			</div>
			<div class="col-md-4" style="text-align:center">
				<div>
					<h4 style="font-weight:bold">Plaza Activity</h4>
				</div>
				<br>
				<div id="plaza_history" class="chart_wrap"><button class="btn btn-info btn-lg">Create Activity</button></div>
			</div>
			<?php
		}
		?>
	</div>


	<script>
		var currentCallCenter = '<?php echo $this->session->userdata('tipe') != '123' && $this->session->userdata('tipe') != '147' ? '' : $this->session->userdata('tipe'); ?>';
		$(document).ready(function () {

			getDasboard();

			setInterval(function () {
				getDasboard();
			}, 10000);

			$('.navbar-nav .nav-item:first').html('<div class="title-module">Centro Informação</b></div>');
			$('.navbar-nav .nav-item:first').removeClass('font-weight-semibold');

			setTimeout(function () {
				scrollDown($('#tableAgent').parent());
			}, 2000);

			$('#tableAgent').parent().hover(function () {
				$(this).stop(true);
			}, function () {
				scrollDown($('#tableAgent').parent());
			});


			$('.tab-call-center').click(function () {
				currentCallCenter = $(this).data('val');

				getDasboard();

				$('.tab-call-center.tab-active').removeClass('tab-active');
				$(this).addClass('tab-active');
			});
		});

		function scrollDown(el) {
			el.animate({
				scrollTop: el[0].scrollHeight
			}, 10000, function () {
				scrollUp(el)
			});
		}

		function scrollUp(el) {
			el.animate({
				scrollTop: 0
			}, 10000, function () {
				scrollDown(el);
			});
		}

		function getDasboard(val) {
			var add = '';
			if (val) {
				var dt = new Date();

				var dd = dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate();

				add = '/' + dd + '/' + dd + '/' + val;
			}
			$.ajax({
				type: "GET",
				url: "<?= base_url("CallController/getDashboard"); ?>" + add,
				dataType: "JSON",
				success: function (json) {
					for (var it in json.item) {
						if (json.item[it] != null && json.item[it] > 0) {
							$('#' + it).text(json.item[it]);
						} else {
							$('#' + it).text(0);
						}

						if (it == 'asa' || it == 'handlingTime') {
							$('#' + it).html($('#' + it).text() + ' <small>Second</small>');
						}
					}

					<?php
					if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 || $this->session->userdata('privilege') == 3 || $this->session->userdata('privilege') == 5) {
						?>
						Highcharts.chart('call_Monitoring', {
							chart: {
								plotBackgroundColor: null,
								plotBorderWidth: null,
								plotShadow: false,
								type: 'pie'
							},
							title: {
								text: 'Incoming Call Monitoring'
							},
							tooltip: {
								// pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
								pointFormat: '{series.name}: <b>{point.y}</b>'

							},
							plotOptions: {
								pie: {
									allowPointSelect: true,
									cursor: 'pointer',
									dataLabels: {
										enabled: false
									},
									showInLegend: true,
									animation: false
								}
							},
							series: [{
								name: 'Total',
								colorByPoint: true,
								data: json.callMonitor
							}]
						});
						<?php
					}
					if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2) {
						?>
						var counter = 0;
						Highcharts.chart('topAgent', {
							chart: {
								plotBackgroundColor: null,
								plotBorderWidth: null,
								plotShadow: false,
								type: 'pie'
							},
							title: {
								text: 'Top Agent'
							},
							tooltip: {
								pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
							},
							plotOptions: {
								pie: {
									allowPointSelect: true,
									cursor: 'pointer',
									dataLabels: {
										enabled: true,
										color: '#000000',
										connectorColor: '#000000',
										useHTML: true,
										formatter: function () {
											counter++;
											return '<div class="datalabel">' + Math.round(this.percentage) + ' %</div>'; //<b>' + this.point.name + '</b>: 
										}
									},
									showInLegend: true,
									animation: false
								}
							},
							series: [{
								name: 'total',
								colorByPoint: true,
								data: json.topAgent
							}]
						});
						<?php
					}


					if ($this->session->userdata('privilege') == 0 || $this->session->userdata('privilege') == 9 || $this->session->userdata('privilege') == 1 || $this->session->userdata('privilege') == 2 || $this->session->userdata('privilege') == 3 || $this->session->userdata('privilege') == 5) {
						?>

						Highcharts.chart('livegraph', {
							chart: {
								plotBackgroundColor: null,
								plotBorderWidth: null,
								plotShadow: false,
								type: 'pie',

							},
							title: {
								text: 'Call Monitoring'
							},
							tooltip: {
								// pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
								pointFormat: '{series.name}: <b>{point.y}</b>'
							},
							plotOptions: {
								pie: {
									allowPointSelect: true,
									cursor: 'pointer',
									dataLabels: {
										enabled: false
									},
									showInLegend: true,
									animation: false
								}
							},
							series: [{
								name: 'total',
								colorByPoint: true,
								data: json.totalCallInOut
							}]
						});

					<?php } ?>

					$('#tableAgent').html('');

					for (var ig in json.tableAgent) {
						//                        $('#tableAgent').append('<tr class="' + json.tableAgent[ig].status + '"><td style="width:2rem"><img src="' + (json.tableAgent[ig].pic ? 'upload/' + json.tableAgent[ig].pic : '<?php echo base_url('template/img/users.svg'); ?>') + '" class="card-img-top" alt="..."></td><td>' + json.tableAgent[ig].ext + ' - ' + json.tableAgent[ig].nama + '&nbsp;&nbsp;&nbsp;' + (json.tableAgent[ig].status == 'agentBusy' ? '<b>' + json.tableAgent[ig].call_type + ' ' + json.tableAgent[ig].call_msisdn + '</b>' : '') + '</td></tr>');
						$('#tableAgent').append('<tr class="' + json.tableAgent[ig].status + '"><td style="width:2rem"><img src="<?php echo base_url('template/img/users.svg'); ?>" class="card-img-top" alt="..."></td><td>' + json.tableAgent[ig].ext + ' - ' + json.tableAgent[ig].nama + '&nbsp;&nbsp;&nbsp;' + (json.tableAgent[ig].status == 'agentBusy' ? '<b>' + json.tableAgent[ig].call_type + ' ' + json.tableAgent[ig].call_msisdn + '</b>' : '') + '</td></tr>');
					}
				}
			});
		}
	</script>
	<?php
}
?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<div class='title-module'>CSO Purchase</div>
<div class="subtitle-module">Reports &raquo; CSO Purchase</div>

<div class="card" style="padding:1rem">
	<div class="row" style="color: #655d5d;">
		<div class="col-md-12">
			<form method="get" action="" id="formFilter">
				<div class="row mt-1">
					<div class="col-md-2">
						<label for="call_center_number" style="font-size: 15px;text-align: left">Period</label>
					</div>
					<div class="col-md-6">
						<input type="date" name="tgl1" class="form-control" value="<?php echo $tgl1; ?>"
							style="width:40%"> Until <input type="date" name="tgl2" class="form-control"
							value="<?php echo $tgl2; ?>" style="width:40%">
					</div>
				</div>
				<div class="row mt-1">
					<div class="col-md-2">
						<label for="call_center_number" style="font-size: 15px;text-align: left">Plaza</label>
					</div>
					<div class="col-md-6">
						<select class="form-control" name="id_plaza">
							<option value="">-- All Plaza --</option>
							<?php
							foreach ($listPlaza as $p) {
								echo '<option value="' . $p->id . '" ' . ($p->id == $id_plaza ? 'selected' : '') . '>' . $p->judul . '</option>';
							}
							?>
						</select>
					</div>
				</div>
				<button class="btn btn-success" type="submit">Submit</button>
			</form>
		</div>
	</div>
</div>

<div id="chartData" style="margin-top:2rem;"></div>

<div class="card mt-2 " style="border-radius: 10px;">
	<div class="card-title head-module-action">
		<table>
			<thead>
				<tr>
					<th><span style="color:white"><i class="fa fa-list"></i> List CSO Purchase</span>
						<a href="javascript:exportToExcel('#tableReport');" class="pelebaran"><i
								class="fa fa-file-excel-o" style=""></i> Export To Excell</a>
					</th>
				</tr>
			</thead>
		</table>
	</div>

	<div class="card-body">
		<div class="row">
			<div class="table-responsive">
				<table class="table table-bordered display table-hover table-striped" id="tableReport"
					style="width:100%">
					<thead>
						<tr>
							<th>CSO ID</th>
							<th>CSO Name</th>
							<th>Outlet Code</th>
							<th style="text-align:center">Total Purchase</th>
							<th style="text-align:center">Total Price</th>
						</tr>
					</thead>
					<tbody>
						<?php echo $reportData; ?>
					</tbody>
				</table>
			</div>

		</div>
	</div>
</div>

<script>
	$(document).ready(function () {

	});
	Highcharts.chart('chartData', {
		chart: {
			type: 'column'
		},
		title: {
			text: 'Total Counter Summary Log <?php echo date('d F Y', strtotime($tgl1)) . ' until ' . date('d F Y', strtotime($tgl2)); ?>'
		},
		xAxis: {
			categories: <?php echo json_encode($reportCategory); ?>
		},
		yAxis: {
			title: {
				text: 'Total'
			}
		},
		plotOptions: {
			column: {
				stacking: 'normal'
			}
		},
		tooltip: {
			shared: true,
			formatter: function () {
				let s = '<b>' + this.category + '</b>';
				let total = 0;

				this.points.forEach(function (point) {

					s += '<br><span style="color:' + point.series.color + '">\u25CF</span> ' + point.series.name + ': ' + point.y;
					total += point.y;
				});

				s += '<br><b>Total: ' + total + '</b>'; // Add the calculated total

				return s;
			}
		},
		series: <?php echo json_encode($reportChart); ?>
	});
</script>

<script>
	function exportToExcel(table) {
		var htmls = "";
		var uri = 'data:application/vnd.ms-excel;base64,';
		var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>';
		var base64 = function (s) {
			return window.btoa(unescape(encodeURIComponent(s)))
		};
		var format = function (s, c) {
			return s.replace(/{(\w+)}/g, function (m, p) {
				return c[p];
			})
		};

		htmls = $(table)[0].outerHTML;

		var ctx = {
			worksheet: 'Worksheet',
			table: htmls
		}


		var link = document.createElement("a");
		link.download = "export.xls";
		link.href = uri + base64(format(template, ctx));
		link.click();
	}
</script>
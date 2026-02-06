<?php
if ($all) {
	ob_start();
}
?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<div class='title-module'>Report Performance Detail </div>
<div class="subtitle-module">Reports &raquo; IVR Campaign Performance Detail</div>

<div id="chartData" style="margin-top:2rem;"></div>

<div class="card mt-2 " style="border-radius: 10px;">
	<div class="card-title head-module-action">
		<table>
			<thead>
				<tr>
					<th><span style="color:white"><i class="fa fa-list"></i> List Report Performance Detail</span>
						<a href="javascript:exportToExcel('#tableReport');" class="pelebaran"><i class="fa fa-file-excel-o" style=""></i> Export To Excell</a>
					</th>
				</tr>
			</thead>
		</table>
	</div>

	<div class="card-body">
		<div class="row">
			<div class="table-responsive">
				<?php
				if ($all) {
					ob_end_clean();

					ob_start();
					echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>worksheet</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body>';
				}
				?>
				<table class="table table-bordered display table-hover table-striped" id="tableReport" style="width:100%">
					<thead>
						<tr>
							<th>No</th>
							<th>MSISDN</th>
							<th>Start IVR</th>
							<th>Status</th>
							<th>Result</th>
						</tr>
					</thead>
					<tbody>
						<?php echo $reportData; ?>
					</tbody>
				</table>

				<?php
				if ($all) {
					echo '</body></html>';
					$data = ob_get_clean();

					header("Content-type: application/vnd-ms-excel");
					header("Content-Disposition: attachment; filename=Data IVR Campaign Detail.xls");

					echo $data;

					exit();
				}
				?>

				<div style="text-align: center;margin-top: 2rem;">
					Page :
					<select name="page" onchange="window.location = '?' + $('#formFilter :input').serialize() + '&page=' + this.value;">
						<?php
						for ($p = 1; $p <= $totalPage; $p++) {
							echo '<option value="' . $p . '">' . $p . '</option>';
						}
						?>
					</select>
				</div>
			</div>

		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('[name="page"]').val('<?php echo $page + 1; ?>');
	});
</script>

<script>
	function exportToExcel(table) {
		window.location = '?id=<?php echo $id_campaign; ?>&all=true';
	}
</script>
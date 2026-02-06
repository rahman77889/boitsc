<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Display extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function getCategory($id_category = null)
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET');
		header("Access-Control-Allow-Headers: X-Requested-With");

		if (!$id_category) {
			$list =
				$this->db->select('*')->from('category')->where('statusActive', 'Y')->get()->result_array();

			foreach ($list as $v) {
				echo '<option value="' . $v['categoryId'] . '">' . $v['categoryName'] . '</option>';
			}
		} else {
			$list =
				$this->db->select('*')->from('subcategory')->where('categoryId', $id_category)->where('statusActive', 'Y')->get()->result_array();
			foreach ($list as $v) {
				echo '<option value="' . $v['subCategoryId'] . '">' . $v['subCategory'] . '</option>';
			}
		}
	}

	public function getCategoryJson($type = null)
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET');
		header("Access-Control-Allow-Headers: X-Requested-With");

		if ($type == 'sub') {
			$list =
				$this->db->select('*')->from('subcategory')->get()->result_array();

			echo json_encode($list);
		} else {
			$list =
				$this->db->select('*')->from('category')->get()->result_array();

			echo json_encode($list);
		}
	}

	public function customerSatisfaction($id_counter_setting = 1, $lang = 1)
	{
		$detSetting = $this->db->select('*')->from('queue_setting')->where('id', $id_counter_setting)->get()->row();

		$wording = array();
		$wording[1] = array();
		$wording[1]['opening'] = array();
		$wording[1]['opening'][1] = 'Survei Kepuasan Pelanggan Plaza Telkomcel';
		$wording[1]['opening'][2] = 'Pilih emoji sesuai pengalaman Anda:';
		$wording[1]['opening'][3] = '🙁 Tidak Puas  😐 Biasa Saja  🙂 Puas  😄 Sangat Puas';
		$wording[1]['content'] = array();
		$wording[1]['content'][1] = 'Keramahan dan kesopanan Petugas';
		$wording[1]['content'][2] = 'Petugas melayani dengan cepat dan memberikan solusi';
		$wording[1]['content'][3] = 'Kepuasan terhadap harga dan kualitas produk Telkomcel';
		$wording[1]['content'][4] = 'Stabilitas dan kecepatan internet di lokasi anda';
		$wording[1]['content'][5] = 'Kenyamanan ruang tunggu dan fasilitas Plaza Telkomcel';
		$wording[1]['closing'] = 'Terima kasih, pendapat anda sangat berharga bagi Telkomcel';
		$wording[1]['rate'] = array();
		$wording[1]['rate'][1] = 'Tidak  puas';
		$wording[1]['rate'][2] = 'Puas';
		$wording[1]['rate'][3] = 'Puas';
		$wording[1]['rate'][4] = 'Sangat puas';
		$wording[2] = array();
		$wording[2]['opening'] = array();
		$wording[2]['opening'][1] = 'Plaza Telkomcel Customer Satisfaction Survey';
		$wording[2]['opening'][2] = 'Select the emoji according to your experience:';
		$wording[2]['opening'][3] = '🙁 Dissatisfied 😐 Ordinary 🙂 Satisfied 😄 Very Satisfied';
		$wording[2]['content'] = array();
		$wording[2]['content'][1] = 'Friendliness and politeness of Customer Service';
		$wording[2]['content'][2] = 'Customer service is responsive and provides solutions';
		$wording[2]['content'][3] = 'Your satisfaction with the price and quality of Telkomcel products that you currently use';
		$wording[2]['content'][4] = 'Your satisfaction towards the stabilities and speeds of the internet in your location';
		$wording[2]['content'][5] = 'The comfort of waiting area and facilities at Plaza Telkomcel';
		$wording[2]['closing'] = 'Thank you for your opinion, your opinion  very valuable for Telkomcel';
		$wording[2]['rate'] = array();
		$wording[2]['rate'][1] = 'Poor';
		$wording[2]['rate'][2] = 'Excellent';
		$wording[2]['rate'][3] = 'Satisfied';
		$wording[2]['rate'][4] = 'Verry Satisfied';
		$wording[3] = array();
		$wording[3]['opening'] = array();
		$wording[3]['opening'][1] = 'Peskiza kona ba Satisfasaun Kliente iha Plaza Telkomcel';
		$wording[3]['opening'][2] = 'Hili emoji nebee  tuir ita nia esperiénsia:';
		$wording[3]['opening'][3] = '🙁 La Kontente 😐 Bain-Bain deit 🙂 Kontenti 😄 Kontenti Los';
		$wording[3]['content'] = array();
		$wording[3]['content'][1] = 'Agente atende ho Simpatia no Amigavel';
		$wording[3]['content'][2] = 'Agente atende lalais no fó solusaun';
		$wording[3]['content'][3] = 'Ita nia satisfasaun kona ba presu no qualidade produto Telkomcel neebe ita uza agora';
		$wording[3]['content'][4] = 'Ita nia satisfasaun kona ba estabilidade no velocidade internet iha ita nia área';
		$wording[3]['content'][5] = 'Komfortu Sala atendemento no fasilidade iha Plaza Telkomcel';
		$wording[3]['closing'] = 'Obrigado/a barak , ita boot nia opinion fo valor tebes mai Telkomcel';
		$wording[3]['rate'] = array();
		$wording[3]['rate'][1] = 'La Satisfas';
		$wording[3]['rate'][2] = 'Satisfas';
		$wording[3]['rate'][3] = 'Satisfas';
		$wording[3]['rate'][4] = 'Satisfas tebes';
		?>
		<!DOCTYPE html>
		<html dir="ltr" lang="en-US">

		<head>
			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
			<meta name="author" content="PT. Shibly Teknologi Solusi" />
			<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
			<title><?php echo $detSetting->judul; ?></title>
			<link rel="shortcut icon" href="<?= base_url('template/'); ?>assets/images/favicon_new.png" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0">

			<script type="text/javascript">
				if (window.location.protocol != "https:") {
					window.location.protocol = "https";
				}
			</script>
			<style>
				@import url('https://fonts.googleapis.com/css2?family=Open+Sans&display=swap');

				body {
					padding: 0px;
					margin: 0px;
					font-family: 'Open Sans', sans-serif;
				}

				/*basic reset*/
				* {
					margin: 0;
					padding: 0;
				}

				html {
					height: 100%;
					/*Image only BG fallback*/

					/*background = gradient + image pattern combo*/
					background:
						linear-gradient(rgba(196, 102, 0, 0.6), rgba(155, 89, 182, 0.6));
				}

				body {
					font-family: montserrat, arial, verdana;
				}

				.languageSet {
					font-size: 2.4rem;
					padding: 0rem 0.5rem;
				}
			</style>
			<script src="<?php echo base_url('dist/jquery-3.3.1.js'); ?>"></script>
		</head>

		<body>
			<div style="color: white;text-align: center;padding: 1rem;margin-top:0.5rem;width:90%;margin:auto">
				<div style="background-color: white;border-radius: 5rem;padding:0.7rem 1rem;position:relative;height:100%">
					<table style="width:100%">
						<tr>
							<td style="width:10%;" valign="center">
								<img id="setupCounter" src="<?= base_url() ?>template/img/gear.png" style="height:3rem;">
							</td>
							<td style="color:black;width:20%;" valign="center">
								<span style="font-size:16pt;">Counter <span id="counter_number"
										style="font-weight:bold;background-color: black;color:white;padding:0.2rem;margin:0.2rem"></span></span>
							</td>
							<td style="width:40%;">
								<img src="<?php echo base_url(); ?>template/img/telkomcel.png" style="width:80%;margin:auto;">
							</td>
							<td style="width:30%;">
								<a href="<?= base_url() . '/display/customerSatisfaction/' . $id_counter_setting ?>/3"
									class="languageSet">🇹🇱</a>
								<a href="<?= base_url() . '/display/customerSatisfaction/' . $id_counter_setting ?>/1"
									class="languageSet">🇮🇩</a>
								<a href="<?= base_url() . '/display/customerSatisfaction/' . $id_counter_setting ?>/2"
									class="languageSet">🇺🇸</a>
							</td>
						</tr>
					</table>
				</div>

				<h1 style="margin-top:2rem"><?= $wording[$lang]['opening'][1] ?></h1>
				<h2 style="margin-top:1rem"><?= $wording[$lang]['opening'][2] ?></h2>
				<h2 style="margin-top:1rem"><?= $wording[$lang]['opening'][3] ?></h2>

				<div style="margin-top:1.5rem;width:100%;display:block;padding:0.3rem">
					<?php
					for ($i = 1; $i <= 5; $i++) {
						echo '<div style="background-color:#ff3b3b;border-radius:0.7rem;padding:0.5rem;margin-bottom:0rem">';
						echo '<div style="font-size:17pt">' . $wording[$lang]['content'][$i] . '</div>';
						echo '</div>';

						echo '<div style="padding:0.8rem;margin-bottom:1%;width:100%;height:4rem;text-align:center;">';
						for ($ii = 1; $ii <= 4; $ii++) {
							echo '<label style="
    font-size: 1.5rem;
    padding: 1rem;
    border-radius: 100%;
    
    " data-index="' . $i . '" data-val="' . $ii . '" class=" check_label_' . $i . '"><input class="check_label" type="radio" style="display:none;" name="rating_' . $i . '" value="' . $ii . '"><img src="' . base_url() . 'template/img/' . $ii . '.png" style="height:80%"></label>';
						}
						echo '</div>';
					}
					?>
				</div>
			</div>

			<script>
				$(function () {
					var counter_number = localStorage['counter_number'] ? localStorage['counter_number'] : '0';
					var id_counter = localStorage['id_counter'] ? localStorage['id_counter'] : '';

					$('#counter_number').text(counter_number);

					$('.check_label').unbind('click').click(function () {
						counter_number = localStorage['counter_number'] ? localStorage['counter_number'] : '';
						id_counter = localStorage['id_counter'] ? localStorage['id_counter'] : '';

						$('#counter_number').text(counter_number);

						if (!counter_number) {
							alert('Please fill counter number');
						} else {
							$('.check_label_' + $(this).parent().data('index')).css('background-color', '');
							$(this).parent().css('background-color', 'green');

							var filled = $('input[type="radio"]').serializeArray();

							if (filled.length == 5) {

								$.ajax({
									url: '<?= base_url() . 'display/setDataCS' ?>',
									type: 'post',
									data: $('input[type="radio"]').serialize() + '&ids=<?= $id_counter_setting ?>&ic=' + id_counter,
									success: function (res) {
										if (res.status == 'success') {
											alert('<?= $wording[$lang]['closing'] ?>');
											window.location.reload();
										} else {
											alert('Please try again later');
										}
									}
								});
							}
						}
					});

					$('#setupCounter').click(function () {
						var x = prompt('Please fill counter number');
						if (!isNaN(x) && x) {
							$.get('<?= base_url() ?>display/getCounterSetting/<?= $id_counter_setting ?>/' + x, function (res) {
								if (res.status == 'success') {
									localStorage.setItem('id_counter', res.id);
									localStorage.setItem('counter_number', x);

									$('#counter_number').text(x);
								} else {
									alert('Please fill correct counter number');
								}
							});

						} else {
							alert('Please fill correct counter number');
						}
					});
				});
			</script>
		</body>

		</html>
		<?php
	}

	public function setDataCS()
	{

		$id_counter_setting = $_POST['ids'];
		$id_counter = $_POST['ic'];

		$data = array();
		$data = $_POST;

		unset($data['ids']);
		unset($data['ic']);

		$last_complaint = $this->db->select('id')->from('complain')->where('id_counter_setting', $id_counter_setting)->where('id_counter', $id_counter)->order_by('createDate', 'desc')->limit(1)->get()->row_array();

		if ($last_complaint['id']) {

			$this->db->where('id=' . $last_complaint['id']);

			$ret = array();

			if ($this->db->update('complain', $data)) {
				$ret['status'] = 'success';
			} else {
				$ret['status'] = 'fail';
			}
		} else {
			$ret['status'] = 'fail';
		}

		header('content-type:text/json');
		echo json_encode($ret);
	}

	public function getCounterSetting($id_counter_setting = 1, $counter_number)
	{
		$id_counter_setting = $this->db->select('id')->from('queue')->where('counter', $counter_number)->where('id_counter_setting', $id_counter_setting)->get()->row_array();

		$ret = array();

		if (isset($id_counter_setting['id'])) {
			$ret['status'] = 'success';
			$ret['id'] = $id_counter_setting['id'];
		} else {
			$ret['status'] = 'fail';
			$ret['id'] = '';
		}

		header('content-type:text/json');
		echo json_encode($ret);
	}

	public function start($id_counter_setting = 1)
	{
		$this->index($id_counter_setting);
	}

	public function index($id_counter_setting = 1)
	{
		$detSetting = $this->db->select('*')->from('queue_setting')->where('id', $id_counter_setting)->get()->row();
		$detCounter = $this->db->select('*')->from('queue')->where('id_counter_setting', $id_counter_setting)->get()->result();
		?>
		<!DOCTYPE html>
		<html dir="ltr" lang="en-US">

		<head>
			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
			<meta name="author" content="PT. Shibly Teknologi Solusi" />
			<title><?php echo $detSetting->judul; ?></title>
			<link rel="shortcut icon" href="<?= base_url('template/'); ?>assets/images/favicon_new.png" />

			<style>
				@import url('https://fonts.googleapis.com/css2?family=Open+Sans&display=swap');

				body {
					padding: 0px;
					margin: 0px;
					background-color: black;
					font-family: 'Open Sans', sans-serif;
				}

				.fullCover {
					width: 100%;
					height: 100%;
					object-fit: fill;
					position: fixed;
					top: 0px;
					left: 0px;
				}

				.wrapLogo {
					position: fixed;
					top: 0rem;
					right: 0rem;
					width: 12rem;
					padding: 0.6rem;
					background: rgba(255, 255, 255, 0.6);
					border-radius: 0rem 0rem 0rem 2rem;
				}

				.wrapTitle {
					position: fixed;
					top: 1.5rem;
					left: 2rem;
					z-index: 2;
					width: 30rem;
				}

				#title {
					font-size: 3rem;
					color: white;
				}

				.wrapAntrian {
					max-width: 9rem;
					margin-top: 3rem;
					padding: 1rem;
					background: rgba(255, 255, 255, 0.65);
					border-radius: 2rem 0rem;
					text-align: center;
				}

				.wrapAntrianGuest {
					display: none;
					max-width: 15rem;
					margin-top: 3rem;
					padding: 1rem;
					background: rgba(255, 255, 255, 0.65);
					border-radius: 2rem 0rem;
					text-align: center;
					position: relative;
					left: 18rem;
					top: -19rem;
				}

				#titleAntrianGuest {
					font-size: 1.7rem;
					color: white;
					background-color: rgb(233 15 16 / 65%);
					border-radius: 2rem;
					padding: 0rem 0.5rem;
				}

				#fotoGuest img {
					height: 100%;
					margin-top: 0.5rem;
				}

				#namaGuest {
					font-size: 2rem;
					color: red;
					font-weight: bold;
					text-transform: capitalize;
					background-color: white;
					border-radius: 2rem;
					margin-top: 0.5rem;
				}

				.wrapLoket {
					position: fixed;
					bottom: 2rem;
					left: 2rem;
					z-index: 2;
				}

				#titleAntrianBig {
					font-size: 1.4rem;
					color: white;
					background-color: rgb(233 15 16 / 65%);
					border-radius: 2rem;
					padding: 0rem 0.5rem;
				}

				#loketAntrianBig {
					font-size: 1.5rem;
					color: red;
					font-weight: bold;
					text-transform: capitalize;
					background-color: white;
					border-radius: 2rem;
					margin-top: 0.5rem;
				}

				#noAntrianBig {
					font-size: 2.5rem;
					color: #444444;
				}

				.titleAntrianSmall {
					font-size: 1.3rem;
					color: white;
					background-color: rgb(233 15 16 / 65%);
					border-radius: 2rem;
					padding: 0rem 1rem;
				}

				.loketAntrianSmall {
					font-size: 2rem;
					color: red;
					font-weight: bold;
					text-transform: capitalize;
					background-color: white;
					border-radius: 2rem;
					margin-top: 0.5rem;
				}

				.noAntrianSmall {
					font-size: 2.5rem;
					color: #444444;
				}

				.wrapAntrianSmall {
					float: left;
					margin-right: 1.8rem;
				}
			</style>
		</head>

		<body>
			<video class="fullCover" autoplay="true" loop="true" muted="true">
				<source src="<?php echo base_url($detSetting->video); ?>" />
			</video>
			<div class="fullCover"
				style="z-index: 2;background: linear-gradient(to bottom,  rgba(30,35,42,0) 21%,rgba(30,35,42,0) 66%,rgba(30,35,42,1) 100%);">
			</div>
			<div class="fullCover"
				style="z-index: 2;background: linear-gradient(to left,  rgba(30,35,42,0) 20%,rgba(30,35,42,0) 0%,rgba(30,35,42,1) 100%);">
			</div>
			<div class="wrapLogo">
				<img src="<?php echo base_url(); ?>template/img/telkomcel.png" style="width: 100%">
			</div>
			<div class="wrapTitle">
				<span id="title"><?php echo $detSetting->judul; ?></span>

				<div class="wrapAntrian">
					<div id="titleAntrianBig">Current Call</div>
					<div id="loketAntrianBig"></div>
					<div id="noAntrianBig"></div>
				</div>

				<div class="wrapAntrianGuest">
					<div id="titleAntrianGuest">Guest Info</div>
					<div id="fotoGuest"></div>
					<div id="namaGuest"></div>
				</div>
			</div>
			<div class="wrapLoket"></div>

			<script src="<?php echo base_url('dist/jquery-3.3.1.js'); ?>"></script>
			<script>
				$(document).ready(function () {
					var color = ['darksalmon', 'rgb(0 128 0 / 70%)', 'rgb(0 0 255 / 58%)', 'rgb(138 43 226 / 71%)', 'rgb(255 165 0 / 85%)', 'rgb(165 42 42 / 75%)'];

					function cekQueue() {
						$.post('<?php echo base_url('display/queue/' . $id_counter_setting); ?>', {}, function (res) {

							$('.wrapAntrianSmall').remove();
							for (var k in res) {
								var row = res[k];
								var col = color[k];

								$('.wrapLoket').append('<div class="wrapAntrian wrapAntrianSmall">\n\
																																						<div class="titleAntrianSmall" style="background-color: ' + col + '">Counter</div>\n\
																																						<div class="loketAntrianSmall">' + row['counter'] + '</div>\n\
																																						<div class="noAntrianSmall">' + zeroNumber(row['nomor']) + '</div>\n\
																																					</div>');

								if (row['status'] == 'N' || $('#loketAntrianBig').text() == '') {
									if ($('#loketAntrianBig').text() != '') {
										sayNumber(row['nomor'], row['counter']);
									}

									$('#titleAntrianBig').css('background-color', col);
									$('#loketAntrianBig').text('Counter ' + row['counter']);
									$('#noAntrianBig').text(zeroNumber(row['nomor']));

									if (row['nama']) {
										$('.wrapAntrianGuest').show();
										//                                            $('#fotoGuest').html('<img style="width:100%" src="<?php echo base_url(); ?>' + row['foto'] + '">');
										$('#fotoGuest').html('');
										$('#namaGuest').text(row['nama']);
									} else {
										$('.wrapAntrianGuest').hide();
										$('#fotoGuest').html('');
										$('#namaGuest').text('');
									}
								}
							}
						});
					}

					function zeroNumber(number) {
						var len = number.toString().length;

						if (len == 1) {
							return '00' + number;
						} else if (len == 2) {
							return '0' + number;
						} else {
							return number;
						}
					}

					setInterval(cekQueue, 1000);
				});

				var snd;

				function sayNumber(number, loket) {
					var formatFile = 'wav';
					var langMp3 = 'tt';

					if (number > 0) {
						var mp3 = '<?php echo base_url('wav_tt/'); ?>';
						var numberDigit = parseInt(number);
						var urutanMp3 = ['bell.mp3', 'nomor-antrian.' + formatFile];

						if (langMp3 == 'id') {
							if (numberDigit > 0 && (numberDigit <= 11 || numberDigit == 100)) {
								urutanMp3.push(number + '.' + formatFile);
							} else if (numberDigit < 20) {
								urutanMp3.push((numberDigit - 10) + '.' + formatFile);
								urutanMp3.push('belas.' + formatFile);
							} else if (numberDigit >= 20 && numberDigit < 100) {
								urutanMp3.push(number.toString().substr(0, 1) + '.' + formatFile);
								urutanMp3.push('puluh.' + formatFile);
								if (parseInt(number.toString().substr(1, 1)) > 0) {
									urutanMp3.push(number.toString().substr(1, 1) + '.' + formatFile);
								}
							} else if (numberDigit < 200) {
								urutanMp3.push('100.' + formatFile);

								var numberSeratus = numberDigit - 100;
								var numberSeratusDigit = parseInt(numberSeratus);

								if (numberSeratusDigit > 0 && (numberSeratusDigit <= 11 || numberSeratusDigit == 100)) {
									urutanMp3.push(numberSeratus + '.' + formatFile);
								} else if (numberSeratusDigit > 0 && numberSeratusDigit < 20) {
									urutanMp3.push((numberSeratusDigit - 10) + '.' + formatFile);
									urutanMp3.push('belas.' + formatFile);
								} else if (numberSeratusDigit >= 20) {
									urutanMp3.push(numberSeratus.toString().substr(0, 1) + '.' + formatFile);
									urutanMp3.push('puluh.' + formatFile);
									if (parseInt(numberSeratus.toString().substr(1, 1)) > 0) {
										urutanMp3.push(numberSeratus.toString().substr(1, 1) + '.' + formatFile);
									}
								}
							} else if (numberDigit >= 200) {
								urutanMp3.push(number.toString().substr(0, 1) + '.' + formatFile);
								urutanMp3.push('ratus.' + formatFile);

								var numberSeratus = numberDigit - (100 * parseInt(number.toString().substr(0, 1)));
								var numberSeratusDigit = parseInt(numberSeratus);

								if (numberSeratusDigit > 0 && (numberSeratusDigit <= 11)) {
									urutanMp3.push(numberSeratus + '.' + formatFile);
								} else if (numberSeratusDigit > 0 && numberSeratusDigit < 20) {
									urutanMp3.push((numberSeratusDigit - 10) + '.' + formatFile);
									urutanMp3.push('belas.' + formatFile);
								} else if (numberSeratusDigit >= 20) {
									urutanMp3.push(numberSeratus.toString().substr(0, 1) + '.' + formatFile);
									urutanMp3.push('puluh.' + formatFile);
									if (parseInt(numberSeratus.toString().substr(1, 1)) > 0) {
										urutanMp3.push(numberSeratus.toString().substr(1, 1) + '.' + formatFile);
									}
								}
							}
						} else if (langMp3 == 'tt') {
							if (numberDigit > 0 && numberDigit <= 10) {
								urutanMp3.push(number + '.' + formatFile);
							} else if (numberDigit == 11) {
								urutanMp3.push('10.' + formatFile);
								urutanMp3.push('belas.' + formatFile);
								urutanMp3.push('1.' + formatFile);
							} else if (numberDigit >= 12 && numberDigit <= 19) {
								urutanMp3.push('10.' + formatFile);
								urutanMp3.push('belas.' + formatFile);
								urutanMp3.push((numberDigit - 10) + '.' + formatFile);
							} else if (numberDigit == 20 || numberDigit == 30 || numberDigit == 40 || numberDigit == 50 || numberDigit == 60 || numberDigit == 70 || numberDigit == 80 || numberDigit == 90) {
								urutanMp3.push(number.toString().substr(0, 1) + '.' + formatFile);
								urutanMp3.push('puluh.' + formatFile);
							} else if (numberDigit >= 21 && numberDigit <= 99) {
								urutanMp3.push(number.toString().substr(0, 1) + '.' + formatFile);
								urutanMp3.push('puluh.' + formatFile);
								urutanMp3.push('belas.' + formatFile);
								urutanMp3.push(number.toString().substr(1, 1) + '.' + formatFile);
							} else if (numberDigit == 100) {
								urutanMp3.push('ratus.' + formatFile);
								urutanMp3.push('1.' + formatFile);
							} else if (numberDigit > 100) {
								urutanMp3.push('ratus.' + formatFile);
								urutanMp3.push(number.toString().substr(0, 1) + '.' + formatFile);

								var numberSeratus = numberDigit - (100 * parseInt(number.toString().substr(0, 1)));

								console.log('number_1', parseInt(number.toString().substr(0, 1)));
								console.log('numberSeratus', numberSeratus);

								if (numberSeratus > 0 && numberSeratus <= 10) {
									urutanMp3.push('belas.' + formatFile);
									urutanMp3.push(numberSeratus + '.' + formatFile);
								} else if (numberSeratus == 11) {
									urutanMp3.push('10.' + formatFile);
									urutanMp3.push('belas.' + formatFile);
								} else if (numberSeratus >= 12 && numberSeratus <= 19) {
									urutanMp3.push('10.' + formatFile);
									urutanMp3.push('belas.' + formatFile);
									urutanMp3.push((numberSeratus - 10) + '.' + formatFile);
								} else if (numberSeratus == 20 || numberSeratus == 30 || numberSeratus == 40 || numberSeratus == 50 || numberSeratus == 60 || numberSeratus == 70 || numberSeratus == 80 || numberSeratus == 90) {
									urutanMp3.push(numberSeratus.toString().substr(0, 1) + '.' + formatFile);
									urutanMp3.push('puluh.' + formatFile);
								} else if (numberSeratus >= 21 && numberSeratus <= 99) {
									urutanMp3.push(numberSeratus.toString().substr(0, 1) + '.' + formatFile);
									urutanMp3.push('puluh.' + formatFile);
									urutanMp3.push('belas.' + formatFile);
									urutanMp3.push(numberSeratus.toString().substr(1, 1) + '.' + formatFile);
								}
							}
						}

						console.log('urutanMp3', urutanMp3);

						urutanMp3.push('silahkan-menuju-ke-loket-nomor.' + formatFile);
						urutanMp3.push(loket + '.' + formatFile);

						var key = 0;
						snd = new Audio(mp3 + urutanMp3[key]);
						snd.play();
						snd.onended = function () {
							key++;
							if (urutanMp3[key] != null) {
								snd.src = mp3 + urutanMp3[key];
								snd.load();
								snd.playbackRate = 1.2;
								snd.play();
							}
						}
					}
				}
			</script>
		</body>

		</html>

		<?php
	}

	public function queue($id_counter = 1)
	{
		$queue = $this->db->select('queue.*, queue_list.nama, queue_list.foto')->from('queue')->where('queue.id_counter_setting', $id_counter)->join('queue_list', 'queue_list.id=queue.id_list', 'left')->get()->result_array();

		$update = array();
		$update['status'] = 'Y';

		$this->db->where('id_counter_setting', $id_counter)->update('queue', $update);

		header('content-type:text/json');
		echo json_encode($queue);
	}

	private function token()
	{
		// $arrContextOptions = array(
		// 	"ssl" => array(
		// 		"verify_peer"      => false,
		// 		"verify_peer_name" => false,
		// 	)
		// );

		// $data = file_get_contents('https://apimytelkomcel.telkomcel.tl:8445/MyTelkomcelREST/getToken', false, stream_context_create($arrContextOptions));
		// $json = json_decode($data, true);

		// return $json['access_token'];

		return '';
	}

	public function finishLastQueue()
	{
		//set finish previous activity
		$update_activity = array();
		$update_activity['finish_time'] = date('Y-m-d H:i:s');

		$this->db->where('finish_time', null);
		$this->db->where('id_user', $this->session->userdata('id'));
		$this->db->update('plaza_history', $update_activity);

		redirect($_SERVER['HTTP_REFERER']);
	}

	public function nextQueue($reset = 'N', $repeat = 'N')
	{
		//set finish previous activity
		$update_activity = array();
		$update_activity['finish_time'] = date('Y-m-d H:i:s');

		$this->db->where('finish_time', null);
		$this->db->where('id_user', $this->session->userdata('id'));
		$this->db->update('plaza_history', $update_activity);


		$detail_queue = $this->db->select('counter')->from('queue')->where('id', $this->session->userdata('id_counter'))->where('id_counter_setting', $this->session->userdata('id_counter_setting'))->get()->row()->nomor;
		$last_nomor = $this->db->select('antrian')->from('queue_list')->where('status', $repeat == 'Y' ? 'Y' : 'N')->where('id_counter_setting', $this->session->userdata('id_counter_setting'))->order_by('antrian', 'ASC')->limit(1)->get()->row();
		$last_nomor = isset($last_nomor->antrian) ? $last_nomor->antrian : 0;
		$loket_nomor = $detail_queue->counter;

		$update = array();
		$update['nomor'] = $repeat == 'Y' ? $last_nomor : $last_nomor;
		$update['status'] = 'N';

		$this->db->where('id', $this->session->userdata('id_counter'));
		$this->db->where('id_counter_setting', $this->session->userdata('id_counter_setting'));
		$this->db->update('queue', $update);

		if ($reset == 'Y') {
			$update = array();
			$update['nomor'] = 0;
			$update['status'] = 'N';

			$this->db->where('id_counter_setting', $this->session->userdata('id_counter_setting'))->update('queue', $update);
		}

		if ($repeat == 'N' && $reset == 'N') { //send sms to guest
			$last_guest = $this->db->select('*')->from('queue_list')->where('status', 'N')->where('id_counter_setting', $this->session->userdata('id_counter_setting'))->order_by('tgl', 'asc')->limit(1)->get()->row();

			if ($last_guest->msisdn) {
				$detail_customer = array();

				$arrContextOptions = array(
					"ssl" => array(
						"verify_peer" => false,
						"verify_peer_name" => false,
					)
				);

				$data = file_get_contents('http://172.17.12.126:9080/anc/' . $last_guest->msisdn, false, stream_context_create($arrContextOptions));
				$detail_customer = json_decode($data, true);

				//send sms notif
				$msg = 'Hallo ' . $detail_customer['customername'] . ', your turn for the queue has coming, please enter the counter number ' . $loket_nomor;
				// file_get_contents('http://150.242.110.240:8280/myTelkomcel/bta?msisdn=' . $last_guest->msisdn . '&sourceAddress=Telkomcel&message=' . urlencode($msg) . '&trxid=' . mt_rand(100000, 999999) . '&type=11&appId=0110&token=' . $this->token(), false, stream_context_create($arrContextOptions));
				file_get_contents('https://api.telkomcel.tl/send-sms-bypass/ka?msisdn=' . $last_guest->msisdn . '&sender=Telkomcel&msg=' . urlencode($msg), false, stream_context_create($arrContextOptions));
			} else {
				$update = array();
				$update['id_list'] = $last_guest->id;

				$this->db->where('id', $this->session->userdata('id_counter'));
				$this->db->where('id_counter_setting', $this->session->userdata('id_counter_setting'));
				$this->db->update('queue', $update);
			}

			//update refer antrian
			$data = array();
			$data['status'] = 'Y';
			$data['tgl_call'] = date('Y-m-d H:i:s');

			$this->db->where('id', $last_guest->id);
			$this->db->update('queue_list', $data);
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	public function newQueue()
	{
		$msisdn = $this->input->post('msisdn');
		$nama = $this->input->post('nama');
		//        $foto               = $this->input->post('foto');
		$id_counter_setting = $this->input->post('id_counter_setting');

		if (!$id_counter_setting) {
			$json = json_decode(file_get_contents('php://input'), true);

			$msisdn = $json['msisdn'];
			$nama = $json['name'];
		}

		$id_counter_setting = $id_counter_setting ? $id_counter_setting : 1; //default counter 1 plaza

		$number = $this->db->select('count(id) as jum')->from('queue_list')->where('id_counter_setting', $id_counter_setting)->where('date(tgl)', date('Y-m-d'))->get()->row();

		if (isset($number->jum)) {
			$number = $number->jum + 1;
		} else {
			$number = 1;
		}

		$data = array();
		$data['tgl'] = date('Y-m-d H:i:s');
		$data['antrian'] = $number;
		$data['id_counter_setting'] = $id_counter_setting;

		if ($msisdn) {
			$data['msisdn'] = $msisdn;
		} else {
			//            $foto     = str_replace('data:image/jpeg;base64,', '', $foto);
			//            $pathFoto = 'upload/' . uniqid() . '.jpg';
			//
			//            file_put_contents($pathFoto, base64_decode($foto));

			$data['nama'] = $nama;
			//            $data['foto'] = $pathFoto;
		}

		$return = array();
		$return['success'] = false;
		$return['number'] = $number;
		$return['name'] = $nama;

		if ($this->db->insert('queue_list', $data)) {
			$return['success'] = true;

			if ($msisdn) {
				$arrContextOptions = array(
					"ssl" => array(
						"verify_peer" => false,
						"verify_peer_name" => false,
					)
				);

				$data = file_get_contents('http://172.17.12.126:9080/anc/' . $msisdn, false, stream_context_create($arrContextOptions));
				$detail_customer = json_decode($data, true);

				$return['name'] = $detail_customer['customername'];

				//send sms notif
				// $msg = 'Hallo ' . $detail_customer['customername'] . ', your queue counter number is ' . $return['number'];
				// file_get_contents('http://150.242.110.240:8280/myTelkomcel/bta?msisdn=' . $msisdn . '&sourceAddress=Telkomcel&message=' . urlencode($msg) . '&trxid=' . mt_rand(100000, 999999) . '&type=11&appId=0110&token=' . $this->token(), false, stream_context_create($arrContextOptions));
			}
		}

		header('content-type:text/json');
		echo json_encode($return);
	}

	public function startQueue()
	{
		$detCounter = $this->db->select('nomor')->from('queue')->where('id', $this->session->userdata('id_counter'))->where('id_counter_setting', $this->session->userdata('id_counter_setting'))->get()->row();
		$ret = array('status' => false, 'date' => date('Y-m-d\TH:i'), 'msisdn' => '', 'name' => '', 'queue_no' => '', 'queue' => '');

		if (isset($detCounter->nomor)) {
			$id_counter = $this->session->userdata('id_counter');
			$queue = $this->db->select('queue.id,queue_list.nama, queue_list.msisdn')->from('queue')->where('queue.id', $id_counter)->join('queue_list', 'queue_list.id=queue.id_list', 'left')->get()->row();

			$ret['queue_no'] = $detCounter->nomor;

			//base_url('ComplainHandling/create_complain/'); ?msisdn=' + $(this).data('msisdn') + '&name=' + $(this).data('nama') + '&sc=' + $(this).data('serviceClass') + '&call_center=' + $(this).data('call_center');
			if (isset($queue->msisdn) && $queue->msisdn) {
				$msisdn = substr($queue->msisdn, 0, 3) == '670' ? $queue->msisdn : '670' . $queue->msisdn;
				$nama = $this->getInfoAnc($msisdn);
				$serviceClass = $this->getInfo($msisdn);
				$serviceClass = $serviceClass['serviceClass'];

				$ret['name'] = isset($nama['customername']) ? $nama['customername'] : '';
				$ret['msisdn'] = $msisdn;
				$ret['sc'] = $serviceClass;

				// header('location:' . base_url('ComplainHandling/create_complain/?msisdn=' . $queue->msisdn . '&name=' . $nama['customername'] . '&sc=' . $serviceClass . '&call_center=&channel=Plaza'));
			} else {

				$ret['name'] = $queue->nama;

				// header('location:' . base_url('ComplainHandling/create_complain/?name=' . $queue->nama . '&channel=Plaza'));
			}
		}

		header('content-type:text/json');
		echo json_encode($ret);
	}

	private function getInfo($msisdn)
	{
		$msisdn = str_replace(array('*', '+'), '', $msisdn);

		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer" => false,
				"verify_peer_name" => false,
			),
		);

		// $data = file_get_contents('http://150.242.110.240:8280/myTelkomcel/bta?type=30&msisdn=' . $msisdn . '&trxid=' . mt_rand(100000, 999999) . '&token=' . $this->token(), false, stream_context_create($arrContextOptions));
		$data = file_get_contents('http://172.17.12.126:9080/vas/account/accountpackages?msisdn=670' . $msisdn . '&trxid=' . mt_rand(100000, 999999) . '&channel=300', false, stream_context_create($arrContextOptions));
		$json = json_decode($data, true);

		return $json;
	}

	private function getInfoAnc($msisdn, $print = false)
	{
		$msisdn = str_replace(array('*', '+'), '', $msisdn);

		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer" => false,
				"verify_peer_name" => false,
			),
		);

		$data = file_get_contents('http://172.17.12.126:9080/anc/' . $msisdn, false, stream_context_create($arrContextOptions));
		$json = json_decode($data, true);

		if (isset($json['customername'])) {
			if (!$print) {
				return $json;
			} else {
				echo $data;
			}
		} else {
			return null;
		}
	}

	public function registration($id_counter_setting = 1)
	{
		$detSetting = $this->db->select('*')->from('queue_setting')->where('id', $id_counter_setting)->get()->row();
		?>
		<!DOCTYPE html>
		<html dir="ltr" lang="en-US">

		<head>

			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
			<meta name="author" content="PT. Shibly Teknologi Solusi" />
			<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
			<title><?php echo $detSetting->judul; ?></title>
			<link rel="shortcut icon" href="<?= base_url('template/'); ?>assets/images/favicon_new.png" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0">

			<script type="text/javascript">
				if (window.location.protocol != "https:") {
					window.location.protocol = "https";
				}
			</script>
			<style>
				@import url('https://fonts.googleapis.com/css2?family=Open+Sans&display=swap');

				body {
					padding: 0px;
					margin: 0px;
					font-family: 'Open Sans', sans-serif;
				}

				/*basic reset*/
				* {
					margin: 0;
					padding: 0;
				}

				html {
					height: 100%;
					/*Image only BG fallback*/

					/*background = gradient + image pattern combo*/
					background:
						linear-gradient(rgba(196, 102, 0, 0.6), rgba(155, 89, 182, 0.6));
				}

				body {
					font-family: montserrat, arial, verdana;
				}

				/*form styles*/
				#msform {
					width: 100%;
					margin: 10px auto;
					text-align: center;
					position: relative;
				}

				#msform fieldset {
					background: white;
					border: 0 none;
					border-radius: 3px;
					box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.4);
					padding: 20px 10px;
					box-sizing: border-box;
					width: 85%;
					margin: 0 7.5%;

					/*stacking fieldsets above each other*/
					position: relative;
				}

				/*Hide all except first fieldset*/
				#msform fieldset:not(:first-of-type) {
					display: none;
				}

				/*inputs*/
				#msform input,
				#msform textarea {
					padding: 15px;
					border: 1px solid #ccc;
					border-radius: 3px;
					margin-bottom: 10px;
					width: 100%;
					box-sizing: border-box;
					font-family: montserrat;
					color: #2C3E50;
					font-size: 13px;
				}

				/*buttons*/
				#msform .action-button {
					width: 100px;
					background: #27AE60;
					font-weight: bold;
					color: white;
					border: 0 none;
					border-radius: 1px;
					cursor: pointer;
					padding: 10px 5px;
					margin: 10px 5px;
				}

				#msform .action-button:hover,
				#msform .action-button:focus {
					box-shadow: 0 0 0 2px white, 0 0 0 3px #27AE60;
				}

				/*headings*/
				.fs-title {
					font-size: 15px;
					text-transform: uppercase;
					color: #2C3E50;
					margin-bottom: 10px;
				}

				.fs-subtitle {
					font-weight: normal;
					font-size: 13px;
					color: #666;
					margin-bottom: 20px;
				}

				/*progressbar*/
				#progressbar {
					margin-bottom: 30px;
					overflow: hidden;
					/*CSS counters to number the steps*/
					counter-reset: step;
				}

				#progressbar li {
					list-style-type: none;
					color: white;
					text-transform: uppercase;
					font-size: 9px;
					width: 33.33%;
					float: left;
					position: relative;
				}

				#progressbar li:before {
					content: counter(step);
					counter-increment: step;
					width: 20px;
					line-height: 20px;
					display: block;
					font-size: 10px;
					color: #333;
					background: white;
					border-radius: 3px;
					margin: 0 auto 5px auto;
				}

				/*progressbar connectors*/
				#progressbar li:after {
					content: '';
					width: 100%;
					height: 2px;
					background: white;
					position: absolute;
					left: -50%;
					top: 9px;
					z-index: -1;
					/*put it behind the numbers*/
				}

				#progressbar li:first-child:after {
					/*connector not needed before the first step*/
					content: none;
				}

				/*marking active/completed steps green*/
				/*The number of the step and the connector before it = green*/
				#progressbar li.active:before,
				#progressbar li.active:after {
					background: #27AE60;
					color: white;
				}
			</style>

		<body>
			<div style="font-size: 2rem;color: white;text-align: center;padding: 2rem;margin-top:1rem">
				Registration
			</div>

			<!-- multistep form -->
			<form id="msform" onsubmit="return false;">
				<!-- progressbar -->
				<ul id="progressbar">
					<li class="active">Choose your option</li>
					<li>Detail Guest</li>
					<li>Review & Submit</li>
				</ul>
				<!-- fieldsets -->
				<fieldset>
					<h2 class="fs-title">Do you have active number Telkomcel ?</h2>
					<h3 class="fs-subtitle">Step 1</h3>

					<div>
						<label><input type="radio" name="have_msisdn" value="Y" style="width: auto;">
							Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<label><input type="radio" name="have_msisdn" value="N" style="width: auto;"> No</label>
					</div>

					<input type="button" name="next" class="next action-button" value="Next" />
				</fieldset>
				<fieldset>

					<div id="formFoto">
						<h2 class="fs-title">Please fill your name</h2>
						<h3 class="fs-subtitle">Step 2</h3>

						<div>
							<div id="my_camera"></div>
							<!--<input type="button" class="action-button" value="Take Photo" onclick="take_snapshot()">-->
							<!--<input type="button" class="action-button" value="Re-Take Photo" onclick="startCamera()">-->
							<input type="text" id="txtNama" name="nama" placeholder="Your name Ex : Davidson Gonzales">

							<div id="results"></div>
						</div>
					</div>
					<div id="formNomor">
						<h2 class="fs-title">Fill your active Telkomcel number</h2>
						<h3 class="fs-subtitle">Step 2</h3>

						<input type="number" min="73000000" name="msisdn" placeholder="Your MSISDN Ex: 73000000" />
					</div>

					<input type="button" name="previous" class="previous action-button" value="Previous" />
					<input type="button" name="next" class="next action-button" value="Next" />
				</fieldset>
				<fieldset>
					<h2 class="fs-title">Resume Details</h2>
					<h3 class="fs-subtitle">Final Step</h3>

					<div id="formReview">

					</div>

					<input type="button" name="previous" class="previous action-button" value="Previous" />
					<input type="button" name="submit" class="submit action-button" value="Submit" />
				</fieldset>
			</form>

			<!--<input id="file" type="file" name="foto" style="display:none" onchange="take_snapshot()" accept="image/*" capture="camera"/>-->

			<script src="<?php echo base_url('dist/jquery-3.3.1.js'); ?>"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
			<!--<script src="<?php echo base_url('dist/webcamjs-master/webcam.min.js'); ?>"></script>-->
			<script>
				//                    var imgResult = '';

				//                    function startCamera() {
				//                        imgResult = '';
				//
				//                        //                                        Webcam.set({
				//                        //                                            width: 280,
				//                        //                                            height: 200,
				//                        //                                            image_format: 'jpeg',
				//                        //                                            jpeg_quality: 90
				//                        //                                        });
				//                        //                                        Webcam.attach('#my_camera');
				//
				//                        $('#file').trigger('click');
				//                    }

				function take_snapshot() {
					//                        Webcam.snap(function (data_uri) {
					//                            Webcam.reset();
					//                            imgResult = data_uri;
					//                            $('#my_camera').html('<img id="imgResult" src="' + data_uri + '"/>');
					//                        document.getElementById('results').innerHTML = '<img src="' + data_uri + '"/>';
					//                        });

					var reader = new FileReader();
					reader.onload = function (e) {
						var image = new Image();
						image.onload = function (imageEvent) {

							// Resize the image
							var canvas = document.createElement('canvas'),
								max_size = 544, // TODO : pull max size from a site config
								width = image.width,
								height = image.height;
							if (width > height) {
								if (width > max_size) {
									height *= max_size / width;
									width = max_size;
								}
							} else {
								if (height > max_size) {
									width *= max_size / height;
									height = max_size;
								}
							}
							canvas.width = width;
							canvas.height = height;
							canvas.getContext('2d').drawImage(image, 0, 0, width, height);
							//                                imgResult = canvas.toDataURL('image/jpeg');
							//                                var resizedImage = dataURLToBlob(dataUrl);
							//                                $.event.trigger({
							//                                    type: "imageResized",
							//                                    blob: resizedImage,
							//                                    url: dataUrl
							//                                });

							//                                $('#my_camera').html('<img id="imgResult" style="width:100%" src="' + imgResult + '"/>');
						}
						image.src = e.target.result;
					};
					if ($('#file')[0].files[0] != null) {
						reader.readAsDataURL($('#file')[0].files[0]);
					}
				}

				$(document).ready(function () {

					//jQuery time
					var current_fs, next_fs, previous_fs; //fieldsets
					var left, opacity, scale; //fieldset properties which we will animate
					var animating; //flag to prevent quick multi-click glitches

					$(".next").click(function () {
						if ($('[name="have_msisdn"]').is(':visible')) { //step 1
							if (!$('[name="have_msisdn"]').is(':checked')) {
								alert('Please choose your option first');

								return false;
							} else {
								if ($('[name="have_msisdn"]:checked').val() == 'Y') {
									$('#formFoto').hide();
									$('#formNomor').show();
								} else {
									//                                        startCamera();

									$('#formNomor').hide();
									$('#formFoto').show();
								}
							}
						} else if ($('#txtNama').is(':visible')) { //step 2
							if (!$('#txtNama').val()) { //|| $('#imgResult').length == 0
								//                                    alert('Please take your photo and fill your name');
								alert('Please fill your name');

								return false;
							}
						} else { //step 3

						}

						setTimeout(function () {
							$('.dataResult').remove();

							if (!$('[name="have_msisdn"]').is(':visible') && !$('#txtNama').is(':visible')) {
								if ($('[name="have_msisdn"]:checked').val() == 'Y') {
									$('#formReview').append('<div class="dataResult"><h4>MSISDN : ' + $('[name="msisdn"]').val() + '</h4></div>');
								} else {
									$('#formReview').append('<div class="dataResult"><h4>Name : ' + $('#txtNama').val() + '</h4></div>');
									//                                        $('#formReview').append('<div class="dataResult"><h4>Foto : </h4></div>');
									//                                        $('#formReview').append('<div class="dataResult"><img style="width:100%" src="' + imgResult + '"></div>');
								}
							}
						}, 1000);


						if (animating)
							return false;
						animating = true;

						current_fs = $(this).parent();
						next_fs = $(this).parent().next();

						//activate next step on progressbar using the index of next_fs
						$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

						//show the next fieldset
						next_fs.show();
						//hide the current fieldset with style
						current_fs.animate({
							opacity: 0
						}, {
							step: function (now, mx) {
								//as the opacity of current_fs reduces to 0 - stored in "now"
								//1. scale current_fs down to 80%
								scale = 1 - (1 - now) * 0.2;
								//2. bring next_fs from the right(50%)
								left = (now * 50) + "%";
								//3. increase opacity of next_fs to 1 as it moves in
								opacity = 1 - now;
								current_fs.css({
									'transform': 'scale(' + scale + ')',
									'position': 'absolute'
								});
								next_fs.css({
									'left': left,
									'opacity': opacity
								});
							},
							duration: 800,
							complete: function () {
								current_fs.hide();
								animating = false;
							},
							//this comes from the custom easing plugin
							easing: 'easeInOutBack'
						});
					});

					$(".previous").click(function () {
						if (animating)
							return false;
						animating = true;

						current_fs = $(this).parent();
						previous_fs = $(this).parent().prev();

						//de-activate current step on progressbar
						$("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

						//show the previous fieldset
						previous_fs.show();
						//hide the current fieldset with style
						current_fs.animate({
							opacity: 0
						}, {
							step: function (now, mx) {
								//as the opacity of current_fs reduces to 0 - stored in "now"
								//1. scale previous_fs from 80% to 100%
								scale = 0.8 + (1 - now) * 0.2;
								//2. take current_fs to the right(50%) - from 0%
								left = ((1 - now) * 50) + "%";
								//3. increase opacity of previous_fs to 1 as it moves in
								opacity = 1 - now;
								current_fs.css({
									'left': left
								});
								previous_fs.css({
									'transform': 'scale(' + scale + ')',
									'opacity': opacity
								});
							},
							duration: 800,
							complete: function () {
								current_fs.hide();
								animating = false;
							},
							//this comes from the custom easing plugin
							easing: 'easeInOutBack'
						});
					});

					$(".submit").click(function () {

						$.post('<?php echo base_url('display/newQueue'); ?>', {
							'msisdn': $('[name="msisdn"]').val(),
							'nama': $('#txtNama').val(),
							//                                        'foto': imgResult,
							'id_counter_setting': '<?php echo $id_counter_setting; ?>'
						}, function (res) {

							if (res.success) {
								if ($('#txtNama').val()) {
									alert('Nomor antrian anda adalah ' + res.number + ', Silahkan Tunggu');
								} else {
									alert('Nomor antrian sudah dikirimkan ke nomor anda');
								}
							} else {
								alert('Data failed saved, please ask to administrator');
							}

							window.location = '<?php base_url('display/registration/' . $id_counter_setting); ?>';
						});

					});

				});
			</script>
		</body>

		</html>
		<?php
	}
}

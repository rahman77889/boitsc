<div class='title-module'>Call Number</div>
<div class="subtitle-module">Smart Call &raquo; Call Number</div>

<div class="row flex-grow">
    <div class="col-12">
        <div class="card" style="border-radius: 10px;" >
            <div class="card-title head-module-action" style="text-align: left">
                <a href="javascript:void(0)"><i class="fa fa-phone"></i> Call Number</a>
            </div>
            <div class="card-body body-module-action">
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="containerPadNumber">
                                    <div class="one number">
                                        <h1 class="num1" data-val="1">1</h1>
                                    </div>

                                    <div class="two number">
                                        <h1 class="num2" data-val="2">2</h1>
                                        <ul>
                                            <li>A</li>
                                            <li>B</li>
                                            <li>C</li>
                                        </ul>
                                    </div>

                                    <div class="three number">
                                        <h1 class="num3" data-val="3">3</h1>
                                        <ul>
                                            <li>D</li>
                                            <li>E</li>
                                            <li>F</li>
                                        </ul>
                                    </div>

                                    <div class="four number">
                                        <h1 class="num4" data-val="4">4</h1>
                                        <ul>
                                            <li>G</li>
                                            <li>H</li>
                                            <li>I</li>
                                        </ul>
                                    </div>

                                    <div class="five number">
                                        <h1 class="num5" data-val="5">5</h1>
                                        <ul>
                                            <li>J</li>
                                            <li>K</li>
                                            <li>L</li>
                                        </ul>
                                    </div>

                                    <div class="six number">
                                        <h1 class="num6" data-val="6">6</h1>
                                        <ul>
                                            <li>M</li>
                                            <li>N</li>
                                            <li>O</li>
                                        </ul>
                                    </div>

                                    <div class="seven number">
                                        <h1 class="num7" data-val="7">7</h1>
                                        <ul>
                                            <li>P</li>
                                            <li>Q</li>
                                            <li>R</li>
                                            <li>S</li>
                                        </ul>
                                    </div>

                                    <div class="eight number">
                                        <h1 class="num8" data-val="8">8</h1>
                                        <ul>
                                            <li>T</li>
                                            <li>U</li>
                                            <li>V</li>
                                        </ul>
                                    </div>

                                    <div class="nine number">
                                        <h1 class="num9" data-val="9">9</h1>
                                        <ul>
                                            <li>W</li>
                                            <li>X</li>
                                            <li>Y</li>
                                            <li>Z</li>
                                        </ul>
                                    </div>

                                    <div class="star number">
                                        <h1 class="star1" data-val="*">*</h1>
                                    </div>

                                    <div class="zero number">
                                        <h1 class="zero1" data-val="0">0</h1>
                                        <ul>
                                            <li>+</li>
                                        </ul>
                                    </div>

                                    <div class="tag number">
                                        <h1 class="tag1" data-val="#">#</h1>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 style="margin-top:1rem">Call To Number : </h3>
                                        <input type="text" id="msisdnCall" oninput="this.value = this.value.replace(/[^0-9*#]/g, '').replace(/(\..*)\./g, '$1');" class="form-control" style="font-size:1.2rem;margin-top:1rem" placeholder="670XXXXXX" value="670">
                                    </div>
                                    <?php
                                    if ($this->session->userdata('tipe') == '123') {
                                        ?>
                                        <div class="col-md-6">
                                            <button class="btn btn-success btn-lg" id="btnCall" style="margin-top:2rem;width: 100%;font-weight: bold;font-size:1.15rem">Call Using +123</button>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <?php
                                    } elseif ($this->session->userdata('tipe') == '147') {
                                        ?>
                                        <div class="col-md-6">
                                            <button class="btn btn-primary btn-lg" id="btnCall2" style="margin-top:2rem;width: 100%;font-weight: bold;font-size:1.15rem">Call Using +147</button>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="col-md-6">
                                            <button class="btn btn-success btn-lg" id="btnCall" style="margin-top:2rem;width: 100%;font-weight: bold;font-size:1.15rem">Call Using +123</button>
                                        </div>
                                        <div class="col-md-6">
                                            <button class="btn btn-primary btn-lg" id="btnCall2" style="margin-top:2rem;width: 100%;font-weight: bold;font-size:1.15rem">Call Using +147</button>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <div class="col-md-6">
                                        <button class="btn btn-danger btn-lg" id="btnHangup" style="margin-top:1rem;width: 100%;font-weight: bold;font-size:1.15rem">Hangup</button>
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-warning btn-lg" id="btnClear" style="margin-top:1rem;width: 100%;font-weight: bold;font-size:1.15rem">Clear</button>
                                    </div>
                                    <div class="col-md-12">
                                        <h3 style="margin-top:2rem;">Top 30 History Call : </h3>
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Number</th>
                                                    <th>Datetime</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (count($history) > 0) {
                                                    foreach ($history as $his) {
                                                        echo '  <tr>
                                                        <td>' . $his->msisdn . '</td>
                                                        <td>' . $his->tgl . '</td>
                                                    </tr>';
                                                    }
                                                } else {
                                                    echo '  <tr>
                                                        <td colspan="2">There\'s no history</td>
                                                    </tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .containerPadNumber {
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

    .containerPadNumber .number {
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
    .containerPadNumber .number:hover {
        border: 1px solid #EEB111;
    }
    .containerPadNumber .number h1 {
        font-weight: 400;
        padding-top: 1rem;
        margin-bottom: 0px;
    }
    .containerPadNumber .number .num1 {
        margin-top: -20px;
    }
    .containerPadNumber .number ul {
        list-style: none;
        display: flex;
        display: -moz-flex;
        display: -webkit-flex;
        padding-left: 5px;
    }
    .containerPadNumber .number ul li {
        padding: 2px;
    }
</style>
<script>
    $(document).ready(function () {
        $('.containerPadNumber .number').click(function () {
            var val = $('#msisdnCall').val();
            $('#msisdnCall').val($('#msisdnCall').val() + $(this).find('h1').data('val'));
            var url = '<?php echo base_url('template/assets/button.mp3'); ?>';
            $('body audio').remove();

            $('body').append('<audio autoplay="autoplay"><source src="' + url + '" type="audio/mpeg" /></audio>');
        });

        $('#btnCall').click(function () {
            if ($('#msisdnCall').val()) {
                if (localStorage['status'] == 'stand_by') {
//                    $.getJSON("http://localhost:8083/call/" + $('#msisdnCall').val(), function (result) {});

                    placeCall('*' + $('#msisdnCall').val(), true);
                    $('#btnCall, #btnCall2').text('Busy - Ongoing Call').attr('disabled', true);
                } else {
                    if (localStorage['status'] != null) {
                        alert('You still in establish calling');
                    } else {
                        alert('You don\'t have extend number');
                    }
                }
            } else {
                alert('Please provide number');
            }
        });
        $('#btnCall2').click(function () {
            if ($('#msisdnCall').val()) {
                if (localStorage['status'] == 'stand_by') {
//                    $.getJSON("http://localhost:8083/call/" + $('#msisdnCall').val(), function (result) {});

                    placeCall('**' + $('#msisdnCall').val(), true);
                    $('#btnCall, #btnCall2').text('Busy - Ongoing Call').attr('disabled', true);
                } else {
                    if (localStorage['status'] != null) {
                        alert('You still in establish calling');
                    } else {
                        alert('You don\'t have extend number');
                    }
                }
            } else {
                alert('Please provide number');
            }
        });
        $('#btnHangup').click(function () {
            endCall();

            $('#btnCall').text('Call Using +123').removeAttr('disabled');
            $('#btnCall2').text('Call Using +147').removeAttr('disabled');
        });
        $('#btnClear').click(function () {
            $('#msisdnCall').val('670');
        });
    });
</script>
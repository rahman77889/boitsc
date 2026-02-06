<div class='title-module'>Auto Call Number</div>
<div class="subtitle-module">Smart Call &raquo; Auto Call</div>

<div class="row flex-grow">
    <div class="col-12">
        <div class="card" style="border-radius: 10px;" >
            <div class="card-title head-module-action" style="text-align: left">
                <a href="javascript:void(0)"><i class="fa fa-phone"></i> Auto Call Number</a>
            </div>
            <div class="card-body body-module-action">
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <h3>Fill array number : </h3>
                                <h4>Please use separator (comma or enter)</h4>
                                <textarea oninput="this.value = this.value.replace(/[^0-9,\n*#]/g, '').replace(/(\..*)\./g, '$1');"  class="form-control" id="collectNumber" rows="20" placeholder="670XXXXXX" style="font-size:1.5rem;line-height: 1.7rem;margin-top:1rem;"></textarea>

                                <div id="numberAction" style="display:none;min-height:20rem;border:1px solid #dee2e6;font-size:1.5rem;line-height: 1.7rem;margin-top:1rem;padding-top:1rem;padding-bottom:1rem;height:auto;">

                                </div>
                            </div>
                            <div class="col-md-7">
                                <div style="margin-top:5.5rem"><button class="btn btn-success btn-md" id="btnAutoCall" style="width:18rem">Run Auto Call Via Agent Now</button></div>
                                <!--<div style="margin-top:1rem"><button class="btn btn-warning btn-md btnIvr" data-lang="promo" style="width:20rem">Run Auto Call Via IVR Promo Discount</button></div>-->
                                <div style="margin-top:1rem"><button class="btn btn-primary btn-md btnIvr" data-lang="en" style="width:18rem">Run Auto Call Via IVR (English) Now</button></div>
                                <div style="margin-top:1rem"><button class="btn btn-default btn-md btnIvr" style="background-color: gold;width:18rem" data-lang="id"  style="">Run Auto Call Via IVR (Indonesia) Now</button></div>
                                <div style="margin-top:1rem"><button class="btn btn-info btn-md btnIvr" data-lang="tt"  style="width:18rem">Run Auto Call Via IVR (Tetum) Now</button></div>
                                <div style="margin-top:1rem"><button class="btn btn-warning btn-md" id="btnStopAutoCall" style="width:18rem;display: none;;margin-top:2.5rem;font-size:1.5rem">Stop Auto Call</button></div>
                                <div style="margin-top:1rem"><button class="btn btn-danger btn-md" id="btnHangupAutoCall" onclick="endCall();" style="width:18rem;display: none;;margin-top:0.5rem;font-size:1.5rem">Hangup Current Call</button></div>

                                <h3 style="margin-top:2rem;">Top 30 History Call IVR : </h3>
                                <div style="height: 10rem;overflow-x: auto">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Number</th>
                                                <th>Language</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (count($historyIVR) > 0) {
                                                foreach ($historyIVR as $his) {
                                                    echo '  <tr>
                                                        <td>' . $his->msisdn . '</td>
                                                        <td>' . $his->lang . '</td>
                                                        <td>' . $his->status . '</td>
                                                    </tr>';
                                                }
                                            } else {
                                                echo '  <tr>
                                                        <td>There\'s no history</td>
                                                    </tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                                <h3 style="margin-top:2rem;">Top 30 History Call : </h3>
                                <div style="height: 10rem;overflow-x: auto">
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
<style>
    .numberList{
        padding:0.5rem;
    }
</style>
<script>
    var SIC;
    var IVR = false;
    var unique_id = '';
    var SICIVR;
    $(document).ready(function () {
        var listNumber = [];
        var index = -1;

        $('.btnIvr').click(function () {
            var lang = $(this).data('lang');
            $('#btnStopAutoCall').show();
            $('.btnIvr,#btnAutoCall').hide();

            var tipe = '';
            if (lang == 'promo') {
                tipe = 'promo';
            } else {
                tipe = 'anc';
            }

            $.post('<?php echo base_url('SmartCall/auto_call_number/'); ?>', {number: $('#collectNumber').val(), "lang": lang, "tipe": tipe}, function (res) {
                if (res.success) {

                    unique_id = res.unique_id;

                    $('#collectNumber').val('');
                    Swal.fire(
                            'Sukses!',
                            'All number has been success insert to queue and will start call shortly by IVR',
                            'success'
                            );

                    runCalIvr();
                }
            });
        });

        $('#collectNumber').change(function () {
            $('#collectNumber').val($('#collectNumber').val().toString().split(',').join("\n").split(' ').join('').split('+').join(''));
        });

        $('#btnAutoCall').click(function () {
            if ($('#collectNumber').val() && localStorage['status'] == 'stand_by') {
                $(this).hide();

                $('#btnStopAutoCall, #btnHangupAutoCall').show();

                runCall();
            } else {
                if (localStorage['status'] != 'stand_by') {
                    if (localStorage['status'] != null) {
                        alert('You still in establish calling');
                    } else {
                        alert('You don\'t have extend number');
                    }
                } else {
                    alert('Please provide number');
                }
            }
        });

        $('#btnStopAutoCall').click(function () {
            if (unique_id) {
                clearInterval(SICIVR);

                $.get('<?php echo base_url('SmartCall/cancel_ivr/'); ?>' + unique_id, function (res) {
                    if (res.success) {
                        unique_id = '';

                        $('#btnStopAutoCall').hide();
                        $('#collectNumber').val('');
                        $('#collectNumber').show();
                        $('#numberAction').hide().html('');
                        $('.btnIvr,#btnAutoCall').show();

                        Swal.fire(
                                'Sukses!',
                                'Queue has been success canceled',
                                'success'
                                );
                    }
                });
            } else {
                clearInterval(SIC);
                $('#numberAction').html('').hide();
                $('#collectNumber').val('');
                $('#collectNumber').show();
                listNumber = [];
                $('#btnStopAutoCall, #btnHangupAutoCall').hide();
                $('#btnAutoCall').show();
                index = -1;

                IVR = false;

                endCall();
            }
        });

        function runCalIvr() {
            $('#collectNumber').hide();
            $('#numberAction').show().html('');

            getResIvr();

            SICIVR = setInterval(function () {
                getResIvr();
            }, 2000);
        }

        function getResIvr() {
            $.get('<?php echo base_url('SmartCall/result_ivr/'); ?>' + unique_id, function (res) {
                $('#numberAction').html('');

                for (var l in res.list) {
                    var ls = res.list[l];

                    $('#numberAction').append('<div class="numberList">' + ls['msisdn'] + '  (' + ls['lang'] + ') <button class="btn btn-sm btn-' + (ls['result'] ? 'primary' : 'warning') + '">' + (ls['result'] ? ls['result'] : 'waiting') + '</button></div>')
                }
            });
        }

        function runCall() {
            listNumber = $('#collectNumber').val().toString().split("\n");

            $('#collectNumber').hide();

            $('#numberAction').show().html('<div class="numberList">' + listNumber.join('</div><div class="numberList">') + '</div>');
//                console.log(listNumber)
            checkCall(index);
            SIC = setInterval(checkCall, 10000);
        }

        function startCall(index) {
            if (listNumber[index] != null) {
                var msisdn = listNumber[index];

                $('.numberList').css('background-color', 'white');
                $('.numberList:eq(' + index + ')').css('background-color', '#f4d3ff');

                if (msisdn) {
//                    $.getJSON("http://localhost:8083/call/" + msisdn, function (result) {
////                        console.log(result);
//                    });
                    placeCall('*' + msisdn, true);
//                    console.log('call : ' + msisdn)
                }
            } else {
                $('#btnStopAutoCall').trigger('click');
            }
        }

        function checkCall() {
            if (localStorage['status'] == 'stand_by') {
                index += 1;
                startCall(index);

//                clearInterval(SIC);
//                SIC = setInterval(checkCall, 1000);
            } else {
                clearInterval(SIC);
                SIC = setInterval(checkCall, 10000);
            }
        }
    });
</script>
<center>
    <div class="card bg-light mb-3" style="max-width: 18rem;border-radius: 10px; margin-top: 15%">
        <div class="card-header" style="border-top-left-radius: 10px;border-top-right-radius: 10px;font-size: 15px;font-weight: bold;padding:10px;background-color: black;color: white;">Change Password</div>
        <form id="inForm" method="POST" action="javascript:void(0);" class="smart-form"
              novalidate="novalidate">
            <div class="card-body" style="position: relative;margin-left: -37px">

                <div class="row mt-1">
                    <div class="col-md-7">
                        <label for="label-control" style="font-size: 13px">Current Password</label>
                    </div>
                    <div class="col-md-4">
                        <!-- <?php echo $query->fullName; ?> -->
                        <input class="form-control form-control-sm"name="password" type="password" style="width: 150px;position: relative;left: -51%">  
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col-md-6">
                        <label for="label-control" style="font-size: 13px;position: relative;left: 4%">New Password</label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-sm" name="password_dua" type="password" style="width: 150px;position: relative;left: -18%">  
                    </div>
                </div>

                <div class="row mt-1">
                    <div class="col-md-7">
                        <label for="label-control" style="font-size: 13px;position: relative;left: 1%">Re-type New Pass</label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-sm" name="open_password" type="password" style="width: 150px;;position: relative;left: -51%">  
                    </div>
                </div>

                <div style="justify-content: center; margin-top: 10px">
                    <button type="button" class="btn btn-lg btn-primary" style="padding:3%;left: 8%;position: relative">Cancel</button>
                    <button id="update" class="btn btn-secondary btn-lg" style="padding:3%;left: 8%;position: relative">Update</button>
                </div>

            </div>
        </form>
    </div>
</center>

<script>


    $(document).ready(function () {
        $('#update').click(function () {
            $.ajax({
                url: '<?= base_url("Systems/upPassword"); ?>',
                type: 'POST',
//                dataType: 'JSON',
                data: $('form:visible').serialize()
            }).done(function (data) {
                Swal.fire(
                        data,
                        'You clicked the button!',
                        'success'
                        )
                clearInput();
                // showdata();

            }).fail(function () {
                console.log("error");
            }).always(function () {
                console.log("complete");
            });

        });
    });



    function clearInput() {
        $('input[name="password"]').val('');
        $('input[name="password_dua"]').val('');
        $('input[name="open_password"]').val('');

    }
</script>
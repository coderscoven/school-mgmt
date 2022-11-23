<?php
include 'classes/MainClass.php';
$existing_users = $crud->fetch_teachers_bursars();
?>
<div class="container-fluid">
    <div id="msg"></div>

    <form id="manage-user-credentials" role="form">

        <!-- <div class="form-group">
            <label for="assign_user_level">Select access level</label>
            <div id="assign_user_level">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" id="assign_user_level_tch" name="assign_user_level" value="Teacher">
                    <label class="custom-control-label" for="assign_user_level_tch">Teacher</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" id="assign_user_level_burs" name="assign_user_level" value="Bursar">
                    <label class="custom-control-label" for="assign_user_level_burs">Bursar</label>
                </div>
            </div>
        </div> -->

        <div class="form-group">
            <label for="assign_user_tch_id">Select user</label>
            <select name="assign_user_id" id="assign_user_tch_id" class="form-control" required>
                <option></option>
                <?php foreach ($existing_users as $existing_user) { ?>
                    <option value="<?php echo $existing_user['id'] ?>">
                        <?php echo $existing_user['teacher_names']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label for="assign_user_name">Username</label>
            <input type="text" name="assign_user_name" id="assign_user_name" class="form-control" required placeholder="Enter username:">
        </div>
        <div class="form-group">
            <label for="assign_user_password">Password</label>
            <input type="text" name="assign_user_password" id="assign_user_password" class="form-control" required placeholder="Enter password:" value="<?php echo $crud->genRandomPassword(); ?>">
        </div>

    </form>
</div>
<script>
    "use strict"
    $(function() {

        //
        $('#assign_user_tch_id').select2({
            placeholder: "--- select ---",
            width: "100%"
        });

        //
        $('#manage-user-credentials').on("submit", function(e) {

            e.preventDefault();

            let form = $('#manage-user-credentials');

            start_load()

            $.ajax({
                url: 'ajax.php?action=manage_user_credentials',
                method: 'POST',
                data: form.serialize(),
                dataType: "json",
            }).done(function(response) {

                if (response.bool === true) {
                    toastr.success(response.msg)
                    setTimeout(function() {
                        location.reload()
                    }, 1500)
                } else {
                    toastr.error(response.msg)
                    end_load()
                }

            }).fail(function(jqXHR) {
                let response = JSON.parse(jqXHR.responseText)
                toastr.error(response.msg)
                end_load()
            });
        });

    });
</script>
<?php
include 'classes/MainClass.php';

if (isset($_GET['id'])) {
    $qry = $crud->getTeacher($_GET['id']);
    foreach ($qry->fetch_array() as $k => $val) {
        $$k = $val;
    }
}
?>
<div class="container-fluid">
    <form action="" id="manage-teacher" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div id="msg" class="form-group"></div>
        <div class="form-row">
            <?php if (!isset($id)) : ?>
                <div class="form-group col-md-6">
                    <label for="teacher_photo" class="control-label">Photo</label>
                    <input type="file" class="form-control-file" id="teacher_photo" name="teacher_photo" aria-describedby="phblock" <?php echo isset($id) ? '' : 'required' ?>>
                    <div id="phblock" class="form-text small">Size: 1mb. Formats: jpeg, jpg, png</div>
                </div>
            <?php endif ?>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="name" class="control-label">Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo isset($teacher_names) ? $teacher_names : '' ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="gender" class="control-label">Gender</label>
                <div id="gender">
                    <div class="icheck-primary d-inline">
                        <input type="radio" id="gender_male" name="genders" value="Male" <?php echo isset($teacher_sex) && $teacher_sex == 'Male' ? 'checked' : '' ?>>
                        <label for="gender_male">Male
                        </label>
                    </div>
                    <div class="icheck-primary d-inline">
                        <input type="radio" id="gender_female" name="genders" value="Female" <?php echo isset($teacher_sex) && $teacher_sex == 'Female' ? 'checked' : '' ?>>
                        <label for="gender_female">Female
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="dob" class="control-label">Date of birth</label>
                <input type="text" class="form-control" id="dob" name="dob" value="<?php echo isset($teacher_dob) ? $teacher_dob : '' ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="email" class="control-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($teacher_email) ? $teacher_email : '' ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="telcont" class="control-label">Tel</label>
                <input type="text" class="form-control" id="telcont" name="telcont" value="<?php echo isset($teacher_tel) ? $teacher_tel : '' ?>" required>
            </div>
            <div class="form-group col-md-12">
                <label for="education" class="control-label">Education level</label>
                <textarea class="form-control" id="education" name="education" required rows="1"><?php echo isset($teacher_education) ? $teacher_education : '' ?></textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="location" class="control-label">Address/Street</label>
                <textarea class="form-control" id="location" name="location" required rows="1"><?php echo isset($teacher_location_address) ? $teacher_location_address : '' ?></textarea>
            </div>
            <div class="form-group col-md-12">
                <label for="salary" class="control-label">Salary(UGX)</label>
                <input type="text" class="form-control" id="salary" name="salary" value="<?php echo isset($teacher_salary) ? $teacher_salary : '' ?>" required>
            </div>
        </div>
    </form>
</div>
<script>
    $('#manage-teacher').on('reset', function() {
        $('#msg').html('')
        $('input:hidden').val('')
    })
    $('#manage-teacher').submit(function(e) {
        e.preventDefault()
        start_load()
        $('#msg').html('')
        $.ajax({
            url: 'ajax.php?action=save_teacher',
            method: 'POST',
            data: new FormData($(this)[0]),
            cache: false,
            dataType: "json",
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.bool == true) {

                    alert_toast(response.msg, 'success')
                    setTimeout(function() {
                        location.reload()
                    }, 1000)
                } else {
                    $('#msg').html('<div class="alert alert-danger mx-2">' + response.msg + '</div>')
                    end_load()
                }
            }
        })
    })

    $('.select2').select2({
        width: '100%'
    });

    $('#dob').datetimepicker({
        format: 'Y-m-d',
        timepicker: false
    })
</script>
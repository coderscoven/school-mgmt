<?php
include 'classes/MainClass.php';

if (isset($_GET['id'])) {
    $qry = $crud->getStudent($_GET['id']);
    foreach ($qry->fetch_array() as $k => $val) {
        $$k = $val;
    }
}

//
$houses = $crud->getHouses();
//
$classes = $crud->getClasses();
?>
<div class="container-fluid">
    <form action="" id="manage-student" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div id="msg" class="form-group"></div>
        <div class="form-row">
            <?php if (!isset($id)) : ?>
                <div class="form-group col-md-6">
                    <label for="student_photo" class="control-label">Photo</label>
                    <input type="file" class="form-control-file" id="student_photo" name="student_photo" aria-describedby="phblock" <?php echo isset($id) ? '' : 'required' ?>>
                    <div id="phblock" class="form-text small">Size: 1mb. Formats: jpeg, jpg, png</div>
                </div>
            <?php endif ?>
            <div class="form-group col-md-6">
                <label for="stud_class" class="control-label">Class</label>
                <select class="form-control select2" id="stud_class" name="stud_class" required data-placeholder=" --- select --- ">
                    <option></option>
                    <?php foreach ($classes as $key => $v) { ?>
                        <option value="<?php echo $v['id']; ?>" <?php echo isset($class_id) && $class_id == $v['id'] ? 'selected' : '' ?>><?php echo $v['class_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="id_no" class="control-label">Id No.</label>
                <input type="text" class="form-control" name="id_no" value="<?php echo isset($class_id) ? $id_no : $crud->generateStudentIds(); ?>" readonly>
            </div>
            <div class="form-group col-md-6">
                <label for="stud_house" class="control-label">House</label>
                <select class="form-control" id="stud_house" name="stud_house" required>
                    <?php foreach ($houses as $key => $v) { ?>
                        <option value="<?php echo $v['id']; ?>" <?php echo isset($house_id) && $house_id == $v['id'] ? 'selected' : '' ?>><?php echo $v['house_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="name" class="control-label">Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo isset($name) ? $name : '' ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="gender" class="control-label">Gender</label>
                <div id="gender">
                    <div class="icheck-primary d-inline">
                        <input type="radio" id="gender_male" name="genders" value="Male" <?php echo isset($gender) && $gender == 'Male' ? 'checked' : '' ?>>
                        <label for="gender_male">Male
                        </label>
                    </div>
                    <div class="icheck-primary d-inline">
                        <input type="radio" id="gender_female" name="genders" value="Female" <?php echo isset($gender) && $gender == 'Female' ? 'checked' : '' ?>>
                        <label for="gender_female">Female
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="dob" class="control-label">Date of birth</label>
                <input type="text" class="form-control" id="dob" name="dob" value="<?php echo isset($dob) ? $dob : '' ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="email" class="control-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? $email : '' ?>" required>
            </div>
        </div>
    </form>
</div>
<script>
    $('#manage-student').on('reset', function() {
        $('#msg').html('')
        $('input:hidden').val('')
    })
    $('#manage-student').submit(function(e) {
        e.preventDefault()
        start_load()
        $('#msg').html('')
        $.ajax({
            url: 'ajax.php?action=save_student',
            method: 'POST',
            data: new FormData($(this)[0]),
            cache: false,
            dataType: "json",
            contentType: false,
            processData: false,
        }).done(function(response) {

            if (response.bool === true) {

                alert_toast(response.msg, 'success')
                setTimeout(function() {
                    location.reload()
                }, 1000)
            } else {
                $('#msg').html('<div class="alert alert-danger mx-2">' + response.msg + '</div>')
                end_load()
            }

        }).fail(function(jqXHR) {
            end_load()
            let response = JSON.parse(jqXHR.responseText)
            $('#msg').html('<div class="alert alert-danger mx-2">' + response.msg + '</div>')
        });
    })

    $('.select2').select2({
        width: '100%'
    });

    $('#dob').datetimepicker({
        format: 'Y-m-d',
        timepicker: false
    })
</script>
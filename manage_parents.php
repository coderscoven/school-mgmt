<?php
include 'classes/MainClass.php';

if (isset($_GET['id'])) {
    $qry = $crud->getParent($_GET['id']);
    foreach ($qry->fetch_array() as $k => $val) {
        $$k = $val;
    }
}

$studs = $crud->fetchStudentsforParents();
?>
<div class="container-fluid">
    <form action="" id="manage-parents">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div id="msg" class="form-group"></div>


        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo isset($parent_names) ? $parent_names : '' ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="gender">Gender</label>
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
                <label for="telcont">Contact</label>
                <input type="text" class="form-control" id="telcont" name="telcont" value="<?php echo isset($contacts) ? $contacts : '' ?>" required>
            </div>
            <div class="form-group col-md-6">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? $email : '' ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="residence">Residence / Street / Address</label>
                <textarea class="form-control" id="residence" name="residence" required rows="1"><?php echo isset($residence) ? $residence : '' ?></textarea>
            </div>
        </div>
        <?php
        $studarray = array();
        if (isset($student_id)) {
            $studarray = explode(", ", $student_id);
        }
        ?>
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="sel_students">Select student(s)</label>
                <select class="form-control select2" id="sel_students" name="students[]" data-placeholder="--- select student(s) ---" required style="width: 100%;" multiple="multiple">
                    <option></option>
                    <?php foreach ($studs as $stud) : ?>
                        <option value="<?php echo $stud['id']; ?>" <?php echo isset($student_id) && in_array($stud['id'], $studarray) ? 'selected' : '' ?>><?php echo $stud['name'] . ' - ' . $stud['id_no']; ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    </form>
</div>
<script>
    $('#manage-parents').on('reset', function() {
        $('#msg').html('')
        $('input:hidden').val('')
    })
    $('#manage-parents').submit(function(e) {
        e.preventDefault()
        start_load()
        $('#msg').html('')
        let formdata = $('#manage-parents').serialize()
        $.ajax({
            url: 'ajax.php?action=save_parent',
            method: 'POST',
            data: formdata,
            cache: false,
            dataType: "json",
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
        allowClear: true,
        theme: 'bootstrap4'
    });
</script>
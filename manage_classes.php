<?php

include 'classes/MainClass.php';

if (isset($_GET['classnameid']) && isset($_GET['classinfoid'])) {
    $qry = $crud->fetch_sel_class_info($_GET['classnameid'], $_GET['classinfoid']);
    // foreach ($qry->fetch_array() as $k => $val) {
    // }
}

?>
<div class="container-fluid">
    <form action="" role="form" id="manage-class">
        <input type="text" name="classnameid" value="<?php echo isset($qry->classnameid) ? $qry->classnameid : '' ?>">
        <input type="text" name="classinfoid" value="<?php echo isset($qry->classinfoid) ? $qry->classinfoid : '' ?>">
        <div class="row">
            <div class="col-lg-12">
                <div id="msg" class="form-group"></div>
                <div class="form-group">
                    <label for="class_name" class="control-label">Class</label>
                    <input type="text" class="form-control" id="class_name" placeholder="Class name" name="class_name" value="<?php echo isset($qry->class_name) ? $qry->class_name : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="class_teacher">Class teacher</label>
                    <select name="class_teacher" id="class_teacher" class="form-control select2" required data-placeholder='--- select ---'>
                        <option></option>
                        <?php foreach ($crud->fetch_teachers() as $key => $teacher) { ?>
                            <option value="<?php echo $teacher['id'] ?>" <?php echo isset($qry->teacher_id) && $qry->teacher_id == $teacher['id'] ? 'selected' : '' ?>><?php echo $teacher['teacher_names'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="class_fees">Fees</label>
                    <select name="class_fees" id="class_fees" class="form-control" required>
                        <?php foreach ($crud->fetch_fees() as $key => $fees) { ?>
                            <option value="<?php echo $fees['id'] ?>" <?php echo isset($qry->fees_id) && $qry->fees_id == $fees['id'] ? 'selected' : '' ?>><?php echo $fees['amount'] . ' (' . $fees['school_section'] . ')' ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="class_description" class="control-label">Description (optional)</label>
                    <textarea name="class_description" id="class_description" cols="3" rows="2" class="form-control"><?php echo isset($qry->class_details) ? $qry->class_details : '' ?></textarea>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="fee_clone" style="display: none">
    <table>
        <tr>
            <td class="text-center"><button class="btn-sm btn-outline-danger" type="button" onclick="rem_list($(this))"><i class="fa fa-times"></i></button></td>
            <td>
                <input type="hidden" name="fid[]">
                <input type="hidden" name="type[]">
                <p><small><b class="ftype"></b></small></p>
            </td>
            <td>
                <input type="hidden" name="amount[]">
                <p class="text-right"><small><b class="famount"></b></small></p>
            </td>
        </tr>
    </table>
</div>
<script>
    $('#manage-class').on('reset', function() {
        $('#msg').html('')
        $('input:hidden').val('')
    });

    $('#manage-class').on("submit", function(ev) {
        ev.preventDefault()
        start_load()
        $('#msg').html('')

        $.ajax({
            url: 'ajax.php?action=save_class',
            data: $('#manage-class').serialize(),
            cache: false,
            method: 'POST',
            dataType: "JSON",
        }).done(function(response) {
            if (response.bool === true) {
                alert_toast(response.msg, 'success')
                setTimeout(function() {
                    location.reload()
                }, 1000)
            } else {
                alert_toast(response.msg, 'danger')
                end_load()
            }
        }).fail(function(jqXHR) {
            let response = JSON.parse(jqXHR.responseText)
            alert_toast(response.msg, 'danger')
            end_load()
        });
    });

    $('.select2').select2({
        placeholder: "Please Select here",
        width: '100%',
        theme: 'bootstrap4'
    })
</script>
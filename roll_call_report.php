<?php
//
$studentsList = $crud->select_students(true);
$studentsList->execute();
$students = $studentsList->fetchAll();
//
$cls = $crud->getClasses();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow">
                <div class="card-header d-print-none">
                    <form id="student_roll_call_report_dtfrm" role="form" class="d-print-none">
                        <fieldset form="student_roll_call_report_dtfrm">

                            <div class="form-row align-items-md-center align-items-lg-center">
                                <div class="form-group col-md-6">
                                    <label for="roll_call_class">Class</label>
                                    <select name="roll_call_class" id="roll_call_class" class="form-control">
                                        <option value="">--- select class ---</option>
                                        <?php foreach ($cls as $row) : ?>
                                            <option value="<?php echo $row['id'] ?>"><?php echo $row['class_name'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="roll_call_report_date">Date</label>
                                    <input type="text" name="roll_call_date" id="roll_call_report_date" class="form-control" value="<?php echo date('Y-m-d') ?>">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="roll_call_loadbtn" class="mb-5"></label>
                                    <button type="submit" class="btn btn-primary" id="roll_call_loadbtn">Submit</button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="card-body">
                    <span id="rc_feedback"></span>
                    <span id="load_student_roll_call_report"></span>
                </div>
            </div>
        </div>
    </div>
</div>
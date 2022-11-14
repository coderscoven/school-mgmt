<?php
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
//
$studentsList = $crud->select_students(true);
$studentsList->execute();
$students = $studentsList->fetchAll();
//
$terms = $crud->fetch_school_terms();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow">
                <div class="card-header d-print-none">
                    <form id="students_report_dataform" role="form" class="d-print-none">
                        <fieldset form="students_report_dataform">
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="rprt_student">Student</label>
                                    <select name="rprt_student" id="rprt_student" class="form-control select2 rprt-student-filter" data-placeholder="--- select student ---">
                                        <option value="" data-type="dt_student"></option>
                                        <?php foreach ($students as $row) : ?>
                                            <option value="<?php echo $row['id'] ?>" data-type="dt_student"><?php echo $row['name'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-3 d-none">
                                    <label for="rprt_school_term">School Term</label>
                                    <select name="rprt_school_term" id="rprt_school_term" class="form-control rprt-student-filter">
                                        <option value="" data-type="dt_school_term">--- select ---</option>
                                        <?php foreach ($terms as $term) : ?>
                                            <option value="<?php echo $term['id'] ?>" data-type="dt_school_term"><?php echo $term['sch_year'] . ' - ' . $term['sch_term'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="students_report_btn" class="mb-5"></label>
                                    <button type="submit" class="btn btn-primary" id="students_report_btn">Submit</button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="card-body">
                    <span id="load_student_report_results"></span>
                </div>
            </div>
        </div>
    </div>
</div>
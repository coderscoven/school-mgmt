<?php
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
                    <form action="" role="form" id="rprt_requirement_dataform">
                        <fieldset form="rprt_requirement_dataform">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="rprt_student">Student</label>
                                    <select name="rprt_req_student" id="rprt_student" class="form-control select2" data-placeholder="--- select student ---">
                                        <option value=""></option>
                                        <?php foreach ($students as $row) : ?>
                                            <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="rprt_school_term">School Term</label>
                                    <select name="rprt_req_school_term" id="rprt_school_term" class="form-control">
                                        <option value="">--- select ---</option>
                                        <?php foreach ($terms as $term) : ?>
                                            <option value="<?php echo $term['id'] ?>"><?php echo $term['sch_year'] . ' - ' . $term['sch_term'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="rprt_requirement_btn" class="mb-5"></label>
                                    <button type="submit" class="btn btn-primary" id="rprt_requirement_btn">Submit</button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="card-body">
                    <span id="load_requirements_report_cnt"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
//
$teachers = $crud->fetch_teachers();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow">
                <div class="card-header d-print-none">
                    <form id="teacher_info_report_dataform" role="form" class="d-print-none">
                        <fieldset form="teacher_info_report_dataform">
                            <div class="form-row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="rprt_teacher_info">Teacher</label>
                                    <select name="rprt_teacher_info" id="rprt_teacher_info" class="form-control select2" data-placeholder="--- select teacher ---">
                                        <option value=""></option>
                                        <?php foreach ($teachers as $row) : ?>
                                            <option value="<?php echo $row['id'] ?>"><?php echo $row['teacher_names'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="teacher_info_report_btn" class="mb-5"></label>
                                    <button type="submit" class="btn btn-primary" id="teacher_info_report_btn">Submit</button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="card-body">
                    <span id="load_teacher_report_results"></span>
                </div>
            </div>
        </div>
    </div>
</div>
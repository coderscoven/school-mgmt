<div class="container">

    <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12">

            <form action="" role="form" id="save_stud_requirements">

                <fieldset class="row" form="save_stud_requirements">

                    <div class="col-lg-5 col-md-6">
                        <input type="hidden" id="edit_req_student_id" name="edit_req_student_id" value="<?php echo n_a; ?>">

                        <div class="card border-0 shadow">
                            <div class="card-header">
                                <h6 class="card-title my-0">Select student</h6>
                            </div>
                            <div class="card-body">
                                <span id="class_container"></span>
                            </div>
                        </div>

                    </div>
                    <!-- // select student -->


                    <div class="col-lg-7 col-md-6">
                        <div class="card border-0 shadow">
                            <div class="card-header">
                                <h6 class="card-title my-0">Select requirements</h6>
                            </div>
                            <div class="card-body">
                                <span id="load_student_requirements_cnt">
                                    <?php echo $crud->select_requirements($trackStudent); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <!-- // select requirements -->

                </fieldset>
                <!-- // fieldset -->

            </form>
            <!-- // form -->
        </div>
        <!-- // col-lg-12 -->
    </div>
    <!-- // row -->
</div>
<!-- // container -->
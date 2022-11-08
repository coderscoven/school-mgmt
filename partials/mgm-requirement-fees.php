<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-pills nav-justified">
                    <li class="nav-item">
                        <a class="nav-link active" href="#school-requirements" data-toggle="tab">SCHOOL REQUIREMENTS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#school-fees" data-toggle="tab">SCHOOL FEES</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content p-0">
                    <div class="tab-pane active" id="school-requirements">
                        <div class="row">
                            <div class="col-md-5">
                                <span id="response_req"></span>
                                <form action="" method="post" id="create-school-requirements">
                                    <input type="hidden" name="id" value="">
                                    <div class="form-group">
                                        <label for="req_names">Enter requirement name</label>
                                        <input type="text" class="form-control" id="req_names" name="req_names" placeholder="Requirement name:">
                                    </div>
                                    <div class="form-group">
                                        <label for="req_description">Enter requirement description</label>
                                        <textarea class="form-control" id="req_description" name="req_description" placeholder="Requirement description:"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="submit_requirement">Submit</button>
                                </form>
                            </div>
                            <div class="col-md-6 offset-md-1">
                                <span id="requirements_dt"></span>
                            </div>
                        </div>
                    </div>
                    <!-- // school-requirements -->
                    <div class="tab-pane" id="school-fees">
                        <div class="row">
                            <div class="col-md-5">
                                <span id="response_fees"></span>
                                <form action="" method="post" id="create-school-fees">
                                    <input type="hidden" id="sch_fees_id" name="sch_fees_id" value="">
                                    <div class="form-group">
                                        <label for="sel_school_section">Select section</label>
                                        <div id="sel_school_section">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input sel_section_class" id="section_day" name="section" value="Day">
                                                <label class="custom-control-label" for="section_day">Day</label>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" class="custom-control-input sel_section_class" id="section_boarding" name="section" value="Boarding">
                                                <label class="custom-control-label" for="section_boarding">Boarding</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="sel_school_amount">Enter Fees amount</label>
                                        <input type="text" class="form-control" id="sel_school_amount" name="amount" placeholder="Amount:">
                                    </div>
                                    <button type="submit" class="btn btn-primary" id="submit_fees">Submit</button>
                                </form>
                            </div>
                            <div class="col-md-6 offset-md-1">
                                <span id="load_fees_dt"></span>
                            </div>
                        </div>
                    </div>
                    <!-- // school-fees -->
                </div>
                <!-- // tab-content -->
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                REQUIREMENTS
            </div>
            <div class="card-body">

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
        </div>
    </div>
</div>
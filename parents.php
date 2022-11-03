<style>
    td {
        vertical-align: middle !important;
    }

    td p {
        margin: unset
    }
</style>
<div class="container-fluid">

    <div class="row">
        <!-- FORM Panel -->

        <!-- Table Panel -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-10">
                            <strong>Manage Parents' Information</strong>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-primary btn-block btn-sm" id="new_parent">
                                <i class="fas fa-plus"></i> New
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-sm datatableClass table-hover">
                        <thead class="bg-secondary text-light">
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Contact</th>
                                <th scope="col">Email</th>
                                <th scope="col">Gender</th>
                                <th scope="col">Location</th>
                                <th scope="col">Students</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo $crud->parentsList(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Table Panel -->
    </div>

</div>
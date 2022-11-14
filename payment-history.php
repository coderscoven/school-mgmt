<?php

if (isset($_GET['studid']) && isset($_GET['studid'])) {
    //
    $studid = $_GET['studid'];
    $efno   = $_GET['efno'];
    //
    $qry = $crud->getStudent($studid);
    $studentinfo = $qry->fetch_object();
?>
    <div class="container-fluid">
        <div class="row">

            <!-- Table Panel -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="align-items-center row">
                            <div class="col-8">
                                <h5 class="card-title my-1">Payments for <strong><?php echo $studentinfo->name ?></strong></h5>
                            </div>
                            <div class="col-4 text-right">
                                <a href="<?php echo  'index.php?' . http_build_query(["page" => "fees"]) ?>" class="btn btn-success text-sm btn-sm">Return to make payment</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-striped" id="payment_hist_dt">
                                <thead class="bg-secondary text-light">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Year</th>
                                        <th scope="col">Term</th>
                                        <th scope="col">Class</th>
                                        <th scope="col">Serial</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php echo $crud->sel_student_payment_hist($studid); ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <th></th>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else {
    include "partials/page-404.php";
} ?>
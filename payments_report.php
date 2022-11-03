<?php
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
?>
<script>
    var printdt = '<?php echo date("F, Y", strtotime($month)) ?>';
</script>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card_body">
                    <div class="row justify-content-center pt-4">
                        <label for="month" class="mt-2">Month</label>
                        <div class="col-sm-3">
                            <input type="month" name="month" id="month" value="<?php echo $month ?>" class="form-control">
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12">
                        <table class="table table-bordered" id='report-list'>
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="">Date</th>
                                    <th class="">ID No.</th>
                                    <th class="">EF No.</th>
                                    <th class="">Name</th>
                                    <th class="">Paid Amount</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <?php echo $crud->paymentReport($month); ?>
                        </table>
                        <hr>
                        <div class="col-md-12 mb-4">
                            <button class="btn btn-success" type="button" id="print"><i class="fas fa-print"></i> Print</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<noscript>
    <style>
        table#report-list {
            width: 100%;
            border-collapse: collapse
        }

        table#report-list td,
        table#report-list th {
            border: 1px solid
        }

        p {
            margin: unset;
        }

        .text-center {
            text-align: center
        }

        .text-right {
            text-align: right
        }
    </style>
</noscript>
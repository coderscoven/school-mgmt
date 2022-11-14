<?php
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
//
$studentsList = $crud->select_students(true);
$studentsList->execute();
$students = $studentsList->fetchAll();
//
$terms = $crud->fetch_school_terms();
?>
<script>
    var printdt = '<?php echo date("F, Y", strtotime($month)) ?>';
</script>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow">
                <div class="card-header">
                    <form id="payment_report_dataform" role="form">
                        <fieldset form="payment_report_dataform">

                            <div class="form-row align-items-md-center align-items-lg-center">
                                <div class="form-group col-md-5">
                                    <label for="rprt_student">Student</label>
                                    <select name="rprt_student" id="rprt_student" class="form-control select2 rprt-payment-filter" data-placeholder="--- select student ---">
                                        <option value="" data-type="dt_student"></option>
                                        <?php foreach ($students as $row) : ?>
                                            <option value="<?php echo $row['id'] ?>" data-type="dt_student"><?php echo $row['name'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="rprt_school_term">School Term</label>
                                    <select name="rprt_school_term" id="rprt_school_term" class="form-control rprt-payment-filter">
                                        <option value="" data-type="dt_school_term">--- select ---</option>
                                        <?php foreach ($terms as $term) : ?>
                                            <option value="<?php echo $term['id'] ?>" data-type="dt_school_term"><?php echo $term['sch_year'] . ' - ' . $term['sch_term'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="rprt_payment_date">Date range</label>
                                    <input type="text" name="rprt_payment_date" id="rprt_payment_date" class="form-control rprt-payment-filter" data-type="dt_date_range">
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="card-body">
                    <div id="payment_rprt_cnt"></div>
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
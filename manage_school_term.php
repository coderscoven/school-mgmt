<?php
include 'classes/MainClass.php';
if (isset($_GET['id'])) {
    $qry = $crud->school_term_edit($_GET['id']);
    foreach ($qry->fetch_array() as $k => $v) {
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4 col-md-5">
            <form action="" id="set_school_term_form">
                <input type="hidden" name="id" id="school_term_id" value="<?php echo isset($id) ? $id : '' ?>">
                <div class="form-group">
                    <label for="sch_year">Select year</label>
                    <select class="form-control" name="sch_year" id="sch_year">
                        <option selected disabled> --- select --- </option>
                        <?php for ($y = 2010; $y <= 2030; $y++) { ?>
                            <option><?php echo $y ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sch_term">Select term</label>
                    <input type="number" class="form-control" name="sch_term" id="sch_term" required>
                </div>
                <div class="form-group">
                    <label for="sch_sts_act">Make active</label>

                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="sch_sts" name="sch_sts" value="yes">
                        <label class="custom-control-label" for="sch_sts">Yes</label>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-7 offset-lg-1 col-md-6 offset-md-1">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="school_term_dt">
                    <thead class="bg-secondary text-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Year</th>
                            <th scope="col">Term</th>
                            <th scope="col">Active</th>
                            <!-- <th scope="col">Action</th> -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {

        //
        // $('#sch_year').datepicker({
        //     dateFormat: 'yy',
        //     changeMonth: false,
        //     changeYear: true,
        //     showButtonPanel: false,
        //     hideIfNoPrevNext: true,
        //     onClose: function(dateText, inst) {
        //         var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
        //         $(this).datepicker("setDate", new Date(year, 0, 1))
        //     }
        // }).focus(function() {
        //     $(".ui-datepicker-month").hide()
        //     $(".ui-datepicker-calendar").hide()
        //     $(".ui-datepicker-next").hide()
        //     $(".ui-datepicker-prev").hide()
        // });

        var school_term_dt = $("#school_term_dt").DataTable({
            "processing": true,
            "serverSide": true,
            "info": true,
            "autoWidth": false,
            oLanguage: {
                sProcessing: '<div class="spinner-grow text-primary" role="status"><span class="sr-only">Loading...</span></div>'
            },
            "ajax": {
                url: "ajax.php?action=school_term_dt",
                method: "post"
            }
        });

        // delete term status
        $("#school_term_dt tbody").on("click", ".delete_school_term", function(ev) {
            ev.preventDefault();
            let $this = $(this)
            let id = $this.attr("data-id")
            $.ajax({
                method: "post",
                url: "ajax.php?action=delete_school_term",
                data: {
                    "id": id,
                },
                dataType: "json"
            }).done(function(response) {
                if (response.bool) {
                    alert_toast(response.msg, "success")
                    school_term_dt.ajax.reload(null, false)
                } else alert_toast(response.msg, "danger")
            }).fail(function(jqXHR) {
                // let response = JSON.parse(jqXHR.responseText)
                // alert_toast(response.msg, "danger")
            });
        });

        // toggle term status
        $("#school_term_dt tbody").on("click", ".toggle_school_term_status", function(ev) {
            ev.preventDefault();
            let $this = $(this)
            let id = $this.attr("data-id")
            let sts = $this.attr("data-sts")
            $.ajax({
                method: "post",
                url: "ajax.php?action=toggle_school_term_status",
                data: {
                    "id": id,
                    "sts": sts
                },
                dataType: "json"
            }).done(function(response) {
                if (response.bool) {
                    alert_toast(response.msg, "success")
                    school_term_dt.ajax.reload(null, false)
                } else alert_toast(response.msg, "danger")
            }).fail(function(jqXHR) {
                let response = JSON.parse(jqXHR.responseText)
                alert_toast(response.msg, "danger")
            });
        });

        $("#set_school_term_form").submit(function(ev) {
            ev.preventDefault()
            let form = $("#set_school_term_form");
            $.ajax({
                method: "post",
                url: "ajax.php?action=set_school_term",
                data: form.serialize(),
                dataType: "json"
            }).done(function(response) {
                if (response.bool) {
                    alert_toast(response.msg, "success")
                    school_term_dt.ajax.reload(null, false)
                    form[0].reset()
                    // document.querySelector("#set_school_term").innerHTML = response.setterm
                } else alert_toast(response.msg, "danger")
            }).fail(function(jqXHR) {
                let response = JSON.parse(jqXHR.responseText)
                alert_toast(response.msg, "danger")
            });
        });
    })
</script>
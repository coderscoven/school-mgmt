<?php
include 'classes/MainClass.php';

if (isset($_GET['id'])) {
	$qry = $crud->get_sel_student_ef_list($_GET['id']);
	foreach ($qry->fetch_array() as $k => $v) {
		$$k = $v;
	}
}
$student = $crud->select_students_for_enrollment();
// active school term
$schoolterm = $crud->school_term_breadcrumb();
$efnumber = $crud->gen_enrollement_ref_number();

?>
<div class="container-fluid">
	<form role="form" id="manage-fees">
		<div id="msg"></div>
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="schtermid" value="<?php echo isset($school_term) ? $school_term : $schoolterm['id'] ?>">

		<input type="hidden" class="form-control" id="ef_no" name="ef_no" value="<?php echo isset($ef_no) ? $ef_no : $efnumber ?>" required>

		<div class="form-group">
			<label for="school_term" class="control-label">School term.</label>
			<input type="text" class="form-control" id="school_term" name="school_term" value="<?php echo isset($id) ? 'Term ' . $tterm . ' | ' . $tyear : 'Term ' . $schoolterm['term'] . ' | ' . $schoolterm['year']; ?>" readonly>
		</div>

		<div class="form-group">
			<label for="student_id" class="control-label">Student</label>
			<select name="stud_id" id="student_id" class="form-control select2" data-placeholder="--- select student ---">
				<option value=""></option>
				<?php foreach ($student as $row) : ?>
					<option value="<?php echo $row['id'] ?>" <?php echo isset($stud_id) && $stud_id == $row['id'] ? 'selected' : '' ?>>
						<?php echo ucwords($row['name']) . ' | ' . $row['id_no'] ?>
					</option>
				<?php endforeach ?>
			</select>
		</div>
		<?php if (isset($stud_id)) : ?>
			<span id="additional_fee_payment_details">
				<?php echo $crud->edit_student_details_on_fee($stud_id)['view'] ?>
			</span>
		<?php else : ?>
			<span id="additional_fee_payment_details"></span>
		<?php endif ?>

		<div class="form-group mt-3">
			<label for="amout_to_pay" class="control-label">Amount to pay.</label>
			<input type="text" class="form-control" id="amout_to_pay" name="amout_to_pay" value="<?php echo isset($stud_id) ? $crud->edit_student_details_on_fee($stud_id)['amount'] : '0.0' ?>" required placeholder="Amount:" readonly>
		</div>
	</form>
</div>
<script>
	$(function() {
		"use strict"

		var view = $("#additional_fee_payment_details")

		//
		$('#student_id').select2({
			width: '100%',
		});

		$('#student_id').on("change", function() {
			let id = $('#student_id option:selected').val()
			let term = $("#school_term").val()
			$.ajax({
				url: 'ajax.php?action=get_student_details_on_fee',
				method: 'POST',
				data: {
					"id": id,
					"term": term
				},
				dataType: "json",
				beforeSend: function() {
					view.html(`<div class="spinner-border text-secondary" role="status"><span class="sr-only">Loading...</span></div>`)
				},
				success: function(response) {
					view.html(response.view)
					$("#amout_to_pay").val(response.amount)
				}
			});
		});

		$('#manage-fees').submit(function(e) {
			e.preventDefault()
			start_load()
			$.ajax({
				url: 'ajax.php?action=enroll_and_set_fees',
				method: 'POST',
				data: $(this).serialize(),
				dataType: "json",
				success: function(response) {
					if (response.bool === true) {
						alert_toast(response.msg, 'success')
						setTimeout(function() {
							location.reload()
						}, 1000)
					} else {
						$('#msg').html('<div class="alert alert-danger">' + response.msg + '</div>')
						end_load()
					}
				},
				error: function(jqXHR) {
					let response = JSON.parse(jqXHR.responseText)
					$('#msg').html('<div class="alert alert-danger">' + response.msg + '</div>')
					end_load()
				}
			});
		});
	});
</script>
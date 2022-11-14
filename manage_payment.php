<?php
include 'classes/MainClass.php';

if (isset($_GET['id'])) {
	$qry = $crud->sel_payments_info($_GET['id']);
	foreach ($qry->fetch_array() as $k => $v) {
		$$k = $v;
	}
}
$student = $crud->select_students();
$fees = $crud->fetch_encrollement_infos();
$oustandingbalance = $crud->oustandingbalance(isset($ef_id) ? $ef_id : null);
?>
<div class="container-fluid">
	<form id="manage-payment">
		<div id="msg"></div>
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<input type="hidden" name="stud_id" id="stud_id" value="<?php echo isset($stud_id) ? $stud_id : '' ?>">
		<div class="form-group">
			<label for="payment_serial" class="control-label">Payment serial number</label>
			<input type="text" name="payment_serial" id="payment_serial" class="form-control" required value="<?php echo isset($slip_serial) ? $slip_serial : '' ?>" placeholder="Serial no:">
		</div>
		<div class="form-group">
			<label for="payment_date" class="control-label">Payment Date</label>
			<input type="text" name="payment_date" id="payment_date" class="form-control" required value="<?php echo isset($payment_date) ? $payment_date : '' ?>" placeholder="Payment date:">
		</div>
		<div class="form-group">
			<label for="ef_id" class="control-label">Select Student</label>
			<select name="ef_id" id="ef_id" class="custom-select select2" data-placeholder="--- select ---">
				<option value=""></option>
				<?php

				foreach ($fees as $row) :

					$paid = $crud->fetch_sel_enrollment_payment($row['id'], isset($id) ? $id : null);
					$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : '';
					$balance = $row['total_fee'] - $paid;
				?>
					<option value="<?php echo $row['id'] ?>" data-studid="<?php echo $row['studid'] ?>" data-balance="<?php echo $balance ?>" <?php echo isset($ef_id) && $ef_id == $row['id'] ? 'selected' : '' ?>>
						<?php echo $row['ef_no'] . ' | ' . ucwords($row['sname']) ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Outstanding Balance</label>
			<input type="text" class="form-control text-right" id="balance" value="<?php echo $oustandingbalance ?>" required readonly>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Amount paying</label>
			<input type="text" class="form-control text-right" name="amount" value="<?php echo isset($amount) ? $amount : 0 ?>" required autocomplete="off">
		</div>
	</form>
</div>
<script>
	$(function() {

		$("#payment_date").datepicker({
			dateFormat: 'yy-mm-dd'
		});

		$('.select2').select2({
			placeholder: 'Please select here',
			width: '100%'
		})
		$('#ef_id').change(function() {
			let selOption = $('#ef_id option[value="' + $(this).val() + '"]')
			var amount = selOption.attr('data-balance')
			var studid = selOption.attr("data-studid")
			document.querySelector("#stud_id").value = studid

			$('#balance').val(parseFloat(amount).toLocaleString('en-US', {
				style: 'decimal',
				maximumFractionDigits: 2,
				minimumFractionDigits: 2
			}))
		})
		$('#manage-payment').submit(function(e) {
			e.preventDefault()
			start_load()
			$.ajax({
				url: 'ajax.php?action=save_payment',
				method: 'POST',
				data: $(this).serialize(),
				dataType: "json",
			}).done(function(response) {

				end_load()
				if (response.bool === true) {
					alert_toast(response.msg, 'success')
					setTimeout(function() {
						// var nw = window.open('receipt.php?ef_id=' + response.ef_id + '&pid=' + response.pid, "_blank", "width=900,height=600")
						setTimeout(function() {
							// nw.print()
							setTimeout(function() {
								// nw.close()
								location.reload()
							}, 500)
						}, 500)
					}, 500)
				} else {
					alert_toast(response.msg, 'danger')
				}
			}).fail(function(jqXHR) {
				end_load()
				console.error(jqXHR)
			});
		});
	});
</script>
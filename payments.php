<style>
	td {
		vertical-align: middle !important;
	}

	td p {
		margin: unset
	}

	img {
		max-width: 100px;
		max-height: 150px;
	}
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div class="row align-items-center">
						<div class="col-10">
							<strong>List of Payments</strong>
						</div>
						<div class="col-2">
							<div class="btn-group btn-group-sm float-right" role="group" aria-label="payment action button">
								<button type="button" class="btn btn-warning" id="new_payment">
									<i class="fas fa-plus"></i> New Payment
								</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-sm table-striped table-hover datatableClass" id="makepayments_dt">
							<thead class="bg-secondary text-light">
								<tr>
									<th scope="col">#</th>
									<th scope="col">Date</th>
									<th scope="col">Payslip serial</th>
									<th scope="col">ID No.</th>
									<th scope="col">Name</th>
									<th scope="col">Amount paid</th>
									<th scope="col" class="text-center">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php echo $crud->payments();  ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
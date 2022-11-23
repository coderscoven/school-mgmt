<style>
	input[type=checkbox] {
		/* Double-sized Checkboxes */
		-ms-transform: scale(1.3);
		/* IE */
		-moz-transform: scale(1.3);
		/* FF */
		-webkit-transform: scale(1.3);
		/* Safari and Chrome */
		-o-transform: scale(1.3);
		/* Opera */
		transform: scale(1.3);
		padding: 10px;
		cursor: pointer;
	}

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
		<!-- FORM Panel -->

		<!-- Table Panel -->
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div class="row align-items-center">
						<div class="col-sm-8">
							<strong>List of Student fees</strong>
						</div>
						<div class="col-sm-4">
							<div class="btn-group btn-group-sm float-right" role="group" aria-label="enrollment action buttons">
								<a href="javascript:;" class="btn btn-warning" id="new_fees">
									<i class="fas fa-plus"></i> New Enrollement
								</a>
								<a href="<?php echo 'index.php?' . http_build_query(['page' => 'payments']) ?>" class="btn btn-info">
									Make Payments <i class="fas fa-arrow-alt-circle-right"></i>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body">
					<table class="table table-sm table-striped table-hover datatableClass" id="payments_dt">
						<thead class="bg-secondary text-light">
							<tr>
								<th scope="col">#</th>
								<th scope="col">Term</th>
								<th scope="col">Class</th>
								<th scope="col">ID No.</th>
								<th scope="col">Name</th>
								<th scope="col" class="text-right">Payable Fee</th>
								<th scope="col" class="text-right">Paid</th>
								<th scope="col" class="text-right">Balance</th>
								<th class="text-center" scope="col">Action</th>
							</tr>
						</thead>
						<tbody>
							<?= $crud->fees(); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- Table Panel -->
	</div>
</div>
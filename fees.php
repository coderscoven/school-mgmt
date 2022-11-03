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
						<div class="col-10">
							<strong>List of Student fees</strong>
						</div>
						<div class="col-2">
							<a class="btn btn-primary btn-block btn-sm" href="javascript:void(0)" id="new_fees">
								<i class="fas fa-plus"></i> New
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<table class="table table-sm table-bordered table-hover datatableClass">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="">ID No.</th>
								<th class="">EF No.</th>
								<th class="">Name</th>
								<th class="">Payable Fee</th>
								<th class="">Paid</th>
								<th class="">Balance</th>
								<th class="text-center">Action</th>
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
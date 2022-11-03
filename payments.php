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
							<a class="btn btn-primary btn-block btn-sm" href="javascript:void(0)" id="new_payment">
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
								<th class="">Date</th>
								<th class="">ID No.</th>
								<th class="">EF No.</th>
								<th class="">Name</th>
								<th class="">Paid Amount</th>
								<th class="text-center">Action</th>
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
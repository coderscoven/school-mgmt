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

		<!-- Table Panel -->
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<div class="row align-items-center">
						<div class="col-10">
							<strong>List of Classes and Fees</strong>
						</div>
						<div class="col-2">
							<a class="btn btn-primary btn-block btn-sm" href="javascript:void(0)" id="new_course">
								<i class="fas fa-plus"></i> New Entry
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<table class="table table-sm datatableClass table-bordered table-hover">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="">Class/Level</th>
								<th class="">Description</th>
								<th class="">Total Fee</th>
								<th class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php echo $crud->courses(); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- Table Panel -->
	</div>
</div>
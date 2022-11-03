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
							<strong>List of Students</strong>
						</div>
						<div class="col-2">
							<a class="btn btn-primary btn-block btn-sm" href="javascript:void(0)" id="new_student">
								<i class="fas fa-plus"></i> New
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<table class="table table-sm datatableClass table-hover">
						<thead class="bg-secondary text-light">
							<tr>
								<th scope="col">Class</th>
								<th scope="col">Photo</th>
								<th scope="col">ID No.</th>
								<th scope="col">Name</th>
								<th scope="col">Information</th>
								<th class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php echo $crud->studentsList(); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- Table Panel -->
	</div>

</div>
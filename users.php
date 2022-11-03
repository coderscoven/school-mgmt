<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<div class="row align-items-center">
						<div class="col-10">
							<strong>User List</strong>
						</div>
						<div class="col-2">
							<button class="btn btn-primary btn-sm btn-block" id="new_user"><i class="fas fa-plus"></i> New user</button>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped table-sm datatableClass">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">Name</th>
									<th scope="col">Username</th>
									<th scope="col">Type</th>
									<th scope="col">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php echo $crud->usersList(); ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
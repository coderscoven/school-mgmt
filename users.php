<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<div class="row align-items-center">
						<div class="col-md-8 col-sm-12">
							<strong>User List</strong>
						</div>
						<div class="col-md-4 col-sm-12">
							<div class="btn-group btn-group-sm" role="group" aria-label="action buttons">
								<button class="btn btn-warning" id="new_user">
									<i class="fas fa-plus"></i> Add new user</button>
								<button class="btn btn-info" id="assign_user_credentials">
									<i class="fas fa-plus"></i> Assign user credentials</button>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped table-sm datatableClass">
							<thead class="bg-secondary text-light">
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
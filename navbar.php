<style>
	.collapse a {
		text-indent: 10px;
	}

	/* nav#sidebar {
		/*background: url(assets/uploads < echo $_SESSION['system']['cover_img'] ?>) !important*/
	/*}

	*/
</style>

<nav id="sidebar" class='mx-lt-5 bg-dark'>

	<div class="sidebar-list">
		<a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fas fa-dashboard "></i></span>
			Dashboard
		</a>

		<a href="index.php?page=classes" class="nav-item nav-classes"><span class='icon-field'><i class="fas fa-scroll "></i></span>
			Classes
		</a>

		<a href="index.php?page=teachers" class="nav-item nav-teachers"><span class='icon-field'><i class="fas fa-chalkboard-teacher "></i></span>
			Teacher Mgmt
		</a>

		<!-- ============================================ -->
		<!-- ============================================ -->

		<div class="text-white font-weight-bold bg-black py-1 pl-4">STUDENT</div>

		<a href="index.php?page=students" class="nav-item nav-students"><span class='icon-field'><i class="far fa-circle "></i></span>
			Mgmt
		</a>
		<a href="index.php?page=requirements" class="nav-item nav-requirements"><span class='icon-field'><i class="far fa-circle "></i></span>
			Requirements
		</a>
		<a href="index.php?page=fees" class="nav-item nav-fees"><span class='icon-field'><i class="far fa-circle "></i></span>
			Fees
		</a>
		<a href="index.php?page=payments" class="nav-item nav-payments"><span class='icon-field'><i class="far fa-circle "></i></span>
			Payments
		</a>
		<a href="index.php?page=parents" class="nav-item nav-parents"><span class='icon-field'><i class="far fa-circle "></i></span>
			Parents
		</a>

		<!-- ============================================ -->
		<!-- ============================================ -->

		<div class="text-white font-weight-bold bg-black py-1 pl-4">REPORTS</div>

		<a href="index.php?page=payments_report" class="nav-item nav-payments_report"><span class='icon-field'><i class="far fa-circle "></i></span>
			Payments
		</a>
		<a href="index.php?page=requirements_report" class="nav-item nav-requirements_report"><span class='icon-field'><i class="far fa-circle "></i></span>
			Requirements
		</a>
		<a href="index.php?page=students_report" class="nav-item nav-students_report"><span class='icon-field'><i class="far fa-circle "></i></span>
			Students
		</a>

		<!-- ============================================ -->
		<!-- ============================================ -->

		<div class="text-white font-weight-bold bg-black py-1 pl-4">SYSTEMS</div>
		<?php if ($_SESSION['login_type'] == 1) : ?>
			<a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i class="fas fa-users "></i></span> Users</a>
			<!-- <a href="index.php?page=site_settings" class="nav-item nav-site_settings"><span class='icon-field'><i class="fa fa-cogs"></i></span> System Settings</a> -->
		<?php endif; ?>
		<a href="ajax.php?action=logout" class="nav-item"><span class='icon-field'><i class="fas fa-sign-out"></i></span>
			Logout
		</a>
	</div>
</nav>
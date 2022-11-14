<style>
	.collapse a {
		text-indent: 10px;
	}

	/* nav#sidebar {
		/*background: url(assets/uploads < echo $_SESSION['system']['cover_img'] ?>) !important*/
	/*}

	*/
</style>

<nav id="sidebar" class='mx-lt-5 bg-dark d-print-none'>

	<div class="sidebar-list">
		<a href="<?php echo 'index.php?' . http_build_query(['page' => 'home']); ?>" class="nav-item nav-home">
			<span class='icon-field'><i class="fas fa-dashboard "></i></span>
			Dashboard
		</a>

		<a href="<?php echo 'index.php?' . http_build_query(['page' => 'classes']); ?>" class="nav-item nav-classes">
			<span class='icon-field'><i class="fas fa-scroll "></i></span>
			Classes
		</a>

		<!-- ============================================ -->
		<!-- ============================================ -->

		<div class="text-white font-weight-bold bg-black py-1 pl-4">TEACHER</div>

		<a href="<?php echo 'index.php?' . http_build_query(['page' => 'teachers']); ?>" class="nav-item nav-teachers">
			<span class='icon-field'><i class="fas fa-chalkboard-teacher "></i></span>
			Teacher Mgmt
		</a>

		<a href="<?php echo 'index.php?' . http_build_query(['page' => 'roll-call']); ?>" class="nav-item nav-roll-call">
			<span class='icon-field'><i class="fas fa-bullhorn "></i></span>
			Roll call
		</a>

		<!-- ============================================ -->
		<!-- ============================================ -->

		<div class="text-white font-weight-bold bg-black py-1 pl-4">STUDENT</div>

		<a href="<?php echo 'index.php?' . http_build_query(['page' => 'students']); ?>" class="nav-item nav-students">
			<span class='icon-field'><i class="far fa-circle "></i></span>
			Student Mgmt
		</a>
		<a href="<?php echo 'index.php?' . http_build_query(['page' => 'requirements']); ?>" class="nav-item nav-requirements <?php echo $_SESSION['login_access_level'] == LV_2 ? 'd-none' : '' ?>">
			<span class='icon-field'><i class="far fa-circle "></i></span>
			Requirements
		</a>

		<?php if ($_SESSION['login_access_level'] == LV_1 || $_SESSION['login_access_level'] == LV_2) : ?>
			<a href="<?php echo 'index.php?' . http_build_query(['page' => 'fees']); ?>" class="nav-item nav-fees">
				<span class='icon-field'><i class="far fa-circle "></i></span>
				Student Fees
			</a>
			<a href="<?php echo 'index.php?' . http_build_query(['page' => 'payments']); ?>" class="nav-item nav-payments">
				<span class='icon-field'><i class="far fa-circle "></i></span>
				Make Payments
			</a>
		<?php endif ?>

		<a href="<?php echo 'index.php?' . http_build_query(['page' => 'parents']); ?>" class="nav-item nav-parents">
			<span class='icon-field'><i class="far fa-circle "></i></span>
			Parents Mgmt
		</a>

		<!-- ============================================ -->
		<!-- ============================================ -->

		<div class="text-white font-weight-bold bg-black py-1 pl-4">REPORTS</div>

		<a href="<?php echo 'index.php?' . http_build_query(['page' => 'payments_report']); ?>" class="nav-item nav-payments_report">
			<span class='icon-field'><i class="far fa-circle "></i></span>
			Payments
		</a>
		<a href="<?php echo 'index.php?' . http_build_query(['page' => 'requirements_report']); ?>" class="nav-item nav-requirements_report">
			<span class='icon-field'><i class="far fa-circle "></i></span>
			Requirements
		</a>
		<a href="<?php echo 'index.php?' . http_build_query(['page' => 'students_report']); ?>" class="nav-item nav-students_report">
			<span class='icon-field'><i class="far fa-circle "></i></span>
			Students
		</a>
		<a href="<?php echo 'index.php?' . http_build_query(['page' => 'teacher_details_report']); ?>" class="nav-item nav-teacher_details_report">
			<span class='icon-field'><i class="far fa-circle "></i></span>
			Teachers details
		</a>

		<!-- ============================================ -->
		<!-- ============================================ -->

		<div class="text-white font-weight-bold bg-black py-1 pl-4">SYSTEMS</div>

		<?php if ($_SESSION['login_access_level'] == LV_1) : ?>
			<a href="<?php echo 'index.php?' . http_build_query(['page' => 'users']); ?>" class="nav-item nav-users">
				<span class='icon-field'><i class="fas fa-users "></i></span> Users</a>
		<?php endif; ?>


		<a href="<?php echo 'index.php?' . http_build_query(['page' => 'logout']); ?>" class="nav-item">
			<span class='icon-field'><i class="fas fa-sign-out"></i></span>
			Logout
		</a>
	</div>
</nav>
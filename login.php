<!DOCTYPE html>
<html lang="en">
<?php
include "classes/MainClass.php";
ob_start();
// if(!isset($_SESSION['system'])){
$system = $crud->systemSettings();
foreach ($system as $k => $v) {
	$_SESSION['system'][$k] = $v;
}
// }
ob_end_flush();
?>

<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">

	<title><?php echo $_SESSION['system']['name'] ?></title>


	<?php include('./header.php'); ?>
	<?php
	if (isset($_SESSION['login_id']))
		header("location:index.php?page=home");
	?>

</head>
<style>
	body {
		width: 100%;
		height: calc(100%);
		position: fixed;
		top: 0;
		left: 0
			/*background: #007bff;*/
	}

	main#main {
		width: 100%;
		height: calc(100%);
		display: flex;
	}
</style>

<body id="login-body">


	<main id="main">

		<div class="align-self-center w-100">
			<div class="text-center mb-4">
				<a href="">
					<img src="assets/img/icon-48x48.png" alt="Logo" class="img-fluid">
				</a>
				<h4 class="text-white font-weight-bold"><?php echo $_SESSION['system']['name'] ?></h4>
			</div>
			<div id="login-center" class="row justify-content-center">
				<div class="card border-0 shadow col-md-4 bg-dark text-light">
					<div class="card-body">
						<form id="login-form" role="form">
							<div class="form-group">
								<label for="username">Username</label>
								<input type="text" id="username" name="username" class="form-control shadow-sm" placeholder="Username:">
							</div>
							<div class="form-group">
								<label for="access_level">Access level</label>
								<select id="access_level" name="access_level" class="custom-select shadow-sm">
									<option selected disabled> --- select --- </option>
									<option value="Admin">Admin</option>
									<option value="Bursar">Bursar</option>
									<option value="Teacher">Teacher</option>
								</select>
							</div>
							<div class="form-group">
								<label for="password">Password</label>
								<input type="password" id="password" name="password" class="form-control shadow-sm" placeholder="Password:">
							</div>
							<div class="form-group">
								<button type="submit" class="btn btn-block btn-primary">LOGIN</button>
							</div>
						</form>
					</div>
					<div class="card-footer">
						<p class="my-1 text-center card-text">All rights reserved &copy; <?php echo date('Y'); ?></p>
					</div>
				</div>
			</div>
		</div>
	</main>

	<a href="#" class="back-to-top"><i class="icofont-simple-up"></i></a>


	<?php require_once 'footer.php'; ?>

	<script>
		$('#login-form').submit(function(e) {
			e.preventDefault()
			$('#login-form button[type="button"]').attr('disabled', true).html('Logging in...');
			if ($(this).find('.alert-danger').length > 0)
				$(this).find('.alert-danger').remove();
			$.ajax({
				url: 'ajax.php?action=login',
				method: 'POST',
				data: $(this).serialize(),
				dataType: "json",
				error: err => {
					console.log(err)
					$('#login-form button[type="button"]').removeAttr('disabled').html('Login');

				},
				success: function(resp) {
					if (resp.bool == true) {
						location.href = 'index.php?page=home';
					} else {
						$('#login-form').prepend('<div class="alert alert-danger">' + resp.msg + '</div>')
						$('#login-form button[type="button"]').removeAttr('disabled').html('Login');
					}
				}
			})
		})
	</script>

</body>

</html>
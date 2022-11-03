<?php
include 'classes/MainClass.php';

if (isset($_GET['id'])) {

	$user = $crud->getUser($_GET['id']);
	foreach ($user->fetch_array() as $k => $v) {
		$meta[$k] = $v;
	}
}
?>
<div class="container-fluid">
	<div id="msg"></div>

	<form action="" id="manage-user">
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id'] : '' ?>">
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" name="name" id="name" class="form-control" value="<?php echo isset($meta['name']) ? $meta['name'] : '' ?>" required>
		</div>
		<div class="form-group">
			<label for="username">Username</label>
			<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username'] : '' ?>" required autocomplete="off">
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
			<?php if (isset($meta['id'])) : ?>
				<small><i>Leave this blank if you dont want to change the password.</i></small>
			<?php endif; ?>
		</div>
		<?php if (isset($meta['type']) && ($meta['type'] == 2 || $meta['type'] == 3)) : ?>
			<input type="hidden" name="type" value="<?php echo $meta['type']; ?>">
		<?php else : ?>
			<?php if (!isset($_GET['mtype'])) : ?>
				<div class="form-group">
					<label for="type">User Type</label>
					<select name="type" id="type" class="custom-select">
						<option value="1" <?php echo isset($meta['type']) && $meta['type'] == 1 ? 'selected' : '' ?>>Admin</option>
						<option value="2" <?php echo isset($meta['type']) && $meta['type'] == 2 ? 'selected' : '' ?>>Bursar</option>
						<option value="3" <?php echo isset($meta['type']) && $meta['type'] == 3 ? 'selected' : '' ?>>Teacher</option>
					</select>
				</div>
			<?php endif; ?>
		<?php endif; ?>


	</form>
</div>
<script>
	$('#manage-user').submit(function(e) {
		e.preventDefault();
		start_load()
		$.ajax({
			url: 'ajax.php?action=save_user',
			method: 'POST',
			data: $(this).serialize(),
			dataType: "json",
		}).done(function(response) {

			if (response.bool == true) {
				toastr.success(response.msg)
				setTimeout(function() {
					location.reload()
				}, 1500)
			} else {
				toastr.error(response.msg)
				end_load()
			}

		}).fail(function(jqXHR) {
			let response = JSON.parse(jqXHR.responseText)
			toastr.error(response.msg)
			end_load()
		});
	})
</script>
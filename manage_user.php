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

		<div class="form-group">
			<label for="accesslevel">Access level</label>
			<select name="accesslevel" id="accesslevel" class="custom-select">
				<option value="">--- select ---</option>
				<option value="Admin" <?php echo isset($meta['access_level']) && $meta['access_level'] == LV_1 ? 'selected' : '' ?>>Admin</option>
				<option value="Bursar" <?php echo isset($meta['access_level']) && $meta['access_level'] == LV_2 ? 'selected' : '' ?>>Bursar</option>
				<option value="Teacher" <?php echo isset($meta['access_level']) && $meta['access_level'] == LV_3 ? 'selected' : '' ?>>Teacher</option>
			</select>
		</div>


	</form>
</div>
<script>
	$('#manage-user').submit(function(e) {
		e.preventDefault();
		start_load()
		$.ajax({
			url: 'ajax.php?action=save_user',
			method: 'POST',
			data: $('#manage-user').serialize(),
			dataType: "json",
		}).done(function(response) {

			if (response.bool === true) {
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
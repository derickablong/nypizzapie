<div class="pos-login">
	<div class="pos-login-wrap">
		<div class="pos-login-con">
			<form action="" method="post">
				<a href="/" class="pos-logo">
					<img src="<?php echo NYPIZZA_URI ?>/media/img/logov2.png" alt="" title="">
				</a>

				<?php echo $nypizza->employee->error; ?>
				<div class="pos-fields">
					<label for="username">Username:</label>
					<input type="text" name="username" id="username">
				</div>
				<div class="pos-fields">
					<label for="password">Password:</label>
					<input type="password" name="password" id="password">
				</div>
				<input type="submit" value="Login" name="pos-user-login">
			</form>

		</div>
	</div>
</div>
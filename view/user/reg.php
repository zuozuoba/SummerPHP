<div class="am-g am-container">
	<div class="am-u-lg-9">
		<h4>注册</h4>
		<form method="post" class="am-form" action="<?= $controllerUrl ?>">
			<input type="hidden" name="safe_token" value="<?= $safe_token ?>">
			<label for="username">姓名:</label>
			<input type="text" name="username" id="email" value="">
			<br>
			<label for="mobile">手机:</label>
			<input type="text" name="mobile" id="email" value="">
			<br>
			<label for="password">密码:</label>
			<input type="password" name="password" id="password" value="">
			<br>
			<label for="password">确认密码:</label>
			<input type="password" name="password_confirm" id="password_confirm" value="">
			<br>
			<label for="remember-me">
				<input id="remember-me" type="checkbox" name="remember_me">七天内记住密码
			</label>
			<input type="submit" name="reg" value="注 册" class="am-btn am-btn-sm am-fr color-title">
			<br />
			<br />

		</form>
	</div>
</div>
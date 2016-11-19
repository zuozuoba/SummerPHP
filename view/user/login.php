<div class="am-g am-container">
	<div class="am-u-lg-9">
		<h4>登录</h4>
		<form method="post" class="am-form" action="<?= $actionUrl ?>">
			<input type="hidden" name="safe_token" value="<?= $safe_token ?>">
			<label for="username">姓名:</label>
			<input type="text" name="username" value="">
			<br>
			<label for="password">密码:</label>
			<input type="password" name="password" id="password" value="">
			<br>
			<label for="remember-me">
				<input id="remember-me" name="remember_me" type="checkbox"> 记住密码
			</label>
			<input type="submit" name="login" value="登 录" class="am-btn am-btn-sm am-fr color-title">
			<!-- <input type="submit" name="" value="忘记密码 ^_^? " class="am-btn am-btn-default am-btn-sm am-fr">-->
			<br />
			<br />
		</form>
	</div>
</div>
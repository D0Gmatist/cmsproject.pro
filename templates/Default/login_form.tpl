<!DOCTYPE html>
<html lang="ru">
<head>
	{include file="include/main/head.tpl"}
</head>

<body class="login">

	<div class="logo">
		<a href="/">
			<img src="/templates/Default/img/logo.png" alt="" /> </a>
	</div>
	<div class="content">

		<form class="login-form" data-form="login" action="/" method="post">
			<h3 class="form-title font-green">Авторизация</h3>
			<div class="form-group">
				<label class="control-label visible-ie8 visible-ie9">Логин</label>
				<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Логин" name="login" />
			</div>
			<div class="form-group">
				<label class="control-label visible-ie8 visible-ie9">Пароль</label>
				<input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Пароль" name="password" />
			</div>
			<div class="form-group" style="margin-bottom: 0;height: 20px;">
				<label class="rememberme check mt-checkbox mt-checkbox-outline"><input type="checkbox" name="remember" value="1">Запомнить<span></span></label>
			</div>
			<div class="form-actions">
				<input type="hidden" name="action" value="login" />
				<button type="submit" class="btn green uppercase" data-btn="formGo" data-action="login">Войти</button>
				<button type="button" id="forget-password" class="btn green btn-outline uppercase pull-right" data-btn="form" data-name-form="forget">Забыли пароль?</button>
			</div>
		</form>

		<form class="forget-form" data-form="forget" action="/" method="post" novalidate="novalidate" style="display: none;">
			<h3 class="font-green">Забыть пароль?</h3>
			<p>Введите адрес электронной почты, который указан в вашем профиле на этом сайте.</p>
			<div class="form-group">
				<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email"> </div>
			<div class="form-actions">
				<button type="button" id="back-btn" class="btn green btn-outline" data-btn="form" data-name-form="login">Назад</button>
				<button type="submit" class="btn btn-success uppercase pull-right" data-btn="formGo" data-action="forget">Выслать</button>
			</div>
		</form>

	</div>

	{include file="include/main/script.tpl"}

</body>
</html>
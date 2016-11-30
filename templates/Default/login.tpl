<div class="logo">
	<a href="/"><img src="/templates/Default/img/logo.png" alt="" /> </a>
</div>
<div class="content">

	<form class="login-form" data-form="login" method="post" style="display: {form_login};">
		<h3 class="form-title font-green">Авторизация</h3>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Логин</label>
			<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Логин" name="login" value="{login}" />
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Пароль</label>
			<input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Пароль" name="password" value="{password}" />
		</div>
		<div class="form-actions">
			<input type="hidden" name="action" value="login" />
			<button type="submit" class="btn green uppercase" data-btn="formGo" data-action="login">Войти</button>
			<button type="button" id="forget-password" class="btn green btn-outline uppercase pull-right" data-btn="form" data-name-form="forget">Забыли пароль?</button>
		</div>
		<div class="btn btn-info" data-btn="form" data-name-form="registration" style="display:block;margin:0 -40px -30px;padding:15px 0 17px;">Создать новый аккаунт</div>
	</form>

	<form class="forget-form" data-form="forget" method="post" style="display: {form_forget};">
		<h3 class="font-green">Забыть пароль?</h3>
		<p>Введите адрес электронной почты, который указан в вашем профиле на этом сайте.</p>
		<div class="form-group">
			<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email" value="{forget_email}" />
		</div>
		<div class="form-actions">
			<input type="hidden" name="action" value="forget" />
			<button type="button" id="back-btn" class="btn green btn-outline" data-btn="form" data-name-form="login">Назад</button>
			<button type="submit" class="btn btn-success uppercase pull-right" data-btn="formGo" data-action="forget">Выслать</button>
		</div>
	</form>

	<form class="register-form" data-form="registration" method="post" style="display: {form_registration};">
		<h3 class="form-title font-green">Регистрирация</h3>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Логин</label>
			<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Логин" name="login" value="{registration_login}" />
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Email</label>
			<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email" value="{registration_email}" />
		</div>
		<div class="form-group">
			<label class="control-label visible-ie8 visible-ie9">Пароль</label>
			<input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Пароль" name="password" value="{registration_password}" />
		</div>
		<div class="form-actions">
			<input type="hidden" name="action" value="registration" />
			<button type="button" id="back-btn" class="btn green btn-outline" data-btn="form" data-name-form="login">Назад</button>
			<button type="submit" class="btn btn-success uppercase pull-right" data-btn="formGo" data-action="registration">Отправить</button>
		</div>
	</form>

</div>
<div class="row">
	<div class="col-lg-4"></div>
	<div class="col-lg-4">
		<div class="portlet light portlet-fit bordered">
			<div class="portlet-body">

				<form class="login-form" data-form="login" action="/?action=login" method="post" style="display: {form_login};">
					<h3 class="form-title form-title font-green">Авторизация</h3>
					<div class="form-group">
						<label class="control-label visible-ie8 visible-ie9">Логин</label>
						<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Логин" name="login" />
					</div>
					<div class="form-group">
						<label class="control-label visible-ie8 visible-ie9">Пароль</label>
						<input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Пароль" name="password" />
					</div>
					<div class="form-group">
						<input type="hidden" name="action" value="login" />
						<button type="submit" class="btn green-meadow uppercase" data-btn="formGo" data-action="login">Войти</button>
						<a class="btn green-meadow uppercase" href="{vk_login_url}">Вотйти через VK</a>
						<button type="button" id="forget-password" class="btn green-meadow btn-outline uppercase pull-right" data-btn="form" data-name-form="forget">Забыли пароль?</button>
					</div>
					<div class="form-actions">
						<div class="btn blue-hoki" style="display: block;" data-btn="form" data-name-form="registration">Создать новый аккаунт</div>
					</div>
				</form>

				<form class="forget-form" data-form="forget" action="/?action=login" method="post" novalidate="novalidate" style="display: {form_forget};">
					<h3 class="form-title font-green">Забыть пароль?</h3>
					<p>Введите адрес электронной почты, который указан в вашем профиле на этом сайте.</p>
					<div class="form-group">
						<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email"> </div>
					<div class="form-actions">
						<input type="hidden" name="action" value="forget" />
						<button type="button" id="back-btn" class="btn green-meadow btn-outline" data-btn="form" data-name-form="login">Назад</button>
						<button type="submit" class="btn green-meadow uppercase pull-right" data-btn="formGo" data-action="forget">Выслать</button>
					</div>
				</form>

				<form class="register-form" data-form="registration" action="/?action=login" method="post" style="display: {form_registration};">
					<h3 class="form-title font-green">Регистрирация</h3>
					<div class="form-group">
						<label class="control-label visible-ie8 visible-ie9">Логин</label>
						<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Логин" name="login" value="" />
					</div>
					<div class="form-group">
						<label class="control-label visible-ie8 visible-ie9">Email</label>
						<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email" value="" />
					</div>
					<div class="form-group">
						<label class="control-label visible-ie8 visible-ie9">Пароль</label>
						<input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Пароль" name="password" value="" />
					</div>
					<div class="form-actions">
						<input type="hidden" name="action" value="registration" />
						<button type="button" id="back-btn" class="btn green-meadow btn-outline" data-btn="form" data-name-form="login">Назад</button>
						<button type="submit" class="btn green-meadow uppercase pull-right" data-btn="formGo" data-action="registration">Отправить</button>
					</div>
				</form>

			</div>
		</div>
	</div>
	<div class="col-lg-4"></div>
</div>
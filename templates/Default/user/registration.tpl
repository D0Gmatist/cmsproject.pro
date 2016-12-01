[form_registration]
<div class="row">
	<div class="col-md-4"></div>
	<div class="col-md-4">

		<div class="portlet box blue ">
			<div class="portlet-title">
				<div class="caption"><i class="fa fa-gift"></i> Регистрация </div>
			</div>
			<div class="portlet-body form">

				<form role="form" data-form="registration" method="post" style="margin: 0;">

					<div class="form-body">
						<div class="form-group">
							<label class="control-label visible-ie8 visible-ie9">Логин</label>
							<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Логин" name="login" value="{login}" />
						</div>
						<div class="form-group">
							<label class="control-label visible-ie8 visible-ie9">Email</label>
							<input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email" value="{email}" />
						</div>
						<div class="form-group">
							<label class="control-label visible-ie8 visible-ie9">Пароль</label>
							<input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Пароль" name="password" value="{password}" />
						</div>
					</div>

					<div class="form-actions">
						<input type="hidden" name="action" value="registration" />
						<button type="submit" class="btn green uppercase" data-btn="formGo">Войти</button>
					</div>

				</form>

			</div>
		</div>

	</div>
	<div class="col-md-4"></div>
</div>
[/form_registration]
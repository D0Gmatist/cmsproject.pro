<div class="row">
	<div class="col-md-12">

		<form data-form="vk_search">

			<div class="portlet box blue-soft">
				<div class="portlet-title">
					<div class="caption">
						<i class="icon-settings"></i>
						<span class="caption-subject sbold uppercase">Форма поиска</span>
					</div>
					<div class="tools">
						<a href="javascript:;" class="collapse" data-original-title="Скрыть/Показать"> </a>
					</div>
				</div>
				<div class="portlet-body tabs-below form" style="display: block;">

					<div class="row">
							<div class="col-md-6">

								<div class="form-horizontal">
									<div class="form-body">

										<div class="form-group">
											<label class="col-md-3 control-label">Поиск</label>
											<div class="col-md-9">
												<input type="text" name="q" class="form-control spinner" placeholder="поиск фразе (не обязательно)">
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Страна</label>
											<div class="col-md-9">
												<select class="form-control" name="countries" data-ready="countries" data-change="countries" data-select="select2" data-placeholder="выберите страну (не обязательно)" style="width: 100%"></select>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Регион</label>
											<div class="col-md-9">
												<select class="form-control" name="regions" data-change="regions" data-select="select2" data-placeholder="выберите регион (не обязательно)" style="width: 100%" disabled></select>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Населённый пункт</label>
											<div class="col-md-9">
												<select class="form-control" name="cities" data-change="cities" data-select="select2" data-placeholder="выберите населённый пункт (не обязательно)" style="width: 100%" disabled></select>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Дополнительно</label>
											<div class="col-md-3">
												<div class="mt-checkbox-inline">
													<label class="mt-checkbox"><input type="checkbox" name="has_photo" value="1"> с фотографией<span></span></label>
												</div>
											</div>
											<div class="col-md-3">
												<div class="mt-checkbox-inline">
													<label class="mt-checkbox"><input type="checkbox" name="online" value="1"> сейчас на сайте<span></span></label>
												</div>
											</div>
										</div>

									</div>
								</div>

							</div>
							<div class="col-md-6">

								<div class="form-horizontal">
									<div class="form-body">

										<div class="form-group">
											<label class="col-md-3 control-label">Пол</label>
											<div class="col-md-9">
												<select class="form-control" name="sex" data-select="select1" style="width: 100%">
													<option value="0" selected="selected">Любой</option>
													<option value="1">Женский</option>
													<option value="2">Мужской</option>
												</select>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Семейное положение</label>
											<div class="col-md-9">
												<select class="form-control" name="status" data-select="select1" style="width: 100%">
													<option value="0" selected="selected">Любое</option>
													<option value="1">Не женат (не замужем)</option>
													<option value="2">Встречается</option>
													<option value="3">Помолвлен(-а)</option>
													<option value="4">Женат (замужем)</option>
													<option value="5">Всё сложно</option>
													<option value="6">В активном поиске</option>
													<option value="7">Влюблен(-а)</option>
												</select>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Возраст</label>
											<div class="col-md-4">
												<select class="form-control" name="age_from" data-change="age_from" data-select="select2" data-placeholder="От" style="width: 100%">
													{age_from}
												</select>
											</div>
											<div class="col-md-1"><div style="text-align: center;line-height: 34px;">-</div></div>
											<div class="col-md-4">
												<select class="form-control" name="age_to" data-change="age_to" data-select="select2" data-placeholder="До" style="width: 100%">
													{age_to}
												</select>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Дата рождения</label>
											<div class="col-md-3">
												<select class="form-control" name="birth_year" data-change="birth_year" data-select="select2" data-placeholder="Год" style="width: 100%">
													{birth_year}
												</select>
											</div>
											<div class="col-md-3">
												<select class="form-control" name="birth_month" data-change="birth_month" data-select="select2" data-placeholder="Месяц" style="width: 100%">
													{birth_month}
												</select>
											</div>
											<div class="col-md-3">
												<select class="form-control" name="birth_day" data-change="birth_day" data-select="select2" data-placeholder="День" style="width: 100%">
													{birth_day}
												</select>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Сортировка</label>
											<div class="col-md-9">
												<select class="form-control" name="sort" data-select="select1" style="width: 100%">
													<option value="0" selected="selected">по популярности</option>
													<option value="1">по дате регистрации</option>
												</select>
											</div>
										</div>

										<div class="form-group">
											<div class="col-md-12 text-right">
												<div class="btn btn-default" data-btn="vk_search"><i class="glyphicon glyphicon-search"></i> Искать</div>
											</div>
										</div>

									</div>
								</div>

							</div>
					</div>

				</div>
			</div>

			<div class="portlet box blue-soft">
				<div class="portlet-title">
					<div class="caption">
						<i class="icon-settings"></i>
						<span class="caption-subject sbold uppercase">Наличие обязательной информации</span>
					</div>
					<div class="tools">
						<a href="javascript:;" class="collapse" data-original-title="Скрыть/Показать"> </a>
					</div>
				</div>
				<div class="portlet-body tabs-below form" style="display: block;">

					<div class="row">
							<div class="col-md-12">

								<div class="form-horizontal">
									<div class="form-body">

										<div class="form-group">
											<div class="col-md-2">
												<div class="mt-checkbox-inline">
													<label class="mt-checkbox"><input type="checkbox" name="mobile_phone" value="1"> Мобильный<span></span></label>
												</div>
											</div>
											<div class="col-md-2">
												<div class="mt-checkbox-inline">
													<label class="mt-checkbox"><input type="checkbox" name="home_phone" value="1"> Домашний<span></span></label>
												</div>
											</div>
											<div class="col-md-2">
												<div class="mt-checkbox-inline">
													<label class="mt-checkbox"><input type="checkbox" name="skype" value="1"> Skype<span></span></label>
												</div>
											</div>
											<div class="col-md-2">
												<div class="mt-checkbox-inline">
													<label class="mt-checkbox"><input type="checkbox" name="facebook" value="1"> Facebook<span></span></label>
												</div>
											</div>
											<div class="col-md-2">
												<div class="mt-checkbox-inline">
													<label class="mt-checkbox"><input type="checkbox" name="twitter" value="1"> Twitter<span></span></label>
												</div>
											</div>
											<div class="col-md-2">
												<div class="mt-checkbox-inline">
													<label class="mt-checkbox"><input type="checkbox" name="instagram" value="1"> Instagram<span></span></label>
												</div>
											</div>
										</div>

									</div>
								</div>

							</div>
					</div>

				</div>
			</div>

		</form>

		<div data-content="vk_search_result"></div>

	</div>
</div>
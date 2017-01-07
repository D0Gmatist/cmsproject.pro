<div class="row">
	<div class="col-md-12">

		<form data-form="vk_group_search">

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

										<div class="form-group has-error">
											<label class="col-md-3 control-label">Поиск</label>
											<div class="col-md-9">
												<input type="text" name="q" data-required-field="form-group" class="form-control spinner" placeholder="поиск по фразе (обязательно)">
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Тип сообщества</label>
											<div class="col-md-9">
												<select class="form-control" name="type" data-select="select1" style="width: 100%" title="">
													<option value="0" selected="selected">Любое</option>
													<option value="group">Группа</option>
													<option value="page">Публичная страница</option>
													<option value="event">Мероприятие</option>
												</select>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Сортировать</label>
											<div class="col-md-9">
												<select class="form-control" name="sort" data-select="select1" style="width: 100%" title="">
													<option value="0" selected="selected">по умолчанию</option>
													<option value="1">по скорости роста</option>
													<option value="2">по отношению дневной посещаемости к количеству пользователей</option>
													<option value="3">по отношению количества лайков к количеству пользователей</option>
													<option value="4">по отношению количества комментариев к количеству пользователей</option>
													<option value="5">по отношению количества записей в обсуждениях к количеству пользователей</option>
												</select>
											</div>
										</div>

									</div>
								</div>

							</div>
							<div class="col-md-6">

								<div class="form-horizontal">
									<div class="form-body">

										<div class="form-group">
											<label class="col-md-3 control-label">Страна</label>
											<div class="col-md-9">
												<select class="form-control" name="countries" data-ready="countries" data-change="countries" data-select="select2" data-placeholder="выберите страну (не обязательно)" style="width: 100%" title=""></select>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Регион</label>
											<div class="col-md-9">
												<select class="form-control" name="regions" data-change="regions" data-select="select2" data-placeholder="выберите регион (не обязательно)" style="width: 100%" title="" disabled></select>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label">Населённый пункт</label>
											<div class="col-md-9">
												<select class="form-control" name="cities" data-change="cities" data-select="select2" data-placeholder="выберите населённый пункт (не обязательно)" style="width: 100%" title="" disabled></select>
											</div>
										</div>

										<div class="form-group">
											<div class="col-md-12 text-right">
												<div class="btn btn-default" data-btn="vk_group_search"><i class="glyphicon glyphicon-search"></i> Искать</div>
											</div>
										</div>

									</div>
								</div>

							</div>
					</div>

				</div>
			</div>

		</form>

		<div data-content="vk_group_search_result"></div>

	</div>
</div>
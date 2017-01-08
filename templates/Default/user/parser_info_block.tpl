<li class="dropdown dropdown-extended dropdown-tasks" id="header_task_bar">
	<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
		<i class="icon-calendar"></i>
		<span class="badge badge-default">{parser_active}</span>
	</a>
	<ul class="dropdown-menu extended tasks">
		<li class="external">
			<h3>
				В работе <span class="bold">{parser_active}</span> [declination={parser_active}]зада|ча|чи|ч[/declination]
			</h3>
			<a href="#">Все задачи</a>
		</li>
		<li>
			<ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
				{parser_info}
			</ul>
		</li>
	</ul>
</li>
<li class="dropdown dropdown-user">
	<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
		<img alt="" class="img-circle" src="{user_avatar}" />
		<span class="username username-hide-on-mobile"> {user_login} </span>
		<i class="fa fa-angle-down"></i>
	</a>
	<ul class="dropdown-menu dropdown-menu-default">
		[not-group=5]
		<li>
			<a href="/"><i class="icon-user"></i> Мой профиль </a>
		</li>
		<li class="divider"> </li>
		<li>
			<a href="?action=logout"><i class="icon-key"></i> Выход </a>
		</li>
		[/not-group]
		[group=5]
		<li>
			<a href="?action=login"><i class="icon-key"></i> Вход </a>
		</li>
		[/group]
	</ul>
</li>
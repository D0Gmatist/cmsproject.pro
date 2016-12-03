<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>Title</title>

	{include file="include/main/style.tpl"}
</head>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white[group=5] page-sidebar-closed[/group]">

    <div class="page-header navbar navbar-fixed-top">
		<div class="page-header-inner ">

            {include file="include/main/navbar/logo.tpl"}

            {include file="include/main/navbar/sidebar_toggler_btn.tpl"}

			{user_panel}

        </div>
    </div>

    <div class="clearfix"></div>

    <div class="page-container">
        [not-group=5]{include file="include/main/sidebar.tpl"}[/not-group]

        <div class="page-content-wrapper">
            <div class="page-content"[group=5] style="margin-left: 0 !important;"[/group]>
                {include file="include/main/spidbar.tpl"}

                <h3 class="page-title"> Dashboard<small>dashboard & statistics</small></h3>

                <!-- content -->
				{msg}
				{content}

            </div>
        </div>

    </div>

    <div class="page-footer">
    {include file="include/main/footer.tpl"}
    </div>

    {include file="include/main/script.tpl"}

</body>

</html>
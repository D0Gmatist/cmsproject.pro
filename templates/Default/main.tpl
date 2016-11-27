<!DOCTYPE html>
<html lang="ru">
<head>
<!-- include/main/head -->
{include file="include/main/head.tpl"}
<!-- include/main/head -->
</head>

<body class="[not-group=5]page-header-fixed page-sidebar-closed-hide-logo page-content-white[/not-group][group=5]login[/group]">

    [not-group=5]
    <div class="page-header navbar navbar-fixed-top">
    {include file="include/main/navbar.tpl"}
    </div>

    <div class="clearfix"></div>

    <div class="page-container">
        {include file="include/main/sidebar.tpl"}

        <div class="page-content-wrapper">
            <div class="page-content">
                {include file="include/main/spidbar.tpl"}

                <h3 class="page-title"> Dashboard<small>dashboard & statistics</small></h3>

                <!-- content -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light bordered">

                            <div class="portlet-title">
                                <div class="caption">Заголовок</div>
                                <div class="actions">
                                    <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
                                        <i class="icon-cloud-upload"></i>
                                    </a>
                                    <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
                                        <i class="icon-wrench"></i>
                                    </a>
                                    <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="" title=""> </a>
                                    <a class="btn btn-circle btn-icon-only btn-default" href="javascript:;">
                                        <i class="icon-trash"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="portlet-body">
                                content content content content content content content content
                                content content content content content content content content
                                content content content content content content content content
                                content content content content content content content content
                                content content content content content content content content
                                content content content content content content content content
                                content content content content content content content content
                                content content content content content content content content
                                <button type="button" class="btn btn-primary mt-ladda-btn ladda-button" data-style="slide-up">
                                    <span class="ladda-label">Expand Right</span><span class="ladda-spinner"></span>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="page-footer">
    {include file="include/main/footer.tpl"}
    </div>
    [/not-group]

    [group=5]
	{include file="include/main/login.tpl"}
    [/group]

    {include file="include/main/script.tpl"}


</body>
</html>
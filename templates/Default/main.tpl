<!DOCTYPE html>
<html lang="ru">
<head>
<!-- include/main/head -->
{include file="include/main/head.tpl"}
<!-- include/main/head -->
</head>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">

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

            </div>
        </div>

    </div>

    <div class="page-footer">
    {include file="include/main/footer.tpl"}
    </div>

    {include file="include/main/script.tpl"}

</body>
</html>
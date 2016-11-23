<?php

$html = <<<HTML

<html>
<head>
    <link href="templates/Default/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="templates/Default/vendor/components.min.css" rel="stylesheet" type="text/css" />
    <link href="templates/Default/vendor/plugins.min.css" rel="stylesheet" type="text/css" />

</head>
<body>

<a class="btn yellow btn-outline sbold uppercase" data-btn="onclickForm"> View Demo </a>

<script src="templates/Default/vendor/jquery.min.js" type="text/javascript"></script>
<script src="templates/Default/vendor/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="templates/Default/vendor/bootbox/bootbox.min.js" type="text/javascript"></script>
<script src="templates/Default/js/my.js" type="text/javascript"></script>

</body>
</html>

HTML;

echo $html;
<?php
/*
Copyright (c) 2006-2011, Matt Smith
All rights reserved.

Licensed under the New BSD License; see adroit/LICENSE/ADROIT-LICENSE
*/

global $Constants;

echo <<<EOF
<script type="text/javascript" src="{$Constants->APP_HTTP_PATH}/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
	$("input:text:first").focus();
    });
</script>
<link rel="stylesheet" href="{$Constants->APP_HTTP_PATH}/css/reset.css" type="text/css" media="screen, projection" />
<style type="text/css">
body { margin: 10px; }
#form_login1 table {
    width: 200px;
}
#form_login1 th, td {
    border: 1px solid black;
    padding: 2px;
}
#form_login1 th {
    background-color: black;
    color: white;
}

#form_login1 #submit {
    width: 100%;
}
</style>

EOF;

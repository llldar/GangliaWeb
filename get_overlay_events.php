<?php
include_once "./functions.php";
#本PHP 文件用于记录页面覆盖关系的事件，并输出json文件
$event_array = ganglia_events_get(intval($_GET['start']), intval($_GET['end']));
header("Content-type: application/json");
print json_encode($event_array);
exit(0);
?>

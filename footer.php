<?php
$tpl = new Dwoo_Template_File( template("footer.tpl") );
# 加载 Dwoo 的底部模板
$data = new Dwoo_Data(); 
$data->assign("webfrontend_version",$version["webfrontend"]);
# 显示 webfrontend 的版本信息

# 如果数据获取失败则不显示底栏
if (isset($_GET["hide-hf"]) && filter_input(INPUT_GET, "hide-hf", FILTER_VALIDATE_BOOLEAN, array("flags" => FILTER_NULL_ON_FAILURE))) {
  $data->assign("hide_footer", true);
}

# 显示rrdtool 版本信息
if ($version["rrdtool"]) {
   $data->assign("rrdtool_version",$version["rrdtool"]);
}

$backend_components = array("gmetad", "gmetad-python", "gmond");

# 分别显示gmetad gmetad-python gmond 版本信息
foreach ($backend_components as $backend) {
   if (isset($version[$backend])) {
      $data->assign("webbackend_component", $backend);
      $data->assign("webbackend_version",$version[$backend]);
      break;
   }
}

$data->assign("parsetime", sprintf("%.4f", $parsetime) . "s");
# 设置解析用时间

$dwoo->output($tpl, $data);
?>

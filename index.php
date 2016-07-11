<?php
include_once "./eval_conf.php";
# ATD - function.php must be included before get_context.php.  It defines some needed functions.
include_once "./functions.php";
include_once "./get_context.php";
include_once "./ganglia.php";
include_once "./get_ganglia.php";
include_once "./dwoo/dwooAutoload.php";

#类似教务系统登录
$resource = GangliaAcl::ALL_CLUSTERS;
if( $context == "grid" ) {  #老师登录
  $resource = $grid;
} else if ( $context == "cluster" || $context == "host" ) { #大学生或研究生登录
  $resource = $clustername; 
}
if( ! checkAccess( $resource, GangliaAcl::VIEW, $conf ) ) { #密码错误
  header( "HTTP/1.1 403 Access Denied" );
  die("<html><head><title>Access Denied</title><body><h4>Sorry, you do not have access to this resource.</h4></body></html>");
}
    

try
   {
      $dwoo = new Dwoo($conf['dwoo_compiled_dir'], $conf['dwoo_cache_dir']);
       #Dwoo是一个PHP5模板引擎。兼容Smarty模板，它在Smarty语法的基础上完全进行重写。支持通过插件扩展其功能。
       #smarty是一个基于PHP开发的PHP模板引擎。它提供了逻辑与外在内容的分离，简单的讲，目的就是要使用PHP程序员同美工分离,
       #使用的程序员改变程序的逻辑内容不会影响到美工的页面设计，美工重新修改页面不会影响到程序的程序逻辑，这在多人合作的项目中显的尤为重要。

   }
catch (Exception $e)
   {
       print "<H4>There was an error initializing the Dwoo PHP Templating Engine: ".#初始化失败
      $e->getMessage() . "<br><br>The compile directory should be owned and writable by the apache user.</H4>";
      exit;
   }

# Useful for addons.插件
$GHOME = ".";

if ($context == "meta" or $context == "control") {
      $title = "$self ${conf['meta_designator']} Report";
      include_once "./header.php";
      include_once "./meta_view.php";
} else if ($context == "tree") {
      $title = "$self ${conf['meta_designator']} Tree";
      include_once "./header.php";
      include_once "./grid_tree.php";
} else if ($context == "cluster" or $context == "cluster-summary") {
      if (preg_match('/cluster/i', $clustername))//正则匹配
         $title = "$clustername Report";
      else
         $title = "$clustername Cluster Report";

      include_once "./header.php";
      include_once "./cluster_view.php";
} else if ($context == "physical") {
      $title = "$clustername Physical View";
      include_once "./header.php";
      include_once "./physical_view.php";
} else if ($context == "node") {
      $title = "$hostname Node View";
      include_once "./header.php";
      include_once "./show_node.php";
} else if ($context == "host") {
      $title = "$hostname Host Report";
      include_once "./header.php";
      include_once "./host_view.php";
} else if ($context == "views") {
      $title = "$viewname view";
      include_once "./header.php";
      include_once "./views_view.php";
} else if ($context == "compare_hosts") {
      $title = "Compare Hosts";
      include_once "./header.php";
      include_once "./compare_hosts.php";
} else if ($context == "decompose_graph") {
      $title = "Decompose graph";
      include_once "./header.php";
      include_once "./decompose_graph.php";
} else {
      $title = "Unknown Context";
      print "Unknown Context Error: Have you specified a host but not a cluster?.";
}
include_once "./footer.php";

?>

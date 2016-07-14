<script>
$( "#tabs" ).bind( "tabsshow", function(event, ui) {
    jQuery('#jquery-live-search').slideUp(0);
  });
</script>

<?php
// 上面为jquery 代码，把搜索页面和对应tab绑定
require_once('./eval_conf.php');
require_once('./functions.php');

// Load the metric caching code
//$debug = 1;
// 加载参数缓存
retrieve_metrics_cache();

# 判断是否为移动版页面
if ( isset($_GET['mobile']))
  $mobile = 1;
else
  $mobile = 0;

$results = "";

if ( isset($_GET['q']) && $_GET['q'] != "" ) {
  //如果用户进行了查询的话

  $query = $_GET['q'];
  // First we look for the hosts
  // 首先在 主机里面寻找
  foreach ( $index_array['hosts'] as $key => $host_name ) {
    if ( preg_match("/$query/i", $host_name ) ) {
      $clusters = $index_array['cluster'][$host_name];
      foreach ($clusters AS $cluster_name) {
      if ( $mobile )
	$results .= '<a onclick="jQuery(\'#jquery-live-search\').slideUp(0)" href="mobile_helper.php?show_host_metrics=1&h=' . $host_name . '&c=' . $cluster_name . '&r=' . $conf['default_time_range'] . '&cs=&ce=">Host: ' . $host_name ." (" . $cluster_name . ')</a>';  
      else
        $results .= "Host: <a target=\"_blank\" href=\"?c=" . $cluster_name . "&h=" . $host_name . "&m=cpu_report&r=" . $conf['default_time_range']  ."&s=descending&hc=4&mc=2\">" . $host_name . " ( " . $cluster_name . " )</a><br>";
      }
    }
  }

  // Now let's look through metrics.
  // 然后在参数里面去找
  foreach ( $index_array['metrics'] as $metric_name => $hosts ) {
    if ( preg_match("/$query/i", $metric_name ) ) {
      foreach ( $hosts as $key => $host_name ) {
        $clusters = $index_array['cluster'][$host_name];
        foreach ($clusters AS $cluster_name) {
          if ( $mobile ) {
            $results .= 'Metric: <a onclick="jQuery(\'#jquery-live-search\').slideUp(0)" href="mobile_helper.php?show_host_metrics=1&h=' . $host_name . '&c=' . $cluster_name . '&r=' . $conf['default_time_range'] . '&cs=&ce=">' . $host_name . " (" . $metric_name .  ")</a><br>";
          } else {
            $results .= "Metric: <a target=\"_blank\" href=\"?c=" . $cluster_name . "&h=" . $host_name . "&m=cpu_report&r=hour&s=descending&hc=4&mc=2#metric_" . $metric_name  . "\">" . $host_name . " @ " . $cluster_name .  " (" . $metric_name .  ")</a><br>";
          }
        }
      }
    }
  }
  //找不到的处理
} else {
  $results .= "Empty query string";
}

if ( $results == "" ) {
  print "No results. Try a different search term. One term only.";
} else {
  
  if ( $mobile ) {
    
   print $results;
  } else {
    print $results;
  }
  
}

?>

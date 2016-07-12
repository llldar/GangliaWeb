<?php
#
# Retrieves and parses the XML output from gmond. Results stored
# in global variables: $clusters, $hosts, $hosts_down, $metrics.
# Assumes you have already called get_context.php.
#
# 获取并解析 gmond 的 XML 输出
# 结果存储在全局变量 $clusters, $hosts, $hosts_down, $metrics 中
# 假设你已经调用过get_context.php了
#
#
# If we are in compare_hosts, views and decompose_graph context we shouldn't attempt
# any connections to the gmetad
# 如果我们已经在compare_hosts, views and decompose_graph三个情景下的话
# 我们就不应该在进行和 gmetad 的连接了
#

if ( in_array($context, $SKIP_GMETAD_CONTEXTS) ) {
   
} else {
   if (! Gmetad($conf['ganglia_ip'], $conf['ganglia_port']) )
      {
         print "<H4>There was an error collecting ganglia data ".
            "(${conf['ganglia_ip']}:${conf['ganglia_port']}): $error</H4>\n";
         exit;
      }
      
      
   # If we have no child data sources, assume something is wrong.
   # 如果一个集群内没有任何子信息源，就认为出现错误了
   if (!count($grid) and !count($cluster))
      {
         print "<H4>Ganglia cannot find a data source. Is gmond running?</H4>";
         exit;
      }
   # If we only have one cluster source, suppress MetaCluster output.
   # 如果只有一个集群，那么就压缩输出
   if (count($grid) < 2 and $context=="meta")
      {
         # Lets look for one cluster (the other is our grid).
         # 寻找到一个集群
         foreach($grid as $source)
            if (isset($source['CLUSTER']) and $source['CLUSTER'])
               {
                  $standalone = 1;
                  $context = "cluster";
                  # Need to refresh data with new context.
                  # 应该用新的情景来刷新数据
                  Gmetad($conf['ganglia_ip'], $conf['ganglia_port']);
                  $clustername = $source['NAME'];
               }
      }

}

?>

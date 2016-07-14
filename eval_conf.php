<?php
# $Id$
#
# read and evaluate the configuration file
#读取和评估配置文件
#

$base_dir = dirname(__FILE__);
    #给出一个包含有指向一个文件的全路径的字符串，dirname函数返回去掉文件名后的目录名。
set_include_path( "$base_dir/lib:" . ini_get( 'include_path' ) );
    #设置初始值

# Load main config file.加载主配置文件
require_once $base_dir . "/conf_default.php";
require_once 'lib/GangliaAcl.php';
require_once 'lib/GangliaAuth.php';

    # Include user-defined overrides if they exist.如果存在用户定义的覆盖，就包括他们。

if( file_exists( $base_dir . "/conf.php" ) ) {
  include_once $base_dir . "/conf.php";
}

$errors = array();

if ($conf['overlay_events'] && ($conf['overlay_events_provider'] == "json")) {
  $events_file = $conf['overlay_events_file'];
  if (!file_exists($events_file)) {
    $dir = dirname($events_file);
    if (!file_exists($dir)) {
      if (!mkdir($dir, 0755, true))
	$errors[] = "Unable to create directory for overlay events file: " .
	  $dir;
    }
    $f = fopen($events_file, "w");
    if ($f === FALSE)
      $errors[] = "Unable to create overlay events file: " . $events_file;
    else {
      fclose($f);
      chmod($events_file, 0755);
    }
  }
}

if ($conf['overlay_events']) {
  $event_color_map_file = $conf['overlay_events_color_map_file']; 
  if (!file_exists($event_color_map_file)) {
    $dir = dirname($event_color_map_file);
    if (!file_exists($dir)) {
      if (!mkdir($dir, 0755, true))
	$errors[] = "Unable to create directory for event color map file: " .
	  $dir;
    }
    $f = fopen($event_color_map_file, "w");
    if ($f === FALSE)
      $errors[] = "Unable to create event color map file: " . 
	$event_color_map_file;
    else {
      fclose($f);
      chmod($event_color_map_file, 0755);
    }
  }
}

// Installation validity checks检查安装的有效性
if ( ! isset($conf['rrds']) ||  ! is_readable($conf['rrds']) ) {
  $errors[] = "RRDs directory '${conf['rrds']}' is not readable.<br/>".
  "Please adjust <code>\$conf['rrds']</code>."; 
}

if ( ! isset($conf['dwoo_compiled_dir']) || ! is_writeable($conf['dwoo_compiled_dir']) ) {
  $errors[] = "DWOO compiled templates directory '${conf['dwoo_compiled_dir']}' is not writeable.<br/>".
  "Please adjust <code>\$conf['dwoo_compiled_dir']</code>."; 
}

if ( ! isset($conf['dwoo_cache_dir']) || ! is_writeable($conf['dwoo_cache_dir']) ) {
  $errors[] = "DWOO cache directory '${conf['dwoo_cache_dir']}' is not writeable.<br/>".
  "Please adjust <code>\$conf['dwoo_cache_dir']</code>."; 
}

if( ! isSet($conf['views_dir']) || ! is_readable($conf['views_dir']) ) {
  $errors[] = "Views directory '${conf['views_dir']}' is not readable.<br/>".
  "Please adjust <code>\$conf['views_dir']</code>.";
}

if( ! isSet($conf['conf_dir']) || ! is_readable($conf['conf_dir']) ) {
  $errors[] = "Directory used to store configuration information '${conf['conf_dir']}' is not readable.<br/>".
  "Please adjust <code>\$conf['conf_dir']</code>.";
}

$valid_auth_options = array( 'disabled', 'readonly', 'enabled' );
if( ! isSet( $conf['auth_system'] ) ) {
  $errors[] = "Please define \$conf['auth_system'] and set it to one of these values:
  <ul>
    <li><code>'readonly'</code> : All users can view.  No-one can edit.</li>
    <li><code>'disabled'</code> : All users can view and edit anything.</li>
    <li><code>'enabled'</code> : All users can view public clusters.  
      Users may authenticate to gain additional access. (View private clusters, edit views, etc.)  
      Requires configuration of an authentication mechanism in your web server.
    </li>
  </ul>
  <br/>See <a href=\"https://sourceforge.net/apps/trac/ganglia/wiki/ganglia-web-2/AuthSystem\">https://sourceforge.net/apps/trac/ganglia/wiki/ganglia-web-2/AuthSystem</a> for more information.";
} else {
  if( ! in_array( $conf['auth_system'], $valid_auth_options ) ) {
    $errors[] = "Please set \$conf['auth_system'] to one of these values: '".implode( "','", $valid_auth_options ) ."'";
  } else if( $conf['auth_system'] == 'enabled' ) {    
    $auth = GangliaAuth::getInstance();
    if(!$auth->environmentIsValid()) {
      $errors[] = "Problems found with authorization system configuration:".
      "<ul><li>".implode("</li><li>",$auth->getEnvironmentErrors())."</li></ul>".
      "<br/>You may also use <code>\$conf['auth_system'] = 'readonly';</code> or <code>\$conf['auth_system'] = 'disabled';</code>";
    }
  }
}

if( count($errors) ) {
  $e = "<h1>Errors were detected in your configuration.</h1>";
  $e .= "<ul class=\"errors\">";
  foreach($errors as $error) {
    $e .= "<li>$error</li>";
  }
  $e .= "</ul>";

  // Make sure that errors are actually displayed, whether or not the local
  // PHP configuration is set to display them, otherwise it looks as though
  // a blank page is being served.
//    不管本地PHP配置是否设置了显示错误，请确保错误会被显示出来，否则它看起来就像一个空白的即将被送走的网页
  ini_set('display_errors', 1);
  error_reporting(E_ALL);

  trigger_error( $e, E_USER_ERROR );
}

# These are settings derived from the configuration settings, and
# should not be modified.  This file will be overwritten on package upgrades,
# while changes made in conf.php should be preserved
    #这些设置属于配置设置， 不应该被修改， 这些文件会被升级包覆盖， 当conf.php被修改时， 这些文件应该保存下来
$rrd_options = "";
if( isset( $conf['rrdcached_socket'] ) )
{
    if(!empty( $conf['rrdcached_socket'] ) )
    {
        $rrd_options .= " --daemon ${conf['rrdcached_socket']}";
    }
}
?>

<?php
#本文件是php的dwoo库文件，用于加载对应模板
include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Dwoo'. DIRECTORY_SEPARATOR . 'Core.php';

function dwooAutoload($class)
{
	if (substr($class, 0, 5) === 'Dwoo_' || $class === 'Dwoo') {
		include DWOO_DIRECTORY . strtr($class, '_', DIRECTORY_SEPARATOR).'.php';
	}
}

spl_autoload_register('dwooAutoload');

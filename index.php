<?php

namespace infrajs\yml;
use infrajs\catalog\Catalog;
use infrajs\config\Config;

if (!is_file('vendor/autoload.php')) {
    chdir('../../../');
    require_once('vendor/autoload.php');
}

$html = Catalog::cache('ymlshow', function () {
	return Yml::init();
}, array(), isset($_GET['re']));

header("Content-type: text/xml");
echo $html;
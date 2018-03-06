<?php

namespace infrajs\yml;
use infrajs\catalog\Catalog;
use infrajs\config\Config;

if (!is_file('vendor/autoload.php')) {
    chdir('../../../');
    require_once('vendor/autoload.php');
}

$html = Catalog::cache( function () {
	return Yml::init();
});

header("Content-type: text/xml");
echo $html;
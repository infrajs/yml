<?php

namespace infrajs\yml;
use infrajs\akyatkin\Showcase;
use infrajs\config\Config;
use infrajs\access\Access;
use infrajs\once\Once;

$html = Once::func( function () {
	return Yml::parse();
});

header("Content-type: text/xml");
echo $html;
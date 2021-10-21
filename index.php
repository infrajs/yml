<?php

namespace infrajs\yml;
use infrajs\config\Config;
use infrajs\access\Access;
use infrajs\once\Once;
use infrajs\rest\Rest;

$html =  Rest::get(function (){
	return Once::func( function () {
		return Yml::parse();
	});	
},'rss', function (){
	return Once::func( function () {
		return Yml::rss();
	});
},'google', function (){
	return Once::func( function () {
		return Yml::rss();
	});	
});


header("Content-type: text/xml");
echo $html;
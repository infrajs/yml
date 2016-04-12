<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="{~date(:Y-m-d H:i,~true)}">
<shop>

	<name>{conf.name}</name>
	<company>{conf.company}</company>
	<url>{site}</url>
	<platform>Infrajs</platform>
	<agency>{conf.agency}</agency>
	<email>{support}</email>
	<currencies>
		<currency id="RUR" rate="1"/>
	</currencies>
	<categories>{groups::category}
	</categories>
	<offers>
		{poss::pos}	
		
	</offers>
 </shop>
 </yml_catalog>
 {category:}
 		<category id="{id}" parentId="{parentId}">{title}</category>
{pos:}
 	<offer type="vendor.model" id="{id}" available="{Наличие=:да?:true?:false}">
		<url>{...site}catalog/{~encode(producer)}/{~encode(article)}</url>
		<price>{Цена}</price>
		<sales_notes>Работа по 100% предоплате</sales_notes>
		<currencyId>RUB</currencyId>
		<categoryId>{categoryId}</categoryId >
		{images::image}
		<vendor>{Производитель}</vendor>
		<model>{article}</model>
		<description>{Описание}</description>
		{more::param}
		
	</offer>
{param:}
	<param name="{~key}">{.}</param>
{image:}
	<picture>http://{site}{.}</picture>

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="{~date(:Y-m-d H:i,~true)}">
<shop>
	<name>{conf.name}</name>
	<company>{conf.company}</company>
	<url>{conf.site}</url>
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
 	<offer type="vendor.model" id="{id}">
		<url>{conf.site}/catalog/{producer_nick}/{article_nick}</url>
		<model>{Наименование}</model>
		<price>{Цена}</price>
		<currencyId>RUB</currencyId>
		<categoryId>{categoryId}</categoryId>
		{images::image}
		<vendor>{producer}</vendor>
		{Описание?:des}
		<param name="article">{article}</param>
		{more::param}
	</offer>
{des:}
		<description><![CDATA[
			{Описание}
		]]></description>
{param:}
		<param name="{~encode(~key)}">{.}</param>
{image:}
		<picture>{conf.site}/{.}</picture>

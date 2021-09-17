<?xml version="1.0"?>
<rss version="2.0" 
xmlns:g="http://base.google.com/ns/1.0">
<channel>
	<title>{conf.company}</title>
	<link>{conf.site}</link>
	<description>{conf.description}</description>
	{poss::pos}
</channel>
</rss>
{pos:}
	<item>
		<title>{Наименование} {producer} {article}</title>
		<link>{conf.site}/catalog/{producer_nick}/{article_nick}</link>
		{Описание:des}
		{images.0:image}
		<g:condition>new</g:condition>
		<g:availability>preorder</g:availability>
		<g:availability_date>{~date(:Y-m-d,availability_date)}</g:availability_date>
		<g:product_type>{parent} > {group}</g:product_type>
		<g:brand>{producer}</g:brand>
		{Цена:price}
		<g:id>{producer_nick}_{article_nick}</g:id>
		<g:shipping>
			<g:country>Россия</g:country>
			<g:service>Транспортные компании</g:service>
			<g:price>250 RUB</g:price>
			<g:min_handling_time>1</g:min_handling_time>
			<g:max_handling_time>5</g:max_handling_time>
			<g:min_transit_time>1</g:min_transit_time>
			<g:max_transit_time>5</g:max_transit_time>
		</g:shipping>
	</item>
	{des:}<description><![CDATA[{.}]]></description>
	{image:}<g:image_link>{conf.site}/{.}</g:image_link>
	{price:}<g:price>{.} RUB</g:price>
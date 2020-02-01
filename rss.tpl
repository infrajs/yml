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
		<g:product_type>{parent} > {group}</g:product_type>
		<g:brand>{producer}</g:brand>
		<g:price>{Цена}</g:price>
		<g:id>{producer_nick}-{article_nick}</g:id>
	</item>
	{des:}<description><![CDATA[{.}]]></description>
	{image:}<g:image_link>{conf.site}/{.}</g:image_link>
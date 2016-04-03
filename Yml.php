<?php
namespace infrajs\yml;

use infrajs\path\Path;
use infrajs\excel\Xlsx;
use infrajs\template\Template;
use infrajs\cache\Cache;
use infrajs\catalog\Catalog;
use infrajs\view\View;

class Yml
{
	public static $conf = array(
		"name" => '',
		"company" => '',
		"agancy" => ''
	);
	public static function parse($data)
	{
		$gid = 0;
		$pid = 0;
		$groups = array();
		$poss = array();
		$conf = static::$conf;
		
		if (!$conf['name']) {
			throw new \Exception('В конфиге yml требуется указать name. Наименование компании без организационный формы');
		}
		if (!$conf['company']) {
			throw new \Exception('В конфиге yml требуется указать company, название компании с организационной формой ООО');
		}

		Xlsx::runPoss($data, function (&$pos) {
			$pos['Описание'] = Yml::tostr($pos['Описание']);
		});
		Xlsx::runGroups($data, function (&$group, $i, &$parent) use (&$gid, &$groups) {
			$group['id'] = ++$gid;
			if ($parent) {
				$group['parentId'] = $parent['id'];
			}
			$groups[] = &$group;
		});

		Xlsx::runPoss($data, function (&$pos, $i, &$group) use (&$pid, &$poss) {
			$pos['id'] = ++$pid;
			$pos['categoryId'] = $group['id'];
			$poss[] = &$pos;
		});

		$d=array(
			"conf" => $conf,
			"site" => View::getPath(),
			"poss" => $poss,
			"groups" => $groups
		);
		
		$html = Template::parse('-yml/yml.tpl', $d);
		return $html;
	}
	public static function tostr($str)
	{
		$str = preg_replace('/&/', '&amp;', $str);
		$str = preg_replace('/</', '&lt;', $str);
		$str = preg_replace('/>/', '&gt;', $str);
		$str = preg_replace('/"/', '&quot;', $str);
		$str = preg_replace("/'/", '&apos;', $str);
		return $str;
	}
	public static function init()
	{
		$data = Catalog::init();
		Xlsx::runGroups($data, function (&$group, $i, &$parent) {
			$group['data'] = array_filter($group['data'], function (&$pos) {
			//Убираем позиции у которых не указана цена
				//if($pos['Синхронизация']!='Да')return false;
				if (!$pos['Цена']) {
					return false;
				}
				if (strtolower($pos['Маркет']) == 'да') {
					return true;
				}
			});
			$group['data'] = array_values($group['data']);
		});

		Xlsx::runGroups($data, function (&$group, $i, &$parent) {
			if ($group['childs']) {
				$group['childs'] = array_filter($group['childs'], function (&$g) {
					if (!$g['data'] && !$g['childs']) {
						return false;
					}
					return true;
				});
				$group['childs'] = array_values($group['childs']);
			}
		}, array(), true);
		Xlsx::runPoss($data, function (&$pos) {
			$conf = Catalog::$conf;
			Xlsx::addFiles($conf['dir'], $pos, array('Производитель', 'article'));
			foreach ($pos['images'] as $k => $v) {
				$src = $pos['images'][$k];
				$p = explode('/', $src);
				foreach ($p as $i => $n) {
					$p[$i] = urlencode($n);
					$p[$i] = preg_replace('/\+/', '%20', $p[$i]);
				}
				$pos['images'][$k] = implode('/', $p);
			}
		});
		return static::parse($data);
	}
}
<?php
namespace infrajs\yml;

use infrajs\path\Path;
use infrajs\excel\Xlsx;
use infrajs\template\Template;
use infrajs\cache\Cache;
use akiyatkin\showcase\Showcase;
use infrajs\view\View;
use infrajs\load\Load;
use infrajs\event\Event;
use infrajs\access\Access;

Event::$classes["Yml"] = function($pos) { 
	return $pos["producer_nick"].$pos["article_nick"].$pos["item_nick"];
};

class Yml
{
	public static $conf = array(
		"name" => '',
		"company" => '',
		"agancy" => ''
	);
	/*public static function parse($poss, $groups)
	{
		$gid = 0;
		$pid = 0;
		$conf = static::$conf;
		
		if (!$conf['name']) {
			throw new \Exception('В конфиге yml требуется указать name. Наименование компании без организационный формы');
		}
		if (!$conf['company']) {
			throw new \Exception('В конфиге yml требуется указать company, название компании с организационной формой ООО');
		}

		Xlsx::runPoss($data, function &(&$pos, $i, &$group) use (&$pid, &$poss) {
			$pos['id'] = ++$pid;
			$pos['categoryId'] = $group['id'];
			$poss[] = &$pos;
			$r = null;
			return $r;
		});

		$d=array(
			"conf" => $conf,
			"support" => Access::$conf["admin"]["support"],
			"site" => View::getPath(),
			"poss" => $poss,
			"groups" => $groups
		);
		
		$html = Template::parse('-yml/yml.tpl', $d);
		return $html;
	}*/
	public static function tostr($str)
	{
		//$str = strip_tags($str);
		$str = preg_replace('/\&/', '&amp;', $str);
		$str = preg_replace('/</', '&lt;', $str);
		$str = preg_replace('/>/', '&gt;', $str);
		$str = preg_replace('/"/', '&quot;', $str);
		$str = preg_replace("/'/", '&apos;', $str);
		return $str;
	}
	public static function parse()
	{
		$data = Load::loadJSON('-showcase/api/groups');
		$groups = [];
		$conf = static::$conf;
		$gid = 0;
		Xlsx::runGroups($data['root'], function &($group) use (&$groups, &$gid) {
			$groups[$group['group_nick']] = [
				'title' => $group['group'],
				'id' => ++$gid
			];
			if ($group['parent_nick']) {
				$groups[$group['group_nick']]['parentId'] = $groups[$group['parent_nick']]['id'];
			}
			$r = null; return $r;
		});
		
		$md = [
			'more'=>[
				'Цена'=>[
					'yes' => 1
				],
				'images'=>[
					'yes' => 1
				]
			]
		];
		
		$data = Showcase::search($md);
		$poss = $data['list'];
		
		$pid = 0;
		$poss = array_filter($poss, function (&$pos) use(&$pid, $groups) {
			//Убираем позиции у которых не указана цена
			//if($pos['Синхронизация']!='Да')return false;
			$res = Event::fire('Yml.oncheck', $pos);
			if (!$res) return false;
			return true;
		});

		foreach ($poss as $k=>$pos) {
			
			$poss[$k]['id'] = ++$pid;
			$poss[$k]['categoryId'] = $groups[$pos['group_nick']]['id'];
			foreach ($pos['images'] as $j => $v) {
				$src = $pos['images'][$j];
				$p = explode('/', $src);
				foreach ($p as $i => $n) {
					if (!$i) continue;
					$p[$i] = Template::$scope['~encode']($n);
					$p[$i] = preg_replace('/\+/', '%20', $p[$i]);
				}
				$poss[$k]['images'][$j] = implode('/', $p);
			}
			
			
			if (isset($pos['Описание'])) $poss[$k]['Описание'] = Yml::tostr($pos['Описание']);
			if (isset($pos['Наименование'])) $poss[$k]['Наименование'] = Yml::tostr($pos['Наименование']);
			if (isset($pos['article'])) $poss[$k]['article'] = Yml::tostr($pos['article']);
			
			if (isset($pos['more'])) foreach ($pos['more'] as $i => $m) {
				$poss[$k]['more'][$i] = Yml::tostr($pos['more'][$i]);
			}
		};
		$groups = array_values($groups);
		
		

		
		if (!$conf['name']) {
			throw new \Exception('В конфиге yml требуется указать name. Наименование компании без организационный формы');
		}
		if (!$conf['company']) {
			throw new \Exception('В конфиге yml требуется указать company, название компании с организационной формой ООО');
		}
		
		$d = array(
			"conf" => $conf,
			"support" => Access::$conf["admin"]["support"],
			"site" => View::getPath(),
			"poss" => $poss,
			"groups" => $groups
		);
		
		$html = Template::parse('-yml/yml.tpl', $d);
		return $html;
	}
}
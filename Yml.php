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
	return $pos["producer_nick"].$pos["article_nick"];
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
	public static function data($modelids = false) {
		$data = Load::loadJSON('-showcase/api2/groups');
		$groups = [];
		$poss = [];
		$conf = static::$conf;
		if (!empty($data['root'])) {
			Xlsx::runGroups($data['root'], function &($group) use (&$groups) {
				$groups[$group['group_nick']] = [
					'title' => $group['group'],
					'id' => $group['group_id']
				];
				if ($group['parent_nick']) {
					$groups[$group['group_nick']]['parentId'] = $groups[$group['parent_nick']]['id'];
				}
				$r = null; return $r;
			});


			if (!$modelids) {
				$mark = Showcase::getDefaultMark();
				$mark->setVal($conf['search']);
				$md = $mark->getData();
				$data = [];
				Showcase::search($md, $data, 1, true);
				//if (empty($data['list'])) $data['list'] = [];
				$poss = $data['list'];
			} else {
				$poss = [];
				foreach ($modelids as $model_id) {
					$model = Showcase::getModelEasyById($model_id);
					Showcase::addMore($model);
					if (isset($model['Цена']) && !empty($model['images'])) {
						$poss[] = $model;
					}
				}
			}
			
			


			//$poss = Yml::checkPos($poss);



			$poss = array_filter($poss, function ($pos) {
				$res = Event::fire('Yml.oncheck', $pos);
				if (!$res) return false;
				return true;
			});
			foreach ($poss as $k=>$pos) {
				$poss[$k]['availability_date'] = time() + 14*24*60*60;
				$poss[$k]['id'] = $pos['model_id'];
				$poss[$k]['categoryId'] = $groups[$pos['group_nick']]['id'];
				if (isset($pos['images'])) {
					foreach ($pos['images'] as $j => $v) {
						$src = Path::theme($pos['images'][$j]);
						if ($src) { //Может не быть если это адрес в инете
							$p = explode('/', $src);
							foreach ($p as $i => $n) {
								if (!$i) continue;
								$fn = Template::$scope['~encode'];
								$p[$i] = $fn($n);
								$p[$i] = preg_replace('/\+/', '%20', $p[$i]);
							}
							$poss[$k]['images'][$j] = $conf['site'].'/'.implode('/', $p);
						}
					}
				}
				if (isset($pos['Описание'])) $poss[$k]['Описание'] = Yml::tostr($pos['Описание']);
				if (isset($pos['Наименование'])) $poss[$k]['Наименование'] = Yml::tostr($pos['Наименование']);
				if (isset($pos['article'])) $poss[$k]['article'] = Yml::tostr($pos['article']);
				if (isset($pos['Цена'])) {
					$r = Template::$scope['~costround']($pos['Цена']);
					$poss[$k]['Цена'] = (string) $r[0];
					if ($r[1]) $poss[$k]['Цена'] .= '.'. $r[1];
				}
				
				if (isset($pos['more'])) {
					$more = [];
					foreach ($pos['more'] as $i => $m) {
						$more[Yml::tostr($i)] = Yml::tostr($m);
					}
					$poss[$k]['more'] = $more;
				}
				
			};
			$groups = array_values($groups);
			
			

			
			if (!$conf['name']) {
				throw new \Exception('В конфиге yml требуется указать name. Наименование компании без организационный формы');
			}
			if (!$conf['company']) {
				throw new \Exception('В конфиге yml требуется указать company, название компании с организационной формой ООО');
			}
		}
		$d = array(
			"conf" => $conf,
			"support" => Access::$conf["admin"]["support"],
			"poss" => $poss,
			"groups" => $groups
		);
		return $d;
	}
	public static function parse() {
		$data = Yml::data();
		$html = Template::parse('-yml/yml.tpl', $data);
		return $html;
	}
	public static function rss() {
		$data = Yml::data();
		$html = Template::parse('-yml/rss.tpl', $data);
		return $html;
	}
}
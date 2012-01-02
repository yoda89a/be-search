<?php
/*
 * Copyright (c) 2012, webvariants GbR, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class sly_Controller_Besearchapi extends sly_Controller_Ajax {
	public function indexAction() {
		print 'Welcome to the API controller.';
	}

	public function articlesearchAction() {
		$query  = sly_get('q', 'string');
		$sql    = sly_DB_Persistence::getInstance();
		$prefix = sly_Core::getTablePrefix();
		$user   = sly_Util_User::getCurrentUser();
		$clang  = sly_Core::getCurrentClang();

		$sql->query('SELECT id FROM '.$prefix.'article WHERE name LIKE ? GROUP BY id', array("%$query%"));

		foreach ($sql as $row) {
			$id      = $row['id'];
			$article = sly_Util_Article::findById($id, $clang);

			if ($article && sly_Util_Article::canReadArticle($user, $id)) {
				$name = str_replace('|', '/', sly_html($article->getName()));
				$path = $article->getParentTree();

				foreach ($path as $idx => $cat) {
					$path[$idx] = str_replace('|', '/', sly_html($cat->getName()));
				}

				if (count($path) > 3) {
					$path = array_slice($path, -2);
					array_unshift($path, '&hellip;');
				}

				array_unshift($path, '(Homepage)');
				printf("%s|%d|%s|%d\n", $name, $id, implode(' &gt; ', $path), $clang);
			}
		}
	}

	public function checkPermission() {
		return sly_Util_User::getCurrentUser() !== null;
	}
}

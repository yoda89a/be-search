<?php
/*
 * Copyright (c) 2012, webvariants GbR, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

class sly_Controller_Besearchapi extends sly_Controller_Ajax implements sly_Controller_Interface {
	public function indexAction() {
		return new sly_Response('Welcome to the API controller.', 404);
	}

	public function articlesearchAction() {
		$query    = sly_get('q', 'string');
		$sql      = sly_DB_Persistence::getInstance();
		$user     = sly_Util_User::getCurrentUser();
		$prefix   = sly_Core::getTablePrefix();
		$clang    = sly_Core::getCurrentClang();
		$response = sly_Core::getResponse();
		$home     = '('.t('home').')';
		$lines    = array();

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

				array_unshift($path, $home);
				$lines[] = sprintf('%s|%d|%s|%d', $name, $id, implode(' &gt; ', $path), $clang);
			}
		}

		$response->setContentType('text/plain');
		$response->setContent(implode("\n", $lines));

		return $response;
	}

	public function checkPermission($action) {
		return sly_Util_User::getCurrentUser() !== null;
	}
}

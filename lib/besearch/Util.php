<?php
/*
 * Copyright (c) 2012, webvariants GbR, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

/**
 *
 * @author zozi@webvariants.de
 */
abstract class besearch_Util {
	/**
	 * Eventhandler for PAGE_CHECKED Event
	 *
	 * @param array $params
	 */
	public static function controllerFound(array $params) {
		$dispatcher = sly_Core::dispatcher();
		$page       = $params['subject'];
		$assets     = null;

		if ($page === 'structure') {
			$assets = true;
			$dispatcher->register('PAGE_STRUCTURE_HEADER', array(__CLASS__, 'articleSearch'));
		}
		elseif ($page === 'content' || $page === 'contentmeta') {
			$assets = true;
			$dispatcher->register('PAGE_CONTENT_HEADER', array(__CLASS__, 'articleSearch'));
		}
		elseif ($page === 'mediapool') {
			$assets = false;
			$dispatcher->register('SLY_MEDIA_LIST_TOOLBAR', array(__CLASS__, 'mediaToolbar'));
			$dispatcher->register('SLY_MEDIA_LIST_QUERY', array(__CLASS__, 'mediaQuery'));
		}

		if ($assets !== null) {
			self::addAssets($assets);
			sly_Core::getI18N()->appendFile(SLY_ADDONFOLDER.'/be_search/lang/');
		}
	}

	/**
	 * adds some needed assets to page
	 *
	 * @param boolean $articleSearch  add js for article search
	 */
	private static function addAssets($articleSearch) {
		$layout = sly_Core::getLayout();
		$layout->addCSSFile('../data/dyn/public/be_search/css/be_search.css');

		if ($articleSearch) {
			$layout->addJavaScriptFile('assets/js/jquery.autocomplete.min.js');
			$layout->addJavaScriptFile('../data/dyn/public/be_search/js/be_search.js');
		}
	}

	public static function mediaToolbar(array $params) {
		$mediumName = sly_post('be_search_medium_name', 'string');
		$form       = $params['subject'];
		$input      = new sly_Form_Input_Text('be_search_medium_name', '', $mediumName);
		$button     = new sly_Form_Input_Button('submit', 'be_search_submit', t('search'));

		$row = new sly_Form_Freeform('be_search_medium_name', t('be_search_medium_name'), $input->render().' '.$button->render());
		$form->add($row);

		return $form;
	}

	public static function mediaQuery($params) {
		$where      = $params['subject'];
		$mediumName = sly_post('be_search_medium_name', 'string');

		if (sly_post('be_search_submit', 'boolean') || mb_strlen($mediumName) === 0) {
			return $where;
		}

		$pdo        = sly_DB_PDO_Persistence::getInstance();
		$mediumName = $pdo->quote('%'.$mediumName.'%');

		return "$where AND (f.filename LIKE $mediumName OR f.title LIKE $mediumName)";
	}

	public static function articleSearch(array $params) {
		$editUrl    = 'index.php?page=content&article_id=%d&clang=%d';
		$categoryID = sly_request('category_id', 'int', 0);
		$articleID  = sly_Core::getCurrentArticleId();
		$clang      = sly_Core::getCurrentClang();
		$searchID   = sly_request('besearch-article-id', 'int', 0);

		// article search by ID

		if ($searchID !== 0) {
			$article = sly_Util_Article::findById($searchID, $clang);

			if (sly_Util_Article::isValid($article)) {
				sly_Util_HTTP::redirect(sprintf($editUrl, $searchID, $clang));
			}
		}

		$page       = sly_Core::getCurrentController();
		$user       = sly_Util_User::getCurrentUser();
		$selectName = $page === 'structure' ? 'category_id' : 'article_id';
		$quickNavi  = sly_Form_Helper::getCategorySelect($selectName, false, null, null, $user, 'besearch-category-id', true);

		// find current category

		if ($articleID !== 0) {
			$article = sly_Util_Article::findById($articleID, $clang);

			// the article might just have been deleted, so be careful
			if ($article) $categoryID = $article->getCategoryId();
		}

		// pre-select the category
		$quickNavi->setSelected($categoryID);

		ob_start();
		include SLY_ADDONFOLDER.'/be_search/views/toolbar.phtml';
		$bar = ob_get_clean();

		return $bar.$params['subject'];
	}
}

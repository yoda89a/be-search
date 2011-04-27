<?php

/*
 * Copyright (c) 2011, webvariants GbR, http://www.webvariants.de
 *
 * Diese Datei steht unter der MIT-Lizenz. Der Lizenztext befindet sich in der
 * beiliegenden LICENSE Datei und unter:
 *
 * http://www.opensource.org/licenses/mit-license.php
 * http://de.wikipedia.org/wiki/MIT-Lizenz
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
	public static function pageChecked($params) {
		$dispatcher = sly_Core::dispatcher();
		if ($params['subject'] == 'structure') {
			self::addAssets();
			$dispatcher->register('PAGE_STRUCTURE_HEADER', array(__CLASS__, 'articleSearch'));
		} elseif ($params['subject'] == 'content') {
			self::addAssets();
			$dispatcher->register('PAGE_CONTENT_HEADER', array(__CLASS__, 'articleSearch'));
		} elseif ($params['subject'] == 'mediapool') {
			self::addAssets(false);
			$dispatcher->register('SLY_MEDIA_LIST_TOOLBAR', array(__CLASS__, 'mediaToolbar'));
			$dispatcher->register('SLY_MEDIA_LIST_QUERY', array(__CLASS__, 'mediaQuery'));
		}
	}

	/**
	 * adds some needed assets to page
	 * 
	 * @param boolean $articleSearch add js for article search
	 */
	private static function addAssets($articleSearch = true) {
		$layout = sly_Core::getLayout('Sally');
		$layout->addCSSFile('../data/dyn/public/be_search/css/be_search.css');
		if ($articleSearch) {
			$layout->addJavaScriptFile('media/js/jquery.autocomplete.min.js');
			$layout->addJavaScriptFile('../data/dyn/public/be_search/js/be_search.js');
		}
	}

	public static function mediaToolbar($params) {
		$user = sly_Util_User::getCurrentUser();

		if (!$user->isAdmin() && !$user->hasPerm('be_search[mediapool]')) {
			return $params['subject'];
		}

		if (sly_request('subpage', 'string') != '') {
			return $params['subject'];
		}
		$media_name = sly_request('besearch-media-name', 'string');

		$form   = $params['subject'];
		$input  = new sly_Form_Input_Text('besearch-media-name', t('be_search_mpool_media'), $media_name);
		$button = new sly_Form_Input_Button('submit', 'a256_submit', t('be_search_mpool_start'));

		$button->addClass('rex-form-submit');

		$row = new sly_Form_Freeform('besearch-media-name', t('be_search_mpool_media'), $input->render() . ' ' . $button->render());
		$form->add($row);

		return $form;
	}

	public static function mediaQuery($params) {

		$where      = $params['subject'];
		$media_name = sly_request('besearch-media-name', 'string');

		if (!isset($_POST['a256_submit']) || empty($media_name)) {
			return $where;
		}

		$user = sly_Util_User::getCurrentUser();
		if (!$user->isAdmin() && !$user->hasPerm('be_search[mediapool]')) {
			return $where;
		}

		$media_name = mysql_real_escape_string($media_name);
		$where = "(f.filename LIKE '%$media_name%' OR f.title LIKE '%$media_name%')";

		// Suche auf aktuellen Kontext eingrenzen
		$categoryID = (int) $params['category_id'];
		if ($categoryID != 0) {
			$where .= " AND (c.path LIKE '%|$categoryID|%' OR c.id = $categoryID)";
		}

		return $where;
	}

	public static function articleSearch($params) {
		//check permission
		$user = sly_Util_User::getCurrentUser();
		if (!$user->isAdmin() && !$user->hasPerm('be_search[structure]')) {
			return $params['subject'];
		}

		//evaluate 
		$editUrl             = 'index.php?page=content&article_id=%s&mode=edit&clang=%s';
		$category_id         = sly_request('category_id', 'int', 0);
		$article_id          = sly_request('article_id', 'int', 0);
		$clang               = sly_request('clang', 'rex-clang-id', sly_Core::config()->get('START_CLANG_ID'));
		$besearch_article_id = sly_post('besearch-article-id', 'int', 0);

		// ------------ Parameter
		// ------------ Suche via ArtikelId
		if ($besearch_article_id != 0) {
			$article = sly_Util_Article::findById($besearch_article_id, $clang);

			if (sly_Util_Article::isValid($article)) {
				sly_Util_HTTP::redirect(sprintf($editUrl, $besearch_article_id, $clang));
			}
		}

		//render frontend
		// Auswahl eines normalen Artikels => category holen
		if ($article_id != 0) {
			$article = sly_Util_Article::findById($article_id, $clang);
			// Falls Artikel gerade geloescht wird, gibts keinen OOArticle
			if ($article)
				$category_id = $article->getCategoryId();
		}

		$select_name = 'category_id';
		$add_homepage = true;
		$page = sly_Controller_Base::getPage();
		$mode = sly_request('mode', 'string', 'edit');
		$slot = sly_request('slot', 'string', '');

		if ($page == 'content') {
			$select_name  = 'article_id';
			$add_homepage = false;
		}

		$category_select = new rex_category_select(false, null, true, $add_homepage);
		$category_select->setName($select_name);
		$category_select->setId('besearch-category-id');
		$category_select->setSize('1');
		$category_select->setSelected($category_id);

		$search_bar =
				'<div id="besearch-toolbar" class="rex-toolbar">
		<div class="rex-toolbar-content">
			<div class="rex-form">
				<div class="rex-fl-lft">
					<label for="besearch-article-name">' . t('be_search_article_name') . '</label>
					<input autocomplete="off" id="besearch-article-name" type="text" name="besearch-article-name" value="" />
				</div>
				<form action="index.php?page=' . $page . '" method="post">
					<fieldset>
						<input type="hidden" name="mode" value="' . sly_html($mode) . '" />
						<input type="hidden" name="category_id" value="' . $category_id . '" />
						<input type="hidden" name="article_id" value="' . $article_id . '" />
						<input type="hidden" name="clang" value="' . $clang . '" />
						<input type="hidden" name="slot" value="' . sly_html($slot) . '" />

						<div class="rex-fl-lft">
							<label for="besearch-article-id">' . t('be_search_article_id') . '</label>
							<input type="text" name="besearch-article-id" id="besearch-article-id" />
							<input class="rex-form-submit" type="submit" id="besearch-start-search" value="' . t('be_search_start') . '" />
						</div>

						<div class="rex-fl-rght">
							<label for="besearch-category-id">' . t('be_search_quick_navi') . '</label>' .
							$category_select->get() . '
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div>';

		return $search_bar . $params['subject'];
	}

}
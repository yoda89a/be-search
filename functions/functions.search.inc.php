<?php
/*
 * Copyright (C) 2009 REDAXO
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License Version 2 as published by the
 * Free Software Foundation.
 */

/**
 * @package redaxo4
 */

/**
 * Hebt einen Suchtreffer $needle im Suchergebnis $string hervor
 *
 * @param $params
 */
function rex_a256_highlight_hit($string, $needle)
{
	return preg_replace(
		'/(.*)('.preg_quote($needle, '/').')(.*)/i',
		'\\1<span class="a256-search-hit">\\2</span>\\3',
		$string
	);
}

/**
* Bindet ggf. extensions ein
*
* @param $params Extension-Point Parameter
*/
function rex_a256_extensions_handler($params)
{
	switch ($params['subject'])
	{
		case 'structure' :
			rex_be_search_css_add();
			require_once SLY_INCLUDE_PATH.'/addons/be_search/extensions/extension_search_structure.inc.php';
			rex_register_extension('PAGE_STRUCTURE_HEADER', 'rex_a256_search_structure');
		break;

		case 'content' :
			rex_be_search_css_add();
			require_once SLY_INCLUDE_PATH.'/addons/be_search/extensions/extension_search_structure.inc.php';
			rex_register_extension('PAGE_CONTENT_HEADER', 'rex_a256_search_structure');
		break;

		case 'mediapool' :
			rex_be_search_css_add();
			require_once SLY_INCLUDE_PATH.'/addons/be_search/extensions/extension_search_mpool.inc.php';
			rex_register_extension('MEDIA_LIST_TOOLBAR', 'rex_a256_search_mpool');
			rex_register_extension('MEDIA_LIST_QUERY', 'rex_a256_search_mpool_query');
		break;
	}
}

/**
 * Fügt die benötigen Stylesheets ein
 *
 */
function rex_be_search_css_add()
{
	$layout = sly_Core::getLayout();
	$layout->addCSSFile('../data/dyn/public/be_search/be_search.css');
	$layout->addCSSFile('../data/dyn/public/be_search/be_search_ie_lte_7.css', 'all', 'if lte IE 7');
}

function checkCategoryPerm($category_id) {
		global $REX;
		$category_id = (int) $category_id;
		return $REX['USER']->isAdmin() || $REX['USER']->hasPerm('csw[0]') || $REX['USER']->hasPerm('csr['.$category_id.']') || $REX['USER']->hasPerm('csw['.$category_id.']');
	}
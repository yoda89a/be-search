<?php
/*
 * Copyright (C) 2009 REDAXO
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License Version 2 as published by the
 * Free Software Foundation.
 */

/**
 * Backend Search Addon
 *
 * @author  markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @package redaxo4
 */

function rex_a256_search_mpool($params) {
	global $REX;

	if (!$REX['USER']->isAdmin() && !$REX['USER']->hasPerm('be_search[mediapool]')) {
		return $params['subject'];
	}

	if (sly_request('subpage', 'string') != '') return $params['subject'];
	$media_name = sly_request('a256_media_name', 'string');

	$form   = $params['subject'];
	$input  = new sly_Form_Input_Text('a256_media_name', t('be_search_mpool_media'), $media_name);
	$button = new sly_Form_Input_Button('submit', 'a256_submit', t('be_search_mpool_start'));

	$button->addClass('rex-form-submit');

	$row = new sly_Form_Freeform('a256_media_name', t('be_search_mpool_media'), $input->render().' '.$button->render());
	$form->add($row);

	return $form;
}

function rex_a256_search_mpool_query($params) {
	global $REX;

	$where      = $params['subject'];
	$media_name = rex_request('a256_media_name', 'string');

	if (!isset($_POST['a256_submit']) || empty($media_name)) {
		return $where;
	}


	if (!$REX['USER']->isAdmin() && !$REX['USER']->hasPerm('be_search[mediapool]')) {
		return $where;
	}

	$name       = mysql_real_escape_string($media_name);
	$where      = "(f.filename LIKE '%$name%' OR f.title LIKE '%$name%')";
	$service    = sly_Service_Factory::getService('Addon');
	$mode       = $service->getProperty('be_search', 'searchmode', 'local');
	$categoryID = (int) $params['category_id'];

	// Suche auf aktuellen Kontext eingrenzen

	if ($mode == 'local' && $categoryID != 0) {
		$where .= " AND (c.path LIKE '%|$categoryID|%' OR c.id = $categoryID)";
	}

	return $where;
}

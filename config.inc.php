<?php
/*
 * Copyright (c) 2010, webvariants GbR, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

if (!sly_Core::isBackend()) return;

$REX['EXTPERM'][] = 'be_search[mediapool]';
$REX['EXTPERM'][] = 'be_search[structure]';


$I18N->appendFile(SLY_INCLUDE_PATH.'/addons/be_search/lang/');

require_once SLY_INCLUDE_PATH.'/addons/be_search/functions/functions.search.inc.php';

rex_register_extension('PAGE_CHECKED', 'rex_a256_extensions_handler');
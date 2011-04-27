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
 * Backend Search Addon
 *
 * @author zozi@webvariants.de
 */

if (!sly_Core::isBackend()) return;

sly_Loader::addLoadPath(SLY_ADDONFOLDER.'/be_search/lib');
sly_Core::getI18N()->appendFile(SLY_ADDONFOLDER.'/be_search/lang/');
sly_Core::dispatcher()->register('PAGE_CHECKED', array('besearch_Util', 'pageChecked'));

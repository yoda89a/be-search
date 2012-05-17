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
 * Backend Search Addon
 *
 * @author zozi@webvariants.de
 */

if (!sly_Core::isBackend()) return;
define('BESEARCH_PATH', rtrim(dirname(__FILE__), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);

sly_Loader::addLoadPath(BESEARCH_PATH.'lib');
sly_Core::dispatcher()->register('SLY_CONTROLLER_FOUND', array('besearch_Util', 'controllerFound'));

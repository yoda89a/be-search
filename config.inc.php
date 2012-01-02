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

sly_Loader::addLoadPath(SLY_ADDONFOLDER.'/be_search/lib');
sly_Core::dispatcher()->register('SLY_CONTROLLER_FOUND', array('besearch_Util', 'controllerFound'));

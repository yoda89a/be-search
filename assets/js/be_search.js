/*
 * Copyright (c) 2013, webvariants GbR, http://www.webvariants.de
 *
 * This file is released under the terms of the MIT license. You can find the
 * complete text in the attached LICENSE file or online at:
 *
 * http://www.opensource.org/licenses/mit-license.php
 */

jQuery(function($) {
	if ($.fn.autocomplete) {
		$('#besearch-article-name').autocomplete({
			url:            'index.php',
			paramName:      'q',
			extraParams:    {page: 'besearchapi', func: 'articlesearch', clang: $('#besearch-toolbar input[name="clang"]').val()},
			maxCacheLength: 50,
			matchContains:  true,
			resultsClass:   'sly-filter-results',
			showResult:     function(value, data) {
				return '<span class="name"><strong>' + value + '</strong></span><br/><span class="cat">' + data[1] + '</span>';
			},
			onItemSelect:   function(item) {
				window.location = 'index.php?page=content&article_id=' + item.data[0] + '&clang=' + item.data[2];
			}
		});
	}

	$('#besearch-category-id').change(function() {
		var home = $(this).val() === '0';

		if (home) {
			$('input[name=article_id]', this.form).val(0);
			$('input[name=category_id]', this.form).val(0);
			this.form.action = 'index.php?page=structure';
		}

		this.form.submit();
	});
});

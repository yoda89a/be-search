/*
 * Copyright (c) 2011, webvariants GbR, http://www.webvariants.de
 *
 * Diese Datei steht unter der MIT-Lizenz. Der Lizenztext befindet sich in der
 * beiliegenden LICENSE Datei und unter:
 *
 * http://www.opensource.org/licenses/mit-license.php
 * http://de.wikipedia.org/wiki/MIT-Lizenz
 */

jQuery(function($) {
	if ($.fn.autocomplete) {
		$('#besearch-article-name').autocomplete({
			url:            'index.php',
			paramName:      'q',
			extraParams:    {page: 'api', func: 'linklistbutton_search'},
			maxCacheLength: 50,
			matchContains:  true,
			resultsClass:   'sly-filter-results',
			showResult:     function(value, data) {
				return '<span class="name"><strong>' + value + '</strong></span><br/><span class="cat">' + data[1] + '</span>';
			},
			onItemSelect:   function(item) {
				window.location = 'index.php?page=content&article_id=' + item.data[0] + '&mode=edit&clang=' + item.data[2];
			}
		});
	}
	
	$('#besearch-category-id').change(function(){this.form.submit();});
});



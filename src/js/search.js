/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

var searchInput = $("#search-keyword");
var searchButton = $("#search-button");

searchButton.click(function(e) {
	var keyword = searchInput.val();
	if (keyword.length > 0) {
		window.location.href = "/keywords/"+keyword;
	}
});

searchInput.keydown(function(e){
  if (e.keyCode == 13) {
    searchButton.click();
  }
});

searchInput.typeahead({ autoSelect:false });
searchInput.keyup(function(e) {
  if (e.keyCode == 38 || e.keyCode == 40) return;
  var keyword = searchInput.val();
  if (keyword.length > 0) {
    $.get( "/response.instantsearch.php", { keyword: keyword } ).done(function( data ) {
      if (data.length > 2) {
        searchInput.data('typeahead').source = jQuery.parseJSON(data);
        searchInput.data('typeahead').lookup();
      }});
  }
});
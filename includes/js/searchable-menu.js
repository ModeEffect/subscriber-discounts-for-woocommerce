jQuery(document).ready(function () {
	jQuery("select.sdwoo-searchable").chosen({
		disable_search_threshold: 1,
		allow_single_deselect: true,
		disable_search: false,
		placeholder_text_multiple: "Select...",
		no_results_text: "Oops, nothing found!",
		width: "100%"
	})
});
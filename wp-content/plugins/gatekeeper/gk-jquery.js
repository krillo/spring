jQuery(document).ready(function() {
	jQuery("#gk-whitelist-table").hide();
	jQuery("#gk-blacklist-table").hide();
});

jQuery("#gk-whitelist-title").click(function() {
	jQuery("#gk-whitelist-table").toggle('slow');
	jQuery("#gk-blacklist-table").hide('slow');
});

jQuery("#gk-blacklist-title").click(function() {
	jQuery("#gk-blacklist-table").toggle('slow');
	jQuery("#gk-whitelist-table").hide('slow');
});


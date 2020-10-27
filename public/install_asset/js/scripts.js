(function($) {
    "use strict";
	
	$('#next-button').attr('disabled', true);

	$('#hostname, #username, #database').keyup(function() {
		inputCheck();
	});
	
	$(".select2").select2();
	
})(jQuery);

function inputCheck() {
	hostname = $('#hostname').val();
	username = $('#username').val();
	database = $('#database').val();

	if (hostname != '' && username != '' && database != '') {
		$('#next-button').attr('disabled', false);
	} else {
		$('#next-button').attr('disabled', true);
	}
}
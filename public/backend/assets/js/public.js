(function($) {
    "use strict";

	//Print Command
	$(document).on('click','.print',function(){
		$("#preloader").css("display","block");
		var div = "#"+$(this).data("print");	
		$(div).print({
			timeout: 1000,
		});		
	});

	$(document).on('click','#close_alert',function(){
		$("#main_alert").fadeOut();
	});

})(jQuery);	
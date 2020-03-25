(function ($) {

	var popup = $('.popup');

	if( ! popup.length) return;

	var popupID = popup.data('popup-id'),
		popupDelay = popup.data('delay'),
		popupDismiss = popup.data('dismiss') + 'Storage';

	function activatePopup() {
		$('body').addClass('popup-active');
	}

	if(! eval(popupDismiss).getItem('popup_' + popupID + '_dismissed')) {
		if(popupDismiss === 0) {
			activatePopup();
		} else {
			setTimeout(activatePopup, popupDelay);
		}
	}

	popup.find('button').click(function(){
		console.log('popup_' + popupID + '_dismissed');
		$('body').removeClass('popup-active');
		eval(popupDismiss).setItem('popup_' + popupID + '_dismissed', 1);
	});

})(jQuery);
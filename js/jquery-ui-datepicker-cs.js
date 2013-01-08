/* Czech initialisation for the jQuery UI date picker plugin. */
/* Written by Tomas Muller (tomas@tomas-muller.net). */

;(function ($) {

	$.datepicker.regional['cs'] = {
		closeText: 'Zrušit',
		prevText: '« Předchozí',
		nextText: 'Následující »',
		currentText: 'Dnešek',
		monthNames: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen',
			'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
		monthNamesShort: ['led', 'úno', 'bře', 'dub', 'kvě', 'čer',
			'čvc', 'srp', 'zář', 'říj', 'lis', 'pro'],
		dayNames: ['neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota'],
		dayNamesShort: ['ne', 'po', 'út', 'st', 'čt', 'pá', 'so'],
		dayNamesMin: ['ne', 'po', 'út', 'st', 'čt', 'pá', 'so'],
		dateFormat: 'd. m. yy',
		firstDay: 1,
		isRTL: false,
		showButtonPanel: true
	};

	$.datepicker.setDefaults( $.datepicker.regional['cs'] );

})(jQuery);

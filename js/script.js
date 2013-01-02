$(function () {

	$.nette.ext('flashes', {
		init: function () {
			var snippets;
			if (!(snippets = this.ext('snippets'))) return;

			snippets.after(function (el) {
				el.find('.alert.hidable').prepend( $('<button type="button" class="close" data-dismiss="alert">&times;</button>') );
			});
		}
	});


	$.nette.init();

});

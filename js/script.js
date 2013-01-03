$(function () {

	var flashes = function (el) {
		el.find('.alert.hidable').prepend( $('<button type="button" class="close" data-dismiss="alert">&times;</button>') );
	};

	flashes( $('body') );


	$.nette.ext('flashes', {
		init: function () {
			var snippets;
			if (!(snippets = this.ext('snippets'))) return;

			snippets.after(function (el) {
				flashes( el );
			});
		}
	});


	var queries = function (queries) {
		$('#queries').html('')
			.append( '<h3>Queries</h3>' )
			.append( queries );
	};

	if (typeof g_Queries !== 'undefined') { queries( g_Queries ); }


	$.nette.ext('queries', {
		success: function (payload) {
			if (payload.queries) {
				queries( payload.queries );
			}
		}
	});


	$.nette.init();

});

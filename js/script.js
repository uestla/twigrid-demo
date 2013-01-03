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
			.append( $('<h3>').append( $('<a>', {
				text: 'Queries (' + queries.length + ')',
				href: '#n',
				click: function (event) {
					event.preventDefault();
					$(this).parent().next().slideToggle( 256, function () {
						$.cookie('show_queries', $(this).is(':visible') ? true : null)
					} );
				}
			}) ) )
			.append( $('<div class="list">').css('display', $.cookie('show_queries') ? 'block' : 'none').html( queries ) );
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

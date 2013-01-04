$(function () {

	var flashes, datepickers;

	(flashes = function (parent) {
		parent.find('.alert.hidable').prepend( $('<button type="button" class="close" data-dismiss="alert">&times;</button>') );
	})( $('body') );


	(datepickers = function (parent) {
		parent.find('input.date').datepicker({
			format: 'yyyy-mm-dd',
			weekStart: 1
		}).on('show', function (event) {
			var el = $(event.target);
			el.attr('value') === '' && ( el.attr( 'value', el.hasClass('min') ? '1950-05-02' : '2000-01-01' ) );
		});
	})( $('body') );


	$.nette.ext('bind-events', {
		init: function () {
			var snippets;
			if (!(snippets = this.ext('snippets'))) return;

			snippets.after(function (el) {
				flashes( el );
				datepickers( el );
			});
		}
	});


	var queries = function (queries) {
		$('#queries').html('')
			.append( $('<h3>').append( $('<a>', {
				text: 'SQL dotazy (' + queries.length + ')',
				title: 'Rozbalit/sbalit příkazy',
				href: '#n',
				click: function (event) {
					event.preventDefault();
					$(this).parent().next().slideToggle( 256, function () {
						$.cookie('show_queries', $(this).is(':visible') ? true : null);
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

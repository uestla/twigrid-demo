$(function () {

	var flashes, datepickers;

	(flashes = function (parent) {
		parent.find('.alert.hidable').prepend( $('<button type="button" class="close" data-dismiss="alert">&times;</button>') );
	})( $('body') );


	(datepickers = function (parent) {
		parent.find('input.date').datepicker({
			dateFormat: 'dd. mm. yy'
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
			.append( $('<h3>', {
				text: 'SQL dotazy (' + queries.length + ')',
				title: 'Rozbalit/sbalit příkazy',
				href: '#n',
				click: function (event) {
					event.preventDefault();
				}
			}) )
			.append( $('<div class="list">').html( queries ) );
	};

	if (typeof g_Queries !== 'undefined') { queries( g_Queries ); }


	$.nette.ext('queries', {
		success: function (payload) {
			if (payload.queries) {
				queries( payload.queries );
			}
		}
	});


	$.nette.ext('spinner', {
		init: function () {
			$('body').append('<style>.twigrid-loading * { cursor: wait !important; }</style>');
		},

		before: function () {
			$('body').addClass('twigrid-loading');
		},

		complete: function () {
			$('body').removeClass('twigrid-loading');
		}
	});


	$.nette.init();

});

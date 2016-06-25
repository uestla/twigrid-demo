$(function () {

	// === sources highlighting ====================

	hljs.initHighlightingOnLoad();


	// === bootstrap tooltips ====================

	$.nette.ext({
		load: function () {
			$('[rel="tooltip"]').remove();

			$('[title]').each(function () {
				$(this).tooltip({
					animation: false,
					container: 'body'
				});
			});
		}
	});


	var flashes, datepickers;


	// === flash messages hiding ====================

	(flashes = function (parent) {
		parent.find('.alert.hidable')
			.prepend($('<button type="button" class="close" data-dismiss="alert">&times;</button>'));

	})($('body'));


	// === datepicker ====================

	(datepickers = function (parent) {
		parent.find('input.date').datepicker({
			autoclose: true,
			format: 'dd. mm. yyyy'

		}).on('show', function (event) {
			var el = $(event.target);
			el.attr('value') === '' && (el.attr('value', el.hasClass('min') ? '1950-05-02' : '2000-01-01'));
		});

	})($('body'));


	$.nette.ext('bind-events', {
		init: function () {
			var snippets;
			if (!(snippets = this.ext('snippets'))) return;

			snippets.after(function (el) {
				flashes(el);
				datepickers(el);
			});
		}
	});


	// === showing SQL queries mechanism ====================

	var queries = function (queries) {
		$('#queries').html('')
			.append($('<h3>', {
				text: 'SQL queries (' + queries.length + ')'
			}))
			.append(queries);
	};

	if (typeof g_Queries !== 'undefined') { queries(g_Queries); }


	$.nette.ext('queries', {
		success: function (payload) {
			if (payload.queries) {
				queries(payload.queries);
			}
		}
	});


	// === AJAX "spinner" (sand-clock cursor) ====================

	$.nette.ext('spinner', {
		init: function () {
			$('body').append('<style>.ajax-loading * { cursor: wait !important; }</style>');
		},

		before: function () {
			$('html').addClass('ajax-loading');
		},

		complete: function () {
			$('html').removeClass('ajax-loading');
		}
	});


	$.nette.init();

});

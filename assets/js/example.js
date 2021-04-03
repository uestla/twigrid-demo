
// this is a example-specific script that extends basic TwiGrid functionality for example purposes

;(function (window, $) {

	$(function () {

		// === sources highlighting ====================

		hljs.highlightAll();


		// === datepicker ====================

		$.nette.ext({
			load: function () {
				$('input.date').datepicker('destroy').datepicker({
					autoclose: true,
					format: 'dd. mm. yyyy'

				}).on('show', function (event) {
					var el = $(event.target);
					el.attr('value') === '' && (el.attr('value', el.hasClass('min') ? '1950-05-02' : '2000-01-01'));
				});
			}
		});


		// === showing SQL queries ====================

		if (typeof g_Queries !== 'undefined') {
			var queries = function (queries) {
				$('#queries').html(
					$('<h3 />', {
						text: 'SQL queries (' + queries.length + ')'
					})
				).append(queries);
			};

			$.nette.ext({
				load: function () {
					queries(g_Queries);
				},

				success: function (payload) {
					if (payload.queries) {
						queries(payload.queries);
					}
				}
			});
		}


		// === AJAX "spinner" (sand-clock cursor) ====================

		$.nette.ext({
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

})(window, window.jQuery);

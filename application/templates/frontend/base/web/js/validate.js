(function ($) {

	$.fn.validate = function (opt) {

		this.each(function (i) {

			var DOM = {},
				state = {},
				$self = $(this);

			// options
			if (!opt) {
				opt = {};
			}
			opt = $.extend({
			}, opt);

			// methods
			var plg = {
				init: function () {
					DOM.$fields = $self.find('[data-validate]');
					$self.find('input[type="submit"]').on('click', plg.submit);
					DOM.$fields.on('focus', function () {
						plg.removeLabel( $(this) );
					})
				},

				test: function (data, type) {

					switch (type) {
						case 'name':
							return /^[а-яіїєґёА-ЯІЇЄҐЁa-zA-Z\-]+\s{0,1}[а-яіїєґёА-ЯІЇЄҐЁa-zA-Z\-]{0,}$/.test(data);
						case 'phone':
							return /^[\(\)0-9\-\s\+]{6,}/.test(data);
						case 'phone-default':
							if (data === null || data === "null" || data === "false" || data === "0" || data === "undefined" ||data === "" ){
								return true;
							} else{
								return /^[\(\)0-9\-\s\+]{6,}/.test(data);
							}
						case 'email':
							return /^[0-9a-zA-Z._-]+@[0-9a-zA-Z_-]+\.[a-zA-Z._-]+/.test(data);
						case 'email-default':
							if (data === null || data === "null" || data === "false" || data === "0" || data === "undefined" ||data === "" ){
								return true;
							} else{
								return /^[0-9a-zA-Z._-]+@[0-9a-zA-Z_-]+\.[a-zA-Z._-]+/.test(data);
							}
						case 'number':
							return /^[0-9]/.test(data);
						case 'zip_code':
							return /^[0-9]{5,10}/.test(data);
						case 'cart':
							return /^[0-9]{16,}/.test(data);
						case 'number-default':
							if (data === null || data === "null" || data === "false" || data === "0" || data === "undefined" ||data === "" ){
								return true;
							} else{
								return /^[0-9]/.test(data);
							}
						case 'default':
							return /^[а-яіїєґёА-ЯІЇЄҐЁa-zA-Z0-9]+/.test(data);
						case 'select':
							return !(data === null || data === "null" || data === "false" || data === "0" || data === "undefined");
						case 'password':
							return /^[a-zA-Z0-9]{6}/.test(data);
						case 'password-default':
							if (data === null || data === "null" || data === "false" || data === "0" || data === "undefined" ||data === "" ){
								return true;
							} else{
								return /^[a-zA-Z0-9]{6}/.test(data);
							}
						case 'empty':
							if (data === null || data === "null" || data === "false" || data === "0" || data === "undefined" ||data === "" ){
								return true;
							} else {
								return /^[а-яіїєґёА-ЯІЇЄҐЁa-zA-Z0-9]+/.test(data);
							}	

						//case 'radio':
							//return $('input[type="radio"]:checked').length==0;
							//return $('input[type="radio"]:checked').val();
						case 'quantity':
							return /^[1-9]/.test(data);
						default:
							return true;
					}
				},
				addLabel: function ($el) {
					$el.parent().addClass('error');
				},
				removeLabel: function ($el) {
					$el.parent().removeClass('error');
				},
				validate: function ($el) {
					if ( plg.test( $el.val(), $el.attr('data-validate') ) ) {
						plg.removeLabel( $el );
					} else {
						plg.addLabel( $el );
						state.errors++;
					}
				},
				validateConfirm: function ($el) {

					var confirmData = $el.attr('data-confirm');
					var elVal = $el.val();
					var elConfirmVal = $el.closest('form').find('[name="'+ confirmData+'"]').val();

					if ( elVal == elConfirmVal) {
						plg.removeLabel( $el );
					} else {
						plg.addLabel( $el );
						state.errors++;
					}
				},

				submit: function (e) {

					state.errors = 0;
					$self.find('[data-validate]').each( function () {
						plg.validate( $(this) );
					} );
					$self.find('[data-confirm]').each( function () {
						plg.validateConfirm( $(this) );
					} );


					if (state.errors) {
						e.preventDefault();
					} else {
						$self.trigger('submit');
					}
				}
			};

			plg.init();

			return plg;
		});
	};

})(jQuery);
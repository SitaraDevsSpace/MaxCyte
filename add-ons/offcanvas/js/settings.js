(function($){
	var nodeId = '';
	FLBuilder.registerModuleHelper('pp-offcanvas-content', {

		rules: {
			item_spacing: {
				required: true,
				number: true
			}
		},

		_templates: {
			module: '',
			row: '',
			layout: ''
		},

		init: function() {
			nodeId = $( '.fl-builder-settings' ).data( 'node' );
			$( 'select[name="content_type"]' ).on( 'change', $.proxy( this._contentTypeChange, this ) );
			this._contentTypeChange();
		},

		_contentTypeChange: function()
		{
			var type = $( 'select[name="content_type"]' ).val();

			if ( 'module' === type ) {
				this._setTemplates('module');
			}
			if ( 'row' === type ) {
				this._setTemplates('row');
			}
			if ( 'layout' === type ) {
				this._setTemplates('layout');
			}
		},

		_getTemplates: function(type, callback)
		{
			if ( 'undefined' === typeof type ) {
				return;
			}

			if ( 'undefined' === typeof callback ) {
				return;
			}

			$.post(
				bb_powerpack.ajaxurl,
				{
					action: 'pp_get_saved_templates',
					type: type
				},
				function( response ) {
					callback(response);
				}
			);
		},

		_setTemplates: function(type)
		{
			var form = $('.fl-builder-settings'),
				select = form.find( 'select[name="content_' + type + '"]' ),
				value = '',
				self = this;

			value = FLBuilderSettingsForms.config.settings['content_' + type];

			if ( this._templates[type] !== '' ) {
				select.html( this._templates[type] );
				select.find( 'option[value="' + value + '"]').attr('selected', 'selected');

				return;
			}

			this._getTemplates(type, function(data) {
				var response = JSON.parse( data );

				if ( response.success ) {
					self._templates[type] = response.data;
					select.html( response.data );
					if ( '' !== value ) {
						select.find( 'option[value="' + value + '"]').attr('selected', 'selected');
					}
				}
			});
		}
	});

	FLBuilder.registerModuleHelper('pp_content_form', {
		init: function() {
			$('.pp-module-id-class').html( '<strong>pp-offcanvas-' + nodeId + '-close</strong>' );
		}
	});

})(jQuery);

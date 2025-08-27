
(function ($) {
	PPSlidingMenus = function (settings) {
		this.settings = settings;
		this.nodeClass = '.fl-node-' + settings.id;
		this.linknav = settings.linknav == 'no' ? true : false;
		this.target = this.nodeClass + ' .pp-slide-menu__menu';
		this.backText = settings.backtext;
		this._initMenu();
	};

	PPSlidingMenus.prototype = {
		settings	: {},
		nodeClass	: '',
		linknav		: true,
		backText	: '',

		_initMenu: function ()
		{
			var self = this,
			link = $(this.target).find('li.pp-slide-menu-item-has-children > a'),
			submenu = link.next('.pp-slide-menu-sub-menu'),
			arrow = link.prev('.pp-slide-menu-arrow'),
			trigger = ( this.linknav ) ? link.add( arrow ) : arrow,
			back = $('<li class="menu-item pp-slide-menu-item pp-slide-menu-back"><span class="pp-slide-menu-arrow"><i class="fa fa-angle-left"></i></span><a href="#" class="pp-slide-menu-item-link pp-menu-sub-item-back">' + this.backText + '</a></li>');

			trigger.on( 'click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				submenu = $(this).parent().find('.pp-slide-menu-sub-menu:first');

				submenu.addClass('pp-slide-menu-is-active');
				$(self.target).css({ height: submenu.height() });
				submenu.parents('ul').first().addClass('pp-slide-menu-is-active-parent');

			} );

			back.off('click').on( 'click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				var parent = $(this).closest('ul'),
				previous = parent.parents('ul').first();

				parent.removeClass('pp-slide-menu-is-active');
				previous.removeClass('pp-slide-menu-is-active-parent');

				$(self.target).css({ height: '' });
				$(self.target).css({ height: previous.height() });

			} );

			submenu.prepend(back);
		}
	};

})(jQuery);
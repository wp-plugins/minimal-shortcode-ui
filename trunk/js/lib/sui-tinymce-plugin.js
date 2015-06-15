/**
 * The SUI TinyMCE plugin that will add a button with an icon
 * to the TinyMCE WYSIWIG editor. This button will open up
 * the Shortcode UI dialog.
 *
 * @copyright Copyright (c) Dutchwise V.O.F. (http://dutchwise.nl)
 * @author Max van Holten (<max@dutchwise.nl>)
 * @license http://www.opensource.org/licenses/mit-license.php MIT license
 */
(function() {
	/**
	 * Translated strings.
	 *
	 * @var object
	 */
	var i18n = {};
	
	if(window.sui && window.sui.i18n) {
		i18n = window.sui.i18n['lib/sui-tinymce-plugin'] || {};
	}

	tinymce.create('tinymce.plugins.Sui', {
		
		/**
		 * Will hold the modal dialog instance
		 * as soon as it's first needed.
		 * 
		 * @var sui.Dialog
		 */
		dialog: undefined,
		
		/**
		 * The shortcode UI editor button click event
		 * handler that will open up the UI dialog
		 * where the user can select and configure
		 * predefined shortcodes.
		 * 
		 * @param {tinymce.plugins.Sui} ctx
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @context window
		 * @return void
		 */
		cmdOpenShortcodeUI: function(ctx, ed) {
			if(!ctx.dialog) {
				ctx.dialog = new window.sui.Dialog();
			}
			
			var promise = ctx.dialog.open();
			
			promise.done(function(tag, values, content, enclosing) {
				var options = { tag: tag, type: 'single', content: content, attrs: values },
					shortcode;
					
				if(enclosing) {
					if(content) {
						options.type = 'closed';
					}
					else {
						options.type = 'self-closing';
					}
				}
				
				shortcode = new wp.shortcode(options);
				
				ed.execCommand('mceInsertContent', 0, shortcode.string());
				return void null;
			});
			
			return void null;
		},
		
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization
		 * so use the onInit event of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 * @return void
		 */
		init: function(ed, url) {
			ed.addButton('shortcode-ui', {
				label: i18n['button-label'],
                title: i18n['button-title'],
				tooltip: i18n['button-tooltip'],
                cmd: 'open-shortcode-ui',
				image: false,
                image : url + '/../../img/tinymce-button-sui-icon.png'
            });
			
			var ctx = this;
			
			ed.addCommand('open-shortcode-ui', function() {
				return ctx.cmdOpenShortcodeUI(ctx, ed);
			});
			
			return void null;
		},
		
		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo: function() {
			return {
				longname : 'Shortcode UI',
				author : 'Dutchwise',
				authorurl : 'http://www.dutchwise.nl/',
				infourl : 'http://www.dutchwise.nl/',
				version : "1.0"
			};
		}
	});
	
	// Register plugin
	tinymce.PluginManager.add('sui', tinymce.plugins.Sui);
	return void null;
})();
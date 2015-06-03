if(!window.sui) {
	window.sui = {};
}

/**
 * The SUI dialog that will allow the
 * user to select and configure a shortcode
 * to add to the editor.
 *
 * @copyright Copyright (c) Dutchwise V.O.F. (http://dutchwise.nl)
 * @author Max van Holten (<max@dutchwise.nl>)
 * @license http://www.opensource.org/licenses/mit-license.php MIT license
 */
window.sui.Dialog = function() {
	'use strict';
	
	/**
	 * Self reference.
	 *
	 * @var sui.Dialog
	 */
	var Dialog = this;
	
	/**
	 * Translated strings.
	 *
	 * @var object
	 */
	var i18n = {};
	
	/**
	 * The dialog container element.
	 *
	 * @var jQuery
	 */
	this.elem = jQuery('<div />');
	
	/**
	 * The shortcode selection area wrapper.
	 *
	 * @var jQuery
	 */
	this.elem_select = jQuery('<div />', { 'id': 'sui-select-wrapper' });
	
	/**
	 * The shortcode input field form wrapper.
	 *
	 * @var jQuery
	 */
	this.elem_form = jQuery('<div />', { 'id': 'sui-form-wrapper' });
	
	/**
	 * Shortcode attribute schema data.
	 *
	 * @var object
	 */
	this.shortcodes = {};
	
	/**
	 * Will contain a deferred object
	 * when dialog is shown.
	 *
	 * @var jQuery.Deferred
	 */
	this.deferred = undefined;
	
	/**
	 * The dialog cancel button event handler.
	 *
	 * @param object event
	 * @context HTMLElement
	 * @return void
	 */
	this.onDialogButtonCancel = function(event) {
		Dialog.reset();
		Dialog.elem.dialog('close');
		return void null;
	};
	
	/**
	 * Will return the shortcode tagname of the
	 * user selected shortcode.
	 *
	 * @return string
	 */
	this.getSelectedShortcodeTagName = function() {
		return this.elem_select.find('select').val();
	};
	
	/**
	 * Gets the currently selected shortcode object.
	 *
	 * @return object|null
	 */
	this.getSelectedShortcode = function() {
		var tagName = this.getSelectedShortcodeTagName(),
			shortcode = null;
		
		if(this.shortcodes[tagName]) {
			shortcode = this.shortcodes[tagName];
		}
		
		return shortcode;
	};
	
	/**
	 * Will return an array with a object containing 
	 * all the shortcode attributes and user filled-in values.
	 * And a string containing the shortcode content.
	 *
	 * @return array [object, string]
	 */
	this.getShortcodeInputValues = function() {
		var fields = this.elem_form.find('input,textarea,select'),
			values = {},
			content = '';
		
		fields.each(function(index, element) {
			if(jQuery(element).attr('type') == 'checkbox') {
				values[element.name] = jQuery(element).is(':checked') ? jQuery(element).val() : '0';
			}
			else if(element.name == '_content') {
				content = jQuery(element).val();
			}
			else {
				values[element.name] = jQuery(element).val();
			}
			
			return void null;
		});
		
		return [values, content];
	};
	
	/**
	 * The dialog add shortcode button event handler.
	 *
	 * @param object event
	 * @context HTMLElement
	 * @return void
	 */
	this.onDialogButtonInsert = function(event) {
		var shortcode = Dialog.getSelectedShortcode(),
			values = Dialog.getShortcodeInputValues();
		
		if(shortcode && values) {
			Dialog.deferred.resolveWith(Dialog, [shortcode.tagName, values[0], values[1], shortcode.enclosed]);
		}
		else {
			Dialog.deferred.rejectWith(Dialog, [null, null, null, null]);
		}
		
		Dialog.close();
		return void null;
	};
	
	/**
	 * Resets the state of the dialog and its fields.
	 * The pending promise/deferred will be rejected.
	 *
	 * @return void
	 */
	this.reset = function() {
		if(this.deferred && this.deferred.state() == 'pending') {
			this.deferred.rejectWith(this, [null, null, null, null]);
		}
		
		this.elem_select.find('select').val('');
		this.elem_form.empty();
		this.deferred = null;
		return void null;
	};
	
	/**
	 * Opens the dialog returning a promise object
	 * that will provide the result data when the user
	 * wishes to add a shortcode.
	 *
	 * @return jQuery.Promise
	 */
	this.open = function() {
		this.deferred = new jQuery.Deferred();
		this.elem.dialog('open');
		
		return this.deferred.promise();
	};
	
	/**
	 * Closes and resets the dialog.
	 *
	 * @return void
	 */
	this.close = function() {
		this.elem.dialog('close');
		return void null;
	};
	
	/**
	 * Dialog close event handler, make sure the
	 * dialog and fields get reset.
	 *
	 * @param object event
	 * @param object ui
	 * @context HTMLElement
	 * @return void
	 */
	this.onDialogClose = function(event, ui) {
		Dialog.reset();
		return void null;
	};
	
	
	/**
	 * Renders the shortcode selection area.
	 *
	 * @return string
	 */
	this.renderShortcodeSelectField = function() {
		var html = '',
			template,
			i18n_template = {};
		
		// check if templates are present
		if(!window.sui.templates) {
			return html;
		}
		
		// lookup i18n strings
		if(window.sui && window.sui.i18n) {
			i18n_template = window.sui.i18n['templates/sui-select'] || {};
		}
		
		// parse template
		template = _.template(window.sui.templates['sui-select']);
		
		// render template
		return template.call(this, { 'data': this.shortcodes, 'i18n': i18n_template });
	};
	
	/**
	 * Renders the shortcode input form fields depending
	 * on the provided shortcode, which should be available
	 * in the this.shortcodes object.
	 *
	 * @param string shortcode
	 * @return string
	 */
	this.renderShortcodeFormFields = function(shortcode) {
		var html = '',
			template,
			shortcode,
			label,
			i18n_template;
		
		// check if templates and the chosen shortcode are present
		if(!window.sui.templates || !this.shortcodes[shortcode]) {
			return html;
		}
		
		// lookup i18n strings
		if(window.sui && window.sui.i18n) {
			i18n_template = window.sui.i18n['templates/sui-form'] || {};
		}
		
		// parse template
		template = _.template(window.sui.templates['sui-form'])
		
		// lookup template data
		shortcode = this.shortcodes[shortcode];
		
		// creates a new property in the attribute schemas: the form label
		jQuery.each(shortcode.schema, function(field, schema) {
			label = schema.name.replace(/_/g, ' ');
			label = label.charAt(0).toUpperCase() + label.slice(1);
			schema.label = label;
			return void null;
		});
		
		// render template
		return template.call(this, { 'data': shortcode, 'i18n': i18n_template });
	};
	
	/**
	 * Attempts to fix dialog height related issues by
	 * temporarily assigning a fixed height to the dialog
	 * and afterwards allowing the height to automatically
	 * be set again.
	 *
	 * @return void
	 */
	this.resetDialogHeight = function() {
		this.elem.dialog('option', 'height', '100');
		this.elem.dialog('option', 'height', 'auto');
		return void null;
	};
	
	/**
	 * Shortcode <select /> 'change' event handler.
	 *
	 * @param object event
	 * @context HTMLElement
	 * @return void
	 */
	this.onChangeShortcode = function(event) {
		var value = jQuery(this).val(),
			fields = Dialog.renderShortcodeFormFields(value);
		
		Dialog.elem_form.html(fields);
		Dialog.resetDialogHeight();
		return void null;
	};
	
	/**
	 * Image field button handler.
	 *
	 * @param object event
	 * @context HTMLElement
	 * @return void
	 */
	this.onClickImageField = function(event) {
		var anchor = jQuery(this),
			input = anchor.next(),
			name = input.attr('name'),
			frame = Dialog._frame;
			
		// make sure the hidden input has a unique name
		if(!name) {
			return false;
		}
		
		// make sure the frame property exists on the dialog
		if(!frame) {
			Dialog._frame = frame = {};
		}
		
		// check if the frame already exists for this field
		if(!frame[name]) {
			// Create a new WP Media frame for this specific field
			Dialog._frame[name] = frame = wp.media({
				title: i18n['media-dialog-title'],
				button: {
					text: i18n['media-dialog-button']
				},
				multiple: false
			});
		}
		else {
			frame = frame[name];
		}
		
		// When an image is selected in the media frame...
		frame.on('select', function() {
			// Get media attachment details from the frame state
			var attachment = frame.state().get('selection').first().toJSON(),
				img = jQuery(new Image, { alt: i18n['field-image-alt'] });
			
			// reset background color onload
			img[0].onload = function() { 
				return img.css('background-color', 'transparent');
			};
			
			// img styles
			img.css({
				'display': 'block',
				'max-width': '50%',
				'min-width': '100px',
				'min-height': '25px',
				'line-height': '25px',
				'background-color': '#eee',
				'text-align': 'center'
			});
			
			// set img url
			img[0].src = attachment.url; 
			
			// set the shortcode attribute's hidden input value
			input.val(attachment.id);
			
			// convert anchor from button to an image
			anchor.removeClass('button');
			anchor.css({'display': 'inline-block', 'width': '100%'});
			anchor.html(img);
			
			Dialog.resetDialogHeight();
			return void null;
		});

		// Finally, open the modal on click
		frame.open();
		return false;
	};
	
	/**
	 * The unofficial class constructor that will
	 * create the jQuery UI dialog and append the
	 * container to the page.
	 *
	 * @return this
	 */
	this.construct = function() {
		// check if shortcode data is present
		if(window.sui.shortcodes) {
			this.shortcodes = window.sui.shortcodes;
		}
		
		// load i18n strings
		if(window.sui.i18n) {
			i18n = window.sui.i18n['lib/sui-modal-dialog'];
		}
		
		// shortcode select field rendering and event binding
		this.elem_select.append(this.renderShortcodeSelectField());
		this.elem_select.on('change', 'select', this.onChangeShortcode);
		
		// image field click event binding (will open up media dialog)
		this.elem_form.on('click', 'a.image', this.onClickImageField);
		
		// append wrapper elements
		this.elem.append(this.elem_select);
		this.elem.append(this.elem_form);
		
		// create dialog buttons
		var buttons = {};
		
		buttons[i18n['dialog-cancel']] = this.onDialogButtonCancel;
		buttons[i18n['dialog-insert']] = this.onDialogButtonInsert;
		
		// initialise modal dialog
		this.elem.dialog({
			title: i18n['dialog-title'],
			autoOpen: false,
			height: 'auto',
        	width: 380,
        	modal: true,
        	buttons: buttons
		});
		
		// dialog close event binding
		this.elem.on('dialogclose', this.onDialogClose);
		
		return this;
	};
	
	this.construct();	
	return void null;
};
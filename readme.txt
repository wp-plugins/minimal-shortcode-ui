=== Plugin Name ===
Contributors: maxdw
Donate link: http://dutchwise.nl/
Tags: shortcodes, developers
Requires at least: 4.2
Tested up to: 4.2.2
Stable tag: 1.0.0
License: MIT
License URI: http://www.opensource.org/licenses/mit-license.php

Allows theme/plugin developers to make their custom shortcodes easily accessible to their users through a modal dialog from the content editor.

== Description ==

The minimum code required that allows the user configure and add an shortcode to the page contents using a popup form that is accessible through the TinyMCE content editor. Suitable for developers that wish to provide their users with an easy way of adding your custom shortcodes. The plugin provides an abstract shortcode class that can be extended and registered into the shortcode factory. Which will result in the shortcodes showing up in the dropdown menu.

== Installation ==

1. Download ZIP file
2. Unzip contents to the /wp-content/plugins/ directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. If developing shortcodes for your theme or plugin, have a look at example.php

== Frequently Asked Questions ==

= Is this plugin multilingual? =

All text is run through translation functions, but no translation files are provided out of the box.

= How do I start adding shortcodes to my content? =

When you activate the plugin a new button will be available in the 'Visual' mode of the content editor. This will open up a dialog that will allow you to select and customize available shortcodes.

= How do I add my own custom shortcodes? =

Make sure the plugin is active and have a look at example.php in the plugin's root directory.

== Screenshots ==

1. screenshot.jpg

== Changelog ==

= 1.0 =
* Initial version
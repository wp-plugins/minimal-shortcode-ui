<?php
namespace Sui;

require_once 'abstract/shortcode.php';

/**
 * Shortcode factory
 *
 * Holds a global registry of shortcode objects that extend Sui\Shortcode.
 * 
 * Use Shortcodes::register('ShortcodeYourShortcodeName') to register your shortcode.
 * Use Shortcodes::unregister('ShortcodeYourShortcodeName') to unregister registered shortcodes.
 * Use Shortcodes::loadShortcodeFiles('/yourdir') to load all .php files in that dir.
 * Use Shortcodes::clear() to unregister all registered shortcodes.
 * Use Shortcodes::registration() to get an array of Sui/Shortcode extended objects.
 *
 * Please see the Sui\Shortcode documentation for creating a registrable Shortcode object.
 *
 * @copyright Copyright (c) Dutchwise V.O.F. (http://dutchwise.nl)
 * @author Max van Holten (<max@dutchwise.nl>)
 * @license http://www.opensource.org/licenses/mit-license.php MIT license
 */
class Shortcodes {
	
	/**
	 * Contains all registered shortcodes.
	 *
	 * @var array
	 */
	protected static $_shortcodes = array();
	
	/**
	 * The directory with all shortcode class files.
	 * This path is relative to the file where this
	 * class is located.
	 *
	 * @var string
	 */
	protected static $_dir = 'shortcodes/';
	
	/**
	 * Returns the full absolute path to the directory
	 * where all shortcode class files are located.
	 *
	 * @return string
	 */
	protected static function _getDefaultShortcodeDirectory() {
		return dirname(__FILE__) . DIRECTORY_SEPARATOR . self::$_dir;
	}
	
	/**
	 * Loads all shortcode class files that should contain
	 * a static call to this class ::register function.
	 *
	 * @param string $dir (optional) Default directory may be used
	 * @return boolean
	 */
	public static function loadShortcodeFiles($dir = null) {
		if(!is_dir($dir)) {
			return false;
		}
		
		if(is_null($dir)) {
			$dir = self::_getDefaultShortcodeDirectory();
		}
		else {
			$dir = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR;
		}
		
		$query = "{$dir}*.php";
		$paths = glob($query);
		
		// load the shortcodes
		foreach($paths as $path) {
			if(is_file($path) && file_exists($path)) {
				include $path;
			}
		}
		
		return true;
	}
	
	/**
	 * Registers a shortcode with WordPress.
	 *
	 * @param string $classname
	 * @return boolean
	 */
	public static function register($classname) {		
		if(!is_subclass_of($classname, '\Sui\Shortcode')) {
			return false;
		}
		
		$shortcode = new $classname();
		$tagName = $shortcode->getTagName();
		
		add_shortcode($tagName, array($shortcode, 'render'));
		
		self::$_shortcodes[$tagName] = $shortcode;
		
		return true;
	}
	
	/**
	 * Unregisters a shortcode with WordPress.
	 *
	 * @param string $classname
	 * @return boolean
	 */
	public static function unregister($classname) {
		if(!is_subclass_of($classname, '\Sui\Shortcode')) {
			return false;
		}
		
		$shortcode = new $classname();
		$tagName = $shortcode->getTagName();
		
		remove_shortcode($tagName);
		
		unset(self::$_shortcodes[$tagName]);
		
		return true;
	}
	
	/**
	 * Unregisters all shortcodes that were added by this class.
	 *
	 * @return int
	 */
	public static function clear() {
		$count = 0;
		
		foreach(self::$_shortcodes as $shortcode) {
			if(self::unregister(get_class($shortcode))) {
				$count++;
			}
		}
		
		return $count;
	}
	
	/**
	 * Returns all registered shortcodes.
	 *
	 * @return array
	 */
	public static function registration() {
		return self::$_shortcodes;
	}
	
	/**
	 * Returns a JSON string of all registered shortcodes.
	 *
	 * @return string
	 */
	public static function json() {
		return json_encode(self::$_shortcodes);
	}
	
}
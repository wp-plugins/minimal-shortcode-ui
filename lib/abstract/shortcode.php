<?php
namespace Sui;

/**
 * Shortcode
 *
 * Used as a base class for registering Shortcodes to the Sui\Shortcodes factory:
 * 
 * - Extend and overwrite the $_enclosed property to indicate whether enclosed content is allowed.
 * - Extend and use the _setSchemaAttribute method inside the constructor to define field types.
 * - Extend and overwrite the $_attributes property to map fields to their default values.
 * - Extend and overwrite the render method and use the $atts argument to render the output.
 * 
 * (OPTIONAL) Naming convention for the shortcode's tag name:
 * 
 * - Make sure that your child class contains the string 'Shortcode'
 * - Anything before the string 'Shortcode' is omitted in the shortcode's tag name.
 * - Everything after the string 'Shortcode' needs to be written in CamelCase
 *
 * For example naming my child Shortcode class 'SuperShortcodeThisDoesSomething' will result
 * in a shortcode tag name of 'this_does_something'. Make sure you overwrite the $_tagName
 * property with a prefix relevant to your theme/plugin. Setting this prefix to e.g. 'sui'
 * will result in the shortcode tag name of 'sui_this_does_something'.
 *
 * Alternatively to the class naming convention you can overwrite the $_tagName property
 * and set the shortcode tag name manually.
 *
 * @copyright Copyright (c) Dutchwise V.O.F. (http://dutchwise.nl)
 * @author Max van Holten (<max@dutchwise.nl>)
 * @license http://www.opensource.org/licenses/mit-license.php MIT license
 */
abstract class Shortcode implements \JsonSerializable {
	
	/**
	 * Will be prepended to the shortcode
	 * its tag name followed by an underscore.
	 *
	 * If this variable is empty nothing will be
	 * prepended to the tag name.
	 *
	 * @var string
	 */
	protected $_tagNamePrefix = '';
	
	/**
	 * The shortcode's tag name may be manually set by
	 * a child class but when left empty will be
	 * determined by class name convention.
	 *
	 * @var string
	 */
	protected $_tagName = '';
	
	/**
	 * The shortcode's description.
	 *
	 * @var string
	 */
	protected $_description = '';
	
	/**
	 * Indicates whether this is an enclosing shortcode.
	 * 
	 * Set this to true to indicate that this shortcode
	 * may enclose content.
	 *
	 * @var boolean
	 */
	protected $_enclosed = false;
	
	/**
	 * Defines the shortcode attributes and default values.
	 *
	 * @var array
	 */
	protected $_attributes = array();
	
	/**
	 * Describes the shortcode attributes including name,
	 * field type, default value and other options.
	 * Defined with self::_setAttribute.
	 *
	 * @var array
	 */
	private $__schema = array();
	
	/**
	 * Inflects the current class name into a underscored slug
	 * suitable for use a the shortcodes tag name.
	 * 
	 * @return string
	 */
	protected function _inflectClassNameSlug() {
		$className = get_class($this);
		$parts = explode('Shortcode', $className);
		$shortcodeName = end($parts);
		
		$regex = '/(^|[a-z])([A-Z])/';			
		$slug = preg_replace($regex, '$1_$2', $shortcodeName);
		
		$slug = strtolower($slug);
		$slug = trim($slug, '_ ');
		
		return $slug;
	}
	
	/**
	 * Returns the attribute schema.
	 *
	 * @return array
	 */
	protected function _getSchema() {
		return $this->__schema;
	}
	
	/**
	 * Describes the provided shortcode attribute and
	 * adds it to the schema and attributes list.
	 *
	 * @param string $name
	 * @param string $value
	 * @param string $type 'text', 'textarea', 'select', 'image', 'checkbox'
	 * @param array $extra
	 * @return void
	 */
	protected function _setSchemaAttribute($name, $value = '', $type = 'text', array $extra = array()) {
		if($type == 'checkbox' && $value == '') {
			$value = '1';
		}
		
		$attribute = compact('name', 'value', 'type', 'extra');
		$this->_attributes[$name] = $value;
		$this->__schema[$name] = $attribute;
	}
	
	/**
	 * Class constructor
	 */
	public function __construct() {
		if(!$this->_tagName) {
			$this->_tagName = $this->_inflectClassNameSlug();
		}
		if($this->_tagNamePrefix) {
			$this->_tagName = $this->_tagNamePrefix . '_' . $this->_tagName;
		}
		
		// defines a default attribute schema
		foreach($this->_attributes as $attribute => $value) {
			$this->_setSchemaAttribute($attribute, $value);
		}
	}
	
	/**
	 * Returns this shortcode's tag name.
	 *
	 * @return string
	 */
	public function getTagName() {
		return $this->_tagName;
	}
	
	/**
	 * Returns whether this shortcode is an enclosement.
	 *
	 * @return boolean
	 */
	public function getEnclosed() {
		return $this->_enclosed;
	}
	
	/**
	 * Returns the attributes and their default values.
	 *
	 * @return array
	 */
	public function getAttributes() {
		return $this->_attributes;
	}
	
	/**
	 * Renders this shortcode.
	 *
	 * @param array $atts
	 * @param string|null $content
	 * @return string
	 */
	abstract public function render($atts, $content = null);
	
	/**
	 * Converts this object to an array exposing its
	 * features and attributes.
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			'tagName' => $this->_tagName,
			'description' => esc_html($this->_description),
			'enclosed' => $this->_enclosed,
			'attributes' => $this->_attributes,
			'attributes_keys' => array_keys($this->_attributes),
			'schema' => $this->__schema
		);
	}
	
	/**
     * When this object gets JSON encoded this
	 * method will make sure the right features
	 * and attributes are serialized.
	 *
	 * @interface JsonSerializable
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->toArray();
	}
	
}
<?php
namespace Jurig;
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Base class for all HTML classes
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 * 
 * @category    HTML
 * @package     HTML_Common
 * @author      Adam Daniel <adaniel1@eesus.jnj.com>
 * @copyright   2001-2009 The PHP Group
 * @license     http://www.php.net/license/3_01.txt PHP License 3.01
 * @version     CVS: $Id$
 * @link        http://pear.php.net/package/HTML_Common/
 */ 

/**
 * Base class for all HTML classes
 *
 * @category    HTML
 * @package     HTML_Common
 * @author      Adam Daniel <adaniel1@eesus.jnj.com>
 * @version     Release: @package_version@
 * @abstract
 */
class HTML_Common
{
    /**
     * Associative array of attributes
     * @var     array
     * @access  protected
     */
    protected $_attributes = array();

    /**
     * Tab offset of the tag
     * @var     int
     * @access  protected
     */
    protected $_tabOffset = 0;

    /**
     * Tab string
     * @var       string
     * @since     1.7
     * @access    protected
     */
    protected $_tab = "\11";

    /**
     * Contains the line end string
     * @var       string
     * @since     1.7
     * @access    protected
     */
    protected $_lineEnd = "\12";

    /**
     * HTML comment on the object
     * @var       string
     * @since     1.5
     * @access    protected
     */
    protected $_comment = '';

    /**
     * Class constructor
     * @param    mixed   $attributes     Associative array of table tag attributes
     *                                   or HTML attributes name="value" pairs
     * @param    int     $tabOffset      Indent offset in tabs
     * @access   public
     */
    public function __construct($attributes = null, $tabOffset = 0)
    {
        $this->setAttributes($attributes);
        $this->setTabOffset($tabOffset);
    } // end constructor

    /**
     * Returns the current API version
     * @access   public
     * @returns  double
     */
    public function apiVersion()
    {
        return 1.7;
    } // end func apiVersion

    /**
     * Returns the lineEnd
     *
     * @since     1.7
     * @access    protected
     * @return    string
     */
    protected function _getLineEnd()
    {
        return $this->_lineEnd;
    } // end func getLineEnd

    /**
     * Returns a string containing the unit for indenting HTML
     *
     * @since     1.7
     * @access    protected
     * @return    string
     */
    protected function _getTab()
    {
        return $this->_tab;
    } // end func _getTab

    /**
     * Returns a string containing the offset for the whole HTML code
     *
     * @return    string
     * @access   protected
     */
    protected function _getTabs()
    {
        return str_repeat($this->_getTab(), $this->_tabOffset);
    } // end func _getTabs

    /**
     * Returns an HTML formatted attribute string
     * @param    array   $attributes
     * @return   string
     * @access   protected
     */
    protected function _getAttrString($attributes)
    {
        $strAttr = '';

        if (is_array($attributes)) {
            $charset = HTML_Common::charset();
            foreach ($attributes as $key => $value) {
                $strAttr .= ' ' . $key . '="' . htmlspecialchars($value, ENT_COMPAT, $charset) . '"';
            }
        }
        return $strAttr;
    } // end func _getAttrString

    /**
     * Returns a valid atrributes array from either a string or array
     * @param    mixed   $attributes     Either a typical HTML attribute string or an associative array
     * @access   protected
     * @return   array
     */
    protected function _parseAttributes($attributes)
    {
        if (is_array($attributes)) {
            $ret = array();
            foreach ($attributes as $key => $value) {
                if (is_int($key)) {
                    $key = $value = strtolower($value);
                } else {
                    $key = strtolower($key);
                }
                $ret[$key] = $value;
            }
            return $ret;

        } elseif (is_string($attributes)) {
            $preg = "/(([A-Za-z_:]|[^\\x00-\\x7F])([A-Za-z0-9_:.-]|[^\\x00-\\x7F])*)" .
                "([ \\n\\t\\r]+)?(=([ \\n\\t\\r]+)?(\"[^\"]*\"|'[^']*'|[^ \\n\\t\\r]*))?/";
            if (preg_match_all($preg, $attributes, $regs)) {
                for ($counter=0; $counter<count($regs[1]); $counter++) {
                    $name  = $regs[1][$counter];
                    $check = $regs[0][$counter];
                    $value = $regs[7][$counter];
                    if (trim($name) == trim($check)) {
                        $arrAttr[strtolower(trim($name))] = strtolower(trim($name));
                    } else {
                        if (substr($value, 0, 1) == "\"" || substr($value, 0, 1) == "'") {
                            $arrAttr[strtolower(trim($name))] = substr($value, 1, -1);
                        } else {
                            $arrAttr[strtolower(trim($name))] = trim($value);
                        }
                    }
                }
                return $arrAttr;
            }
        }
    } // end func _parseAttributes

    /**
     * Returns the array key for the given non-name-value pair attribute
     *
     * @param     string    $attr         Attribute
     * @param     array     $attributes   Array of attribute
     * @since     1.0
     * @access    protected
     * @return    bool
     */
    protected function _getAttrKey($attr, $attributes)
    {
        if (isset($attributes[strtolower($attr)])) {
            return true;
        } else {
            return null;
        }
    } //end func _getAttrKey

    /**
     * Updates the attributes in $attr1 with the values in $attr2 without changing the other existing attributes
     * @param    array   $attr1      Original attributes array
     * @param    array   $attr2      New attributes array
     * @access   protected
     */
    protected function _updateAttrArray(&$attr1, $attr2)
    {
        if (!is_array($attr2)) {
            return false;
        }
        foreach ($attr2 as $key => $value) {
            $attr1[$key] = $value;
        }
    } // end func _updateAtrrArray

    /**
     * Removes the given attribute from the given array
     *
     * @param     string    $attr           Attribute name
     * @param     array     $attributes     Attribute array
     * @since     1.4
     * @access    protected
     * @return    void
     */
    protected function _removeAttr($attr, &$attributes)
    {
        $attr = strtolower($attr);
        if (isset($attributes[$attr])) {
            unset($attributes[$attr]);
        }
    } //end func _removeAttr

    /**
     * Returns the value of the given attribute
     *
     * @param     string    $attr   Attribute name
     * @since     1.5
     * @access    public
     * @return    string|null   returns null if an attribute does not exist
     */
    public function getAttribute($attr)
    {
        $attr = strtolower($attr);
        if (isset($this->_attributes[$attr])) {
            return $this->_attributes[$attr];
        }
        return null;
    } //end func getAttribute

    /**
     * Sets the value of the attribute
     *
     * @param   string  Attribute name
     * @param   string  Attribute value (will be set to $name if omitted)
     * @access  public
     */
    public function setAttribute($name, $value = null)
    {
        $name = strtolower($name);
        if (is_null($value)) {
            $value = $name;
        }
        $this->_attributes[$name] = $value;
    } // end func setAttribute

    /**
     * Sets the HTML attributes
     * @param    mixed   $attributes     Either a typical HTML attribute string or an associative array
     * @access   public
     */
    public function setAttributes($attributes)
    {
        $this->_attributes = $this->_parseAttributes($attributes);
    } // end func setAttributes

    /**
     * Returns the assoc array (default) or string of attributes
     *
     * @param     bool    Whether to return the attributes as string
     * @since     1.6
     * @access    public
     * @return    mixed   attributes
     */
    public function getAttributes($asString = false)
    {
        if ($asString) {
            return $this->_getAttrString($this->_attributes);
        } else {
            return $this->_attributes;
        }
    } //end func getAttributes

    /**
     * Updates the passed attributes without changing the other existing attributes
     * @param    mixed   $attributes     Either a typical HTML attribute string or an associative array
     * @access   public
     */
    public function updateAttributes($attributes)
    {
        $this->_updateAttrArray($this->_attributes, $this->_parseAttributes($attributes));
    } // end func updateAttributes

    /**
     * Removes an attribute
     *
     * @param     string    $attr   Attribute name
     * @since     1.4
     * @access    public
     * @return    void
     */
    public function removeAttribute($attr)
    {
        $this->_removeAttr($attr, $this->_attributes);
    } //end func removeAttribute

    /**
     * Sets the line end style to Windows, Mac, Unix or a custom string.
     *
     * @param   string  $style  "win", "mac", "unix" or custom string.
     * @since   1.7
     * @access  public
     * @return  void
     */
    public function setLineEnd($style)
    {
        switch ($style) {
            case 'win':
                $this->_lineEnd = "\15\12";
                break;
            case 'unix':
                $this->_lineEnd = "\12";
                break;
            case 'mac':
                $this->_lineEnd = "\15";
                break;
            default:
                $this->_lineEnd = $style;
        }
    } // end func setLineEnd

    /**
     * Sets the tab offset
     *
     * @param    int     $offset
     * @access   public
     */
    public function setTabOffset($offset)
    {
        $this->_tabOffset = $offset;
    } // end func setTabOffset

    /**
     * Returns the tabOffset
     *
     * @since     1.5
     * @access    public
     * @return    int
     */
    public function getTabOffset()
    {
        return $this->_tabOffset;
    } //end func getTabOffset

    /**
     * Sets the string used to indent HTML
     *
     * @since     1.7
     * @param     string    $string     String used to indent ("\11", "\t", '  ', etc.).
     * @access    public
     * @return    void
     */
    public function setTab($string)
    {
        $this->_tab = $string;
    } // end func setTab

    /**
     * Sets the HTML comment to be displayed at the beginning of the HTML string
     *
     * @param     string
     * @since     1.4
     * @access    public
     * @return    void
     */
    public function setComment($comment)
    {
        $this->_comment = $comment;
    } // end func setHtmlComment

    /**
     * Returns the HTML comment
     *
     * @since     1.5
     * @access    public
     * @return    string
     */
    public function getComment()
    {
        return $this->_comment;
    } //end func getComment

    /**
     * Abstract method.  Must be extended to return the objects HTML
     *
     * @access    public
     * @return    string
     * @abstract
     */
    public function toHtml():string
    {
        return '';
    } // end func toHtml

    /**
     * Displays the HTML to the screen
     *
     * @access    public
     */
    public function display()
    {
        print $this->toHtml();
    } // end func display

    /**
     * Sets the charset to use by htmlspecialchars() function
     *
     * Since this parameter is expected to be global, the function is designed
     * to be called statically:
     * <code>
     * HTML_Common::charset('utf-8');
     * </code>
     * or
     * <code>
     * $charset = HTML_Common::charset();
     * </code>
     *
     * @param   string  New charset to use. Omit if just getting the 
     *                  current value. Consult the htmlspecialchars() docs 
     *                  for a list of supported character sets.
     * @return  string  Current charset
     * @access  public
     * @static
     */
    public function charset($newCharset = null)
    {
        static $charset = 'ISO-8859-1';

        if (!is_null($newCharset)) {
            $charset = $newCharset;
        }
        return $charset;
    } // end func charset
} // end class HTML_Common
?>

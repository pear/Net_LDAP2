<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

require_once 'PEAR.php';

/**
* Getting the rootDSE entry of a LDAP server
*
* @category Net
* @package  Net_LDAP2
* @author   Jan Wagner <wagner@netsols.de>
* @license  http://www.gnu.org/copyleft/lesser.html LGPL
* @version  CVS $Id$
* @link     http://pear.php.net/package/Net_LDAP22/
*/
class Net_LDAP2_RootDSE extends PEAR
{
    /**
    * @access private
    * @var object Net_LDAP2_Entry
    **/
    var $_entry;

    /**
    * Class constructor
    *
    * @param Net_LDAP2_Entry &$entry Net_LDAP2_Entry object
    */
    function Net_LDAP2_RootDSE(&$entry)
    {
        $this->_entry = $entry;
    }

    /**
    * Gets the requested attribute value
    *
    * Same usuage as {@link Net_LDAP2_Entry::getValue()}
    *
    * @param string $attr    Attribute name
    * @param array  $options Array of options
    *
    * @access public
    * @return mixed Net_LDAP2_Error object or attribute values
    * @see Net_LDAP2_Entry::get_value()
    */
    function getValue($attr = '', $options = '')
    {
        return $this->_entry->get_value($attr, $options);
    }

    /**
    * Alias function of getValue() for perl-ldap interface
    *
    * @see getValue()
    */
    function get_value()
    {
        $args = func_get_args();
        return call_user_func_array(array( &$this, 'getValue' ), $args);
    }

    /**
    * Determines if the extension is supported
    *
    * @param array $oids Array of oids to check
    *
    * @access public
    * @return boolean
    */
    function supportedExtension($oids)
    {
        return $this->_checkAttr($oids, 'supportedExtension');
    }

    /**
    * Alias function of supportedExtension() for perl-ldap interface
    *
    * @see supportedExtension()
    */
    function supported_extension()
    {
        $args = func_get_args();
        return call_user_func_array(array( &$this, 'supportedExtension'), $args);
    }

    /**
    * Determines if the version is supported
    *
    * @param array $versions Versions to check
    *
    * @access public
    * @return boolean
    */
    function supportedVersion($versions)
    {
        return $this->_checkAttr($versions, 'supportedLDAPVersion');
    }

    /**
    * Alias function of supportedVersion() for perl-ldap interface
    *
    * @see supportedVersion()
    */
    function supported_version()
    {
        $args = func_get_args();
        return call_user_func_array(array(&$this, 'supportedVersion'), $args);
    }

    /**
    * Determines if the control is supported
    *
    * @param array $oids Control oids to check
    *
    * @access public
    * @return boolean
    */
    function supportedControl($oids)
    {
        return $this->_checkAttr($oids, 'supportedControl');
    }

    /**
    * Alias function of supportedControl() for perl-ldap interface
    *
    * @see supportedControl()
    */
    function supported_control()
    {
        $args = func_get_args();
        return call_user_func_array(array(&$this, 'supportedControl' ), $args);
    }

    /**
    * Determines if the sasl mechanism is supported
    *
    * @param array $mechlist SASL mechanisms to check
    *
    * @access public
    * @return boolean
    */
    function supportedSASLMechanism($mechlist)
    {
        return $this->_checkAttr($mechlist, 'supportedSASLMechanisms');
    }

    /**
    * Alias function of supportedSASLMechanism() for perl-ldap interface
    *
    * @see supportedSASLMechanism()
    */
    function supported_sasl_mechanism() 
    {
        $args = func_get_args();
        return call_user_func_array(array(&$this, 'supportedSASLMechanism'), $args);
    }

    /**
    * Checks for existance of value in attribute
    *
    * @param array  $values values to check
    * @param string $attr   attribute name
    *
    * @access private
    * @return boolean
    */
    function _checkAttr($values, $attr)
    {
        if (!is_array($values)) $values = array($values);

        foreach ($values as $value) {
            if (!@in_array($value, $this->get_value($attr, 'all'))) {
                return false;
            }
        }
        return true;
    }
}

?>
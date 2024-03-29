<?php
require_once __DIR__ . '/Net_LDAP2_TestBase.php';
require_once 'Net/LDAP2/Util.php';

/**
 * Test class for Net_LDAP2_Util.
 * Generated by PHPUnit_Util_Skeleton on 2007-10-09 at 10:33:22.
 */
class Net_LDAP2_UtilTest extends Net_LDAP2_TestBase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Net_LDAP2_UtilTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Test escape_dn_value()
     */
    public function testEscape_dn_value() {
        $dnval    = '  '.chr(22).' t,e+s"t,\\v<a>l;u#e=!    ';
        $expected = '\20\20\16 t\,e\+s\"t\,\\\\v\<a\>l\;u\#e\=!\20\20\20\20';

        // string call
        $this->assertEquals(array($expected), Net_LDAP2_Util::escape_dn_value($dnval));

        // array call
        $this->assertEquals(array($expected), Net_LDAP2_Util::escape_dn_value(array($dnval)));

        // multiple arrays
        $this->assertEquals(array($expected, $expected, $expected), Net_LDAP2_Util::escape_dn_value(array($dnval,$dnval,$dnval)));
    }

    /**
     * Test unescape_dn_value()
     */
    public function testUnescape_dn_value() {
        $dnval    = '\\20\\20\\16\\20t\\,e\\+s \\"t\\,\\\\v\\<a\\>l\\;u\\#e\\=!\\20\\20\\20\\20';
        $expected = '  '.chr(22).' t,e+s "t,\\v<a>l;u#e=!    ';

        // string call
        $this->assertEquals(array($expected), Net_LDAP2_Util::unescape_dn_value($dnval));

        // array call
        $this->assertEquals(array($expected), Net_LDAP2_Util::unescape_dn_value(array($dnval)));

        // multiple arrays
        $this->assertEquals(array($expected, $expected, $expected), Net_LDAP2_Util::unescape_dn_value(array($dnval,$dnval,$dnval)));
    }

    /**
     * Test escaping of filter values
     */
    public function testEscape_filter_value() {
        $expected  = 't\28e,s\29t\2av\5cal\1eue';
        $filterval = 't(e,s)t*v\\al'.chr(30).'ue';

        // string call
        $this->assertEquals(array($expected), Net_LDAP2_Util::escape_filter_value($filterval));

        // array call
        $this->assertEquals(array($expected), Net_LDAP2_Util::escape_filter_value(array($filterval)));

        // multiple arrays
        $this->assertEquals(array($expected, $expected, $expected), Net_LDAP2_Util::escape_filter_value(array($filterval,$filterval,$filterval)));
    }

    /**
     * Test unescaping of filter values
     */
    public function testUnescape_filter_value() {
        $expected  = 't(e,s)t*v\\al'.chr(30).'ue';
        $filterval = 't\28e,s\29t\2av\5cal\1eue';

        // string call
        $this->assertEquals(array($expected), Net_LDAP2_Util::unescape_filter_value($filterval));

        // array call
        $this->assertEquals(array($expected), Net_LDAP2_Util::unescape_filter_value(array($filterval)));

        // multiple arrays
        $this->assertEquals(array($expected, $expected, $expected), Net_LDAP2_Util::unescape_filter_value(array($filterval,$filterval,$filterval)));
    }

    /**
     * Test asc2hex32()
     */
    public function testAsc2hex32() {

        $expected = '\00\01\02\03\04\05\06\07\08\09\0a\0b\0c\0d\0e\0f\10\11\12\13\14\15\16\17\18\19\1a\1b\1c\1d\1e\1f !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~';
        $str = '';
        for ($i = 0; $i < 127; $i++) {
             $str .= chr($i);
        }
        $this->assertEquals($expected, Net_LDAP2_Util::asc2hex32($str));
    }

    /**
     * Test HEX unescaping
     */
    public function testHex2asc() {
        $expected = '';
        for ($i = 0; $i < 127; $i++) {
             $expected .= chr($i);
        }

        $str = '\00\01\02\03\04\05\06\07\08\09\0a\0b\0c\0d\0e\0f\10\11\12\13\14\15\16\17\18\19\1a\1b\1c\1d\1e\1f !"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~';
        $this->assertEquals($expected, Net_LDAP2_Util::hex2asc($str));
    }

    /**
     * Tests split_rdn_multival()
     *
     * In addition to the above test of the basic split correction,
     * we test here the functionality of mutlivalued RDNs
     */
    public function testSplit_rdn_multival() {
        // One value
        $rdn = 'CN=J. Smith';
        $expected = array('CN=J. Smith');
        $split = Net_LDAP2_Util::split_rdn_multival($rdn);
        $this->assertEquals($expected,  $split);

        // Two values
        $rdn = 'OU=Sales+CN=J. Smith';
        $expected = array('OU=Sales', 'CN=J. Smith');
        $split = Net_LDAP2_Util::split_rdn_multival($rdn);
        $this->assertEquals($expected,  $split);

        // Several multivals
        $rdn = 'OU=Sales+CN=J. Smith+L=London+C=England';
        $expected = array('OU=Sales', 'CN=J. Smith', 'L=London', 'C=England');
        $split = Net_LDAP2_Util::split_rdn_multival($rdn);
        $this->assertEquals($expected,  $split);

        // Unescaped "+" in value
        $rdn = 'OU=Sa+les+CN=J. Smith';
        $expected = array('OU=Sa+les', 'CN=J. Smith');
        $split = Net_LDAP2_Util::split_rdn_multival($rdn);
        $this->assertEquals($expected,  $split);

        // Unescaped "+" in attr name
        $rdn = 'O+U=Sales+CN=J. Smith';
        $expected = array('O+U=Sales', 'CN=J. Smith');
        $split = Net_LDAP2_Util::split_rdn_multival($rdn);
        $this->assertEquals($expected,  $split);

        // Unescaped "+" in attr name + value
        $rdn = 'O+U=Sales+CN=J. Sm+ith';
        $expected = array('O+U=Sales', 'CN=J. Sm+ith');
        $split = Net_LDAP2_Util::split_rdn_multival($rdn);
        $this->assertEquals($expected,  $split);

        // Unescaped "+" in attr name, but not first attr
        // this documents a known bug. However, unfortunately  we cant
        // know wether the "C+" belongs to value "Sales" or attribute "C+N".
        // To solve this, we must ask the schema which we do not right now.
        // The problem is located in _correct_dn_splitting()
        $rdn = 'OU=Sales+C+N=J. Smith';
        $expected = array('OU=Sales+C', 'N=J. Smith');     // The "C+" is treaten as value of "OU"
        $split = Net_LDAP2_Util::split_rdn_multival($rdn);
        $this->assertEquals($expected,  $split);

        // Escaped "+" in attr name and value
        $rdn = 'O\+U=Sales+CN=J. Sm\+ith';
        $expected = array('O\+U=Sales', 'CN=J. Sm\+ith');
        $split = Net_LDAP2_Util::split_rdn_multival($rdn);
        $this->assertEquals($expected,  $split);
    }

    /**
     * Tests attribute splitting ('foo=bar' => array('foo', 'bar'))
     */
    public function testSplit_attribute_string() {
        $attr_str = "foo=bar";

        // properly
        $expected = array('foo', 'bar');
        $split = Net_LDAP2_Util::split_attribute_string($attr_str);
        $this->assertEquals($expected,  $split);

        // escaped "="
        $attr_str = "fo\=o=b\=ar";
        $expected = array('fo\=o', 'b\=ar');
        $split = Net_LDAP2_Util::split_attribute_string($attr_str);
        $this->assertEquals($expected,  $split);

        // escaped "=" and unescaped = later on
        $attr_str = "fo\=o=b=ar";
        $expected = array('fo\=o', 'b=ar');
        $split = Net_LDAP2_Util::split_attribute_string($attr_str);
        $this->assertEquals($expected,  $split);
    }

    /**
     * Tests Ldap_explode_dn()
     */
    public function testLdap_explode_dn() {
        $dn = 'OU=Sales+CN=J. Smith,dc=example,dc=net';
        $expected_casefold_none = array(
            array('CN=J. Smith', 'OU=Sales'),
            'dc=example',
            'dc=net'
        );
        $expected_casefold_upper = array(
            array('CN=J. Smith', 'OU=Sales'),
            'DC=example',
            'DC=net'
        );
        $expected_casefold_lower = array(
            array('cn=J. Smith', 'ou=Sales'),
            'dc=example',
            'dc=net'
        );

        $expected_onlyvalues = array(
            array( 'J. Smith', 'Sales'),
            'example',
            'net'
        );
        $expected_reverse = array_reverse($expected_casefold_upper);


        $dn_exploded_cnone   = Net_LDAP2_Util::ldap_explode_dn($dn, array('casefold' => 'none'));
        $this->assertEquals($expected_casefold_none,  $dn_exploded_cnone,   'Option casefold none failed');

        $dn_exploded_cupper  = Net_LDAP2_Util::ldap_explode_dn($dn, array('casefold' => 'upper'));
        $this->assertEquals($expected_casefold_upper, $dn_exploded_cupper,  'Option casefold upper failed');

        $dn_exploded_clower  = Net_LDAP2_Util::ldap_explode_dn($dn, array('casefold' => 'lower'));
        $this->assertEquals($expected_casefold_lower, $dn_exploded_clower,  'Option casefold lower failed');

        $dn_exploded_onlyval = Net_LDAP2_Util::ldap_explode_dn($dn, array('onlyvalues' => true));
        $this->assertEquals($expected_onlyvalues,     $dn_exploded_onlyval, 'Option onlyval failed');

        $dn_exploded_reverse = Net_LDAP2_Util::ldap_explode_dn($dn, array('reverse' => true));
        $this->assertEquals($expected_reverse,        $dn_exploded_reverse, 'Option reverse failed');
    }

    /**
     * Tests if canonical_dn() works
     *
     * Note: This tests depend on the default options of canonical_dn().
     */
    public function testCanonical_dn() {
        // test empty dn (is valid according to rfc)
        $this->assertEquals('', Net_LDAP2_Util::canonical_dn(''));

        // default options with common dn
        $testdn   = 'cn=beni,DC=php,c=net';
        $expected = 'CN=beni,DC=php,C=net';
        $this->assertEquals($expected, Net_LDAP2_Util::canonical_dn($testdn));

        // casefold tests with common dn
        $expected_up = 'CN=beni,DC=php,C=net';
        $expected_lo = 'cn=beni,dc=php,c=net';
        $expected_no = 'cn=beni,DC=php,c=net';
        $this->assertEquals($expected_up, Net_LDAP2_Util::canonical_dn($testdn, array('casefold' => 'upper')));
        $this->assertEquals($expected_lo, Net_LDAP2_Util::canonical_dn($testdn, array('casefold' => 'lower')));
        $this->assertEquals($expected_no, Net_LDAP2_Util::canonical_dn($testdn, array('casefold' => 'none')));

        // reverse
        $expected_rev = 'C=net,DC=php,CN=beni';
        $this->assertEquals($expected_rev, Net_LDAP2_Util::canonical_dn($testdn, array('reverse' => true)), 'Option reverse failed');

        // DN as arrays
        $dn_index = array('cn=beni', 'dc=php', 'c=net');
        $dn_assoc = array('cn' => 'beni', 'dc' => 'php', 'c' => 'net');
        $expected = 'CN=beni,DC=php,C=net';
        $this->assertEquals($expected, Net_LDAP2_Util::canonical_dn($dn_index));
        $this->assertEquals($expected, Net_LDAP2_Util::canonical_dn($dn_assoc));

        // DN with multiple rdn value
        $testdn       = 'ou=dev+cn=beni,DC=php,c=net';
        $testdn_index = array(array('ou=dev','cn=beni'),'DC=php','c=net');
        $testdn_assoc = array(array('ou' => 'dev','cn' => 'beni'),'DC' => 'php','c' => 'net');
        $expected     = 'CN=beni+OU=dev,DC=php,C=net';
        $this->assertEquals($expected, Net_LDAP2_Util::canonical_dn($testdn));
        $this->assertEquals($expected, Net_LDAP2_Util::canonical_dn($testdn_assoc));
        $this->assertEquals($expected, Net_LDAP2_Util::canonical_dn($expected));

        // test DN with OID
        $testdn = 'OID.2.5.4.3=beni,dc=php,c=net';
        $expected = '2.5.4.3=beni,DC=php,C=net';
        $this->assertEquals($expected, Net_LDAP2_Util::canonical_dn($testdn));

        // test with leading and ending spaces
        $testdn   = 'cn=  beni  ,DC=php,c=net';
        $expected = 'CN=\20\20beni\20\20,DC=php,C=net';
        $this->assertEquals($expected, Net_LDAP2_Util::canonical_dn($testdn));

        // test with to-be escaped characters in attr value
        $specialchars = array(
            ',' => '\,',
            '+' => '\+',
            '"' => '\"',
            '\\' => '\\\\',
            '<' => '\<',
            '>' => '\>',
            ';' => '\;',
            '#' => '\#',
            '=' => '\=',
            chr(18) => '\12',
            '/' => '\2f'
        );
        foreach ($specialchars as $char => $escape) {
            $test_string = 'CN=be'.$char.'ni,DC=ph'.$char.'p,C=net';
            $test_index  = array('CN=be'.$char.'ni', 'DC=ph'.$char.'p', 'C=net');
            $test_assoc  = array('CN' => 'be'.$char.'ni', 'DC' => 'ph'.$char.'p', 'C' => 'net');
            $expected = 'CN=be'.$escape.'ni,DC=ph'.$escape.'p,C=net';

            $this->assertEquals($expected, Net_LDAP2_Util::canonical_dn($test_string), 'String escaping test ('.$char.') failed');
            $this->assertEquals($expected, Net_LDAP2_Util::canonical_dn($test_index),  'Indexed array escaping test ('.$char.') failed');
            $this->assertEquals($expected, Net_LDAP2_Util::canonical_dn($test_assoc),  'Associative array encoding test ('.$char.') failed');
        }
    }

    /**
    * Test if split_attribute_string() works
    */
    public function testSplitAttributeString() {
        // test default behavour
        $this->assertEquals(array('fooAttr', 'barValue'), Net_LDAP2_Util::split_attribute_string("fooAttr=barValue"));
        $this->assertEquals(array('fooAttr', '=barValue'), Net_LDAP2_Util::split_attribute_string("fooAttr==barValue"));
        $this->assertEquals(array('fooAttr', 'bar=Value'), Net_LDAP2_Util::split_attribute_string("fooAttr=bar=Value"));
        $this->assertEquals(array('foo\=Attr', 'barValue'), Net_LDAP2_Util::split_attribute_string("foo\=Attr=barValue"));
	$this->assertEquals(array('fooAttr', 'bar\=Value'), Net_LDAP2_Util::split_attribute_string("fooAttr=bar\=Value"));

        // test default behaviour with delim
        $this->assertEquals(array('fooAttr', '=', 'barValue'), Net_LDAP2_Util::split_attribute_string("fooAttr=barValue", false, true));
        $this->assertEquals(array('fooAttr', '=', '=barValue'), Net_LDAP2_Util::split_attribute_string("fooAttr==barValue", false, true));
        $this->assertEquals(array('fooAttr', '=', 'bar=Value'), Net_LDAP2_Util::split_attribute_string("fooAttr=bar=Value", false, true));
        $this->assertEquals(array('foo\=Attr', '=', 'barValue'), Net_LDAP2_Util::split_attribute_string("foo\=Attr=barValue", false, true));

        // test basic extended splitting and delimter return
	$test_delimeters = array('=', '~=', '>', '>=', '<','<=');
        foreach ($test_delimeters as $td) {
            // default behavior with simple parameters
            $this->assertEquals(array('fooAttr', 'barValue'), Net_LDAP2_Util::split_attribute_string("fooAttr${td}barValue", true), "AttrString='fooAttr${td}barValue'; sep='$td'");
            $this->assertEquals(array('fooAttr', 'barValue'), Net_LDAP2_Util::split_attribute_string("fooAttr${td}barValue", true, false));

            // test proper escaping
            $tde = addcslashes($td, '=~><');
            $this->assertEquals(array("foo${tde}Attr", 'barValue'), Net_LDAP2_Util::split_attribute_string("foo${tde}Attr${td}barValue", true));
        }

        // negative test case: perform no split
        $this->assertEquals(array('fooAttr barValue'), Net_LDAP2_Util::split_attribute_string('fooAttr barValue'));
        $this->assertEquals(array('fooAttr barValue'), Net_LDAP2_Util::split_attribute_string('fooAttr barValue', true, true));
	$this->assertEquals(array('fooAttr>barValue'), Net_LDAP2_Util::split_attribute_string('fooAttr>barValue')); // extended splitting used, but not activated

        // negative testcase: wrong escaping used
        $this->assertEquals(array('fooAttr\>', 'barValue'), Net_LDAP2_Util::split_attribute_string('fooAttr\>=barValue', false, false));
        $this->assertEquals(array('fooAttr\>', '=', 'barValue'), Net_LDAP2_Util::split_attribute_string('fooAttr\>=barValue', true, true));
	$this->assertEquals(array('fooAttr', '>', '\=barValue'), Net_LDAP2_Util::split_attribute_string('fooAttr>\=barValue', true, true));
	$this->assertEquals(array('fooAttr\>\=barValue'), Net_LDAP2_Util::split_attribute_string('fooAttr\>\=barValue', true, true));

    }

}
?>

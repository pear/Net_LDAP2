<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Net_LDAP2_AllTests::main');
}

// PHPUnit inlcudes
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// Net_LDAP2 test suites includes
chdir(dirname(__FILE__) . '/../');
require_once 'Net_LDAP2_FilterTest.php';
require_once 'Net_LDAP2_UtilTest.php';
require_once 'Net_LDAP2Test.php';
require_once 'Net_LDAP2_EntryTest.php';
require_once 'Net_LDAP2_RootDSETest.php';
require_once 'Net_LDAP2_SearchTest.php';
require_once 'Net_LDAP2_LDIFTest.php';

class Net_LDAP2_AllTests
{
    public static function main()
    {

        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Net_LDAP2 Tests');

       // LDAP independent tests
       $suite->addTestSuite('Net_LDAP2_FilterTest');
       $suite->addTestSuite('Net_LDAP2_UtilTest');
       $suite->addTestSuite('Net_LDAP2_LDIFTest');

       // LDAP dependent tests (require a LDAP server)
       $suite->addTestSuite('Net_LDAP2Test');
       $suite->addTestSuite('Net_LDAP2_SearchTest');
       $suite->addTestSuite('Net_LDAP2_EntryTest');
       $suite->addTestSuite('Net_LDAP2_RootDSETest');

        return $suite;
    }
}


// exec test suite
if (PHPUnit_MAIN_METHOD == 'Net_LDAP2_AllTests::main') {
    Net_LDAP2_AllTests::main();
}
?>

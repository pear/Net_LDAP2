<?php
class Net_LDAP2_TestBase extends PHPUnit_Framework_TestCase
{
    public static function assertTrue($condition, $msg = null)
    {
        if ($condition instanceof Net_LDAP2_Error) {
            self::fail('Error: ' . $condition->getMessage());
        }
        return parent::assertTrue($condition, $msg);
    }
}
?>

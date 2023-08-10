<?php
class Net_LDAP2_TestBase extends \PHPUnit\Framework\TestCase
{
    public static function assertTrue($condition, string $message = ''): void
    {
        if ($condition instanceof Net_LDAP2_Error) {
            self::fail('Error: ' . $condition->getMessage());
        }
        parent::assertTrue($condition, $message);
    }
}
?>

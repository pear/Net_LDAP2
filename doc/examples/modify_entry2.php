<?php
/**
* This is a short example on how to modify a specific entry in the
* directory using Net_LDAP2.
* The way described here is the more compact one but may be useful too.
* The diference is, that this way we use the $ldap object to modify
* the entry directly on the server.
*/

// We use the fetch_entry.php example to get the LDAP-Entry
// which we will modify now.
include_once 'fetch_entry.php';

// Okay, we should have a valid Net_LDAP2_Entry object that represents
// a real existing entry in our directory.

// What we do now is to specify some actions that should be performed.
// Note, that the same rules as in the long version discussed in modify_entry.php
// aplly here too, so for replacing attributes, we must specify the absolute new state.
$changes = array(
    'add' => array(
        'mail' => array('foo@example.org', 'test2@example.org'),
        'telephoneNumber' => '1234567890'
    ),
    'replace' => array(
        'mail' => array('test1@example.org', 'test2@example.org')
    ),

    'delete' => array(
        'mail' => 'test2@example.org',
        'telephoneNumber' => null     // the null value is important here, since array
    )                                 // mode (indexed, associative) is needed to be homogenous
);

// Now it is time to transfer the changes to the ldap
// directory. However, for security reasons, these lines
// are commented out.
// You have two options to carry out the changes, with a small but often
// very important difference:
// The first call will carry out the actions in the order "add->delete->replace",
// while the latter will perform the changes in the order you define.
// (add->replace->delete, in our example)


/*
// METHOD 1: ORDER = add->delete->replace
$result = $ldap->modify($entry, $changes);
if (Net_LDAP2::isError($result)) {
    die('Unable to update entry: '.$result->getMessage());
}
*/

/*
// METHOD 2: ORDER = add->replace->delete
$result = $ldap->modify($entry, array('changes' => $changes));
if (Net_LDAP2::isError($result)) {
    die('Unable to update entry: '.$result->getMessage());
}
*/
?>

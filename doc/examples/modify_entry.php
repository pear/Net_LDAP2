<?php
/**
* This is a short example on how to modify a specific entry in the
* directory using Net_LDAP2.
*
* There is an alternative way of doing this; please have a look at
* examples/modify_entry2.php
*/

// We use the fetch_entry.php example to get the LDAP-Entry
// which we will modify now.
include_once 'fetch_entry.php';

// Okay, we should have a valid Net_LDAP2_Entry object that represents
// a real existing entry in our directory.
// The changes are only locally made and executed on the server
// at the end of the script.

// What we do now is to add two new attributes, one with two values
// Note that we can add attribute values which we haven't selected
// at fetching/searching the entry - but if we do that and
// call getValues(), we will only see the values added and NOT all
// attributes present on the server!
$result = $entry->add(array(
    'mail'            => array('foo@example.org', 'test2@example.org'),
    'telephoneNumber' => '1234567890'
));
if (Net_LDAP2::isError($result)) {
    die('Unable to add attribute: '.$result->getMessage());
}

// Now we modify the first value
// Note, that we must give all old values, otherwise the attribute
// will be deleted. We specify the new absolute attribute state
$result = $entry->replace(array('mail' => array('test1@example.org', 'test2@example.org')));
if (Net_LDAP2::isError($result)) {
    die('Unable to modify attribute: '.$result->getMessage());
}

// And now we delete the second attribute value
// We must provide the old value, so the ldap server knows,
// which value we want to be deleted
$result = $entry->delete(array('mail' => 'test2@example.org'));
if (Net_LDAP2::isError($result)) {
    die('Unable to delete attribute value: '.$result->getMessage());
}

// Finally, we delete the whole attribute 'telephoneNumber':
$result = $entry->delete('telephoneNumber');
if (Net_LDAP2::isError($result)) {
    die('Unable to delete attribute: '.$result->getMessage());
}

// Now it is time to transfer the changes to the ldap
// directory. However, for security reasons, this line is
// commented out.

/*
$result = $entry->update();
if (Net_LDAP2::isError($result)) {
    die('Unable to update entry: '.$result->getMessage());
}
*/
?>
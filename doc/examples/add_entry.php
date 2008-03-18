<?php
/**
* This is a short example on how to add a new entry to your
* directory using Net_LDAP2.
*/

// We use the connecting.php example to get a link to our server.
// This file will also include all required basic Net_LDAP2 classes.
include_once 'connecting.php';

// Okay, we should have a valid link now.
// We must define the DN of the new entry. The DN is the
// global unique path to the data in the directory server,
// similar to a path name in your filesystem.
// Since we want to be a little flexible, we make the base
// dynamic, so it is enough to change the base-dn in your
// $ldap_config array.
$dn = 'cn=Foo Bar,'.$ldap_config['base'];


// It is a good idea to first look if the entry, that should be added,
// is already present:
if ($ldap->dnExists($dn)) {
    die('Could not add entry! Entry already exists!');
}

// The entry does not exist so far, we can safely add him.
// But first, we must construct the entry.
// This is, because Net_LDAP2 was build to make changes only
// locally (in your script), not directly on the server.
$attributes = array(
    'sn'             => 'Foo',
    'gn'             => 'Bar',
    'mail'           => array('foo@example.org', 'bar@example2.org'),
    'employeeNumber' => 123456
);
$new_entry = Net_LDAP2_Entry::createFresh($dn, $attributes);

// Finally add the entry in the server:
$result = $ldap->add($new_entry);
if (Net_LDAP2::isError($result)) {
    die('Unable to add entry: '.$result->getMessage());
}

// The entry is now present in the directory server.
// Additionally, it is linked to the $ldap connection used for the add(),
// so you may call $entry->modify() (and friends) and $entry->update()
// without the need for passing an $ldap object.
// This is only the case if the entry was not linked to an Net_LDAP2 object
// before, so if the entry object would be fetched from a $ldap object
// and then added to $ldap_2, the link of the entry remains to $ldap,
// thus any update() will be performed on directory1 ($ldap).
?>
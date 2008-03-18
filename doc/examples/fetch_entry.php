<?php
/**
* This is a short example on how to fetch a specific entry in the
* directory using Net_LDAP2.
*/

// We use the connecting.php example to get a link to our server.
// This file will also include all required basic Net_LDAP2 classes.
include_once 'connecting.php';

// Okay, we should have a valid link now.
// Lets fetch an entry! We want to know the admins first and last name.
// If we need additional attributes later, we must refetch the entry.
// It is a good practice to only select the attributes really needed.
// Since we want to be a little flexible, we make the base
// dynamic, so it is enough to change the base-dn in your
// $ldap_config array.
$entry = $ldap->getEntry('cn=admin,'.$ldap_config['base'], array('gn', 'sn'));

// Error checking is important!
if (Net_LDAP2::isError($entry)) {
    die('Could not fetch entry: '.$entry->getMessage());
}

// Now fetch the data from the entry
$surename  = $entry->getValue('sn', 'single');
if (Net_LDAP2::isError($surename)) {
    die('Unable to get surename: '.$surename->getMessage());
}
$givenname = $entry->getValue('gn', 'single');
if (Net_LDAP2::isError($givenname)) {
    die('Unable to get surename: '.$givenname->getMessage());
}

// Finally output the data of the entry:
// This will give something like "Name of cn=admin,o=example,dc=org: Foo Bar"
echo 'Name of '.$entry->DN().': '.$givenname.' '.$surename;
?>
<?php
/**
* This is a short example on how to search for entries in your
* directory using Net_LDAP2.
*/

// We use the connecting.php example to get a link to our server.
// This file will also include all required basic Net_LDAP2 classes.
include_once 'connecting.php';

// Okay, we should have a valid link now.
// We must now define a filter that defines, which
// entries we want to have returned.
// We use the Net_LDAP2_Filter class for this purpose,
// because so we don't need to worry about
// RFC-2254 ;)
// In this example, we want all users with first names
// starting with "bened" and the last names ending with "ger".
// Additionally, we want to exclude all users with names
// containing "smith", which will be done throug a "negation".

// Basic filter building
$filter_sn = Net_LDAP2_Filter::create('gn', 'begins',  'bened');
$filter_gn = Net_LDAP2_Filter::create('sn', 'ends',    'ger');

// Building and negating the "no smith" filter component
// this must be done in two steps, because
// you are able to negate EVERY filter, not just leave filters.
// the $filter_smith will not be used afterwards and is only
// necessary for negation here.
$filter_smith   = Net_LDAP2_Filter::create('sn', 'contains','smith');
$filter_nosmith = Net_LDAP2_Filter::combine('not', $filter_smith);

// Now combine all filter components to build our search filter
$filter = Net_LDAP2_Filter::combine('and', array($filter_sn, $filter_gn, $filter_nosmith));


// The filter is ready now, so we can
// use this filter now to search for entries.
// The scope we use is "sub", meaning "all entries below the search base".
// The base is "null", meaning the base defined in $ldap_config. This is similar
// to call $ldap->search($ldap_config['base'], ...
$requested_attributes = array('sn','gn','telephonenumber');
$search = $ldap->search(null, $filter, array('attributes' => $requested_attributes));
if (Net_LDAP2::isError($search)) {
    die('LDAP search failed: '.$search->getMessage());
}

// Lets see what entries we got and print the names and telephone numbers:
if ($search->count() > 0) {
    echo "Found ".$search->count().' entries:<br>';

    // Note, this is is only one of several ways to fetch entries!
    // You can also retrieve all entries in an array with
    //   $entries = $search->entries()
    // or the same thing sorted:
    //   $entries = $search->sorted()
    // Since Net_LDAP2 you can also use a foreach loop:
    //   foreach ($search as $dn => $entry) {
    while ($entry = $search->shiftEntry()) {
        $surename = $entry->getValue('sn', 'single');
        if (Net_LDAP2::isError($surename)) {
            die('Unable to get surename: '.$surename->getMessage());
        }
        $givenname = $entry->getValue('gn', 'single');
        if (Net_LDAP2::isError($givenname)) {
            die('Unable to get givenname: '.$givenname->getMessage());
        }
        $phone = $entry->getValue('telephonenumber', 'single');
        if (Net_LDAP2::isError($phone)) {
            die('Unable to get phone number: '.$phone->getMessage());
        }
        echo "<br>$givenname $surename: $phone";
    }
} else {
    die('Sorry, no entries found!');
}
?>

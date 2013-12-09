<?php
/**
* This file shows you how to connect to a ldap server  using Net_LDAP2.
*
* It also establishes connections for the other examples;
* they include this file to get a ldap link.
*/

// Class includes; this assumes Net_LDAP2 installed in PHPs include path
// or under subfolder "Net" in the local directory.
require_once 'Net/LDAP2.php';

// Configuration
// host can be a single server (string) or multiple ones - if we define more
// servers here (array), we can implement a basic fail over scenario.
// If no credentials (binddn and bindpw) are given, Net_LDAP2 establishes
// an anonymous bind.
// See the documentation for more information on the configuration items!
$ldap_config = array(
// 'host'    => 'ldap.example.org',
    'host'    => array('ldap1.example.org', 'ldap2.example.org'),
// 'binddn'  => 'cn=admin,o=example,dc=org',
// 'bindpw'  => 'your-secret-password',
    'tls'     => false,
    'base'    => 'o=example,dc=org',
    'port'    => 389,
    'version' => 3,
    'filter'  => '(cn=*)',
    'scope'   => 'sub'
);

// Connect to configured ldap server
$ldap = Net_LDAP2::connect($ldap_config);

// It is important to check for errors.
// Nearly every method of Net_LDAP2 returns a Net_LDAP2_Error object
// if something went wrong. Through this object, you can retrieve detailed
// information on what exactly happened.
//
// Here we drop a die with the error message, so the other example
// files will not be calles unless we have a valid link.
if (Net_LDAP2::isError($ldap)) {
    die('BIND FAILED: '.$ldap->getMessage());
}
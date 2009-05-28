<?php
/**
* This is a short example on how to use the schema cache facility.
* Schema caching allows you to store the fetched schema on disk
* (or wherever you want, depending on the cache class used) so
* initialisation of Net_LDAP2 becomes a little faster.
*
* Two examples will be showed here:
* 1. how to use the packaged file based cache
* 2. how to write a custom cache class
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


/*
* EXAMPLE 1: How to use the packaged file based cach
*            This cache class stores the schema object on disk once Net_LDAP2
*            initially fetched it from the LDAP server. This will make Net_LDAP2
*            use the disk version instead of loading the schema from LDAP
*            unless the schema object becomes too old.
*/

// Configuring the schema cacher
// see the source code of SimpleFileSchemaCache.php for config options
// An interesting idea is, to store the file in some tmpfs mount, which will
// result in storing the schema cache in memory instead of disk.
$mySchemaCache_cfg = array(
    'path'    =>  '/tmp/Net_LDAP2_Schema.cache', // place to put cache file
    'max_age' =>  86400 // max age is 24 hours (in seconds)
);

// Initialize cache with the config
$mySchemaCache = new Net_LDAP2_SimpleFileSchemaCache($mySchemaCache_cfg);

// As usual, connect to configured ldap server
$ldap = Net_LDAP2::connect($ldap_config);
if (Net_LDAP2::isError($ldap)) {
    die('BIND FAILED: '.$ldap->getMessage());
}

// and finally register our initialized cache object
$res = $ldap->registerSchemaCache($mySchemaCache);
if (Net_LDAP2::isError($res)) {
    die('REGISTER CACHE FAILED: '.$res->getMessage());
}

// Here we go, Net_LDAP2 will fetch the schema once and then use the disk version.





/*
* EXAMPLE 2: How to write a custom cache class
*            Writing a custom cache class is easy. You just have to wipe out a
*            class that implements the SchemaCache interface.
*            How a cache class must look like is documented in the interface
*            definition file: SchemaCache.interface.php
*            Here we will write a small hacky cache that stores the schema
*            in the php session. This gives us a nice per-user cache that
*            survives for the php session. This cache will obviously not
*            be so performant as the SimpleFileSchemaCache but may be
*            useful for other purposes.
*/

// Firstly, we need our custom schema class...
class MySessionSchemaCache implements Net_LDAP2_SchemaCache {
    /**
    * Initilize the cache
    *
    * Here we do nothing. You can use the class constructor for everything you
    * want, but typically it is used to configure the caches config.
    */
    public function MySessionSchemaCache () {
        // nothing to see here, move along...
    }

    /**
    * Load schema from session
    *
    * For the sake of simplicity we dont implement a cache aging here.
    * This is not a big problem, since php sessions shouldnt last endlessly.
    *
    * @return Net_LDAP2_Schema|Net_LDAP2_Error|false
    */
    public function loadSchema() {
        // Lets see if we have a session, otherwise we cant use this cache
        // and drop some error that will be returned by Net_LDAP2->schema().
        // Minor errors should be indicated by returning false, so Net_LDAP2
        // can continue its work. This will result in the same behavior as
        // no schema cache would have been registered.
        if (!isset($_SESSION)) {
            return new Net_LDAP2_Error(__CLASS__.": No PHP Session initialized.".
                                       " This cache needs an open PHP session.");
        }

        // Here the session is valid, so we return the stores schema.
        // If we cant find the schema (because cahce is empty),w e return
        // false to inidicate a minor error to Net_LDAP2.
        // This causes it to fetch a fresh object from LDAP.
        if (array_key_exists(__CLASS__, $_SESSION)
        && $_SESSION[__CLASS__] instanceof Net_LDAP2_SchemaCache) {
            return $_SESSION[__CLASS__];
        } else {
            return false;
        }
    }

    /**
    * Store the schema object in session
    *
    * @return true|Net_LDAP2_Error
    */
    public function storeSchema($schema) {
        // Just dump the given object into the session
        // unless in loadSchema(), it is important to only return
        // Net_LDAP2_Error objects if something crucial went wrong.
        // If you feel that you want to return an error object, be sure
        // that you have read the comments in Net_LDAP2_SchemaCache.interface.php
        // or you will seriously hurt the performance of your application!!!!
        $_SESSION[__CLASS__] = $schema;
        return true;
    }
}


// Ok, now we have our finished cache object. Now initialize and register it
// the usual way:
$mySchemaCache = new MySessionSchemaCache();

$ldap          = Net_LDAP2::connect($ldap_config);
if (Net_LDAP2::isError($ldap)) {
    die('BIND FAILED: '.$ldap->getMessage());
}

$res = $ldap->registerSchemaCache($mySchemaCache);
if (Net_LDAP2::isError($res)) {
    die('REGISTER CACHE FAILED: '.$res->getMessage());
}

// Now, the Schema is cached in the PHP session :)

<?php

// The secure path should *NEVER* point to a path within "www-root" of the Apache server.
// This is for security considerations to protect the private key, the certificates and
// the configuration file.

// Make sure to use a path outside the www-root that you can protect properly

define('SECURE_PATH', JPATH_LIBRARIES . '/Jdideal/Psp/Advanced/certificates');

/*
If you did not change the directory structure you can use the following path:
define( "SECURE_PATH", "includes/security" );

If you have changed the location of the security directory use the following path:
define( "SECURE_PATH", "http://www.mysite.com/ideal/includes/security" );
(example)

*/

<?php

/**
 * Entry point for the Query component of Wikibase.
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

if ( defined( 'WIKIBASE_QUERYENGINE_VERSION' ) ) {
	// Do not initialize more than once.
	return 1;
}

define( 'WIKIBASE_QUERYENGINE_VERSION', '0.3 alpha' );

// Attempt to include the dependencies mif one of them has not been loaded yet.
// This is the path to the autoloader generated by composer in case of a composer install.
if ( !defined( 'Ask_VERSION' ) && is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include_once( __DIR__ . '/vendor/autoload.php' );
}

// @codeCoverageIgnoreStart
if ( defined( 'MEDIAWIKI' ) ) {
	call_user_func( function() {
		require_once __DIR__ . '/WikibaseQueryEngine.mw.php';
	} );
}
// @codeCoverageIgnoreEnd
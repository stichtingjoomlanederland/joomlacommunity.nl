<?php

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.path' );
jimport( 'joomla.access.access' );

require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'version.php' );
require_once( KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'document.php' );
require_once( KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( KOMENTO_HELPERS . DIRECTORY_SEPARATOR . 'router.php' );
require_once( KOMENTO_CLASSES . DIRECTORY_SEPARATOR . 'comment.php' );

// Load language here
// initially language is loaded in content plugin
// for custom integration that doesn't go through plugin, language is not loaded
// hence, language should be loaded in bootstrap

$lang = JFactory::getLanguage();

$path = JPATH_ROOT;

if( JFactory::getApplication()->isAdmin() )
{
	$path .= DIRECTORY_SEPARATOR . 'administrator';
}

// Load English first as fallback
$konfig = Komento::getKonfig();
if( $konfig->get( 'enable_language_fallback' ) )
{
	$lang->load( 'com_komento', $path, 'en-GB', true );
}

// Load site's selected language
$lang->load( 'com_komento', $path, $lang->getDefault(), true );

// Load user's preferred language file
$lang->load( 'com_komento', $path, null, true );

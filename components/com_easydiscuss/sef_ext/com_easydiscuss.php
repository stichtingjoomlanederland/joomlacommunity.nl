<?php
/**
 * sh404SEF support for com_easydiscuss
 * Author : StackIdeas Private Limited
 * contact : support@stackideas.com
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

global $sh_LANG;

// Include main constants file.
require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once DISCUSS_HELPERS . '/router.php';

if( class_exists( 'shRouter' ) )
{
	$sefConfig		= shRouter::shGetConfig();
}
else
{
	$sefConfig		= Sh404sefFactory::getConfig();
}

$shLangName		= '';
$shLangIso		= '';
$title			= array();
$shItemidString	= '';
$dosef			= shInitializePlugin( $lang, $shLangName, $shLangIso, $option);

if( $dosef == false )
{
	return;
}

// remove common URL from GET vars list, so that they don't show up as query string in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');

// Load language file
$language = JFactory::getLanguage();
$language->load( 'com_easydiscuss' , JPATH_ROOT );

// start by inserting the menu element title (just an idea, this is not required at all)
$task 	= isset($task) ? @$task : null;
$Itemid	= isset($Itemid) ? @$Itemid : null;
$view	= isset( $view ) ? $view : '';

if( !empty($id) && !empty( $view ) )
{
	$permalink  = '';

	switch( $view )
	{
		case 'categories':
			$permalink  = DiscussRouter::getAlias( 'category' , $id );
		break;
		case 'post':
			$permalink  = DiscussRouter::getAlias( 'posts' , $id );
		break;
		case 'profile':
			$permalink  = DiscussRouter::getUserAlias( $id );
		break;
		case 'tags':
			$permalink  = DiscussRouter::getAlias( 'tags' , $id );
		break;
	}
}

if(empty($Itemid))
{
	$Itemid	= DiscussRouter::getItemId( $view );
	shAddToGETVarsList('Itemid' , $Itemid);
}

$name	= shGetComponentPrefix($option);
$name	= empty( $name ) ? getMenuTitle( $option , $task , $Itemid , null , $shLangName ) : $name;
$name	= empty( $name ) || $name == '/' ? 'discuss' : $name;

$title[]	= $name;


if( isset($view) && !empty( $view ) )
{
	// Translate the view
	$title[]	= JText::_( 'COM_EASYDISCUSS_SH404_VIEW_' . JString::strtoupper( $view ) );
	shRemoveFromGETVarsList('view');
}

if( $view == 'categories' && !empty( $category_id ) )
{
	$title[]	= DiscussRouter::getAlias( 'category' , $category_id );
	shRemoveFromGETVarsList( 'category_id' );

	// Remove the view since we don't want to set the view.
	unset( $layout );
	shRemoveFromGETVarsList( 'layout' );
}

if( !empty($id) )
{
	if( !empty( $permalink ) )
	{
		$title[]	= $permalink;
		shRemoveFromGETVarsList('id');
	}
}



// Category id may be category_id=0 in index view.
if( isset( $category_id ) && $category_id == 0 )
{
	shRemoveFromGETVarsList( 'category_id' );
}

if( !empty( $layout ) )
{
	$title[]	= $layout;
	shRemoveFromGETVarsList( 'layout' );
}

if(!empty($format))
{
	$title[]	= $format;
	shRemoveFromGETVarsList('format');
}

if(!empty($Itemid))
{
	shRemoveFromGETVarsList('Itemid');
}

if(!empty($limit))
{
	shRemoveFromGETVarsList('limit');
}

if(isset($limitstart))
{
	shRemoveFromGETVarsList('limitstart'); // limitstart can be zero}
}

// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef){
	$string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString,
		(isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null),
		(isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change ---------------------------

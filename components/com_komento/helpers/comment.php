<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Comment utilities class.
 *
 */
class KomentoCommentHelper
{
	/**
	 * Process comments data
	 **/
	static public function process( $row, $admin = 0 )
	{
		if( isset( $row->processed ) && $row->processed )
		{
			return $row;
		}

		Komento::setCurrentComponent( $row->component );

		$config = Komento::getConfig();
		$konfig = Komento::getKonfig();
		$user = JFactory::getUser()->id;
		$commentsModel	= Komento::getModel( 'comments' );

		Komento::import( 'helper', 'date' );

		// Duplicate created date first before lapsed time messing up the original date
		$row->unformattedDate = $row->created;

		// get number of child for each comment
		$row->childs = $commentsModel->getTotalChilds( $row->id );

		// set url to proper url
		if( !empty($row->url) )
		{
			// Add 'http://' if not present
			$row->url = ( 0 === strpos( $row->url, 'http' ) ) ? $row->url : 'http://' . $row->url;
		}

		// 1. Load article and article details
		$application	= Komento::loadApplication( $row->component )->load( $row->cid );

		if( $application === false )
		{
			$application = Komento::getErrorApplication( $row->component, $row->cid );
		}

		// set component title
		$row->componenttitle = $application->getComponentName();

		// set content title
		$row->contenttitle = $application->getContentTitle();

		// get permalink
		$row->pagelink = $application->getContentPermalink();
		$row->permalink = $row->pagelink . '#kmt-' . $row->id;

		// set parentlink
		if( $row->parent_id != 0 )
		{
			$row->parentlink = $row->pagelink . '#kmt-' . $row->parent_id;
		}

		// to be reassign
		$row->shortlink = $row->permalink;

		// set extension object
		// use this to check if application is able to load article details
		// if row->extension is false, means error loading article details
		$row->extension = $application;

		if( $admin == 0 )
		{
			// frontend

			$actionsModel = Komento::getModel( 'actions' );
			$socialHelper = Komento::getHelper( 'social' );

			// parse comments HTML
			$row->comment = self::parseComment( $row->comment );

			// author's object
			$row->author = Komento::getProfile( $row->created_by );

			// don't convert for guest
			if( $row->created_by != 0 && $row->created_by != $row->author->id )
			{
				if( $config->get( 'enable_orphanitem_convert' ) )
				{
					KomentoCommentHelper::convertOrphanitem( $row->id );
				}
			}

			if( $row->created_by != 0 )
			{
				switch( $config->get( 'name_type' ) )
				{
					case 'username':
						// force username
						$row->name = $row->author->getUsername();
						break;
					case 'name':
						$row->name = $row->author->getName();
						break;
					case 'default':
					default:
						// default name to profile if name is null
						if( empty( $row->name ) )
						{
							$row->name = $row->author->getName();
						}
						break;
				}
			}
			else
			{
				if( empty( $row->name ) )
				{
					$row->name = JText::_( 'COM_KOMENTO_GUEST' );
				}
				else
				{
					if( $config->get( 'guest_label' ) )
					{
						$row->name = JText::_( 'COM_KOMENTO_GUEST' ) . ' - '. $row->name;
					}
				}
			}

			// set datetime
			if( $config->get( 'enable_lapsed_time' ) )
			{
				$row->created = KomentoDateHelper::getLapsedTime( $row->unformattedDate );
			}
			else
			{
				$dateformat = $config->get( 'date_format' );
				$row->created = KomentoDateHelper::toFormat( KomentoDateHelper::dateWithOffSet( $row->created ), $dateformat );
				// $row->created = Komento::getDate( $row->created )->toFormat( $dateformat );
			}

			// get actions likes
			$row->likes = $actionsModel->countAction( 'likes', $row->id );

			// get user liked
			$row->liked = $actionsModel->liked( $row->id, $user );

			// get user reported
			$row->reported = $actionsModel->reported( $row->id, $user );
		}
		else
		{
			// backend

			// format comments
			$row->comment = nl2br( Komento::getHelper( 'comment' )->parseBBCode( $row->comment ) );

			$row->created = KomentoDateHelper::dateWithOffSet( $row->created );
		}

		$row->processed = true;

		return $row;
	}

	static public function parseComment( $comment )
	{
		$config = Komento::getConfig();

		// word censoring
		if( $config->get( 'filter_word' ) )
		{
			$comment = self::parseCensor( $comment );
		}

		// parseBBcode to HTML
		$comment = self::parseBBCode( $comment );

		// parse newline to br tags
		// $comment = nl2br( $comment );

		return $comment;
	}

	public static function parseCensor($text)
	{
		// Komento::getHelper('filter');
		// $filterHelper = new KomentoFilterHelper;

		$filterHelper = Komento::getHelper('filter');
		$config = Komento::getConfig();

		$textToBeFilter	= explode(',', $config->get('filter_word_text'));
		// lets do some AI here. for each string, if there is a space,
		// remove the space and make it as a new filter text.
		if( count($textToBeFilter) > 0 )
		{
			$newFilterSet   = array();
			foreach( $textToBeFilter as $item)
			{
				$item = trim( $item );
				if( JString::stristr($item, ' ') !== false )
				{
					$newKeyWord		= JString::str_ireplace(' ', '', $item);
					$newFilterSet[]	= $newKeyWord;
				}
			}

			if( count($newFilterSet) > 0 )
			{
				$tmpNewFitler		= array_merge($textToBeFilter, $newFilterSet);
				$textToBeFilter		= array_unique($tmpNewFitler);
			}
		}

		$filterHelper->strings	= $textToBeFilter;
		$filterHelper->text		= $text;
		return $filterHelper->filter();
	}

	public static function parseBBCode($text)
	{
		$config = Komento::getConfig();
		$nofollow = $config->get( 'links_nofollow' ) ? ' rel="nofollow"' : '';

		$maxdimension = '';
		if( $config->get( 'max_image_width' ) || $config->get( 'max_image_height' ) )
		{
			$maxdimension = ' style="';

			if( $config->get( 'max_image_width' ) )
			{
				$maxdimension .= 'max-width:' . $config->get( 'max_image_width' ) . 'px;';
			}

			if( $config->get( 'max_image_height' ) )
			{
				$maxdimension .= 'max-height:' . $config->get( 'max_image_width' ) . 'px;';
			}

			$maxdimension .= '"';
		}

		// Converts all html entities properly
		$text	= htmlspecialchars( $text , ENT_NOQUOTES );

		$text	= trim($text);

		$text = preg_replace_callback('/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms', array('KomentoCommentHelper','escape') , $text );

		// avoid smileys in pre tag gets replaced
		$text = KomentoCommentHelper::encodePre( $text );

		// change new line to br (without affecting pre)
		$text	= nl2br( $text );

		// BBCode to find...
		$in = array( 	 '/\[b\](.*?)\[\/b\]/ms',
						 '/\[i\](.*?)\[\/i\]/ms',
						 '/\[u\](.*?)\[\/u\]/ms',
						 '/\[img\](.*jpg|JPG|jpeg|JPEG|png|PNG|gif|GIF)+.*\[\/img\]/ms',
						 '/\[email\](.*?)\[\/email\]/ms',
						 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
						 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
						 '/\[quote\](.*?)\[\/quote\]/ms'
						 // '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
						 // '/\[list\](.*?)\[\/list\]/ms',
						 // '/\[\*\](.*?)\[\/\*\]/ms'
		);
		// And replace them by...
		$out = array(	 '<strong>\1</strong>',
						 '<em>\1</em>',
						 '<u>\1</u>',
						 '<img src="\1" alt="\1"' . $maxdimension . ' />',
						 '<a target="_blank" href="mailto:\1"' . $nofollow . '>\1</a>',
						 '<span style="font-size:\1%">\2</span>',
						 '<span style="color:\1">\2</span>',
						 '<blockquote>\1</blockquote>'
						 // '<ol start="\1">\2</ol>',
						 // '<ul>\1</ul>',
						 // '<li>\1</li>'
		);

		// strip out bbcode data first
		$tmp = preg_replace( $in , '' , $text );

		// strip out bbcode url data
		$urlin = '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms';
		$tmp = preg_replace($urlin, '', $tmp);

		// strip out video links too
		$tmp = Komento::getHelper('videos')->strip($tmp);

		// replace url
		if ($config->get('auto_hyperlink')) {
			$text = self::replaceURL($tmp, $text);
		}

		// replace video links
		if ($config->get('allow_video')) {
			$text = Komento::getHelper('Videos')->replace($text);
		} else {
			$text = Komento::getHelper('Videos')->strip($text);
		}

		// replace bbcode with html
		$text = preg_replace($in, $out, $text);

		// Replace url bbcode with html
		$text = self::replaceBBUrl($text);

		// manual fix for unwrapped li issue
		$text = self::replaceBBList($text);

		$smileyin = array();
		$smileyout = array();

		if( $config->get( 'bbcode_smile' ) )
		{
			$smileyin[] = ':)';
			$smileyout[] = '<img alt=":)" style="width:16px; height:16px;" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-smile.png" />';
		}
		if( $config->get( 'bbcode_happy' ) )
		{
			$smileyin[] = ':D';
			$smileyout[] = '<img alt=":D" style="width:16px; height:16px;" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-happy.png" />';
		}
		if( $config->get( 'bbcode_surprised' ) )
		{
			$smileyin[] = ':o';
			$smileyout[] = '<img alt=":o" style="width:16px; height:16px;" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-surprised.png" />';
		}
		if( $config->get( 'bbcode_tongue' ) )
		{
			$smileyin[] = ':p';
			$smileyout[] = '<img alt=":p" style="width:16px; height:16px;" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-tongue.png" />';
		}
		if( $config->get( 'bbcode_unhappy' ) )
		{
			$smileyin[] = ':(';
			$smileyout[] = '<img alt=":(" style="width:16px; height:16px;" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-unhappy.png" />';
		}
		if( $config->get( 'bbcode_wink' ) )
		{
			$smileyin[] = ';)';
			$smileyout[] = '<img alt=";)" style="width:16px; height:16px;" class="kmt-emoticon" src="'.KOMENTO_EMOTICONS_DIR.'emoticon-wink.png" />';
		}

		// add in custom smileys
		$smileycode = $config->get( 'smileycode' );
		$smileypath = $config->get( 'smileypath' );

		if( is_array( $smileycode ) && is_array( $smileypath ) )
		{
			foreach( $smileycode as $index => $code )
			{
				if( !empty( $code ) && !empty( $smileypath[$index] ) )
				{
					$smileyin[] = $code;
					$smileyout[] = '<img alt="' . $code . '" class="kmt-emoticon" src="' . $smileypath[$index] . '" />';
				}
			}
		}

		$text = str_replace($smileyin, $smileyout, $text);

		// done parsing emoticons and bbcode, decode pre text back
		$text = KomentoCommentHelper::decodePre( $text );

		// paragraphs
		$text = str_replace("\r", "", $text);
		$text = "<p>".preg_replace("/(\n){2,}/", "</p><p>", $text)."</p>";


		// $text = preg_replace_callback('/<pre>(.*?)<\/pre>/ms', "removeBr", $text);
		// $text = preg_replace('/<p><pre(.*)>(.*?)<\/pre><\/p>/ms', "<pre\\1>\\2</pre>", $text);

		$text = preg_replace_callback('/<ul>(.*?)<\/ul>/ms', array('KomentoCommentHelper','removeBr'), $text);
		// fix [list] within [*] causing dom errors
		$text = preg_replace('/<li>(.*?)<ul>(.*?)<\/ul>(.*?)<\/li>/ms', "\\1<ul>\\2</ul>\\3", $text);
		$text = preg_replace('/<p><ul>(.*?)<\/ul><\/p>/ms', "<ul>\\1</ul>", $text);

		return $text;
	}

	public static function replaceURL( $tmp , $text )
	{
		$config = Komento::getConfig();
		$nofollow = $config->get( 'links_nofollow' ) ? ' rel="nofollow"' : '';

		// $pattern    = '@(https?://[-\w\.]+(:\d+)?(/([\w/_\.-]*(\?\S+)?)?)?)@';
		$pattern = '@(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';

		preg_match_all( $pattern , $tmp , $matches );

		if( isset( $matches[ 0 ] ) && is_array( $matches[ 0 ] ) )
		{
			foreach( $matches[ 0 ] as $match )
			{
				$text = str_ireplace( $match , '<a target="_blank" href="' . $match . '"' . $nofollow . '>' . $match . '</a>' , $text );

			}
		}
		return $text;
	}

	/**
	 * Replaces the bbcode url tag
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function replaceBBUrl($text)
	{
		$config = Komento::getConfig();
		$nofollow = $config->get('links_nofollow') ? ' rel="nofollow"' : '';

		$pattern = '/\[url\="?(.*?)"\](.*?)\[\/url\]/ms';

		preg_match_all($pattern, $text, $matches);

		if (!empty($matches)) {

			$sources = array_shift($matches);

			$urls = $matches[0];
			$txts = $matches[1];

			for( $i = 0; $i < count( $sources ); $i++ )
			{
				$source = $sources[$i];

				if( !empty( $source ) )
				{
					$url = $urls[$i];
					// prevent user add javascript in the url element
					$segments = explode(' ', $url);
					$url = $segments[0];

					$txt = $txts[$i];

					if( stripos( $url, 'http://' ) !== 0 && stripos( $url, 'https://' ) !== 0 && stripos( $url, 'ftp://' ) !== 0 )
					{
						$url = 'http://' . $url;
					}

					$replace = '<a target="_blank" href="' . $url . '"' . $nofollow . '>' . $txt . '</a>';

					$text = str_ireplace( $source, $replace, $text );
				}
			}
		}

		return $text;
	}

	public static function replaceBBList( $text )
	{
		// 1. Fix invalid nesting issue with [*] and [list]
		// 2. Create a temporary text with all [list] block removed
		// 3. Wrap all remaining [*] blocks with [list] block in the temporary text
		// 4. Convert [list] and [*] blocks on the original text

		// Step 1
		$pattern = '/\[\*\](.*?)\[list\=(.*?)\](.*?)\[\/list\](.*?)\[\/\*\]/ms';
		$replace = "\\1[list=\\2]\\3[/list]\\4";
		$text = preg_replace( $pattern, $replace, $text );

		$pattern = '/\[\*\](.*?)\[list\](.*?)\[\/list\](.*?)\[\/\*\]/ms';
		$replace = "\\1[list]\\2[/list]\\3";
		$text = preg_replace( $pattern, $replace, $text );

		// Step 2
		$bblist = array(
			'/\[list\=(.*?)\](.*?)\[\/list\]/ms',
			'/\[list\](.*?)\[\/list\]/ms'
		);

		$tmp = preg_replace( $bblist, '', $text );

		// Step 3
		$pattern = '/\[\*\](.*?)\[\/\*\]/ms';

		preg_match_all( $pattern, $tmp, $matches );

		if( !empty( $matches ) )
		{
			$sources = array_shift( $matches );

			if( !empty( $sources ) )
			{
				foreach( $sources as $source )
				{
					$replace = '[list]' . $source . '[/list]';

					$text = str_ireplace( $source, $replace, $text );
				}
			}
		}

		// Step 4
		$in = array(
			'/\[list\=(.*?)\](.*?)\[\/list\]/ms',
			'/\[list\](.*?)\[\/list\]/ms',
			'/\[\*\](.*?)\[\/\*\]/ms'
		);

		$out = array(
			'<ol start="\1">\2</ol>',
			'<ul>\1</ul>',
			'<li>\1</li>'
		);

		$text = preg_replace( $in, $out, $text );

		return $text;
	}

	public static function escape($s) {
		$code = $s[3];

		$code = str_replace("[", "&#91;", $code);
		$code = str_replace("]", "&#93;", $code);

		$brush  = isset( $s[2] ) && !empty( $s[2] ) ? $s[2] : 'xml';
		$code	= html_entity_decode( $code );
		$code	= Komento::getHelper( 'String' )->escape( $code );

		if( $brush != '' )
		{
			$result = '<pre><code class="language-' . htmlspecialchars( $brush ) . '">' . $code . '</code></pre>';

		}
		else
		{
			$result = '<pre><code>' . $code . '</code></pre>';
		}

		return $result;
	}

	public static function encodePre( $text ) {
		$pattern = '/<pre.*?>(.*?)<\/pre>/s';
		preg_match_all( $pattern , $text , $matches );

		if( isset( $matches[ 0 ] ) && is_array( $matches[ 0 ] ) )
		{
			foreach( $matches[ 1 ] as $match )
			{
				$text = str_ireplace( $match , base64_encode( $match ), $text );
			}
		}

		return $text;
	}

	public static function decodePre( $text ) {
		$pattern = '/<pre.*?>(.*?)<\/pre>/s';
		preg_match_all( $pattern , $text , $matches );

		if( isset( $matches[ 0 ] ) && is_array( $matches[ 0 ] ) )
		{
			foreach( $matches[ 1 ] as $match )
			{
				$text = str_ireplace( $match , base64_decode( $match ) , $text );
			}
		}

		return $text;
	}

	public static function removeBr($s) {
		return str_replace("<br />", "", $s[0]);
	}

	public static function convertOrphanitem( $id )
	{
		$config = Komento::getConfig();

		$comment = Komento::getTable( 'comments' );
		$comment->load( $id );
		$comment->created_by = $config->get( 'orphanitem_ownership' );
		$comment->store();

		return true;
	}
}

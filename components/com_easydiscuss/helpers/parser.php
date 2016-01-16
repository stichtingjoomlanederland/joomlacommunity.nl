<?php
// ----------------------------------------------------------------------------
// markItUp! BBCode Parser
// v 1.0.6
// Dual licensed under the MIT and GPL licenses.
// ----------------------------------------------------------------------------
// Copyright (C) 2009 Jay Salvat
// http://www.jaysalvat.com/
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ----------------------------------------------------------------------------
// Thanks to Arialdo Martini, Mustafa Dindar for feedbacks.
// ----------------------------------------------------------------------------


/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

define ("EMOTICONS_DIR", DISCUSS_MEDIA_URI . '/images/markitup/');

require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';

class EasyDiscussParser
{
	/**
	 * Main bbcode processing here.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function bbcode( $text , $debug = false )
	{
		// $text	= htmlspecialchars($text , ENT_NOQUOTES );
		$text	= trim( $text );

		// We need to escape the content to avoid xss attacks
		$text 			= DiscussHelper::getHelper( 'String' )->escape( $text );


		// Replace [code] blocks
		$text 			= self::replaceCodes( $text );

		// BBCode to find...
		$bbcodeSearch = array( 	 '/\[b\](.*?)\[\/b\]/ims',
						 '/\[i\](.*?)\[\/i\]/ims',
						 '/\[u\](.*?)\[\/u\]/ims',
						 '/\[img\](.*?)\[\/img\]/ims',
						 '/\[email\](.*?)\[\/email\]/ims',
						 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ims',
						 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ims',
						 '/\[quote]([^\[\/quote\]].*?)\[\/quote\]/ims',
						 '/\[quote](.*?)\[\/quote\]/ims',
						 '/\[list\=(.*?)\](.(\[\*\])+.*?)\[\/list\]/ims',
						 '/\[list\](.(\[\*\])+.*?)\[\/list\]/ims',
						 '/\[\*\]\s?(.*?)\n/ims',
						 '/\[\*\]\s?(.*?)/ims',
						 '/\[font\="?(.*?)"?\](.*?)\[\/font\]/ims',
						 '/\[font]([^\[\/font\]].*?)\[\/font\]/ims',
						 '/\[center\](.*?)\[\/center\]/ims',
		);

		// And replace them by...
		$bbcodeReplace = array(	 '<strong>\1</strong>',
						 '<em>\1</em>',
						 '<u>\1</u>',
						 '<img src="\1" alt="\1" />',
						 '<a href="mailto:\1">\1</a>',
						 '<span style="font-size:\1%">\2</span>',
						 '<span style="color:\1">\2</span>',
						 '<blockquote>\1</blockquote>',
						 '<blockquote>\1</blockquote>',
						 '<ol start="\1">\2</ol>',
						 '<ul>\1</ul>',
						 '<li>\1</li>',
						 '<li>\1</li>',
						 '<font face="\1">\2</font>',
						 '<font face="\1">\2</font>',
						 '<center>\1</center>'
		);

		//$text .= "\n";

		// @rule: Replace URL links.
		// We need to strip out bbcode's data first.
		$tmp	= preg_replace( $bbcodeSearch , '' , $text );

		// Replace video codes
		$tmp	= DiscussHelper::getHelper( 'Videos' )->strip( $tmp );

		// @rule: Replace video links
		$text	= DiscussHelper::getHelper( 'Videos' )->replace( $text );



		// -start
		// Need to decode if not the html special chars will get detect as smiley
		// A hidden &quot;) will translate into wink smiley
		// $text = htmlspecialchars_decode( $text );
		// $text = str_replace($in, $out, $text);
		// -end

		// we treat the quote abit special here for the nested tag.
		$parserUtil = new EasyDiscussParserUtilities( 'quote' );
		$text	= $parserUtil->parseTagsRecursive( $text );

		// special treatment to UL and LI. Need to do this step 1st before send for replacing the rest bbcodes. @sam
		$text	= EasyDiscussParserUtilities::parseListItems( $text );

		// Replace bbcodes
		$text 	= preg_replace( $bbcodeSearch , $bbcodeReplace, $text);

		// Urls have special treatments
		$text	= self::replaceBBCodeURL( $text );

		// Replace URLs ! important, we only do this url replacement after the bbcode url processed. @sam at 07 Jan 2013
		$text	= DiscussHelper::getHelper( 'URL' )->replace( $tmp , $text );

		// Replace smileys before anything else
		$text 			= self::replaceSmileys( $text );


		// Auto detect email address in content and link it
		// preg_match_all("/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i", $text, $matches);

		// if( !empty( $matches ) )
		// {
		// 	$text = str_replace($matches[0], '<a href="mailto:'. $matches[0] .'">' . $matches[0] . '</a>', $text);
		// }

		//$text = str_replace( 'EXCLUDE_HERE', $syntaxHighlighterContent[2], $text );



		return $text;
	}

	public static function replaceSmileys( $text )
	{
		// Smileys to find...
		$in = array( 	 ':D',
						 ':)',
						 ':o',
						 ':p',
						 ':(',
						 ';)'
		);
		// And replace them by...
		$out = array(	 '<img alt=":D" class="bb-smiley" src="'.EMOTICONS_DIR.'emoticon-happy.png" />',
						 '<img alt=":)" class="bb-smiley" src="'.EMOTICONS_DIR.'emoticon-smile.png" />',
						 '<img alt=":o" class="bb-smiley" src="'.EMOTICONS_DIR.'emoticon-surprised.png" />',
						 '<img alt=":p" class="bb-smiley" src="'.EMOTICONS_DIR.'emoticon-tongue.png" />',
						 '<img alt=":(" class="bb-smiley" src="'.EMOTICONS_DIR.'emoticon-unhappy.png" />',
						 '<img alt=";)" class="bb-smiley" src="'.EMOTICONS_DIR.'emoticon-wink.png" />'
		);

		$text 	= str_replace( $in , $out , $text );

		return $text;
	}

	public static function replaceBBCodeURL( $text )
	{
		$config			= DiscussHelper::getConfig();

		//we need to make sure no special characters at this points
		$text = htmlspecialchars_decode($text, ENT_QUOTES);

		preg_match_all( '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ims', $text, $matches );

		if( !empty( $matches ) && isset( $matches[ 0 ] ) && !empty( $matches[ 0 ] ) )
		{
			// Get the list of url tags
			$urlTags 	= $matches[ 0 ];
			$urls 		= $matches[ 1 ];
			$titles 	= $matches[ 2 ];

			$total 		= count( $urlTags );

			for( $i = 0; $i < $total; $i++ )
			{
				$url 	= $urls[ $i ];

				if( stristr( $url , 'http://' ) === false && stristr( $url , 'https://' ) === false && stristr( $url , 'ftp://' ) === false )
				{
					$url	= 'http://' . $url;
				}

				$targetBlank	= $config->get( 'main_link_new_window' ) ? ' target="_blank"' : '';
				$text			= str_ireplace( $urlTags[ $i ] , '<a href="' . $url . '"' . $targetBlank . '>' . $titles[ $i ] . '</a>' , $text );
			}
		}

		return $text;
	}

	public static function removeBr($s)
	{
		// $string = str_replace("<br />", "", $s[0]);
		// $string = str_replace("<br>", "", $s[0]);

		$string = strip_tags($s[0], '<pre></pre>');
		return $string;
	}

	public static function removeNewline($s) {
		return str_replace("\r\n", "", $s[0]);
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function replaceCodes( $text , $debug = false )
	{
		// @rule: Replace [code type=&quot*&quot]*[/code]
		$codesPattern	= '/\[code( type=&quot;(.*?)&quot;)?\](.*?)\[\/code\]/ms';
		$text			= preg_replace_callback( $codesPattern , array( 'EasyDiscussParser' , 'processCodeBlocks' ) , $text );

		// @rule: Replace [code type="*"]*[/code]
		$codesPattern	= '/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms';
		$text			= preg_replace_callback( $codesPattern , array( 'EasyDiscussParser' , 'processCodeBlocks' ) , $text );

		return $text;
	}

	/**
	 * Replace [code] blocks with prism.js compatibility
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The contents
	 * @return
	 */
	public static function processCodeBlocks( $blocks )
	{
		$code 	= $blocks[ 3 ];

		// Remove break tags
		$code	= str_ireplace( "<br />" , "" , $code );

		$code	= str_replace( "[" , "&#91;" , $code);
		$code	= str_replace( "]" , "&#93;" , $code);

		// Determine the language type
		$language 	= isset( $blocks[ 2 ] ) && !empty( $blocks[ 2 ] ) ? $blocks[ 2 ] : 'markup';

		// Fix legacy code blocks
		if( $language == 'xml' || $language == 'html' )
		{
			$language 	= 'markup';
		}

		// Because the text / contents are already escaped, we need to revert back to the original html codes only
		// for the codes.
		$code 	= html_entity_decode( $code );

		// Fix html codes not displaying correctly
		$code   = htmlspecialchars($code , ENT_NOQUOTES );

		return '<pre class="line-numbers"><code class="language-' . $language . '">'.$code.'</code></pre>';
	}

	public static function removeCodes( $content )
	{
		$codesPattern	= '/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms';

		return preg_replace( $codesPattern , '' , $content );
	}

	public static function filter($text)
	{
		$text	= htmlspecialchars($text , ENT_NOQUOTES );
		$text	= trim($text);

		// @rule: Replace [code]*[/code]
		$text = preg_replace_callback('/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms', array( 'EasyDiscussParser' , 'replaceCodes' ) , $text );

		// BBCode to find...
		$bbcodeSearch = array( 	 '/\[b\](.*?)\[\/b\]/ims',
						 '/\[i\](.*?)\[\/i\]/ims',
						 '/\[u\](.*?)\[\/u\]/ims',
						 '/\[img\](.*?)\[\/img\]/ims',
						 '/\[email\](.*?)\[\/email\]/ims',
						 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ims',
						 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ims',
						 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ims',
						 '/\[quote](.*?)\[\/quote\]/ims',
						 '/\[list\=(.*?)\](.(\[\*\])+.*?)\[\/list\]/ims',
						 '/\[list\](.(\[\*\])+.*?)\[\/list\]/ims',
						 '/\[\*\]\s?(.*?)\n/ims'
		);

		// @rule: Replace URL links.
		// We need to strip out bbcode's data first.
		$text	= preg_replace( $bbcodeSearch , '' , $text );
		$text	= DiscussHelper::getHelper( 'URL' )->replace( $text , $text );


		// Smileys to find...
		$in = array( 	 ':)',
						 ':D',
						 ':o',
						 ':p',
						 ':(',
						 ';)'
		);
		// And replace them by...
		$out = array(	 '<img alt=":)" src="'.EMOTICONS_DIR.'emoticon-smile.png" />',
						 '<img alt=":D" src="'.EMOTICONS_DIR.'emoticon-happy.png" />',
						 '<img alt=":o" src="'.EMOTICONS_DIR.'emoticon-surprised.png" />',
						 '<img alt=":p" src="'.EMOTICONS_DIR.'emoticon-tongue.png" />',
						 '<img alt=":(" src="'.EMOTICONS_DIR.'emoticon-unhappy.png" />',
						 '<img alt=";)" src="'.EMOTICONS_DIR.'emoticon-wink.png" />'
		);
		$text = str_replace($in, $out, $text);

		// now we need to decode the the special html chars back to original chars.
		$text = html_entity_decode( $text );

		return $text;
	}

	/**
	 * Converts html codes to bbcode
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The text to lookup for
	 * @return	string 	The proper contents in bbcode format.
	 */
	public static function html2bbcode( $text )
	{
		if( (stripos($text, '<p') === false) && (stripos($text, '<div') === false) &&  (stripos($text, '<br') === false))
		{
			return $text;
		}

		$bbcodeSearch = array(
			'/<strong>(.*?)<\/strong>/ims',
			'/<b>(.*?)<\/b>/ims',
			'/<big>(.*?)<\/big>/ims',
			'/<em>(.*?)<\/em>/ims',
			'/<i>(.*?)<\/i>/ims',
			'/<u>(.*?)<\/u>/ims',
			'/<img.*?src=["|\'](.*?)["|\'].*?\>/ims',
			'/<[pP]>/ims',
			'/<\/[pP]>/ims',
			'/<blockquote>(.*?)<\/blockquote>/ims',
			'/<ol.*?\>(.*?)<\/ol>/ims',
			'/<ul.*?\>(.*?)<\/ul>/ims',
			'/<li.*?\>(.*?)<\/li>/ims',
			'/<a.*?href=["|\']mailto:(.*?)["|\'].*?\>.*?<\/a>/ims',
			'/<a.*?href=["|\'](.*?)["|\'].*?\>(.*?)<\/a>/ims',
			'/<pre.*?\>(.*?)<\/pre>/ims',
		);

		$bbcodeReplace = array(
			'[b]\1[/b]',
			'[b]\1[/b]',
			'[b]\1[/b]',
			'[i]\1[/i]',
			'[i]\1[/i]',
			'[u]\1[/u]',
			'[img]\1[/img]',
			'',
			'<br />',
			'[quote]\1[/quote]',
			'[list=1]\1[/list]',
			'[list]\1[/list]',
			'[*] \1',
			'[email]\1[/email]',
			'[url="\1"]\2[/url]',
			'[code type="xml"]\1[/code]',
		);

		// Smileys to find...
		$out = array( 	 ':)',
						 ':D',
						 ':o',
						 ':p',
						 ':(',
						 ';)'
		);
		// And replace them by...
		$in = array(	 '<img alt=":)" src="'.EMOTICONS_DIR.'emoticon-smile.png" />',
						 '<img alt=":D" src="'.EMOTICONS_DIR.'emoticon-happy.png" />',
						 '<img alt=":o" src="'.EMOTICONS_DIR.'emoticon-surprised.png" />',
						 '<img alt=":p" src="'.EMOTICONS_DIR.'emoticon-tongue.png" />',
						 '<img alt=":(" src="'.EMOTICONS_DIR.'emoticon-unhappy.png" />',
						 '<img alt=";)" src="'.EMOTICONS_DIR.'emoticon-wink.png" />'
		);

		//@samhere
		//$text = str_replace($in, $out, $text);

		// Replace bbcodes
		$text	= strip_tags($text, '<br><strong><em><u><img><a><p><blockquote><ol><ul><li><b><big><i><pre>');
		$text	= preg_replace( $bbcodeSearch , $bbcodeReplace, $text);
		$text	= str_ireplace('<br />', "\r\n", $text);
		$text	= str_ireplace('<br>', "\r\n", $text);

		return $text;
	}


	public static function smiley2bbcode( $content )
	{

		$pattern		= '/<img.*?src=["|\'](.*?)["|\'].*?\>/';
		preg_match_all( $pattern , $content , $matches );

		if( isset( $matches[0] ) &&	count( $matches[0] ) > 0 )
		{
			for( $i = 0; $i < count( $matches[0] ); $i++ )
			{
				$imgTag = $matches[0][$i];
				$imgSrc = $matches[1][$i];

				if( strpos($imgSrc, 'emoticon-smile.png') !== false )
				{
					$content	= str_replace( $imgTag , ':)' , $content);
					continue;
				}

				if( strpos($imgSrc, 'emoticon-happy.png') !== false )
				{
					$content	= str_replace( $imgTag , ':D' , $content);
					continue;
				}

				if( strpos($imgSrc, 'emoticon-surprised.png') !== false )
				{
					$content	= str_replace( $imgTag , ':o' , $content);
					continue;
				}

				if( strpos($imgSrc, 'emoticon-tongue.png') !== false )
				{
					$content	= str_replace( $imgTag , ':p' , $content);
					continue;
				}

				if( strpos($imgSrc, 'emoticon-unhappy.png') !== false )
				{
					$content	= str_replace( $imgTag , ':(' , $content);
					continue;
				}

				if( strpos($imgSrc, 'emoticon-wink.png') !== false )
				{
					$content	= str_replace( $imgTag , ';)' , $content);
					continue;
				}

			}
		}

		return $content;
	}



	public static function removeBrTag( $content )
	{
		$content	= nl2br($content);

		//Remove BR in pre tag
		$content = preg_replace_callback('/<pre.*?\>(.*?)<\/pre>/ims', array( 'EasyDiscussParser' , 'removeBr' ) , $content );

		return $content;
	}

	public static function quoteBbcode( $text )
	{
		// BBCode to find...
		$bbcodeSearch = array( 	 '/\[b\](.*?)\[\/b\]/ims',
						 '/\[i\](.*?)\[\/i\]/ims',
						 '/\[u\](.*?)\[\/u\]/ims',
						 '/\[img\](.*?)\[\/img\]/ims',
						 '/\[email\](.*?)\[\/email\]/ims',
						 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ims',
						 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ims',
						 '/\[list\=(.*?)\](.*?)\[\/list\]/ims',
						 '/\[list\](.*?)\[\/list\]/ims'
		);

		// And replace them by...
		$addQuote = array('[quote][b]\1[/b][/quote]',
						 '[quote][i]\1[/i][/quote]',
						 '[quote][u]\1[/u][/quote]',
						 '[quote][img]\1[/img][/quote]',
						 '[quote][email]\1[/email][/quote]',
						 '[quote][size="\1"]\2[/size][/quote]',
						 '[quote][color="\1"]\2[/color][/quote]',
						 '[quote][list="\1"]\2[/list][/quote]',
						 '[quote][list]\1[/list][/quote]'
					);

		$quoteSearch = array(	'/\[quote](.*?)\[\/quote\]/ims',

		);

		$quoteReplace = array(
						 '<blockquote>\1</blockquote>',
		);

		// Replace bbcodes
		$text = preg_replace( $bbcodeSearch , $addQuote, $text);
		$text = preg_replace( $quoteSearch, $quoteReplace, $text );

		return $text;
	}
}

class EasyDiscussParserUtilities
{
	var $bbcode = '';

	public function __construct( $bbcode )
	{
		$this->bbcode = $bbcode;
	}

	public function parseTagsRecursive( $inputs )
	{
		$inputs = preg_replace('#\[quote\]#', '<blockquote>', $inputs );
		$inputs = preg_replace('#\[quote=(.+?)\]#', '<blockquote>', $inputs );
		$inputs = preg_replace('#\[quote=(.+?);(.+?)\]#', '<blockquote>', $inputs );
		$inputs = preg_replace('#\[quote=(.+?)\d+\]#', '<blockquote>', $inputs);
		$inputs = preg_replace('#\[/quote\]#', '</blockquote>', $inputs );

	  	return $inputs;
	 }

	// public function parseTagsRecursiveOld( $inputs )
	// {
	// 	// var_dump( $inputs );
	// 	$bbcode = $this->bbcode;

	// 	$bbcodeSearch = array('/quote/');

	// 	// And replace them by...
	// 	$bbcodeReplace = array('blockquote');

	// 	$htmlTagToUse   = preg_replace($bbcodeSearch,$bbcodeReplace, $bbcode);

	// 	$regex = '#\['.$bbcode.']((?:[^[]|\[(?!/?'.$bbcode.'])|(?R))+)\[/'.$bbcode.']#';

	//     if (is_array($inputs)) {
	// 		$inputs 	= '<' . $htmlTagToUse . '>' . $inputs[1] . '</' . $htmlTagToUse .'>';
	//     }

	// 	return preg_replace_callback( $regex , array( 'EasyDiscussParserUtilities' , 'parseTagsRecursive' ) , $inputs );

	// }

	public static function parseListItems( $content )
	{
		// BBCode to find...
		$bbcodeListItemsSearch = '#\[list.*?\](.*?)\[\/list\]#ims';

		// BBCode to find...
		$bbcodeLISearch = array(
			 '/\[\*\]\s?(.*?)\n/ims',
			 '/\[\*\]\s?(.*?)/ims'
		);

		// And replace them by...
		$bbcodeLIReplace = array(
			 '<li>\1</li>',
			 '<li>\1</li>'
		);

		// And replace them by...
		$bbcodeLIReplaceString = array(
			 '\1',
			 '\1'
		);

		// BBCode to find...
		$bbcodeULSearch = array(
			 '/\[list\=(.*?)\](.*?)\[\/list\]/ims',
			 '/\[list\](.*?)\[\/list\]/ims',
		);

		// And replace them by...
		$bbcodeULReplace = array(
			 '<ol start="\1">\2</ol>',
			 '<ul>\1</ul>'
		);

		// And replace them by...
		$bbcodeULReplaceString = array(
			 '\2',
			 '\1'
		);


		preg_match_all($bbcodeListItemsSearch, $content, $matches);


		if( $matches && count( $matches[0] ) > 0 )
		{
			foreach( $matches[0] as $match)
			{

				if( strpos($match, '[*]') !== false )
				{
					$text 	= preg_replace( $bbcodeULSearch , $bbcodeULReplace, $match);
					$text 	= preg_replace( $bbcodeLISearch , $bbcodeLIReplace, $text);
				}
				else
				{
					$text 	= preg_replace( $bbcodeULSearch , $bbcodeULReplaceString, $match);
				}

				$content = JString::str_ireplace( $match, $text, $content);
			}
		}

		// replace orphan [*] items
		$content 	= preg_replace( $bbcodeLISearch , $bbcodeLIReplaceString, $content);

		return $content;
	}



}

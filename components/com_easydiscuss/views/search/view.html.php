<?php
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

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewSearch extends EasyDiscussView
{
	public function display( $tmpl = null )
	{
		$document	= JFactory::getDocument();
		$mainframe	= JFactory::getApplication();
		$user		= JFactory::getUser();
		$config		= DiscussHelper::getConfig();
		$category	= JRequest::getInt( 'category_id' , 0 );

		// Add breadcrumbs
		$this->setPathway( JText::_('COM_EASYDISCUSS_SEARCH') );

		DiscussHelper::setPageTitle();
		// Set the meta of the page.
		DiscussHelper::setMeta();

		$query		= JRequest::getString( 'query' , '' );
		$limitstart	= null;
		$posts		= null;
		$pagination	= null;

		if(! empty($query))
		{
			$searchModel	= DiscussHelper::getModel( 'Search' );
			$posts 			= $searchModel->getData( true , 'latest' , null , 'allposts' , $category );
			$pagination 	= $searchModel->getPagination( 0 , 'latest' , 'allposts' , $category );

			$posts 			= DiscussHelper::formatPost( $posts , true );

			$badgesTable	= DiscussHelper::getTable( 'Profile' );

			if( count($posts) > 0 )
			{
				$searchworda = preg_replace('#\xE3\x80\x80#s', ' ', $query);
				$searchwords = preg_split("/\s+/u", $searchworda);
				$needle = $searchwords[0];
				$searchwords = array_unique($searchwords);

				for($i = 0; $i < count($posts); $i++ )
				{
					$row =& $posts[$i];

					if(! $row->isProtected() )
					{
						$introtext 	= strip_tags( $row->content );
						$introtext  = preg_replace( '/\s+/' , ' ' , $introtext );
						$pos 		= strpos( $introtext, $needle);

						if( $pos !== false )
						{
							$text   	= '...';
							$startpos 	= ( $pos - 10 ) >= 0 ? $pos - 10 : 0;
							//$endpos     = ( $pos - 10 ) >= 0 ? 10 : JString::strlen($needle) + 1;
							$endpos     = ( $pos - 10 ) >= 0 ? 10 : ($pos - $startpos);

							$front  	= JString::substr($introtext, $startpos, $endpos);

							if( JString::strlen( $introtext ) > $endpos )
							{
								$endpos     = $pos + JString::strlen($needle);
								$end    	= JString::substr($introtext, $endpos, 10);

								if( JString::strlen( $front ) > 0 )
								{
									$text  = $text . $front;
								}

								$text  = $text . $needle;

								if( JString::strlen( $end ) > 0 )
								{
									$text  = $text . $end . '...';
								}
							}
							else
							{
								$text  = $front;
							}

							$introtext  = $text;
						}
					}
					else
					{
						//password protected content.
						$introtext = $row->content; // when come to here, the content is already contain the password input form ( via formatPost function.)
					}

					//$introtext	= JString::substr($introtext, 0, $config->get( 'layout_introtextlength' ));

					$searchRegex = '#(';
					$x = 0;

					foreach ($searchwords as $k => $hlword)
					{
						$searchRegex .= ($x == 0 ? '' : '|');
						$searchRegex .= preg_quote($hlword, '#');
						$x++;
					}
					$searchRegex .= '(?!(?>[^<]*(?:<(?!/?a\b)[^<]*)*)</a>))#iu';


					$row->title		= preg_replace($searchRegex, '<span class="highlight">\0</span>', $row->title);
					$row->introtext	= $row->isProtected() ? $introtext :  preg_replace($searchRegex, '<span class="highlight">\0</span>', $introtext);

					//display password input form.
					if( $row->isProtected() )
					{
						$row->content = $row->content; // when come to here, the content is already contain the password input form ( via formatPost function.)
					}
					else
					{
						$row->content = preg_replace($searchRegex, '<span class="highlight">\0</span>', $introtext);
					}

					$badgesTable->load( $row->user->id );
					$row->badges = $badgesTable->getBadges();
				}
			}
		}

		$tpl = new DiscussThemes();
		$tpl->set( 'query'			, $query );
		$tpl->set( 'posts'			, $posts );
		$tpl->set( 'paginationType'	, DISCUSS_SEARCH_TYPE );
		$tpl->set( 'pagination'		, $pagination );
		$tpl->set( 'parent_id'		, $query );

		echo $tpl->fetch( 'search.php' );
	}
}

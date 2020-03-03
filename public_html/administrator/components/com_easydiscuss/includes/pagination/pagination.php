<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.html.pagination');

class EasyDiscussPagination extends EasyDiscuss
{
	public $pagination = null;
	public $total = null;
	public $limitstart = null;
	public $limit = null;
	public $prefix = null;

	public function __construct($opts = array())
	{
		$total = '';
		$limitstart = '';
		$limit = '';
		$prefix = '';


		if (isset($opts[0])) {
			$this->total = $opts[0];
		}

		if (isset($opts[1])) {
			$this->limitstart = $opts[1];
		}

		if (isset($opts[2])) {
			$this->limit = $opts[2];
		}

		if (isset($opts[3])) {
			$this->prefix = $opts[3];
		}

		$this->pagination = new JPagination($this->total, $this->limitstart, $this->limit, $this->prefix);
	}

	/**
	 * Proxy method to JPagination
	 *
	 * @since	4.0.19
	 * @access	public
	 */
	public function getPagesLinks($viewpage = 'index', $filtering = array(), $doReplace = false)
	{
		return $this->toHTML($viewpage, $filtering, $doReplace);
	}

	/**
	 * Get current page number
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPageNumber()
	{
		$data = $this->pagination->getData()->pages;

		foreach ($data as $page) {
			if ($page->active) {
				return $page->text;
			}
		}

		return false;
	}

	/**
	 * Retrieves the html block for pagination codes
	 *
	 * @since	4.0.17
	 * @access	public
	 */
	public function getListFooter($path = 'admin', $url = '')
	{
		// Retrieve pages data from Joomla itself.
		$theme = ED::themes();

		// If there's nothing here, no point displaying the pagination
		if ($this->pagination->total == 0) {
			return;
		}

		$data = $this->pagination->getData();

		$theme->set('data', $data);
		$theme->set('pagination', $this->pagination);

		$contents = $theme->output($path . '/pagination/default');

		return $contents;
	}

	/**
	 * Alias for JPagination
	 *
	 * @since	4.0.20
	 * @access	public
	 */
	public function setAdditionalUrlParam($key, $value)
	{
		return $this->pagination->setAdditionalUrlParam($key, $value);
	}

	/**
	 * Renders the pagination on the front end
	 *
	 * @since	4.0.17
	 * @access	public
	 */
	public function toHTML($viewpage = 'index', $filtering = array(), $doReplace = false)
	{
		$data = $this->pagination->getData();

		// Nothing to paginate.
		if (!$data->pages) {
			return;
		}

		$queries = '';
		if (!empty($filtering)) {
			
			if (isset($filtering['category_id']) && $filtering['category_id']) {
				
				if (is_array($filtering['category_id'])) {
					$filtering['category_id'] = $filtering['category_id'][0];
				}

				$queries .= '&layout=listings&category_id=' . $filtering['category_id'];
			}

			if (isset($filtering['filter'])) {
				$queries .= '&filter=' . $filtering['filter'];
			}

			if (isset($filtering['sort'])) {
				$queries .= '&sort=' .$filtering['sort'];
			}

			if (isset($filtering['status'])) {
				$queries .= '&status=' .$filtering['status'];
			}

			if (isset($filtering['query'])) {
				$queries .= '&query=' .$filtering['query'];
			}

			// profile
			if (isset($filtering['viewtype'])) {
				$queries .= '&viewtype=' .$filtering['viewtype'];
			}

			if (isset($filtering['id'])) {
				$queries .= '&id=' .$filtering['id'];
			}
		}

		if (!empty($data) && $doReplace) {
			$curPageLink = 'index.php?option=com_easydiscuss&view=' . $viewpage . $queries;

			// start
			if (!empty($data->start->link)) {
				$limitstart = (!empty($data->start->base)) ? '&limitstart=' . $data->start->base : '';
				$data->start->link = EDR::_($curPageLink . $limitstart);
			}

			foreach ($data->pages as $page) {
				
				if (!empty($page->link)) {
					$limitstart = (!empty($page->base)) ? '&limitstart=' . $page->base : '';
					$page->link = EDR::_($curPageLink . $limitstart);
				}
			}

			// newer link
			if (!empty($data->next->link)) {
				$limitstart = (!empty($data->next->base)) ? '&limitstart=' . $data->next->base : '';
				$data->next->link = EDR::_($curPageLink . $limitstart);
			}

			// older link
			if (!empty($data->previous->link)) {
				$limitstart = ( !empty($data->previous->base) ) ? '&limitstart=' . $data->previous->base : '';
				$data->previous->link = EDR::_($curPageLink . $limitstart);
			}

			// end
			if (!empty($data->end->link)) {
				$limitstart = (!empty($data->end->base)) ? '&limitstart=' . $data->end->base : '';
				$data->end->link = EDR::_($curPageLink . $limitstart);
			}

		}

		ob_start();
		?>
		<div class="o-pagination-wrap text-center t-lg-mt--xl">
			<ul class="o-pagination">
				<li class="disabled"><span><?php echo JText::_('COM_EASYDISCUSS_PAGINATION_PAGE');?> :</span></li>

				<?php if( $data->start->link ){ ?>
					<li class="older"><a href="<?php echo $data->start->link ?>" rel="nofollow"><i class="fa fa-fast-backward" title="<?php echo JText::_( 'COM_EASYDISCUSS_PAGINATION_OLDER' , true );?>"></i></a></li>
				<?php } ?>

				<?php if( $data->previous->link ){ ?>
					<li class="older"><a href="<?php echo $data->previous->link ?>" rel="nofollow"><i class="fa fa-backward" title="<?php echo JText::_( 'COM_EASYDISCUSS_PAGINATION_OLDER' , true );?>"></i></a></li>
				<?php } ?>

				<?php foreach( $data->pages as $page ){ ?>
				<?php 	if( $page->link ) { ?>
				<li><a href="<?php echo $page->link ?>" rel="nofollow"><?php echo $page->text;?></a></li>
				<?php 	} else { ?>
				<li class="active"><span><?php echo $page->text;?></span></li>
				<?php 	} ?>
				<?php } ?>

				<?php if( $data->next->link ){ ?>
					<li class="newer"><a href="<?php echo $data->next->link ?>" rel="nofollow"><i class="fa fa-forward" title="<?php echo JText::_( 'COM_EASYDISCUSS_PAGINATION_NEWER' , true );?>"></i></a></li>
				<?php } ?>

				<?php if( $data->end->link ){ ?>
					<li class="newer"><a href="<?php echo $data->end->link ?>" rel="nofollow"><i class="fa fa-fast-forward" title="<?php echo JText::_( 'COM_EASYDISCUSS_PAGINATION_NEWER' , true );?>"></i></a></li>
				<?php } ?>				
			</ul>
		</div>
		<?php
		$html	= ob_get_clean();
		return $html;
	}

	public function getCounter()
	{
		$start	= $this->limitstart + 1;
		$end	= $this->limitstart + $this->limit < $this->total ? $this->limitstart + $this->limit : $this->total;
		ob_start();
		?>



<div class="dc-pagination">
	<?php if( $this->total > 0 ){ ?>
	<b><?php echo $start;?></b> - <b><?php echo $end;?></b>
	<em class="ffg"><?php echo JText::_( 'of' );?></em>
	<b><?php echo $this->total;?></b>
	<?php } else { ?>
	<em class="ffg"><?php echo JText::_( 'No conversations yet' );?></em>
	<?php } ?>
</div>
		<?php
		$html	= ob_get_clean();
		return $html;
	}
}

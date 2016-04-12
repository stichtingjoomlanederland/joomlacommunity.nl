<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<?php if ($this->jversion->isCompatible('3.0')) { ?>
	<link rel="stylesheet" href="<?php echo JHtml::_('stylesheet', 'jui/bootstrap.min.css', array(), true, true); ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo JHtml::_('stylesheet', 'jui/bootstrap-responsive.min.css', array(), true, true); ?>" type="text/css" />
	<link rel="stylesheet" href="<?php echo JHtml::_('stylesheet', 'jui/bootstrap-extended.css', array(), true, true); ?>" type="text/css" />
	<?php } else { ?>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap-responsive.min.css" type="text/css" />
	<?php } ?>
	<title><?php echo JText::_('COM_RSFIREWALL_403_FORBIDDEN'); ?></title>
	<style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
      }
    </style>
</head>
<body>
	<div class="container">
		<div class="alert alert-error text-center">
			<h4><?php echo JText::_('COM_RSFIREWALL_403_FORBIDDEN'); ?></h4>
			<?php echo $this->reason; ?>
		</div>
	</div>
</body>
</html>
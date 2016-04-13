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
?>
<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_HELLO' ); ?>,<br /><br />
<?php echo JText::sprintf( 'COM_EASYDISCUSS_EMAILTEMPLATE_CONVERSATION' , $this->escape( $authorName ) ); ?><br />
<br />
<hr style="clear:both;margin:10px 0 15px;padding:0;border:0;border-top:1px solid #ddd" />
<img src="<?php echo $authorAvatar; ?>" width="80" alt="<?php echo $this->escape( $authorName ); ?>" style="width:80px;height:80px;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;float:left;margin:0 15px 0 0" />
<?php echo $content; ?>
<br style="clear:both" />
<br />
<div style="padding:20px;border-top:1px solid #ccc;padding:20px 0 10px;margin-top:20px;line-height:19px;color:#555;font-family:'Lucida Grande',Tahoma,Arial;font-size:12px;text-align:left">
	<div>
	<!--[if mso]>
	  <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo $conversationLink;?>" style="height:30px;padding-left:15px;padding-right:15px;v-text-anchor:middle;" arcsize="7%" strokecolor="#caa200" fillcolor="#ffcc00">
	    <w:anchorlock/>
	    <center style="color:#534200;font-family:sans-serif;font-size:13px;font-weight:bold;"><?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_READ_THIS_CONVERSATION' );?> &nbsp; &raquo;</center>
	  </v:roundrect>
	<![endif]-->
	<![if !mso]>
	<a href="<?php echo $conversationLink;?>"
	style="background-color:#ffcc00;border:1px solid #caa200;padding-left:15px;padding-right:15px;border-radius:2px;color:#534200;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:30px;text-align:center;text-decoration:none;-webkit-text-size-adjust:none;" target="_blank"><?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_READ_THIS_CONVERSATION' );?> &nbsp; &raquo;</a>
	<![endif]>
	</div>
</div>

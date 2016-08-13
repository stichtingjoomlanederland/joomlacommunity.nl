<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acyarchiveview">
<div><?php if($this->config->get('frontend_subject',1)){ ?><h1 class="contentheading<?php echo $this->values->suffix; ?>"><?php echo $this->mail->subject; ?>
	<?php if($this->frontEndManagement AND ($this->config->get('frontend_modif',1) || ($this->mail->userid == $this->my->id)) AND ($this->config->get('frontend_modif_sent',1) || empty($this->mail->senddate))){ ?>
		<a <?php if(JRequest::getCmd('tmpl') == 'component') echo 'target="_blank" '; ?> href="<?php echo acymailing_completeLink('frontnewsletter&task=edit&mailid='.$this->mail->mailid.'&listid='.$this->list->listid); ?>"><img class="icon16" src="<?php echo ACYMAILING_IMAGES ?>icons/icon-16-edit.png" alt="<?php echo JText::_('ACY_EDIT',true) ?>"/></a>
	<?php } ?>
</h1>
<?php } ?>
	<?php if($this->config->get('frontend_print',0) OR $this->config->get('frontend_pdf',0)) {
		$link = 'archive&task=view&mailid='.$this->mail->mailid.'-'.$this->mail->alias;
		$listid = JRequest::getCmd('listid');
		if(!empty($listid)) $link .= '&listid='.$listid;
		$key = JRequest::getCmd('key');
		if(!empty($key)) $link .= '&key='.$key; ?>
	<div align="right" style="float:right;">
		<table>
		<tr>
	<?php if(!ACYMAILING_J16 && $this->config->get('frontend_pdf',0)){?>
		<td class="buttonheading">
	<?php $pdfimage = JHTML::_('image.site',  'pdf_button.png', !ACYMAILING_J16 ? '/images/M_images/' : '/media/system/images/', NULL, NULL, JText::_( 'PDF' ) );
		$pdflink = acymailing_completeLink($link,true);
		$pdflink .= strpos($pdflink,'?') ? '&format=pdf' : '?format=pdf';
		?>
		<a href="<?php echo $pdflink; ?>" title="<?php echo JText::_( 'PDF' ); ?>" onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;" rel="nofollow"><?php echo $pdfimage; ?></a>
		</td>
	<?php }
		if($this->config->get('frontend_print',0)){?>
		<td class="buttonheading">
		<?php
		if(ACYMAILING_J30){
			$printimage = '<img src="'.ACYMAILING_IMAGES.'icons/icon-32-acyprint.png" alt="'.JText::_( 'ACY_PRINT',true ).'" />';
		}else{
			$printimage = JHTML::_('image.site',  'printButton.png', !ACYMAILING_J16 ? '/images/M_images/' : '/media/system/images/', NULL, NULL, JText::_( 'ACY_PRINT' ) );
		} ?>
		<a title="<?php echo JText::_( 'ACY_PRINT',true ); ?>" href="#" onclick="if(document.getElementById('iframepreview')){document.getElementById('iframepreview').contentWindow.focus();document.getElementById('iframepreview').contentWindow.print();}else{window.print();}return false;"><?php echo $printimage; ?></a>

		</td>
	<?php } ?>
		</tr></table>
	</div>
	<?php } ?>
</div>
<div class="newsletter_body" style="min-width:80%" id="newsletter_preview_area"><?php echo $this->mail->html ? $this->mail->body : nl2br($this->mail->altbody); ?></div>
<?php if(!empty($this->mail->attachments)){?>
<fieldset class="newsletter_attachments"><legend><?php echo JText::_( 'ATTACHMENTS' ); ?></legend>
<table>
	<?php foreach($this->mail->attachments as $attachment){
			echo '<tr><td><a href="'.$attachment->url.'" target="_blank">'.$attachment->name.'</a></td></tr>';
	}?>
</table>
</fieldset>
<?php }
	if($this->config->get('comments_feature') == 'jcomments'){
		$comments = ACYMAILING_ROOT.'components'.DS.'com_jcomments'.DS.'jcomments.php';
		if (file_exists($comments)) {
			require_once($comments);
			echo JComments::showComments($this->mail->mailid, 'com_acymailing', $this->mail->subject);
		}
	}elseif($this->config->get('comments_feature') == 'jomcomment'){
		$comments = ACYMAILING_ROOT.'plugins'.DS.'content'.DS.'jom_comment_bot.php';
		if (file_exists($comments)) {
			require_once($comments);
			echo jomcomment($this->mail->mailid, 'com_acymailing');
		}
	}elseif($this->config->get('comments_feature') == 'disqus'){
		$disqus_shortname = $this->config->get('disqus_shortname');
		if(!empty($disqus_shortname))
		{
			$lang = JFactory::getLanguage();
			$lang_shortcode = explode('-', $lang->getTag());
?>
			<div style="clear:both;"></div><div id="disqus_thread"></div>
			<script type="text/javascript">
				var disqus_identifier = "Joomla_Disqus_MAILID_<?php echo $this->mail->mailid; ?>";
				var disqus_shortname = "<?php echo $disqus_shortname; ?>";
				var disqus_config = function () {
					this.language = "<?php echo $lang_shortcode[0]; ?>";
				};
				(function() {
					var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
					dsq.src = "http://" + disqus_shortname + ".disqus.com/embed.js";
					(document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
				})();
			</script>
			<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
<?php
		}
	}elseif($this->config->get('comments_feature') == 'rscomments'){
		echo '{rscomments option="com_acymailing" id="'.$this->mail->mailid.'"}';
	}elseif($this->config->get('comments_feature') == 'komento'){
		require_once(JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_komento'.DIRECTORY_SEPARATOR.'bootstrap.php' );
		echo Komento::commentify('com_acymailing', $this->mail,array());
	}

?>
</div>

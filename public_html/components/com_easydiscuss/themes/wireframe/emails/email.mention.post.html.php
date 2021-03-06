<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<!-- title section -->
<tr>
    <td style="text-align: center;padding: 40px 10px 0;">
        <div style="margin-bottom:15px;">
            <div style="font-family:Arial;font-size:32px;font-weight:normal;color:#333;display:block; margin: 4px 0">
                <?php echo JText::_('COM_EASYDISCUSS_EMAILS_MENTIONED_NEW_TITLE') ?>
            </div>
        </div>
    </td>
</tr>

<!-- content section -->
<tr>
    <td style="text-align: center;font-size:12px;color:#888">
        <table align="center" border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed;width:100%;">
        <tr>
        <td align="center">
            <table width="540" cellspacing="0" cellpadding="0" border="0" align="center" style="table-layout:fixed;margin: 0 auto;">
                <tr>
                    <td>
                        <p style="text-align:left;">
                            <?php echo JText::_('COM_EASYDISCUSS_EMAILS_HELLO'); ?>
                        </p>
                        <p style="text-align:left;">
                            <?php echo JText::sprintf( 'COM_EASYDISCUSS_EMAILTEMPLATE_MENTION_POST', $authorName, $postTitle); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
        </tr>
        </table>
        <a style="
                display:inline-block;
                text-decoration:none;
                font-weight:bold;
                margin-top: 20px;
                padding:10px 15px;
                line-height:20px;
                color:#fff;font-size: 12px;
                background-color: #428bca;
                border-color: #357ebd;


                border-style: solid;
                border-width: 1px;

                border-radius:3px; -moz-border-radius:3px; -webkit-border-radius:3px;
                " href="<?php echo $postLink;?>"><?php echo JText::_('COM_EASYDISCUSS_EMAILTEMPLATE_READ_THIS_DISCUSSION');?></a>
    </td>
</tr>

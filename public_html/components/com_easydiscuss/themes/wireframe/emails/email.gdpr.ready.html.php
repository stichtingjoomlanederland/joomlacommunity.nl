<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
                <?php echo JText::_('COM_ED_EMAILS_GDPR_DOWNLOAD_HEADING') ?>
            </div>
            <div style="font-size:12px; color: #798796;font-weight:normal">
                <?php echo JText::_('COM_ED_EMAILS_GDPR_DOWNLOAD_SUBHEADING'); ?>
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
                    </td>
                </tr>
            </table>
            <table width="540" cellspacing="0" cellpadding="0" border="0" align="center" style="table-layout:fixed;margin: 20px auto 0;background-color:#f8f9fb;padding:15px 20px;">
                <tbody>
                    <tr>
                        <td valign="top">
                            <?php echo JText::sprintf('COM_ED_EMAILS_GDPR_DOWNLOAD_DESCRIPTION', $downloadLink); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
        </tr>
        </table>
    </td>
</tr>


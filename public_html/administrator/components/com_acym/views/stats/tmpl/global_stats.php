<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.6.1
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<div id="acym_stats_global">
    <?php
    if (empty($data['sentMails'])) {
        include __DIR__.DS.'global_stats_example.php';
    } else {
        include __DIR__.DS.'global_stats_data.php';
    }
    ?>
</div>
<?php acym_formOptions(); ?>


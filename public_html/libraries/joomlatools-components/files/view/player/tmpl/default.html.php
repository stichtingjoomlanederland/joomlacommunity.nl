<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2017 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/joomlatools-framework-files for the canonical source repository
 */
defined('KOOWA') or die;
?>

<?= helper('behavior.koowa'); ?>

<ktml:style src="assets://files/css/plyr.css" />
<ktml:script src="assets://files/js/plyr/plyr.js" />
<script>
    kQuery(function($){
        plyr.setup();
    });
</script>




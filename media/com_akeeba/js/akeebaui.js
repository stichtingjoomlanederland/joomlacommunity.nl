/**
 * Akeeba Backup
 * The modular PHP5 site backup software solution
 * This file contains the jQuery-based client-side user interface logic
 * @package akeebaui
 * @copyright Copyright (c)2009-2015 Nicholas K. Dionysopoulos
 * @license GNU GPL version 3 or, at your option, any later version
 * @version $Id$
 **/

/**
 * Setup (required for Joomla! 3)
 */
if(typeof(akeeba) == 'undefined') {
	var akeeba = {};
}
if(typeof(akeeba.jQuery) == 'undefined') {
	akeeba.jQuery = jQuery.noConflict();
}

/** @var array The translation strings used in the GUI */
var akeeba_translations = [];
akeeba_translations['UI-LASTRESPONSE'] = 'Last server response %ss ago';
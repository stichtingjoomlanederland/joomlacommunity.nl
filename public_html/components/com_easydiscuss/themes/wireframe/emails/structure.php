<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<title></title>
	<!--[if !mso]><!-- -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<!--<![endif]-->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style type="text/css">
		#outlook a { padding:0; }
		body { margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%; }
		table, td { border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt; }
		img { border:0;height:auto;line-height:100%; outline:none;text-decoration:none;-ms-interpolation-mode:bicubic; }
		p { display:block;margin:13px 0; }
	</style>
	<!--[if mso]>
	<xml>
	<o:OfficeDocumentSettings>
	  <o:AllowPNG/>
	  <o:PixelsPerInch>96</o:PixelsPerInch>
	</o:OfficeDocumentSettings>
	</xml>
	<![endif]-->
	<!--[if lte mso 11]>
	<style type="text/css">
	  .mj-outlook-group-fix { width:100% !important; }
	</style>
	<![endif]-->
	
  <!--[if !mso]><!-->
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" type="text/css">
	<style type="text/css">
	  @import url(https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap);
	</style>
  <!--<![endif]-->

	<style type="text/css">
	@media only screen and (min-width:320px) {
	.mj-column-per-100 { width:100% !important; max-width: 100%; }
	.mj-column-px-40 { width:40px !important; max-width: 40px; }
	.mj-column-px-400 { width:400px !important; max-width: 400px; }
	.mj-column-px-235 { width:235px !important; max-width: 235px; }
	.mj-column-px-10 { width:10px !important; max-width: 10px; }
	.mj-column-per-15 { width:15% !important; max-width: 15%; }
	.mj-column-per-85 { width:85% !important; max-width: 85%; }
	.mj-column-per-49 { width:49% !important; max-width: 49%; }
	.mj-column-px-10 { width:10px !important; max-width: 10px; }
	  }
	</style>


	<style type="text/css">
	@media only screen and (max-width:320px) {
	  table.mj-full-width-mobile { width: 100% !important; }
	  td.mj-full-width-mobile { width: auto !important; }
	}
	</style>
  </head>
<body style="background-color:#ffffff;">

	<!-- Body -->
	<div style="background-color:#ffffff;">

		<?php echo $this->html('email.logo', $logo); ?>

		<?php echo $contents;?>

		<?php if ($replySeparator) { ?>
			<?php echo $this->html('email.replySeparator', $replySeparator); ?>
		<?php } ?>

		<?php echo $this->html('email.footer'); ?>
	</div>
</body>
</html>

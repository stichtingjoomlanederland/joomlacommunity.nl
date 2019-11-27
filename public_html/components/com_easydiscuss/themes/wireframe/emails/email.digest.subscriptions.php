<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

    <title><?php echo JText::sprintf('COM_EASYDISCUSS_DIGEST_EMAIL_DIGEST', $now);?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <!--[if !mso]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!--<![endif]-->

     <style type="text/css">

     .ReadMsgBody { width: 100%; background-color: #F6F6F6; }
     .ExternalClass { width: 100%; background-color: #F6F6F6; }
     body { width: 100%; background-color: #f6f6f6; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; font-family: Arial, Times, serif }
     table { border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }

     @-ms-viewport{ width: device-width; }

     @media only screen and (max-width: 639px){
     .wrapper{ width:100%;  padding: 0 !important; }
     }

     @media only screen and (max-width: 480px){
     .centerClass{ margin:0 auto !important; }
     .imgClass{ width:100% !important; height:auto; }
     .wrapper{ width:320px; padding: 0 !important; }
     .header{ width:320px; padding: 0 !important; background-image: url(http://placehold.it/320x400) !important; }
     .container{ width:300px;  padding: 0 !important; }
     .mobile{ width:300px; display:block; padding: 0 !important; text-align:center !important;}
     .mobile50{ width:300px; padding: 0 !important; text-align:center; }
     *[class="mobileOff"] { width: 0px !important; display: none !important; }
     *[class*="mobileOn"] { display: block !important; max-height:none !important; }
     }

    </style>

    <!--[if gte mso 15]>
    <style type="text/css">
        table { font-size:1px; line-height:0; mso-margin-top-alt:1px;mso-line-height-rule: exactly; }
        * { mso-line-height-rule: exactly; }
    </style>
    <![endif]-->

</head>
<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" style="background-color:#f5f5f5;  font-family:Arial,serif; margin:0; padding:0; min-width: 100%; -webkit-text-size-adjust:none; -ms-text-size-adjust:none;">

    <!--[if !mso]><!-- -->
    <img style="min-width:640px; display:block; margin:0; padding:0" class="mobileOff" width="640" height="1" src="<?php echo rtrim(JURI::root(), '/'); ?>/media/com_easydiscuss/images/spacer.gif">
    <!--<![endif]-->

    <!-- Start Background -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#F6F6F6">
        <tr>
            <td width="100%" valign="top" align="center">

            <!-- Start Wrapper -->
            <table width="640" cellpadding="0" cellspacing="0"  border="0" class="wrapper" bgcolor="#ffffff">
                <tbody>
                    <tr>
                        <td style="padding-top:20px;padding-left:20px;">
                            <img src="<?php echo $logo;?>" />
                        </td>
                    </tr>
                    
                    <tr>
                        <td height="20" style="line-height:20px; font-size:20px;"> </td><!-- Spacer -->
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff">

                            <!-- Start Container -->
                            <table width="560" align="center" cellpadding="0" cellspacing="0" border="0" class="container">
                                <tr>
                                    <td class="mobile" style="font-family:arial, sans-serif; font-size:18px; line-height:32px; font-weight:bold;">
                                        <?php echo JText::sprintf('COM_EASYDISCUSS_DIGEST_EMAIL_FROM', $sitename); ?> <br />
                                    </td>

                                    <td class="mobile" style="font-family:arial, sans-serif; font-size:16px; line-height:26px;color:#888;text-align:right;">
                                        <?php echo $now; ?>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2" height="20" style="line-height:20px; font-size:20px;"> </td><!-- Spacer -->
                                </tr>

                                <tr>
                                    <td colspan="2" height="20" style="line-height:20px; font-size:20px;"> </td><!-- Spacer -->
                                </tr>
                            </table>
                            <!-- End Container -->

                            <?php echo $posts; ?>

                            <?php echo $replies; ?>

                            <?php echo $comments; ?>


                        </td>
                    </tr>
                    <tr>
                        <td height="20" style="line-height:20px; font-size:20px;"> </td><!-- Spacer -->
                    </tr>
                </tbody>
            </table>
            <!-- End Wrapper -->


            </td>
        </tr>
    </table>
    <!-- End Background -->

</body>
</html>

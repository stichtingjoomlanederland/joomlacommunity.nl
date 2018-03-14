<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-md-6">
        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_LAYOUT_USERS_DISPLAY');?>
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php
                                $nameFormat = array();
                                $nameFormat[] = JHTML::_('select.option', 'name', JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_REAL_NAME' ) );
                                $nameFormat[] = JHTML::_('select.option', 'username', JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_USERNAME' ) );
                                $nameFormat[] = JHTML::_('select.option', 'nickname', JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_NICKNAME' ) );
                                $showdet = JHTML::_('select.genericlist', $nameFormat, 'layout_nameformat', 'class="form-control"  ', 'value', 'text', $this->config->get('layout_nameformat' , 'name' ) );
                                echo $showdet;
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SHOW_ONLINE_STATE'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_user_online', $this->config->get('layout_user_online'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SHOW_TIMELAPSE'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_timelapse', $this->config->get('layout_timelapse')); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_DESCRIPTION_ENABLE'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_description_visibility', $this->config->get('main_description_visibility'));?>
                        </div>
                    </div>                    

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_SIGNATURE_ENABLE'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_signature_visibility', $this->config->get('main_signature_visibility'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ALLOW_PUBLIC_USERS_TO_VIEW_PROFILE'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_profile_public', $this->config->get('main_profile_public'));?>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_LAYOUT_MEMBERS_TITLE'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ALLOW_USER_LISTINGS'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'main_user_listings', $this->config->get('main_user_listings'));?>
                        </div>
                    </div>

					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_EXCLUDE_MEMBERS'); ?>
                        </div>
                        <div class="col-md-7">
							<input type="text" name="main_exclude_members" class="form-control" size="60" value="<?php echo $this->config->get( 'main_exclude_members' ); ?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
        <div class="panel">
            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EDIT_PROFILE'); ?>

            <div id="option01" class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_ACCOUNT'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_profile_showaccount', $this->config->get('layout_profile_showaccount'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_SOCIAL'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_profile_showsocial', $this->config->get('layout_profile_showsocial'));?>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_LOCATION'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_profile_showlocation', $this->config->get('layout_profile_showlocation'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_URL'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_profile_showurl', $this->config->get('layout_profile_showurl'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_LAYOUT_PROFILE_SHOW_SITE_DETAILS'); ?>
                        </div>
                        <div class="col-md-7">
                            <?php echo $this->html('form.boolean', 'layout_profile_showsite', $this->config->get('layout_profile_showsite'));?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
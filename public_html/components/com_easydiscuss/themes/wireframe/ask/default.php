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
<form autocomplete="off" action="<?php echo JRoute::_('index.php');?>" method="post" enctype="multipart/form-data" data-ed-ask-form>

	<div class="ed-ask t-lg-mt--xl">
	<div role="alert" class="o-alert o-alert--danger" data-ed-post-alert hidden></div>

	<?php if ($post->isPending()) { ?>
	<div role="alert" class="o-alert o-alert--info">
		<h4><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_PREVIEW_POST_INFO_TITLE');?></h4>
		<?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_PREVIEW_POST_INFO_DESC');?>
	</div>
	<?php } ?>
		<div class="ed-ask__hd">
			<input type="text" name="title" placeholder="<?php echo JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE', true);?>" class="form-control input-lg ed-ask__input-title"
				autocomplete="off"
				data-ed-post-title
				data-minimum-title="<?php echo $minimumTitle;?>"
				value="<?php echo $this->html('string.escape', $post->title);?>"
			>
			<div class="t-hidden ed-ask-similar-menu" data-ed-similar-questions>
				<div class="ed-ask-similar-menu__arrow"></div>
				<div class="o-loading">
					<div class="o-loading__content">
						<i class="fa fa-spinner fa-spin"></i>
					</div>
				</div>

				<div data-ed-similar-list>
				</div>

				<a href="javascript:void(0);" class="ed-ask-similar-menu__btn-close" data-ed-similar-question-close><i class="fa fa-close"></i></a>
			</div>
		</div>

		<div class="ed-ask__bd">
			<div class="o-row">

				<div class="o-col o-col--top t-lg-pr--md t-xs-pr--no">
					<div class="form-group">
						<label for="category_id"><?php echo JText::_('COM_EASYDISCUSS_SELECT_A_CATEGORY');?></label>
						<?php echo $categories; ?>
					</div>
				</div>

				<?php if ($this->config->get('layout_post_types')) { ?>

				<div class="o-col o-col--top t-lg-pr--md t-xs-pr--no">
					<div class="form-group">
						<label for="post_type-<?php echo $uid;?>"><?php echo JText::_('COM_EASYDISCUSS_SELECT_A_POST_TYPE');?></label>

						<div data-post-types-wrapper>
							<?php echo $this->output('site/ask/post.types', array('uid' => $uid)); ?>
						</div>
					</div>
				</div>
				<?php } ?>

				<?php if ($this->config->get('post_priority')) { ?>
				<div class="o-col o-col--top">
					<div class="form-group">
						<label for="priority"><?php echo JText::_('COM_EASYDISCUSS_SELECT_PRIORITY');?></label>
						<select name="priority" class="form-control" id="priority">
							<option value=""><?php echo JText::_('COM_EASYDISCUSS_SELECT_A_PRIORITY');?></option>
							<?php foreach ($priorities as $priority) { ?>
							<option value="<?php echo $priority->id;?>"<?php echo ($post->priority == $priority->id) ? ' selected="selected"' : ''; ?>><?php echo JText::_($priority->title);?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<?php } ?>
			</div>

			<?php if ($this->config->get('main_private_post', false) && $this->my->id) { ?>
			<div class="o-checkbox t-lg-mb--lg small" style="<?php echo !$post->id && $defaultCategory && $defaultCategory->getParams()->get('cat_enforce_private', false) ? 'display:none;' : '';?>" data-private-post>
				<input id="private" type="checkbox" name="private" value="1"
					<?php echo $post->private || (!$post->id && $defaultCategory && $defaultCategory->getParams()->get('cat_default_private', false)) ? ' checked="checked"' : '';?> 
				/>

				<label for="private">
					<?php echo JText::_('COM_EASYDISCUSS_MAKE_THIS_POST_PRIVATE');?>
				</label>
			</div>
			<?php } ?>

			<?php if ($this->config->get('main_anonymous_posting')) { ?>
			<div class="o-checkbox t-lg-mb--lg small">
				<input id="anonymous" type="checkbox" name="anonymous" value="1"<?php echo $post->anonymous ? ' checked="checked"' : '';?> />
				<label for="anonymous">
					<?php echo JText::_('COM_EASYDISCUSS_POST_ANONYMOUSLY');?>
				</label>
			</div>
			<?php } ?>

			<div class="ed-editor ed-editor--<?php echo $composer->getEditorClass();?> <?php echo $composer->hasTabs() ? '' : 'has-no-tab'; ?>" <?php echo $composer->uid;?>>

				<div class="ed-editor-widget ed-editor-widget--no-pad">
					<?php echo $composer->renderEditor(); ?>

					<?php echo $composer->renderTabs(); ?>
				</div>

				<?php if ($this->config->get('main_master_tags') && $this->acl->allowed('add_tag')) { ?>
					<?php echo $this->output('site/composer/forms/tags', array('post' => $post)); ?>
				<?php } ?>

				<?php echo $this->output('site/composer/forms/location', array('post' => $post, 'editorId' => $composer->uid, 'operation' => $composer->operation)); ?>

				<?php if (!$this->my->id) { ?>
				<div class="ed-editor-widget t-lg-mt--xl">
					<div class="ed-editor-widget__title">
						<?php echo JText::_('COM_EASYDISCUSS_YOUR_DETAILS'); ?>
					</div>

					<div class="ed-editor-widget__note">
						<p><?php echo JText::_('COM_EASYDISCUSS_YOUR_DETAILS_NOTE'); ?></p>
					</div>

					<div class="ed-editor-widget__note">
						<div class="o-row">
							<div class="o-col">
								<div class="form-group t-lg-mr--md">
									<input name="poster_name" class="form-control" type="text" placeholder="<?php echo JText::_('COM_EASYDISCUSS_YOUR_NAME');?>" value="<?php echo $post->poster_name;?>" />
								</div>
							</div>
							<div class="o-col">
								<div class="form-group">
									<input name="poster_email" class="form-control" type="text" placeholder="<?php echo JText::_('COM_EASYDISCUSS_YOUR_EMAIL');?>" value="<?php echo $post->poster_email;?>" />
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>

				<?php if ($captcha->enabled() && !$post->id) { ?>
				<div class="ed-editor-widget t-lg-mt--xl" data-ed-captcha-form>
					<?php if (!$captcha->isInvisible()) { ?>
					<div class="ed-editor-widget__title">
						<?php echo JText::_('COM_EASYDISCUSS_CAPTCHA_TITLE'); ?>
					</div>
					<div class="ed-editor-widget__note">
						<?php echo JText::_('COM_EASYDISCUSS_CAPTCHA_INFO'); ?>
					</div>
					<?php } ?>
					
					<?php echo $captcha->html();?>
				</div>
				<?php } ?>

				<div class="ed-editor__ft">

					<?php if ($this->config->get('main_tnc_question')) { ?>
					<div class="pull-left">
						<div class="o- t-lg-mb--lg">

							<div class="o-checkbox o-checkbox--inline t-mr--md">
								<input type="checkbox" name="tnc-ask" id="tnc-ask" data-ed-ask-tnc-checkbox <?php echo ED::tnc()->hasAcceptedTnc('question') ? 'checked="checked"' : '' ?>/>
								<label for="tnc-ask">
									<?php echo JText::_('COM_EASYDISCUSS_I_HAVE_READ_AND_AGREED');?>
									<a href="javascript:void(0);" style="text-decoration: underline;" data-ed-ask-tnc-link>
										<?php echo JText::_('COM_EASYDISCUSS_TERMS_AND_CONDITIONS');?>
									</a>
								</label>
							</div>

						</div>
					</div>
					<?php } ?>

					<div class="pull-right">
						<?php if ($post->id && $post->isPending() && (ED::isSiteAdmin() || $this->acl->allowed('manage_pending')) || (ED::isModerator() && $post->isPending())) { ?>
						<a class="btn btn-link t-lg-pl--xs" href="<?php echo $cancel;?>">
							<?php echo JText::_('COM_EASYDISCUSS_CANCEL');?>
						</a>
						<button class="btn btn-primary btn-sm" type="button" data-ed-approve-button data-id="<?php echo $post->id;?>">
							<?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POST_APPROVE');?>
						</button>
						<button class="btn btn-danger btn-sm" type="button" data-ed-reject-button data-id="<?php echo $post->id;?>">
							<?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POST_REJECT');?>
						</button>
						<?php } else { ?>
						<a class="btn btn-link t-lg-pl--xs" href="<?php echo $cancel;?>">
							<?php echo JText::_('COM_EASYDISCUSS_CANCEL_AND_DISCARD');?>
						</a>

						<button class="btn btn-primary pull-right" type="button" data-ed-submit-button>
							<?php if ($post->id) { ?>
								<?php echo JText::_('COM_EASYDISCUSS_BUTTON_UPDATE_POST');?>
							<?php } else { ?>
								<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT');?>
							<?php } ?>
						</button>
						<?php } ?>
					</div>
				</div>
			</div>

		</div>
	</div>

	<?php echo $this->html('form.hidden', 'posts', 'posts', 'save'); ?>

	<?php if (!empty($reference) && $referenceId) { ?>
	<input type="hidden" name="reference" value="<?php echo $reference; ?>" />
	<input type="hidden" name="reference_id" value="<?php echo $referenceId; ?>" />
	<?php } ?>

	<?php if (!empty($clusterId) && $clusterId) { ?>
	<input type="hidden" name="cluster_id" value="<?php echo $clusterId; ?>" />
	<?php } ?>

	<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
	<input type="hidden" name="id" id="id" value="<?php echo $post->id; ?>" />
</form>

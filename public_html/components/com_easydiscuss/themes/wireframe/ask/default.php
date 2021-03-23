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
<form autocomplete="off" action="<?php echo JRoute::_('index.php');?>" method="post" enctype="multipart/form-data" data-ed-ask-form>

	<div role="alert" class="o-alert o-alert--danger t-mb--md t-d--none" data-ed-alert>
	</div>

	<?php if ($post->isPending()) { ?>
	<div class="o-card o-card--ed-pending t-mb--lg">
		<div class="o-card__body">
			<h3>
				<?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_PREVIEW_POST_INFO_TITLE');?>
			</h3>
			<?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_PREVIEW_POST_INFO_DESC');?>
		</div>
	</div>
	<?php } ?>

	<div class="ed-ask">
		<div class="ed-ask__hd">
			<input type="text" name="title" placeholder="<?php echo JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE', true);?>" class="o-form-control ed-ask__input-title"
				autocomplete="off"
				data-ed-post-title
				data-minimum-title="<?php echo $minimumTitle;?>"
				value="<?php echo $this->html('string.escape', $post->title);?>"
			>
			<?php echo $this->html('triggers.html', 'easydiscuss', 'afterDisplayTitle', [&$post]); ?>
		</div>

		<?php if (!$post->isNew() && (ED::isSiteAdmin() || ED::moderator()->isModerator($post->category_id))) { ?>
		<?php $alias = $post->alias; ?>
		<div class="ed-ask-alias t-mb--sm" data-ed-ask-alias>
			<div class="ed-ask-alias__label">
				<?php echo JText::_('COM_ED_POST_ALIAS'); ?>:
			</div>

			<div class="ed-ask-alias__preview">
				<div class="ed-ask-alias__sample">
					<a href="javascript:void(0);" class="ed-ask-alias__post-name si-link" data-alias-preview>
						<?php echo $alias; ?>
					</a>
					<div class="ed-ask-alias__edit-field t-d--none" data-alias-input-wrapper>
						<input type="text" class="o-form-control o-form-control--sm ed-ask__input-alias" name="alias" value="<?php echo $this->html('string.escape', $alias);?>" data-alias-input />
					</div>
				</div>

				<div class="ed-ask-alias__edit-action">
					<a href="javascript:void(0);" class="o-btn o-btn--default-o o-btn--sm" data-alias-edit><?php echo JText::_('COM_ED_POST_ALIAS_EDIT_BUTTON'); ?></a>
					<a href="javascript:void(0);" class="o-btn o-btn--primary o-btn--sm t-d--none" data-alias-update>
						<i class="fa fa-check"></i>
					</a>
					<a href="javascript:void(0);" class="o-btn o-btn--default-o o-btn--sm t-d--none" data-alias-cancel>
						<i class="fa fa-times"></i>
					</a>
				</div>
			</div>
		</div>
		<?php } ?>

		<?php if ($this->config->get('main_private_post', false) && $this->my->id) { ?>
		<div class="o-form-check t-mt--md t-mb--md" data-private-post style="<?php echo !$post->id && $defaultCategory && $defaultCategory->getParams()->get('cat_enforce_private', false) ? 'display:none;' : '';?>">
			<input id="private" class="o-form-check-input" type="checkbox" name="private" value="1" 
				<?php echo $post->private || (!$post->id && $defaultCategory && $defaultCategory->getParams()->get('cat_default_private', false)) ? ' checked="checked"' : '';?> 
			/>
			<label for="private" class="o-form-check-label">
				<?php echo JText::_('COM_EASYDISCUSS_MAKE_THIS_POST_PRIVATE');?>
			</label>
		</div>
		<?php } ?>

		<?php if ($this->config->get('main_anonymous_posting')) { ?>
			<?php echo $composer->renderAnonymousField($post->anonymous);?>
		<?php } ?>


		<div class="ed-ask__bd">
			<?php echo $this->html('form.honeypot'); ?>

			<div class="ed-ask__post-type lg:o-grid lg:o-grid--gutters">
				<div class="lg:o-grid__cell">
					<div class="o-form-group">
						<label class="" for="category_id"><?php echo JText::_('COM_EASYDISCUSS_SELECT_A_CATEGORY');?></label>
						<?php echo $categories; ?>
					</div>
				</div>
			</div>

			<?php if ($this->config->get('layout_post_types') || ($this->config->get('main_labels') && $this->acl->allowed('set_label')) || $this->config->get('post_priority')) { ?>
			<div class="ed-ask__post-type lg:o-grid lg:o-grid--gutters">
				<?php if ($this->config->get('layout_post_types')) { ?>
				<div class="lg:o-grid__cell">
					<?php echo $composer->renderPostTypesField($defaultCategoryId, $post->post_type);?>
				</div>
				<?php } ?>

				<?php if ($this->config->get('main_labels') && $this->acl->allowed('set_label')) { ?>
				<div class="lg:o-grid__cell">
					<?php echo $composer->renderPostLabelsField($post->getCurrentLabel());?>
				</div>
				<?php } ?>

				<?php if ($this->config->get('post_priority')) { ?>
				<div class="lg:o-grid__cell">
					<?php echo $composer->renderPriorityField($post->priority);?>
				</div>
				<?php } ?>
			</div>
			<?php } ?>

			<div class="ed-editor ed-editor--<?php echo $composer->getEditorClass();?> <?php echo $composer->hasTabs($defaultCategoryId) ? '' : 'has-no-tab'; ?>" <?php echo $composer->uid;?> data-ed-editor-wrapper data-ed-editor-uuid="<?php echo $composer->uuid; ?>">

				<div class="ed-editor-widget t-pt--no">
					<?php echo $composer->renderEditor(); ?>
				</div>

				<?php if ($composer->hasTabs($defaultCategoryId)) { ?>
				<div class="ed-editor-widget t-pt--no">
					<div data-ed-editor-tabs>
						<?php echo $composer->renderTabs($defaultCategoryId); ?>
					</div>
				</div>
				<?php } ?>

				<?php if ($this->config->get('main_master_tags') && $this->acl->allowed('add_tag')) { ?>
					<?php echo $this->output('site/composer/forms/tags', array('post' => $post)); ?>
				<?php } ?>

				<?php if (!$this->my->id) { ?>
					<?php echo $composer->renderNameField('question');?>
				<?php } ?>

				<?php if ($captcha->enabled() && !$post->id) { ?>
					<?php echo $composer->renderCaptcha($captcha); ?>
				<?php } ?>

				<div class="ed-editor__ft">

					<?php if ($this->config->get('main_tnc_question')) { ?>
						<?php echo $composer->renderTnc('question');?>
					<?php } ?>

					<div class="t-ml--auto">
						<?php if ($post->id && $post->isPending() && (ED::isSiteAdmin() || $this->acl->allowed('manage_pending')) || (ED::isModerator() && $post->isPending())) { ?>
						<a class="si-link t-font-size--02 t-mr--sm" href="<?php echo $cancel;?>">
							<?php echo JText::_('COM_EASYDISCUSS_CANCEL');?>
						</a>
						<button class="o-btn o-btn--danger t-mr--sm" type="button" data-ed-reject-button data-id="<?php echo $post->id;?>">
							<?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POST_REJECT');?>
						</button>
						<button class="o-btn o-btn--primary" type="button" data-ed-approve-button data-id="<?php echo $post->id;?>">
							<?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_MANAGE_POST_APPROVE');?>
						</button>
						<?php } else { ?>
						<a class="si-link t-font-size--02 t-mr--sm" href="<?php echo $cancel;?>">
							<?php echo JText::_('COM_EASYDISCUSS_CANCEL_AND_DISCARD');?>
						</a>

						<button class="o-btn o-btn--primary" type="button" data-ed-submit-button>
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

	<?php echo $this->html('form.action', 'posts', 'posts', 'save'); ?>

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

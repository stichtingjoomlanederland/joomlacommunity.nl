<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.1.4
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><div id="acym__editor__content" class="grid-x acym__content acym__editor__area">
	<div class="cell grid-x align-right">
		<input type="hidden" id="acym__mail__edit__editor" value="<?php echo acym_escape($data['mail']->editor); ?>">
		<input type="hidden" class="acym__wysid__hidden__save__thumbnail" id="editor_thumbnail" name="editor_thumbnail" value="<?php echo acym_escape($data['mail']->thumbnail); ?>"/>
		<input type="hidden" id="acym__mail__edit__editor__social__icons" value="<?php echo empty($data['social_icons']) ? '{}' : acym_escape($data['social_icons']); ?>">
        <?php
        if ($data['mail']->type == 'notification') {
            ?>
			<button type="submit" data-task="test" class="cell medium-shrink button-secondary auto button acy_button_submit acym__template__save acym__template__prevent__unload">
                <?php echo acym_translation('ACYM_SEND_TEST'); ?>
			</button>
            <?php
        } else {
            echo acym_modal_include(
                '<button type="button" id="acym__template__start-from" class="cell medium-shrink button-secondary auto button">'.acym_translation('ACYM_START_FROM').'</button>',
                dirname(__FILE__).DS.'choose_template_ajax.php',
                'acym__template__choose__modal',
                $data
            );
        }
        ?>
		<button id="apply" type="button" data-task="apply" class="cell medium-shrink button-secondary auto button acym__template__save <?php echo 'html' == $data['mail']->editor ? 'acy_button_submit' : 'acym__template__prevent__unload'; ?>">
            <?php echo acym_translation('ACYM_SAVE'); ?>
		</button>
		<button style="display: none;" data-task="apply" class="acy_button_submit" id="data_apply"></button>
		<button id="save" type="button" data-task="save" class="cell medium-shrink auto button margin-left-1 <?php echo 'html' == $data['mail']->editor ? 'acy_button_submit' : 'acym__template__prevent__unload'; ?>">
            <?php echo acym_translation('ACYM_SAVE_EXIT'); ?>
		</button>
		<button style="display: none;" data-task="save" class="acy_button_submit" id="data_save"></button>
	</div>
	<div class="cell grid-x grid-padding-x acym__editor__content__options">
        <?php
        echo !empty($data['return']) ? '<input type="hidden" name="return" value="'.acym_escape($data['return']).'"/>' : '';
        $mainSize = 'xlarge-3 medium-6';
        if ($data['mail']->type == 'notification') {
            echo '<input type="hidden" name="notification" value="'.acym_escape($data['mail']->name).'"/>';
            $mainSize = '';
            $sizes = '';
        } else {
            if ($data['mail']->type == 'automation') {
                $mainSize = 'medium-6';
            }
            $sizes = 'xlarge-3 medium-6';
            ?>
			<div class="cell <?php echo $mainSize; ?>">
				<label>
                    <?php echo acym_translation($data['mail']->type == 'automation' ? 'ACYM_NAME' : 'ACYM_TEMPLATE_NAME'); ?>
					<input name="mail[name]" type="text" class="acy_required_field" value="<?php echo acym_escape($data['mail']->name); ?>" required>
				</label>
			</div>
            <?php
        }
        ?>

		<div class="cell <?php echo $mainSize; ?>">
			<label>
                <?php echo acym_translation('ACYM_SUBJECT'); ?>
				<input name="mail[subject]" type="text" value="<?php echo acym_escape($data['mail']->subject); ?>" <?php echo in_array($data['mail']->type, array('welcome', 'unsubscribe', 'automation')) ? 'required' : ''; ?>>
			</label>
		</div>

        <?php
        if ($data['mail']->type == 'automation') {
            ?>
			<div class="cell"></div>
			<div class="cell <?php echo $sizes; ?>">
				<label>
                    <?php echo acym_translation('ACYM_FROM_NAME'); ?>
					<input name="mail[from_name]" type="text" value="<?php echo acym_escape($data['config']->get('from_name')); ?>">
				</label>
			</div>
			<div class="cell <?php echo $sizes; ?>">
				<label>
                    <?php echo acym_translation('ACYM_FROM_EMAIL'); ?>
					<input name="mail[from_email]" type="text" value="<?php echo acym_escape($data['config']->get('from_email')); ?>">
				</label>
			</div>
			<div class="cell <?php echo $sizes; ?>">
				<label>
                    <?php echo acym_translation('ACYM_REPLYTO_NAME'); ?>
					<input name="mail[replyto_name]" type="text" value="<?php echo acym_escape($data['config']->get('replyto_name')); ?>">
				</label>
			</div>
			<div class="cell <?php echo $sizes; ?>">
				<label>
                    <?php echo acym_translation('ACYM_REPLYTO_EMAIL'); ?>
					<input name="mail[replyto_email]" type="text" value="<?php echo acym_escape($data['config']->get('replyto_email')); ?>">
				</label>
			</div>
            <?php
        } elseif ($data['mail']->type != 'notification') {
            ?>
			<div class="cell <?php echo $sizes; ?>">
                <?php if ($data['mail']->type == 'welcome' || $data['mail']->type == 'unsubscribe') { ?>
					<label><?php echo acym_translation('ACYM_TYPE'); ?>
						<input name="mail[type]" type="text" value="<?php echo acym_escape($data['mail']->type); ?>" readonly>
					</label>
                <?php } else { ?>
					<label>
                        <?php echo acym_translation('ACYM_TYPE');
                        $templateTypes = array(
                            'standard' => acym_translation('ACYM_STANDARD'),
                            'welcome' => acym_translation('ACYM_WELCOME_MAIL'),
                            'unsubscribe' => acym_translation('ACYM_UNSUBSCRIBE_MAIL'),
                        );
                        echo acym_select(
                            $templateTypes,
                            "mail[type]",
                            acym_escape($data['mail']->type),
                            'required="required"',
                            null,
                            null,
                            'acym__template__type'
                        ); ?>
					</label>
                <?php } ?>
			</div>
			<div class="cell <?php echo $sizes; ?>">
				<label>
                    <?php echo acym_translation('ACYM_TAGS'); ?>
                    <?php echo acym_selectMultiple(
                        $data['allTags'],
                        "template_tags",
                        $data['mail']->tags,
                        ['id' => 'acym__tags__field', 'placeholder' => acym_translation('ACYM_ADD_TAGS')],
                        "name",
                        "name"
                    ); ?>
				</label>
			</div>
			<div class="cell grid-x" id="acym__mail__edit__html__stylesheet__container">
                <?php
                echo acym_tooltip(
                    '<p id="acym__mail__edit__html__stylesheet__button">'.acym_translation('ACYM_CUSTOM_ADD_STYLESHEET').' <i class="material-icons">keyboard_arrow_down</i></p>',
                    acym_translation('ACYM_STYLESHEET_HTML_DESC')
                );
                $stylesheet = empty($data['mail']->stylesheet) ? '' : $data['mail']->stylesheet;
                ?>
				<textarea class="margin-top-1" name="editor_stylesheet" id="acym__mail__edit__html__stylesheet" cols="30" rows="15" style="display: none"><?php echo $stylesheet; ?></textarea>
			</div>
			<div class="cell grid-x" id="acym__mail__edit__headers__container">
				<p id="acym__mail__edit__headers__button"><?php echo acym_translation('ACYM_CUSTOM_HEADERS'); ?> <i class="material-icons">keyboard_arrow_down</i></p>
				<textarea class="margin-top-1" name="editor_headers" id="acym__mail__edit__headers" cols="30" rows="15" style="display: none"><?php echo $data['mail']->headers; ?></textarea>
			</div>
            <?php
        }
        ?>
	</div>
</div>
<input type="hidden" name="mail[id]" value="<?php echo acym_escape($data['mail']->id); ?>"/>
<input type="hidden" name="id" value="<?php echo acym_escape($data['mail']->id); ?>"/>
<input type="hidden" name="thumbnail" value="<?php echo empty($data['mail']->thumbnail) ? '' : acym_escape($data['mail']->thumbnail); ?>"/>
<?php
acym_formOptions();

$editor = acym_get('helper.editor');
$editor->content = $data['mail']->body;
$editor->autoSave = !empty($data['mail']->autosave) ? $data['mail']->autosave : '';
if (!empty($data['mail']->editor)) $editor->editor = $data['mail']->editor;
if (!empty($data['mail']->id)) $editor->mailId = $data['mail']->id;
if (!empty($data['mail']->type)) $editor->automation = $data['isAutomationAdmin'];
echo $editor->display();

<?php
defined('_JEXEC') or die('Restricted access');
?><div class="cell grid-x acym__content" id="acym__plugin__installed">
	<form id="acym_form" action="<?php echo acym_completeLink(acym_getVar('cmd', 'ctrl')); ?>" method="post" name="acyForm" class="cell grid-x acym__form__campaign__edit" data-abide>
		<input type="hidden" name="all__plugins" id="acym__plugins__all" value="<?php echo empty($data['plugins']) ? '[]' : acym_escape($data['plugins']); ?>">
        <?php
        $workflow = acym_get('helper.workflow');
        echo $workflow->display($data['tabs'], $data['tab'], false, true);
        ?>
		<div id="acym__plugin__installed__application" class="cell grid-x">
            <?php if (strpos($data['plugins'], '"total":"0"') !== false) { ?>
				<div class="cell grid-x align-center">
					<h2 class="cell text-center acym__title__primary__color"><?php echo acym_translation('ACYM_YOU_DONT_HAVE_ADD_ONS'); ?></h2>
					<a href="<?php echo acym_completeLink('plugins&task=available'); ?>" class="cell shrink button  text-center margin-top-1 margin-bottom-2"><?php echo acym_translation('ACYM_DOWNLOAD_MY_FIRST_ONE'); ?></a>
				</div>
            <?php } else { ?>
				<div class="cell grid-x grid-margin-x">
					<input type="text" class="cell medium-3" v-model="search" placeholder="<?php echo acym_translation('ACYM_SEARCH'); ?>">
					<div class="cell medium-3">
						<select2 :name="'acym__plugins__type'" :options="<?php echo acym_escape(json_encode($data['types'])); ?>" v-model="type"></select2>
					</div>
					<div class="cell medium-3">
						<select2 :name="'acym__plugins__level'" :options="<?php echo acym_escape(json_encode($data['level'])); ?>" v-model="level"></select2>
					</div>
					<div class="cell grid-x medium-3 align-right">
						<button type="button" class="acy_button_submit button button-secondary" data-task="checkUpdates"><?php echo acym_translation('ACYM_CHECK_FOR_UPDATES'); ?><i class="acymicon-autorenew"></i></button>
					</div>
				</div>
				<div class="cell grid-x" v-show="noPluginTodisplay" style="display: none;">
					<h2 class="cell text-center acym__title__primary__color"><?php echo acym_translation('ACYM_NO_ADD_ONS_TO_DISPLAY'); ?></h2>
				</div>
				<div class="cell grid-x margin-bottom-2" v-show="!noPluginTodisplay">
					<div class="cell grid-x align-center text-center acym__plugin__available__loader__page margin-top-3 margin-bottom-3" v-show="loading">
                        <?php echo acym_loaderLogo(); ?>
					</div>
					<div class="cell grid-x grid-margin-x grid-margin-y" v-show="!loading" style="display: none;" v-infinite-scroll="loadMorePlugins" :infinite-scroll-disabled="busy">
						<div class="acym__plugins__card cell grid-x xlarge-3 large-4 medium-6" v-for="(plugin, index) in displayedPlugins" :key="plugin" :style="transitionDelay(index)">
							<button @click="deletePlugin(plugin.id)" type="button" class="acym__plugins__button__delete">
								<i class="acymicon-trash-o"></i>
							</button>
							<div class="acym__plugins__card__image margin-bottom-1 cell grid-x align-center">
								<img :src="imageUrl(plugin.folder_name)" alt="plugin image" class="cell">
								<div class="acym__plugins__card__params_type shrink cell" :style="typesColors[plugin.category]">{{ plugin.category }}</div>
							</div>
							<div class="acym__plugins__card__params cell grid-x">
								<div class="cell grid-x acym_vcenter acym__plugins__card__params__first">
									<h2 class="cell medium-10 acym__plugins__card__params__title">{{ plugin.title }}</h2>
									<a target="_blank" :href="documentationUrl(plugin.folder_name)" class="acym__plugins__link cell medium-2"><i class="acymicon-book"></i></a>
								</div>
								<div ref="plugins" :class="isOverflown(index)" class="acym__plugins__card__params_desc cell" v-html="plugin.description"></div>
								<div class="acym__plugins__card__actions cell grid-x acym_vcenter" v-show="rightLevel(plugin.level)">
									<div class="cell grid-x acym_vcenter medium-8">
										<span class="cell shrink"><?php echo acym_translation('ACYM_ENABLED'); ?>:</span>
										<vue-switch :plugin="plugin" :ischecked="isActivated(plugin.active)"></vue-switch>
									</div>
									<div class="cell grid-x acym_vcenter medium-4 align-right" v-show="plugin.uptodate === '0'">
										<button data-acym-tooltip="<?php echo acym_translation('ACYM_UPDATE'); ?>" type="button" class="acym__plugins__button shrink acym__plugins__button__update cell text-center" @click="updatePlugin(plugin)">
											<span v-show="!updating[plugin.id]"><i class="acymicon-file_download"></i></span>
											<span v-show="updating[plugin.id]"><?php echo acym_loaderLogo(); ?></span>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
                <?php
            } ?>
		</div>
        <?php echo acym_formOptions(); ?>
	</form>
</div>


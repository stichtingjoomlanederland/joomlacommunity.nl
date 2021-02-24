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
<script type="application/ld+json">
{
	"@context": "https://schema.org",
	"@type": "QAPage",
	"mainEntityOfPage": "<?php echo $post->getPermalink(true, true); ?>",
	"mainEntity": {
		"@type": "Question",
		"name": "<?php echo $this->html('string.escape', $post->getTitle());?>",
		"text": "<?php echo ED::normalizeSchema($post->getContent(false, false, false, false)); ?>",
		"editor": "<?php echo $post->getAuthor()->nickname;?>",
		"genre": "<?php echo $post->getCategory()->getTitle();?>",
		"publisher": {
			"@type": "Organization",
			"name": "<?php echo ED::getSiteName(); ?>",
			"logo": <?php echo ED::getSchemaLogo(); ?>
		},
		"author": {
			"@type": "Person",
			"name": "<?php echo $post->getAuthor()->nickname;?>",
			"image": "<?php echo $post->getAuthor()->getAvatar();?>"
		},
		"datePublished": "<?php echo $post->getDateObject()->format('Y-m-d');?>",
		"dateCreated": "<?php echo $post->getDateObject()->format('Y-m-d');?>",
		"dateModified": "<?php echo $post->getModifiedDate()->format('Y-m-d');?>",
		"answerCount": "<?php echo $answer ? 1 : 0; ?>",
		"upvoteCount": "<?php echo $post->getTotalVotes(false, DISCUSS_VOTE_UP_STRING); ?>",
		"downvoteCount": "<?php echo $post->getTotalVotes(false, DISCUSS_VOTE_DOWN_STRING); ?>"

		<?php if ($this->config->get('main_ratings') && $post->getRatings()->total > 0) { ?>,
		"aggregateRating": {
			"@type": "http://schema.org/AggregateRating",
			"ratingValue": "<?php echo round($ratings->ratings / 2, 2); ?>",
			"worstRating": "1",
			"bestRating": "5",
			"ratingCount": "<?php echo $ratings->total; ?>"
		}
		<?php } ?>

		<?php if ($this->config->get('main_master_tags') && $schemaTags) { ?>,
		"keywords": "<?php echo implode(',', $schemaTags); ?>"
		<?php } ?>

		<?php if ($answer && $answer !== true) { ?>,
		"acceptedAnswer": {
			"@type": "Answer",
			"text": "<?php echo ED::normalizeSchema($answer->getContent(false, false, false, false)); ?>",
			"url": "<?php echo $post->getPermalink(true, true); ?>",
			"author": {
				"@type": "Person",
				"name": "<?php echo $answer->getAuthor()->nickname;?>",
				"image": "<?php echo $answer->getAuthor()->getAvatar();?>"
			},
			"dateCreated": "<?php echo $answer->getDateObject()->format('Y-m-d');?>",
			"upvoteCount": "<?php echo $answer->getTotalVotes(false, DISCUSS_VOTE_UP_STRING); ?>",
			"downvoteCount": "<?php echo $answer->getTotalVotes(false, DISCUSS_VOTE_DOWN_STRING); ?>"
		}
		<?php } ?>
	}
}
</script>
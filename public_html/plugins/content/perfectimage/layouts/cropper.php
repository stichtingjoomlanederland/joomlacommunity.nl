<?php
/*
 * @package		Perfect Image Form Field
 * @copyright	Copyright (c) 2016 Perfect Web Team / perfectwebteam.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Input
$width          = $displayData['width'];
$ratio          = $displayData['ratio'];
$id             = $displayData['id'];
$maxSize        = $displayData['max_size'];
$maxSizeMessage = $displayData['max_size_message'];
$maxDimension   = $displayData['max_dimension'];
?>

<div class="btn-toolbar perfect-image-toolbar">

    <div class="btn-group">
        <label class="btn btn-primary btn-upload perfect-image-upload" for="<?php echo $id; ?>_select" title="Upload image file">
            <input type="file" id="<?php echo $id; ?>_select" name="file" accept="image/*">
            Selecteer bestand
        </label>
    </div>

    <div class="btn-group">
        <button type="button" class="btn" data-method="rotate" data-option="-90" title="Rotate -90°">Roteer links -90°</button>
        <button type="button" class="btn" data-method="rotate" data-option="-45">-45°</button>
        <button type="button" class="btn" data-method="rotate" data-option="-30">-30°</button>
        <button type="button" class="btn" data-method="rotate" data-option="-15">-15°</button>
    </div>

    <div class="btn-group">
        <button type="button" class="btn" data-method="rotate" data-option="90" title="Rotate 90°">Roteer rechts 90°</button>
        <button type="button" class="btn" data-method="rotate" data-option="45">45°</button>
        <button type="button" class="btn" data-method="rotate" data-option="30">30°</button>
        <button type="button" class="btn" data-method="rotate" data-option="15">15°</button>
    </div>

    <div class="btn-group">
        <button type="button" class="btn" data-method="scaleX" data-option="-1" title="Flip Horizontal">Spiegel horizontaal</button>
        <button type="button" class="btn" data-method="scaleY" data-option="-1" title="Flip Vertical">Spiegel verticaal</button>
    </div>

</div>

<div class="cropper-container cropper-bg">
    <img class="perfect-image-image">
</div>
<img id="temp-image-holder" style="display: none;"/>
<input type="hidden" class="perfect-image-data" name="perfect-image-data">
<input type="hidden" class="perfect-image-width" name="perfect-image-width" value="<?php echo $width; ?>">
<input type="hidden" class="perfect-image-ratio" name="perfect-image-ratio" value="<?php echo $ratio; ?>">
<input type="hidden" class="perfect-image-maxsize" name="perfect-image-maxsize" value="<?php echo $maxSize; ?>">
<input type="hidden" class="perfect-image-maxsize-message" name="perfect-image-maxsize-message" value="<?php echo $maxSizeMessage; ?>">
<input type="hidden" class="perfect-image-dimensionsize" name="perfect-image-dimensionsize" value="<?php echo $maxDimension; ?>">



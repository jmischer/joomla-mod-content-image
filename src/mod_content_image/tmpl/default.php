<?php
/**
 * @var string $content_image
 */
// No direct access
defined('_JEXEC') or die;

// Check image data
if (!isset($data['image'])) {
	return;
}

// Get image data
$image = $data['image'];
?>
<div class="content-image <?=$data['type']?>"><?= JHtml::image($image['path'], $image['alt']); ?></div>

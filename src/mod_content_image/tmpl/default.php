<?php
/**
 * @var string $content_image
 */
// No direct access
defined('_JEXEC') or die;

// Get image
$image = $data['image'];
if (!isset($image)) {
	return;
}
?>
<div class="content-image <?=$data['type']?>"><?= JHtml::image($image, $data['alt']); ?></div>

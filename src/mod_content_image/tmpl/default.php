<?php
/**
 * @var mixed $data
 * 
 */
// No direct access
defined('_JEXEC') or die;

// Check data
if (!$data) {
	return;
}

// Get image from data
$image = $data['image'];
if (!$image) {
	return;
}
?>
<div class="content-image <?=$data['type']?>"><?= JHtml::image($image['path'], $image['alt']); ?></div>

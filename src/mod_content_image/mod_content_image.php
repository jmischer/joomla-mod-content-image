<?php
use Joomla\CMS\Helper\ModuleHelper;

/**
 * 
 * @var array $params
 */
// No direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

// Get mod content image helper data
$data = ModContentImageHelper::getData($params);

// Return, if data empty
if (!$data) {
	return false;
}

// Get layout path and render template
require ModuleHelper::getLayoutPath('mod_content_image');
?>

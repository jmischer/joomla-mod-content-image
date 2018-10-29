<?php
/**
 * 
 * @var array $params
 */

// No direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$data = ModContentImageHelper::getData($params);

require JModuleHelper::getLayoutPath('mod_content_image');
?>

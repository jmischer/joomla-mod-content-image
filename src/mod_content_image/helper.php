<?php
/**
 * 
 * @author jmischer
 *
 */
class ModContentImageHelper {
	/**
	 * 
	 * @param mixed $var
	 */
	private static function debug($var) {
		echo '<pre>' . print_r($var, true) . '</pre>';
	}
	
	/**
	 * 
	 * @return \Joomla\CMS\Application\CMSApplication
	 */
	protected static function getApplication() {
		return Joomla\CMS\Factory::getApplication();
	}
	
	/**
	 * 
	 * @param array $params
	 * @return string
	 */
	public static function getData($params) {
		// Get application
		$app = ModContentImageHelper::getApplication();
		
		// Get option and view
		$option = $app->input->get('option');
		$view = $app->input->get('view');
		$id = $app->input->getInt('id');
// 		$item_id = $app->input->getInt('Itemid');
		
		// Return null, if image directory does not exist
		if (!file_exists(JPATH_BASE . DIRECTORY_SEPARATOR . $params['image_directory'])) {
			return null;
		}
		
		// Get image by option and view
		switch ("$option#$view") {
			case "com_content#article":
				if (ModContentImageHelper::checkHidden($id, $params['hide_for_article_ids'])) {
					return false;
				}
				$article = ModContentImageHelper::getArticleInfo($id);
				$image = ModContentImageHelper::getArticleImageData($article,
						$params);
				break;
			case "com_content#category":
				if (ModContentImageHelper::checkHidden($id, $params['hide_for_category_ids'])) {
					return false;
				}
				$category = ModContentImageHelper::getCategoryInfo($id);
				$image = ModContentImageHelper::getCategoryImageData($category,
						$params);
				break;
			case "com_contact#contact":
				if (ModContentImageHelper::checkHidden($id, $params['hide_for_contact_ids'])) {
					return false;
				}
				$contact = ModContentImageHelper::getContactInfo($id);
				$image = ModContentImageHelper::getContactImageData($contact,
						$params);
				break;
			case "com_contact#category":
			default:
				$image = ModContentImageHelper::getDefaultContentImageData(
				$option, $view, $params);
				break;
		}

		// Get default image
		if (!$image) {
			$image = ModContentImageHelper::getDefaultImageData($params);
		}
		
		// Return result
		return [
			'type' => $option . '-' . $view,
			'image' => $image
		];
	}
	
	/**
	 *
	 * @param mixed $id
	 * @param array $hiddenIds
	 * @return boolean
	 */
	protected function checkHidden($id, $hiddenIds) {
		if (!is_array($hiddenIds)) {
			$hiddenIds = explode(',', str_replace(' ', '', $hiddenIds));
		}
		return in_array($id, $hiddenIds);
	}
	
	/**
	 * 
	 * @param mixed $params
	 * @return array|false
	 */
	protected static function getArticleImageData(array $article, $params) {
		// Return null, if show article image disabled
		if (!$params['show_article_image']) {
			return false;
		}
		
		// Initialize default properties
		$image_dir = $params['image_directory'];
		$image_name = ModContentImageHelper::replacePlaceholders($params['article_image_name'],
				ModContentImageHelper::getApplication()->input->getArray() + $article);
		
		// Get image path by article image name
		$image_path = ModContentImageHelper::checkImagePath($image_dir, $image_name);
		
		// Get image path by article category alias
		if (!$image_path && $params['show_default_article_image']) {
			$image_name = ModContentImageHelper::replacePlaceholders($params['alternative_article_image_name'],
					ModContentImageHelper::getApplication()->input->getArray() + $article);
			$image_path = ModContentImageHelper::checkImagePath($image_dir, $image_name);
		}
		
		// Get image path for default article image
		if (!$image_path && $params['show_default_article_image']) {
			$image_path = ModContentImageHelper::checkImagePath($image_dir, $params['default_article_image']);
		}
		
		// Return null, if no image path found
		if (!$image_path) {
			return false;
		}
		
		// Return result
		return [
			'path' => $image_path,
			'alt' => $article['title'],
		];
	}
	
	/**
	 *
	 * @param mixed $params
	 * @return array|false
	 */
	protected static function getCategoryImageData($category, $params) {
		// Return null, if show category image disabled
		if (!$params['show_category_image']) {
			return false;
		}
		
		// Initialize default properties
		$image_dir = $params['image_directory'];
		$image_name = ModContentImageHelper::replacePlaceholders($params['category_image_name'],
				ModContentImageHelper::getApplication()->input->getArray() + $category);
		
		// Get image path by category image name
		$image_path = ModContentImageHelper::checkImagePath($image_dir, $image_name);
		
		// Get image path for default category image
		if (!$image_path && $params['show_default_category_image']) {
			$image_path = ModContentImageHelper::checkImagePath($image_dir, $params['default_category_image']);
		}
		
		// Return null, if no image path found
		if (!$image_path) {
			return false;
		}
		
		// Return result
		return [
			'path' => $image_path,
			'alt' => $category['title'],
		];
	}
	
	/**
	 * 
	 * @param mixed $params
	 * @return array|false
	 */
	protected static function getContactImageData($contact, $params) {
		// Return null, if show contact image disabled
		if (!$params['show_contact_image']) {
			return false;
		}
		
		// Initialize default properties
		$image_dir = $params['image_directory'];
		$image_name = ModContentImageHelper::replacePlaceholders($params['contact_image_name'],
				ModContentImageHelper::getApplication()->input->getArray() + $contact);
		
		// Get image path by contact image name
		$image_path = ModContentImageHelper::checkImagePath($image_dir, $image_name);
		
		// Get image path by contact contact alias
		if (!$image_path && $params['show_default_contact_image']) {
			$image_name = ModContentImageHelper::replacePlaceholders($params['alternative_contact_image_name'],
					ModContentImageHelper::getApplication()->input->getArray() + $contact);
			$image_path = ModContentImageHelper::checkImagePath($image_dir, $image_name);
		}
		
		// Get image path for default contact image
		if (!$image_path && $params['show_default_contact_image']) {
			$image_path = ModContentImageHelper::checkImagePath($image_dir, $params['default_contact_image']);
		}
		
		// Return null, if no image path found
		if (!$image_path) {
			return false;
		}
		
		// Return result
		return [
			'path' => $image_path,
			'alt' => $contact['name'],
		];
	}
	
	/**
	 * 
	 * @param string $option
	 * @param string $view
	 * @param mixed $params
	 * @return array|false
	 */
	protected static function getDefaultContentImageData($option, $view, $params) {
		// Return false, if showing the default content image is not wanted 
		if (!$params['show_default_content_image']) {
			return false;
		}
		
		// Get image dir
		$image_dir = $params['image_directory'];
		
		// Get name
		$name = $params['default_content_image_name'];
		
		// Replace placeholders in name
		$name = ModContentImageHelper::replacePlaceholders($name, 
				ModContentImageHelper::getApplication()->input->getArray());
		
		// Check image path
		$image_path = ModContentImageHelper::checkImagePath($image_dir, $name);
		
		// Return null, if no image path found
		if (!$image_path) {
			return false;
		}
		
		// Return result
		return [
			'path' => $image_path,
			'alt' => "Image $option $view"
		];
	}
	
	/**
	 * 
	 * @param string $str
	 * @param array $values
	 * @return mixed
	 */
	protected static function replacePlaceholders($str, array $values) {
		// Match replace name params
		$matches = [];
		
		// Match placeholders
		preg_match_all('#{([^}]+)}#', $str, $matches);
		$place_holders = $matches[0];
		$keys = $matches[1];
		
		// Loop over found placeholders
		foreach ($place_holders as $i => $replace) {
			$key = $keys[$i];
			if (!isset($values[$key])) {
				continue;
			}
			$str = preg_replace("#$replace#", $values[$key], $str);
		}
		
		// Return str
		return $str;
	}
	
	/**
	 *
	 * @param mixed $params
	 * @return array|false
	 */
	protected static function getDefaultImageData($params) {
		// Return null, if show default image disabled
		if (!$params['show_default_image']) {
			return false;
		}
		
		// Initialize default properties
		$image_dir = $params['image_directory'];
		$default_image = $params['default_image'];
		
		// Get image path by category image name
		$image_path = ModContentImageHelper::checkImagePath($image_dir, $default_image);
		
		// Return null, if no image path found
		if (!$image_path) {
			return false;
		}
		
		// Return result
		return [
			'path' => $image_path,
			'alt' => $params['default_image_alt'] ?: 'Default Content Image'
		];
	}
	
	/**
	 * 
	 * @param string $imageDir
	 * @param string $name
	 * @param string $extension
	 * @param string $prefix
	 * @return string[]|false
	 */
	protected static function checkImagePath($imageDir, $name, $options = []) {
		// Initialize default options
		$options += [
			'extension' => null,
			'prefix' => null
		];
		
		// Append extension to name, if set
		$extension = $options['extension'];
		if ($extension) {
			if (!preg_match('#^\.#', $extension)) {
				$extension = '.' . $extension;
			}
			$name .= $extension;
		}
		
		// Build path
		$path = $imageDir . DIRECTORY_SEPARATOR . $options['prefix'] . $name;
		$fs_path = JPATH_BASE . DIRECTORY_SEPARATOR . $path;
		
		// Return false, if image does not exist
		if (!JFile::exists($fs_path)) {
			return false;
		}
		
		// Return path
		return $path;
	}
	
	/**
	 * 
	 * @return JDatabaseDriver
	 */
	protected static function getDBO() {
		return Joomla\CMS\Factory::getDbo();
	}
	
	/**
	 *
	 * @param number $id
	 * @return null|array
	 */
	protected static function getArticleInfo($id) {
		if (!$id) {
			return null;
		}
		$db = ModContentImageHelper::getDBO();
		$query = $db->getQuery(true)
			->select('a.id, a.title, a.alias, a.images, c.title as cat_title, c.alias as cat_alias')
			->from('#__content a, #__categories c')
			->where("a.id = $id AND a.catid = c.id");
		$db->setQuery($query);
		return $db->loadAssoc();
	}
	
	/**
	 *
	 * @param number $id
	 * @return array
	 */
	protected static function getCategoryInfo($id) {
		$db = ModContentImageHelper::getDBO();
		$query = $db->getQuery(true)
			->select('id, title, alias, params')
			->from('#__categories')
			->where("id = $id");
		$db->setQuery($query);
		return $db->loadAssoc();
	}
	
	/**
	 *
	 * @param number $id
	 * @return array
	 */
	protected static function getContactInfo($id) {
		$db = ModContentImageHelper::getDBO();
		$query = $db->getQuery(true)
			->select('cd.id, cd.name, cd.alias, c.title as cat_title, c.alias as cat_alias')
			->from('#__contact_details cd, #__categories c')
			->where("cd.id = $id AND cd.catid = c.id");
		$db->setQuery($query);
		return $db->loadAssoc();
	}
}
?>

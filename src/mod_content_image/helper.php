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
	 * @param array $params
	 * @return string
	 */
	public static function getData($params) {
// 		self::debug($params);
		// Get option and view
		$option = Joomla\CMS\Factory::getApplication()->input->get('option');
		$view = Joomla\CMS\Factory::getApplication()->input->get('view');
		
		// Return null, if image directory does not exist
		if (!file_exists(JPATH_BASE . DIRECTORY_SEPARATOR . $params['image_directory'])) {
			return null;
		}
		
		// Get com_content article image
		if ($option == 'com_content' && $view == 'article') {
			$image = self::getArticleImageData($params);
		}
		
		// Get com_content category image
		elseif ($option == 'com_content' && $view == 'category') {
			$image = self::getCategoryImageData($params);
		}
		
		// Get com_contact contact image
// 		elseif ($option == 'com_contact' && $view == 'contact') {
// 			$image = self::getContactImageData($params);
// 		}
		
		// Get default content image
		else {
			$image = self::getDefaultContentImageData($option, $view, $params);
		}
		
		// Get default image
		if (!$image) {
			$image = self::getDefaultImageData($params);
		}
		
		// Return result
		return [
			'type' => $option . '-' . $view,
			'image' => $image
		];
	}
	
	/**
	 * 
	 * @param mixed $params
	 * @return array|false
	 */
	protected static function getArticleImageData($params) {
		// Return null, if show article image disabled
		if (!$params['show_article_image']) {
			return false;
		}
		
		// Get article id from input
		$id = Joomla\CMS\Factory::getApplication()->input->getInt('id');
		
		// Get article
		$article = self::getArticleInfo($id);
		if (!$article) {
			return false;
		}
		
		// Initialize default properties
		$image_dir = $params['image_directory'];
		$extension = $params['article_image_extension'];
		$prefix = $params['article_image_prefix'];
		
		// Get image path by article image name
		$image_path = self::checkImagePath($image_dir,
				$article['alias'], [
					'extension' => $extension,
					'prefix' => $prefix
				]);
		
		// Get image path by article category alias
		if (!$image_path && $params['show_default_article_image']) {
			$image_path = self::checkImagePath($image_dir, $article['cat_alias'], [
				'extension' => $extension,
				'prefix' => $prefix
			]);
		}
		
		// Get image path for default article image
		if (!$image_path && $params['show_default_article_image']) {
			$image_path = self::checkImagePath($image_dir, $params['default_article_image']);
		}
		
		// Return null, if no image path found
		if (!$image_path) {
			return false;
		}
		
		// Return result
		return [
			'path' => $image_path,
			'alt' => $article['title']
		];
	}
	
	/**
	 *
	 * @param mixed $params
	 * @return array|false
	 */
	protected static function getCategoryImageData($params) {
		// Return null, if show category image disabled
		if (!$params['show_category_image']) {
			return false;
		}
		
		// Get category id from input
		$id = Joomla\CMS\Factory::getApplication()->input->getInt('id');
		
		// Get category
		$category = self::getCategoryInfo($id);
		if (!$category) {
			return false;
		}
		
		// Initialize default properties
		$image_dir = $params['image_directory'];
		$extension = $params['category_image_extension'];
		$prefix = $params['category_image_prefix'];
		
		// Get image path by category image name
		$image_path = self::checkImagePath($image_dir,
				$category['alias'], [
					'extension' => $extension,
					'prefix' => $prefix
				]);
		
		// Get image path for default category image
		if (!$image_path && $params['show_default_category_image']) {
			$image_path = self::checkImagePath($image_dir, $params['default_category_image']);
		}
		
		// Return null, if no image path found
		if (!$image_path) {
			return false;
		}
		
		// Return result
		return [
			'path' => $image_path,
			'alt' => $category['title']
		];
	}
	
	/**
	 * 
	 * @param mixed $params
	 * @return array|false
	 */
	protected static function getContactImageData($params) {
		// Return null, if show contact image disabled
		if (!$params['show_contact_image']) {
			return false;
		}
		
		// Get contact id from input
		$id = Joomla\CMS\Factory::getApplication()->input->getInt('id');
		
		// Get contact
		$contact = self::getContactInfo($id);
		if (!$contact) {
			return false;
		}
		
		// Initialize default properties
		$image_dir = $params['image_directory'];
		$extension = $params['contact_image_extension'];
		$prefix = $params['contact_image_prefix'];
		
		// Get image path by contact image name
		$image_path = self::checkImagePath($image_dir,
				$contact['alias'], [
					'extension' => $extension,
					'prefix' => $prefix
				]);
		
		// Get image path by contact contact alias
		if (!$image_path && $params['show_default_contact_image']) {
			$image_path = self::checkImagePath($image_dir, $contact['cat_alias'], [
				'extension' => $extension,
				'prefix' => $prefix
			]);
		}
		
		// Get image path for default contact image
		if (!$image_path && $params['show_default_contact_image']) {
			$image_path = self::checkImagePath($image_dir, $params['default_contact_image']);
		}
		
		// Return null, if no image path found
		if (!$image_path) {
			return false;
		}
		
		// Return result
		return [
			'path' => $image_path,
			'alt' => $contact['name']
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
		if (!$params['show_default_content_image']) {
			return false;
		}
		
		// Get image dir
		$image_dir = $params['image_directory'];
		
		// Get name
		$name = $params['default_content_image_name'];
		
		// Match replace name params
		$matches = [];
		preg_match_all('#{([^}]+)}#', $name, $matches);
		foreach ($matches[0] as $i => $replace) {
			$param = $matches[1][$i];
			$value = Joomla\CMS\Factory::getApplication()->input->get($param, '');
			$name = preg_replace("#$replace#", $value, $name); 
		}
		
		// Check image path
		$image_path = self::checkImagePath($image_dir, $name);
		
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
		$image_path = self::checkImagePath($image_dir, $default_image);
		
		// Return null, if no image path found
		if (!$image_path) {
			return false;
		}
		
		// Return result
		return [
			'path' => $image_path,
			'alt' => $category['title']
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
			'exension' => null,
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
// 		self::debug([$path, $fs_path]);
		
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
	protected function getDBO() {
		return Joomla\CMS\Factory::getDbo();
	}
	
	/**
	 *
	 * @param number $id
	 * @return null|array
	 */
	protected function getArticleInfo($id) {
		if (!$id) {
			return null;
		}
		$db = self::getDBO();
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
	protected function getContactInfo($id) {
		$db = self::getDBO();
		$query = $db->getQuery(true)
			->select('cd.id, cd.name, cd.alias, c.title as cat_title, c.alias as cat_alias')
			->from('#__contact_details cd, #__categories c')
			->where("cd.id = $id AND cd.catid = c.id");
		$db->setQuery($query);
		return $db->loadAssoc();
	}
	
	/**
	 *
	 * @param number $id
	 * @return array
	 */
	protected function getCategoryInfo($id) {
		$db = self::getDBO();
		$query = $db->getQuery(true)
			->select('id, title, alias, params')
			->from('#__categories')
			->where("id = $id");
		$db->setQuery($query);
		return $db->loadAssoc();
	}
}
?>

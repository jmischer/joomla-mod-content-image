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
		
		$image_dir = $params['image_directory'];
		if (!file_exists(JPATH_BASE . DIRECTORY_SEPARATOR . $image_dir)) {
			
		}
		$extension = '.png';
		
		// Initialize result
		$result = [
			'type' => "$option-$view",
			'image' => null,
			'alt' => null,
		];
		
		// com_content article
		if ($option == 'com_content' && $view == 'article') {
			$id = Joomla\CMS\Factory::getApplication()->input->getInt('id');
			$article = self::getArticleInfo($id);
			$result['alt'] = $article['title'];
			if (JFile::exists(JPATH_BASE . '/' . $image_dir . '/article_' . $article['alias'] . $extension)) {
				$result['image'] = $image_dir . '/article_' . $article['alias'] . $extension;
			}
			elseif (JFile::exists(JPATH_BASE . '/' . $image_dir . '/category_' . $article['cat_alias'] . $extension)) {
				$result['image'] = $image_dir . '/category_' . $article['cat_alias'] . $extension;
			}
			elseif (JFile::exists(JPATH_BASE . '/' . $image_dir . '/article_default' . $extension)) {
				$result['image'] = $image_dir . '/article_default' . $extension;
			}
		}
		
		// com_content category
		elseif ($option == 'com_content' && $view == 'category') {
			$id = Joomla\CMS\Factory::getApplication()->input->getInt('id');
			$category = self::getCategoryInfo($id);
			$result['alt'] = $category['title'];
			
			if (JFile::exists(JPATH_BASE . '/' . $image_dir . '/category_' . $category['alias'] . $extension)) {
				$result['image'] = $image_dir . '/category_' . $category['alias'] . $extension;
			}
			elseif (JFile::exists(JPATH_BASE . '/' . $image_dir . '/category_default' . $extension)) {
				$result['image'] = $image_dir . '/category_default' . $extension;
			}
		}
		
		// com_contact contact
		elseif ($option == 'com_contact' && $view == 'contact') {
			$id = Joomla\CMS\Factory::getApplication()->input->getInt('id');
			$contact = self::getContactInfo($id);
			$result['alt'] = $contact['name'];
			if (JFile::exists(JPATH_BASE . '/' . $image_dir . '/article_' . $contact['alias'] . $extension)) {
				$result['image'] = $image_dir . '/contact_' . $contact['alias'] . $extension;
			}
			elseif (JFile::exists(JPATH_BASE . '/' . $image_dir . '/category' . $contact['cat_alias'] . $extension)) {
				$result['image'] = $image_dir . '/contact_' . $contact['cat_alias'] . $extension;
			}
			elseif (JFile::exists(JPATH_BASE . '/' . $image_dir . '/contact_default' . $extension)) {
				$result['image'] = $image_dir . '/contact_default' . $extension;
			}
		}
		
		if (!isset($result['image']) && JFile::exists(JPATH_BASE . '/' . $image_dir . '/default' . $extension)) {
			$result['image'] = $image_dir . '/default' . $extension;
		}
		
		// Return result
		return $result;
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
	 * @return array
	 */
	protected function getArticleInfo($id) {
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

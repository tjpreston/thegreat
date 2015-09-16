<?php

/**
 * Catalog Helper
 * 
 */
class CatalogHelper extends AppHelper
{
	/**
	 * An array containing the names of helpers this helper uses.
	 *
	 * @var mixed A single name as a string or a list of names as an array.
	 * @access public
	 */
	public $helpers = array('Paginator');
	
	/**
	 * URL for pagination helper.
	 *
	 * @var string
	 * @access private
	 */
	private $_paginationUrl = '/../../';
	
	/**
	 * URL for sort / show selects.
	 *
	 * @var string
	 * @access private
	 */
	private $_url = '';
	
	/**
	 * Set URLs for category viewing requests.
	 * 
	 * @param array $categoryPath
	 * @return void
	 * @access public
	 */
	public function setCategoryUrls($categoryPath = array())
	{
		if (!empty($categoryPath))
		{			
			foreach ($categoryPath as $cat)
			{
				$this->_paginationUrl .= $cat['CategoryName']['url'] . '/';
			}
			
			$this->_paginationUrl = substr($this->_paginationUrl, 0, -1);
			$this->Paginator->options(array('url' => $this->_paginationUrl));			
			$this->_url = substr($this->_paginationUrl, 6) . '?';
			
		}
	}
	
	/**
	 * Set URLs for product search requests.
	 * 
	 * @param string $keyword
	 * @return void
	 * @access public
	 */
	public function setSearchUrls($keyword)
	{
		$this->Paginator->options(array('url' => '/../../search/search:' . $keyword));
		$this->_url = '/search/search:' .$keyword . '?';
	}
	
	/**
	 * Set URLs for special offers requests.
	 * 
	 * @return void
	 * @access public
	 */
	public function setSpecialsUrls()
	{
		$this->Paginator->options(array('url' => '/../../specials'));
		$this->_url = '/specials?';
	}
	
	/**
	 * Set URLs for manufacturer requests.
	 * 
	 * @return void
	 * @access public
	 */
	public function setManufacturerUrls($manuUrl)
	{
		$this->Paginator->options(array('url' => '/../../brands/' . $manuUrl));
		$this->_url = '/brands/' .$manuUrl . '?';
	}

	/**
	 * Get URL.
	 * 
	 * @return string
	 * @access public
	 */	
	public function getUrl()
	{
		return $this->_url;
	}
	
	/**
	 * Get pagination URL.
	 * 
	 * @return string
	 * @access public
	 */	
	public function getPaginationUrl()
	{
		return $this->_paginationUrl;
	}
	
	/**
	 * Get product URL.
	 * 
	 * @param array $product
	 * @return string
	 * @access public
	 */
	public function getProductUrl($product)
	{
		$url = '/';
		
		if (Configure::read('Catalog.manufacturer_name_in_product_urls') && !empty($product['Manufacturer']['url']))
		{
			$url .= $product['Manufacturer']['url'] . '/';
		}
		
		$url .= $product['ProductMeta']['url'];
		
		return $url;
		
	}
	
	public function formatSpec($spec)
	{
		$html = '<dl>';
		
		foreach ($spec as $line => $item)
		{
			$html .= '<dt>' . $item[0] . ':</dt>';
			$html .= '<dd>' . $item[1] . '</dd>';
		}
		
		$html .= '</dl>';
		
		return $html;
		
	}
	
	
	/**
	 * Re-key array of selected attribute filter values. 
	 * 
	 * @param object $selected_attributes_filter_values
	 * @return array
	 * @access public
	 */
	public function rekeySelectedAttributeValues($selected_attributes_filter_values)
	{
		$filteredValues = array();
		
		foreach ($selected_attributes_filter_values as $v)
		{
			$temp = explode(':', $v);
			$attributeID = $temp[1];
			$url = $temp[0];
			
			if (empty($filteredValues[$attributeID]))
			{
				$filteredValues[$attributeID] = array();
			}
			
			$filteredValues[$attributeID][] = $url;
			
		}
		
		return $filteredValues;
		
	}

	public function cleanSmartQuotes($str)
	{
		return $str;

		$search = array(chr(145), chr(146),	chr(147), chr(148),	chr(151));
		$replace = array("'", "'", '"', '"', '-');
		return str_replace($search, $replace, $str);
	}
	
	
	
}





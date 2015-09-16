<?php

/**
 * Giftwrap Product Model
 *
 */
class GiftwrapProduct extends AppModel
{
	/**
	 * Bind category name(s) to categories
	 *
	 * @param object $model
	 * @param mixed $languageID
	 * @param bool $reset
	 * @return void
	 * @access public
	 */
	public function bindName($languageID = null, $reset = false)
	{
		$this->bind($this, 'GiftwrapProductName', $languageID, $reset);
	}
	


}


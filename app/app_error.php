<?php

/**
 * Application Error Handler
 * 
 */
class AppError extends ErrorHandler
{
	public function error404($params)
	{
		extract($params, EXTR_OVERWRITE);

		if (!isset($url))
		{
			$url = $this->controller->here;
		}
		
		$url = Router::normalize($url);
		$this->controller->initFront();
		$this->controller->header("HTTP/1.0 404 Not Found");
		$this->controller->set(array(
			'code' => '404',
			'name' => __('Not Found', true),
			'message' => h($url),
			'base' => $this->controller->base,
			'title_for_layout' => 'Page Not Found'
		));
		$this->controller->addCrumb('', 'Page Not Found');
		
		$this->_outputMessage('error404');
		
	}
	
	public function error401($params)
	{
		extract($params, EXTR_OVERWRITE);

		if (!isset($url))
		{
			$url = $this->controller->here;
		}
		
		$url = Router::normalize($url);
		$this->controller->initFront();
		$this->controller->header("HTTP/1.0 401 Unauthorized");
		$this->controller->set(array(
			'code' => '401',
			'name' => __('Unauthorized', true),
			'message' => h($url),
			'base' => $this->controller->base
		));
		
		$this->_outputMessage('error401');
		
	}

	function _outputMessage($template)
	{
		$this->controller->helpers[] = 'AssetCompress.AssetCompress';

		parent::_outputMessage($template);
	}
	
}



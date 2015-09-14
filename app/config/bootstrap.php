<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php
 *
 * This is an application wide file to load any function that is not used within a class
 * define. You can also use this to include or require any files in your application.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * App::build(array(
 *     'plugins' => array('/full/path/to/plugins/', '/next/full/path/to/plugins/'),
 *     'models' =>  array('/full/path/to/models/', '/next/full/path/to/models/'),
 *     'views' => array('/full/path/to/views/', '/next/full/path/to/views/'),
 *     'controllers' => array('/full/path/to/controllers/', '/next/full/path/to/controllers/'),
 *     'datasources' => array('/full/path/to/datasources/', '/next/full/path/to/datasources/'),
 *     'behaviors' => array('/full/path/to/behaviors/', '/next/full/path/to/behaviors/'),
 *     'components' => array('/full/path/to/components/', '/next/full/path/to/components/'),
 *     'helpers' => array('/full/path/to/helpers/', '/next/full/path/to/helpers/'),
 *     'vendors' => array('/full/path/to/vendors/', '/next/full/path/to/vendors/'),
 *     'shells' => array('/full/path/to/shells/', '/next/full/path/to/shells/'),
 *     'locales' => array('/full/path/to/locale/', '/next/full/path/to/locale/')
 * ));
 *
 */

/**
 * As of 1.3, additional rules for the inflector are added below
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

Configure::load('config');

// CodeIgniter iamge lib constants
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

$viewPaths = array(
	VIEWS,
	VIEWS . '/admin/',
);

App::build(array(
	'views' => $viewPaths
));


/*
 * BEGIN
 * SagepayForm functions
 * @TODO why are they here ??
 *
 */
function simpleXor($inString, $key)
{
	$keyList = array();
	$output = '';
	
	for ($i = 0; $i < strlen($key); $i++)
	{
		$keyList[$i] = ord(substr($key, $i, 1));
	}
	
	for ($i = 0; $i < strlen($inString); $i++)
	{
		$output.= chr(ord(substr($inString, $i, 1)) ^ ($keyList[$i % strlen($key)]));
	}
	
	return $output;
	
}

function base64Encode($plain)
{
	$output = "";
	$output = base64_encode($plain);
	return $output;
}

function base64Decode($scrambled)
{
	$output = "";
	$scrambled = str_replace(" ", "+", $scrambled);
	$output = base64_decode($scrambled);
	return $output;
}
/*
 * END
 * SagepayForm functions
 * 
 */
 
 
/**
 * Get all $array elements apart from $key.
 * 
 * @param array $array
 * @param string $key
 * @return array
 * @access private
 */
function getAllExcluding($array, $key)
{
	$out = array();
	
	foreach ($array as $k => $v)
	{
		if (!empty($v) && ($k != $key))
		{
			array_push($out, $v);
		}
	}
	
	return $out;
	
}


function isEmpty($var)
{
	return empty($var);
}

function notEmpty($var)
{
	return !empty($var);
}



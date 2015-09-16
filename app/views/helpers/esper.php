<?php

/**
 * Esper Helper
 * 
 */
class EsperHelper extends AppHelper
{
	
	public function extendElement($file, $to, $insert, $params = array())
	{

		extract($params);
		
		$file = str_replace('elements/app', 'elements/esper', $file);
		
		$esperElement = file_get_contents($file);
		
		// $doc = new DOMDocument();
		// $doc->formatOutput = false;
		
		// @$doc->loadHTML($esperElement);
		// $msg = $doc->createTextNode($insert);
		// $leadtime = $doc->getElementById($to);
		// $leadtime->appendChild($msg);
		
		// $out = substr($doc->saveHTML(), 107, -15);
		// $out = str_replace('<html><body>', '', $out);
		// $out = html_entity_decode($out);
		
		
		$out = "?" . ">" . $esperElement;
		
		eval($out);
		
	}
	
}



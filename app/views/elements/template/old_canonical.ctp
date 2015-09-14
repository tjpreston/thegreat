<?php

/**
 * This element will generate a canonical tag to convert new Esper URLs into their Iona equivalent.
 * This was used when the Iona site was still live, and the Esper site was on popcorndev, and we needed
 * a canonical tag to reference to the equivalent URL on the live website.
 *
 * Now the Esper site is live, this should no longer be used.
 */

$canonicalDomain = 'http://www.michelherbelin.co.uk';

$canonical = null;

switch ($this->params['controller']) {
	case 'pages':
		require APP . 'config' . DS . 'oldsite_rewrites' . DS . 'page_rules.php';
		$pageRules = array_flip($pageRules);

		if(isset($pageRules[$this->here])){
			$canonical = $canonicalDomain . $pageRules[$this->here];
		}

		break;

	case 'catalog':
		if($this->params['action'] != 'view_product' || empty($record['Product']['id'])){
			break;
		}

		require APP . 'config' . DS . 'oldsite_rewrites' . DS . 'product_rules.php';
		$productRules = array_flip($productRules);

		if(isset($productRules[$record['Product']['id']])){
			$canonical = $canonicalDomain . $productRules[$record['Product']['id']];
		}

		break;
	
	default:
		$canonical = $canonicalDomain;
		break;
}

if(!empty($canonical)){
	echo '<link rel="canonical" href="' . $canonical . '" />';
}

?>
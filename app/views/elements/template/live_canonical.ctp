<?php

/**
 * This element will generate a canonical tag to convert popcorndev URLs to their live equivalent.
 */

$canonicalDomain = 'http://www.michelherbelin.co.uk';

$canonical = $canonicalDomain . $this->here;

if(!empty($canonical)){
	echo '<link rel="canonical" href="' . $canonical . '" />';
}

?>
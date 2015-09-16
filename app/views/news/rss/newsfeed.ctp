<?php

    $this->set('documentData', array(
        'xmlns:dc' => 'http://purl.org/dc/elements/1.1/'));

    $this->set('channelData', array(
        'title' => __("Hytek GB Recent News", true),
        'link' => $this->Html->url('/', true),
        'description' => __("Hytek GB Recent News.", true),
        'language' => 'en-uk'
	));

	foreach ($posts as $post) 
	{
        $postTime = strtotime($post['Article']['created']);
 
        $postLink = '/news/' . $post['Article']['slug'];

        // You should import Sanitize
        App::import('Sanitize');

        // This is the part where we clean the body text for output as the description 
        // of the rss item, this needs to have only text to make sure the feed validates
        $bodyText = preg_replace('=\(.*?\)=is', '', $post['Article']['blurb']);
        $bodyText = $this->Text->stripLinks($bodyText);
        $bodyText = Sanitize::stripAll($bodyText);
        $bodyText = $this->Text->truncate($bodyText, 400, array(
            'ending' => '...',
            'exact'  => true,
            'html'   => true,
        ));
 
        echo $this->Rss->item(array(), array(
            'title' => $post['Article']['name'],
            'link' => $postLink,
            'guid' => array('url' => $postLink, 'isPermaLink' => 'true'),
            'description' =>  $bodyText,
            'dc:creator' => 'Hytek GB Ltd',
            'pubDate' => $post['Article']['published'])
		);

    }

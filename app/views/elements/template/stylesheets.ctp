<?php

$this->AssetCompress->css('reset');
$this->AssetCompress->css('utils');
$this->AssetCompress->css('template');
$this->AssetCompress->css('nav');
$this->AssetCompress->css('home');
$this->AssetCompress->css('forms');
$this->AssetCompress->css('catalog');
$this->AssetCompress->css('basket');
$this->AssetCompress->css('customers');
$this->AssetCompress->css('checkout');
$this->AssetCompress->css('news');

echo $this->AssetCompress->includeAssets();


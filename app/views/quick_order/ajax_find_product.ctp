<?php 

if (!empty($product))
{
	$data = array();
	
	$data['id'] = $product['Product']['id'];

	if (!empty($product['ProductImage'][0]['tiny_web_path']))
	{
		$data['img'] = $product['ProductImage'][0]['tiny_web_path'];
	}
	
	if (!empty($product['ProductOptionStock']))
	{	
		$data['optionid'] = $product['ProductOptionStock']['id'];
		$data['name'] = $product['ProductOptionStock']['sku'] . ' - ' . $product['ProductName']['name'];
		
		// $data['id'] .= $product['ProductOptionStock']['id'];
		// $data['name'] .= ' (' . $product['ProductOptionStock']['name'] . ')';
		
		// $data['option_ids'] = $product['ProductOptionStock']['option_ids'];
		// $data['value_ids'] = $product['ProductOptionStock']['value_ids'];
	}
	else
	{
		$data['name'] = $product['Product']['sku'] . ' - ' . $product['ProductName']['name'];
	}

	if (!empty($price))
	{
		$data['price'] = $price;
	}
	
	echo json_encode($data);
	
}


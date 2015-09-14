<?php

echo "<script>\n";

$stockToValues = "var optionValueIDs = new Array();\n";
$valuesToSort  = "var IDorders = new Array();\n";
$valuesToStock = "var valuesStockIDs = new Array();\n";

$i = 1;

foreach ($optionsStock as $k => $line)
{
	$split = str_replace('-', ', ', $line['ProductOptionStock']['value_ids']);
	
	$stockToValues .= 'optionValueIDs[' . $line['ProductOptionStock']['id'] . '] = [' . $split . "];\n";
	$valuesToSort  .= 'IDorders["' . str_replace(', ', '-', $split) . '"] = ' . $i . ";\n";
	$valuesToStock .= 'valuesStockIDs["' . str_replace(', ', '-', $split) . '"] = ' . $line['ProductOptionStock']['id'] . ";\n";

	$i++;

}

echo $stockToValues . "\n";
echo $valuesToSort . "\n";
echo $valuesToStock . "\n";

echo "</script>";


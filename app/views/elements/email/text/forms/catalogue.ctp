Hi

Somebody has requested that they receive a <?php echo Configure::read('Site.name'); ?> catalogue in the post.
Their details are as follows:

Name: <?php echo $data['title'] . ' ' . $data['first_name'] . ' ' . $data['surname']; ?> 
Email: <?php echo $data['email']; ?> 
Telephone: <?php echo $data['telephone']; ?> 

Address: 
<?php

echo $data['address_line_1'] . "\n";

if(!empty($data['address_line_2'])){
	echo $data['address_line_2'] . "\n";
}

if(!empty($data['address_line_3'])){
	echo $data['address_line_3'] . "\n";
}

echo $data['town'] . "\n";
echo $data['county'] . "\n";
echo $data['postcode'];

?>
<?php

/**
 * NewsletterRecipient Model
 * 
 */
class NewsletterRecipient extends AppModel
{	
	public $validate = array(
		'email' => array(
			'rule' => 'email',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a valid email address'
		)
	);

	public function generateCSV(){
		$records = $this->find('all');

		$tmpFilename = TMP . 'recipients_download.csv';
		$fh = fopen($tmpFilename, 'w+');

		$headers = array(
			'Email Address',
			'Date Created'
		);
		fputcsv($fh, $headers);

		foreach($records as $record){
			$row = array(
				$record[$this->name]['email'],
				$record[$this->name]['created']
			);
			fputcsv($fh, $row);
		}

		fclose($fh);

		return $tmpFilename;

	}
}
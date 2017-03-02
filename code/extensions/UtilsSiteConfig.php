<?php

class UtilsSiteConfig extends DataExtension {

	private static $has_one = array(
		'FallbackImage' => 'Image'
	);

	public function updateCMSFields(FieldList $fields)
	{
		$fields->addFieldToTab('Root.Media', new UploadField('FallbackImage'));
	}

}
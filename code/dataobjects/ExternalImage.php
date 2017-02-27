<?php

class ExternalImage extends Image
{

	private static $db = array(
		'ExternalURL' => 'Varchar(255)',
		'IsDownloaded' => 'Boolean'
	);

	public function downloadExternal()
	{
		die();
		if ($this->ExternalURL) {
			try {
				$client = new Guzzle\Http\Client();
				$request = $client->get($this->ExternalURL, array(), array(
					'save_to' => '/path/to/file'
				));
				$this->IsDownloaded = true;
				$this->write();
			} catch (Exception $e) {
				return false;
			}
			return true;
		}
		return false;
	}
}




//Test csv: https://daisycon.io/datafeed/?filter_id=14510&settings_id=3290
//Flow: csv download
//csv import
//csv verwijderen
//last import date zetten
<?php

class ExternalImage extends Image
{

	private static $db = array(
		'ExternalURL' => 'Varchar(255)',
		'IsDownloaded' => 'Boolean'
	);

	public function __construct($record = null, $isSingleton = false, $model = null)
	{
		parent::__construct($record, $isSingleton, $model);
		if($this->ID && !$this->IsDownloaded){
			$this->downloadExternal();
		}
	}

	protected function validate() {
		return new ValidationResult(true);
	}

	public function downloadExternal()
	{
		if ($this->ExternalURL && !$this->IsDownloaded) {
			try {
				$client = new \GuzzleHttp\Client();
				$res = $client->request('GET', $this->ExternalURL);
				file_put_contents($this->getFullPath(), $res->getBody());
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
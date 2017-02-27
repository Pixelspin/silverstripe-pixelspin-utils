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
		if(!$this->exists()){
			$this->downloadExternal();
		}
	}

	public function downloadExternal()
	{
		if ($this->ExternalURL && !$this->exists()) {
			try {
				$client = new \GuzzleHttp\Client();
				$res = $client->request('GET', $this->ExternalURL);
				$extension = File::get_file_extension($this->ExternalURL);
				file_put_contents($this->getFullPath() . '.' . $extension, $res->getBody());
				$this->IsDownloaded = true;
				$this->setName($this->Name . '.' . $extension);
				$this->write();
			} catch (Exception $e) {
				return false;
			}
			return true;
		}
		return false;
	}
}
<?php

class ExternalImage extends DataObject
{

	private static $indexes = array(
		'ExternalURL' => true
	);

	private static $db = array(
		'ExternalURL' => 'Varchar(255)',
		'DownloadTryCount' => 'Int',
		'RelationName' => 'Varchar',
		'RelationType' => "Enum('has_one,many_many','has_one')",
		'FileName' => 'Varchar(255)',
		'Folder' => 'Varchar(255)',
		'TargetClass' => 'Varchar',
		'TargetID' => 'Int'
	);

	public function getTarget(){
		if(!$this->TargetClass || !$this->TargetID){
			return false;
		}
		$target = singleton($this->TargetClass);
		return $target::get()->byID($this->TargetID);
	}

	public function tryToDownload()
	{
		//Check target
		if(!$target = $this->getTarget()){
			$this->delete();
			return false;
		}
		//Check external url
		if(!$this->ExternalURL){
			$this->delete();
			return false;
		}else{
			//Try to download
			try {
				//Download file
				$client = new \GuzzleHttp\Client();
				$res = $client->request('GET', $this->ExternalURL);
				//Extension
				$extension = 'jpg';
				$contentType = $res->getHeader('Content-Type');
				if($contentType){
					$contentType = $contentType[0];
					switch ($contentType){
						case 'image/gif':
							$extension = 'gif';
							break;
						case 'image/png':
							$extension = 'png';
							break;
					}
				}
				//Create image object
				$folder = Folder::find_or_make($this->Folder);
				$image = new Image();
				$image->setName($this->FileName . '.' . $extension);
				$image->Title = $this->FileName;
				$image->setParentID($folder->ID);
				$image->write();
				//Save image
				file_put_contents($image->getFullPath(), $res->getBody());
			} catch (Exception $e) {
				//Download error
				$this->downloadError();
				return false;
			}
			//Save relation
			$relationName = $this->RelationName;
			if($this->RelationType == 'has_one'){
				$relationName = $relationName . 'ID';
				$target->$relationName = $image->ID;
				$target->write();
			}else{
				$target->$relationName()->add($image);
			}
			//Delete
			$this->delete();
			return true;
		}
	}

	public function downloadError(){
		if($this->DownloadTryCount == 2){
			$this->delete();
		}else{
			$this->DownloadTryCount = $this->DownloadTryCount + 1;
			$this->write();
		}
	}

}
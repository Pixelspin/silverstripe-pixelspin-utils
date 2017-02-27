<?php

class DownloadExternalImagesTask extends BuildTask {

		protected $title = 'Download external images';
		protected $description = 'Download external images and save them as asset';

		public function run($request) {
				$images = ExternalImage::get()->filter(array(
										'IsDownloaded' => false
								))->limit(10);
				foreach ($images as $image) {
						$image->downloadExternal();
				}
		}

}

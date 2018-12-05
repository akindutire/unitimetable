<?php 

	namespace zil\factory;
	use \zil\config\Config;
	use \finfo;

	class Filehandler extends Config{

			public function __construct(){
				
				$this->init();

			}

			private function init(){
				
			}

			/**
			*	Directory Methods
			*/

			public function isDir(string $pathdir){

				if (is_dir($pathdir) || file_exists($pathdir))
					return true;

				return null;
			}

			public function createDir(string $path, int $mode=0777){
			   
			    if(!is_dir($path))
			    	return mkdir($path,$mode,true);

			    return null;
			}

			public function createFile(string $filename,$context = null){

			    if(!is_dir(dirname($filename)))
                    $this->createDir(dirname($filename));
                
				try{
					
					$handle =   fopen($filename,"w");
					
					if(is_null($handle))
						throw new Exception("Couldn't open the file {$filename} for writing");

					if(fwrite($handle,$context) !== false) {
                 
						fclose($handle);
					
						return true;
					}

					fclose($handle);

				}catch(\Exception $e){
					
					echo $e->getMessage();
					return false;
				}            
            }


			public  function copy(string $source, string $destination, $context = null){

			    if(is_dir($source) && is_dir($destination))
			        return copy($source,$destination,$context);

			    return null;
			}

			public function removeDir(string $dir){

			    $files = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::CHILD_FIRST);

				error_reporting(0);
			    foreach ( $files as $finfo ){
					
					$op = ($finfo->isDir() ? 'rmdir' : 'unlink');
			        $op($finfo->getRealPath());
                }
				return rmdir($dir);
			}

			public function rename(string $old_dir, string $new_dir){
		
				return rename($old_dir, $new_dir);		
			}

			public function openFile(string $filename, string $width='200px', string $height='auto'){

				$finfo = new finfo(FILEINFO_MIME_TYPE);
				
				$type = mime_content_type("{$_SERVER['DOCUMENT_ROOT']}{$filename}");
				
				$string = null;

				if($type == 'image/jpeg' || $type == 'image/gif' || $type == 'image/bmp' || $type == 'image/png' || $type == 'image/webp' || $type == 'image/jpg'){

					$string = "<img style='' src='$filename' width='$width' height='$height'>";

				}else if ($type == 'video/3gpp' || $type == 'video/jpm' || $type=='video/jpeg' || $type=='video/mp4' || $type=='video/mpeg' || $type=='video/x-matroska' || $type=='video/quicktime' || $type=='video/ogg' || $type=='video/webm') {

					$string = "<video style='' src='$filename' width='$width' height='$height' controls></video>";
					
				}else if($type == 'audio/vnd.dts' || $type == 'audio/mpeg' || $type=='audio/mp4' || $type=='audio/ogg' || $type=='audio/x-pn-realaudio' || $type=='audio/wav' || $type=='audio/mp3'){

					if ($type == 'audio/mp3') {
					
						$type = 'audio/mpeg';
					
					}

					$string = "<audio src='$filename' controls style=''></audio>";

				}else if($type == 'application/msword' || $type == 'application/vnd.ms-word.document.macroenabled.12' || $type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){

					$string = "<button style='border:none; background: mediumslateblue;' id='btn' href='$filename'>Open File</button>";

				}else if($type == 'application/vnd.ms-powerpoint' || $type =='application/vnd.ms-powerpoint.template.macroenabled.12' || $type =='application/vnd.openxmlformats-officedocument.presentationml.template' || $type == 'application/vnd.ms-powerpoint.addin.macroenabled.12' || $type == 'application/vnd.cups-ppd' || $type == 'image/x-portable-pixmap' || $type == 'application/vnd.ms-powerpoint' || $type == 'application/vnd.ms-powerpoint.slideshow.macroenabled.12' || $type == 'application/vnd.openxmlformats-officedocument.presentationml.slideshow' || $type == 'application/vnd.ms-powerpoint' || $type == 'application/vnd.ms-powerpoint.presentation.macroenabled.12' || $type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation'){

						$string = "<a  style='border:none; background: mediumslateblue;' id='btn' href='$filename'>Open File</a>";

				}else if ($type == 'application/pdf') {
					
					$string = "<iframe style='' width='$width' height='$height' src='$filename' style='border:none;'></iframe>";
				}

				return $string;
			}
	}
?>
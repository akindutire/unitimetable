<?php 

	namespace zil\factory;
	use \zil\config\Config;
	use \finfo;

	class Fileuploader extends Config{

			private static $ERROR = null;
			private static $ERR_CODE = null;

			public function __construct(){
				
				
			}

        /**
         * @param array $data
         */
        public function init($data=[]){



				/**
				*
				*	Array Format as ['file'=>filename,'size'=>expected_size in byte,'type'=>[expected_type1,expected_type2],destination=>path,compress=>true]
				*
				*/

				$Log = new Logger;

				$file = $data['file'];


				if(array_key_exists('compress',$data) != true)
				    $data['compress'] = false;
                

				if($this->checkFileValid($file) != 0){

					if ($this->checkFileType($file,$data['type']) != 0) {
						
						if($this->checkFileSize($file,$data['size']) != 0){

							if($data['compress'] == true){
								
								
								$mime_type = mime_content_type($file);

								/** 
								*	To Ensure image file are compressed using the right method 
								*/

								if(strpos($mime_type, 'image') !== false){

									
									if($this->compressImg($file,$data['destination'],80) == true){

										Logger::Log("{$file} Compressed and Uploaded to {$data['destination']}");
									
										return true;

									}else{

										self::$ERR_CODE = 'FCE';
										
										self::$ERROR = "Couldn't complete File Upload and Compression of {$file} to {$data['destination']} ".__LINE__." in ".__METHOD__;
										
										Logger::Log(self::$ERROR);

										return null;
									}

								}else{


									if($this->compressToZip($file) == true){

										Logger::Log("{$file} Compressed and Uploaded to {$data['destination']}");
										
										return true;

									}else{

										self::$ERR_CODE = 'FCE';
										
										self::$ERROR = "Couldn't complete File Upload and Compression of {$file} to {$data['destination']} ".__LINE__." in ".__METHOD__;
										
										Logger::Log(self::$ERROR);

										return null;
									
									}

								}

							}else{

								if(move_uploaded_file($file, $data['destination'])){
									
									Logger::Log("{$file} Uploaded to {$data['destination']}");
									
									return true;

								}else if($this->renameFile($file, $data['destination'])){

									Logger::Log("{$file} Moved to {$data['destination']}");
									
									return true;

								}else{
									
									self::$ERR_CODE = 'FUE';
									
									self::$ERROR = "Couldn't complete File Upload of {$file} to {$data['destination']} ".__LINE__." in ".__METHOD__;
									
									Logger::Log(self::$ERROR);

								}
							}

						}else{

							self::$ERR_CODE = 'FSE';
							
							self::$ERROR = "{$file} is too large, Expecting {$data['size']} on line ".__LINE__." in ".__METHOD__;
							
							Logger::Log(self::$ERROR);
							
							return null;
						
						}

					}else{

						
						$expected = implode(',', $data['type']);
						
						self::$ERR_CODE = 'FTE';
						
						self::$ERROR = "{$file} is not an Expected type, Expecting {$expected} file on line ".__LINE__." in ".__METHOD__;
						
						Logger::Log(self::$ERROR);
						
						return null;
					
					}

				}else{

					self::$ERR_CODE = 'FNE';
					
					self::$ERROR = "{$file} is not a file on line ".__LINE__." in ".__METHOD__;
					
					Logger::Log(self::$ERROR);
					
					return null;
				
				}

				return null;

			}


			public function error(){
            
                return self::$ERROR;

            }

            public function errorCode(){

            	return self::$ERR_CODE;
            }

			private function checkFileValid($file){
			
				if(file_exists($file))
					return true;
				else
					return 0;
			
			}


			private function checkFileType($file,$data_accept_type=[]){

				if(in_array('*/*',$data_accept_type) )
					return true;

			
				$mime_type = mime_content_type($file);
				
				$result = array_search($mime_type,$data_accept_type);

				
				if(is_int($result))
					return true;
				else
					return 0;
				
				
			}



			private function checkFileSize($file, $maximum_size){
				
				if($maximum_size >= filesize($file))
					return true;
				else
					return 0;

			}


			private function compressImg($file, $destination, $quality){
				

				if($this->checkFileType($file,['image/jpeg','image/png','image/gif'])){

					try {

						$IMG_INFO =getimagesize($file);
						
						if ($IMG_INFO['mime'] = 'image/jpeg'){
						
							$image = imagecreatefromjpeg($file);

						}else if ($IMG_INFO['mime'] = 'image/gif'){

							$image = imagecreatefromgif($file);
						
						}else if($IMG_INFO['mime'] = 'image/png'){

							$image = imagecreatefrompng($file);

						}else{
						    
						    throw new \Exception("Couldnt get Image details in ".__METHOD__." On Line ".__LINE__);
                        
                        }

						if(imagejpeg($image, $destination, $quality)){

							return true;
						
						}else{
						
							return false;
						
						}

					} catch (\Exception $e) {
						
						self::$ERROR = "Error creating Image From Source in ".__METHOD__." On Line ".__LINE__." | ".$e->getMessage();
						
						Logger::Log(self::$ERROR);

					}
				}

				return $destination;

			}


			public function compressToZip($file){

			}
			

			public function renameFile($file,$newfile){
			
				if (rename($file, $newfile))
					return true;
				else
					return 0;
			
			}

	}
?>
<?php 

	namespace zil\factory;
	use \zil\config\Config;


	class Security extends Config{



		public function __construct(){

		}

		public function encryptData($data,$key){
			//USING SHA256
			
			$klen = strlen($key);

			if(strlen($key) > 16){

				for($i = (17); $i <= $klen; $i++){
					
					$key[$i] = null;
				}

				$key = '$5$rounds=5000$'.$key.'$';

				if (CRYPT_SHA256 == 1) {
	    		
	    			$hashed = crypt($data, $key);
				}else{

					Logger::Log("Encryption Algorithm Not Found at ".__METHOD__." around ".__LINE__);

					$hashed = 0;
				}

			}else{

				Logger::Log("Encryption Key Must be At Least 16 ".__METHOD__." around ".__LINE__);

				$hashed = 0;
			}

			return $hashed;
		}

		public function hash($data,$securitylevel=11){

			if ($securitylevel > 31){
			
				$securitylevel = 31;
			
			}else if ($securitylevel < 4) {
			
				$securitylevel = 4;
			
			}

			$option = ['cost' => $securitylevel ];

				$data = $this->Encode($data);
				
				if($hashed = password_hash($data,PASSWORD_BCRYPT,$option)){

					if (password_needs_rehash($hashed,PASSWORD_BCRYPT,$option)) {
						
						$new_result = password_hash($data,PASSWORD_BCRYPT,$option);	

						return $new_result;
					
					}else{
						
						return $hashed;
					
					}
				
				}else{

                    Logger::Log("Encryption Fails at ".__METHOD__." on line ".__LINE__);

                    return 0;

				}

		}

		public function hashVerify($userpassword, $knownhash){
			
			$userhash = $this->Encode($userpassword);
			if(password_verify($userhash,$knownhash) == true)
				return true;
			
			return false;
		}

		private function primeSwitcher($string){

		    $secret 	= 	$string;

		    $count 		= 	0;

			$A 			= 	[1,1,1];


			for($j=$A[2]; $j < strlen($string); $j++){
				
				for ($i=$A[2]; $i < strlen($string); $i++) { 
					
					
					$is_prime = pow(2,($i-1)) % $i;

					if($is_prime == 1){

						if ($count <= 1) {

							$A[$count] = $i;
							
							/*"Sees $i as prime";*/

							$count+=1;
						
						}else{

							/* Prime Count Exceeded";*/
							break;
						}

					}else{

						/*echo "Not Prime Skipped $i <br>";*/

						continue;
					}
			
				}
				

				if ($A[1] != null) {
					

					/*$A[1]."  Was saved as the Conjugate Prime to be last prime seen<br>";*/
					/*Next Iteration Start From lastPrime saved*/
					
					$tmp = $string[$A[0]];
					
					$string[$A[0]] = $string[$A[1]];
					
					$string[$A[1]] = $tmp;
					
					$A[2] =  $A[1];
					
					$A[1] =  null;
					
					$A[0] =  null;
					
					$count = 0;
					
				}else{

					/*"No Conjugate Prime Found, No Need for swaps";*/
					break;
				}

			}

            return $secret.base64_encode("XX").$string;

		}


		private function primeDeSwitcher($string){


			$length_of_original_key = strpos(trim($string),base64_encode("XX"));

			$DS = substr(trim($string),0,$length_of_original_key);

            return $DS;

		}

        private function MapChars($data){

            /**
             * /,\,'," are none acceptable characters, it is nullified by default
             */

            $data = str_replace("\"",null,$data);

            $data = str_replace("\\",null,$data);
            
            $data = str_replace("/",null,$data);
            
            $data = str_replace("'",null,$data);

            

            $enc_table = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789 .,!@#$%^&*()_-+=-|?[]{}:;<>';

            $to_be_encoded = $data;

            $new_encoded = null;

            $pos = null;

            for($i=0; $i<strlen($data); $i++){

                $char_pos = strpos($enc_table,$to_be_encoded[$i]);
               
                if ($char_pos !== false) {

                    for($j=0; $j<strlen($enc_table); $j++){

                        if($to_be_encoded[$i]===$enc_table[$j]){

                            $pos = -$j;


                            $new_encoded .= $enc_table[$pos];

                        }else{

                            continue;
                        }
                    }

                }else{

                    continue;
                }

            }

            if (empty($new_encoded)) {
                $new_encoded = $to_be_encoded;
            }

            return $new_encoded;

        }

		public function Encode($data){

            $new_data = $this->primeSwitcher($this->MapChars($data));

            return $new_data;

		}


		public function Decode($data){

		    $decoded = $this->MapChars($this->primeDeSwitcher($data));
            
            return $decoded;

		}

	
		public function decrypt($encrypted,$key){

			#future
		}


	}
?>
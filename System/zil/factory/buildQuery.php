<?php

namespace zil\factory;

use \zil\Config\Config;

	class BuildQuery extends Config{

		private static $connection_handle 		= 	null;	
		
		private $lastLogicalOfConditionString 	=	null;
		private $ConditionString 	= 	null;
		private $ConditionValue 		= 	[];


		public function __construct($connection_handle){
			
			if($connection_handle != null)
				self::$connection_handle = $connection_handle;
			else
				Logger::Log("Couldn't initialize CRUD Class, no database resource found");
			
		}

		public function create($table,$data=[]){

			try {
				if(!is_array($data))
					throw new \Exception(__METHOD__." argument #2 expect an array ".gettype($data)." given");
					
					$length = sizeof($data);		
			} catch (\Exception $e) {

				Logger::Log($e->getMessage());
				return null;
			}
			
			$i=1; $variable_space = null;
			
			while ($i <= $length) {
			
				$variable_space.='?,';
				$i++;
			}

			$variable_space = rtrim($variable_space,',');
			$query = "INSERT INTO $table VALUES($variable_space)";
            Logger::Log($query);

			try {

				if(is_null(self::$connection_handle))
					throw new \PDOException(__METHOD__."::Error: Database Resource not found");
						
				$rs = self::$connection_handle->prepare($query);
				$rs->execute($data);

				if ($rs->rowCount() == 1)
					return true;
				else
					throw new \PDOException();
				
			} catch (\PDOException $e) {

                Logger::Log($e->getMessage());
				return null;
			}
		}

		public function read($table, $data=[[[]]], $data_field_selected=[], $extra=[]){
			
			try{
				
				if(!\is_array($data))
					throw new \Exception(__METHOD__." argument #2 expect an array ".gettype($data)." given");
					
				if(!\is_array($data_field_selected))
					throw new \Exception(__METHOD__." argument #3 expect an array ".gettype($data_field_selected)." given");
				
				if(!\is_array($extra))
					throw new \Exception(__METHOD__." argument #4 expect an array ".gettype($extra)." given");
				

				$length =  sizeof($data) != 0 ? sizeof($data) : null; 	
				$condition = null;
				$extralength =  sizeof($extra) != 0 ? sizeof($extra) : null;
				$ConditionAndValue = ['condition'=>'','value'=>[]];
				
				if (sizeof($data_field_selected) == 0) {
					
					$field_to_select = '*';
				}else{

					$field_to_select = null;
					foreach ($data_field_selected as $field_to_selecting) {
						
						$field_to_select.= "{$field_to_selecting},";
					}
					$field_to_select = rtrim($field_to_select,',');
				}

				$extra_query = null;

				if ($extralength != null) 
					$extra_query = $extra[0];

				if ($length != null) {
					
					$ConditionAndValue = $this->extractCondition($data);
					$condition = $ConditionAndValue['condition'];
					$query = "SELECT $field_to_select FROM $table WHERE $condition $extra_query";
				}else{	
				
					$query = "SELECT $field_to_select FROM $table $extra_query";
				}
				
				try {

					if(is_null(self::$connection_handle))
						throw new \PDOException(__METHOD__."::Error: Database Resource not found");

                    Logger::Log($query);
					
					$rs = self::$connection_handle->prepare($query);
					
					if(!is_object($rs))
						throw new \PDOException(__METHOD__."::Error: Database Resource not found");

					if ($rs->execute($ConditionAndValue['value']) != false){
					
						return $rs;
					}else{

                        throw new \PDOException(__METHOD__."::Error: Couldn't execute Query");
					}

				} catch (\PDOException $e) {
                    
                    Logger::Log($e->getMessage());
					return null;
				}

			}catch(\Exception $e){

				Logger::Log($e->getMessage());	
			}
            return null;
		}

		public function update($table,$data=[[[]]],$data_field_updated=[[]],$extra=[]){
			
			try{

				if(!\is_array($data))
					throw new \Exception(__METHOD__." argument #2 expect an array ".gettype($data)." given");
					
				if(!\is_array($data_field_updated))
					throw new \Exception(__METHOD__." argument #3 expect an array ".gettype($data_field_updated)." given");
				
				if(!\is_array($extra))
					throw new \Exception(__METHOD__." argument #4 expect an array ".gettype($extra)." given");


				$length =  sizeof($data)!=0?sizeof($data):null; 	
				$condition = null;
				$extralength =  sizeof($extra)!=0?sizeof($extra):null;
				
				$ConditionAndValue = ['condition'=>'','value'=>[]];

				$field_to_update = null;
				
				foreach ($data_field_updated as $as_update_array) {

					if (!is_array($as_update_array) && sizeof($as_update_array) != 2)
						throw new \Exception("SQL Error: Line ".__LINE__." on ".__METHOD__." Expecting Nested Array as Arguement, Expecting two(2) parameters");
							
					$field = $as_update_array[0];		
					$field_val = $as_update_array[1];

					$field_to_update.=	"{$field} = '{$field_val}',";				
				}

				$field_to_update = rtrim($field_to_update,",");

				$extra_query = null;
				
				if ($extralength != null)				
					$extra_query = $extra[0];
				
				
				if ($length != null) {
				
					$ConditionAndValue = $this->extractCondition($data);
					$condition = $ConditionAndValue['condition'];
					$query = "UPDATE $table SET $field_to_update  WHERE $condition $extra_query";
				}else{	
				
					$query = "UPDATE $table SET $field_to_update $extra_query";
				}

				try {		

					if(is_null(self::$connection_handle))
						throw new \PDOException(__METHOD__."::Error: Database Resource not found");

					Logger::Log($query);
					
					$rs = self::$connection_handle->prepare($query);
					
					if(!is_object($rs))
						throw new \PDOException(__METHOD__."::Error: Database Resource not found");

					if ($rs->execute($ConditionAndValue['value']) != false){
					
						return $rs;
					}else{

                        throw new \PDOException(__METHOD__."::Error: Couldn't execute Query");
					}

				} catch (\PDOException $e) {
                    
                    Logger::Log($e->getMessage());
					return null;
				}

			}catch(\Exception $e){
                
                Logger::Log($e->getMessage());
				return null;
			}
            
		}

		public function delete($table,$data=[[[]]],$extra=[]){
		
			try{

				if(!\is_array($data))
					throw new \Exception(__METHOD__." argument #2 expect an array ".gettype($data)." given");
					
				
				if(!\is_array($extra))
					throw new \Exception(__METHOD__." argument #3 expect an array ".gettype($extra)." given");

				$length =  sizeof($data) != 0 ? sizeof($data) : null; 	
				$condition = null;
				$extralength =  sizeof($extra) != 0 ? sizeof($extra) : null;
				$ConditionAndValue = ['condition'=>'','value'=>[]];

				$extra_query = null;
			
				if ($extralength != null)	
					$extra_query = $extra[0];
			

				$query = "DELETE FROM $table $extra_query";	
				
				if ($length != null) {
									
					$ConditionAndValue = $this->extractCondition($data);
					$condition = $ConditionAndValue['condition'];
					$query = "DELETE FROM $table WHERE $condition $extra_query";

				}

				try {
					
                    Logger::Log($query);
		
					$rs = self::$connection_handle->prepare($query);
					
					if(is_null(self::$connection_handle))
						throw new \PDOException(__METHOD__."::Error: Database Resource not found");

					if ($rs->execute($ConditionAndValue['value']) != false){
	
						return $rs;
					}else{

						throw new \PDOException(__METHOD__."::Error: Couldn't execute Query");					
					}
						
				} catch (\PDOException $e) {
        
                    Logger::Log($e->getMessage());
					return null;
				}

			}catch(\Exception $e){
                Logger::Log($e->getMessage());
				return null;
			}
		}

		public function truncate($table){

			$query = "TRUNCATE $table";

			try {

				if(is_null(self::$connection_handle))
					throw new \PDOException("Database Resource not found");

                Logger::Log($query);
               
                $rs = self::$connection_handle->prepare($query);
               
                if ($rs != false){

                    return $rs;
                }else{

					throw new \PDOException(__METHOD__."::Error: Couldn't execute Query");
                }

            } catch (\PDOException $e) {
               
                Logger::Log($e->getMessage());
                return null;
            }
		}

		private function extractConditionEx($array,$index = 0){
			
			$array = array_values($array);

			if(!is_array($array))
				Logger::Log(__METHOD__."::Error: #1 not an array");	
			
			if(sizeof($array) != 0){

				$defaultLogicalOperator = "AND";

				while($index < sizeof($array)){

					if(!isset($array[$index]))
						break;

					if(is_array($array[$index]) && sizeof($array[$index]) != 0){

						$sub_array = $this->checkArray($array[$index]);

						/**
						*Sub Arrays must be more than 2 otherwise it is Ignored
						*/

						if ($sub_array != false) {
							
							$this->ConditionString .= " (";

							$this->extractConditionEx($sub_array,0);
							
						}else{

							if (sizeof($array) == 1) {
							
				    			if (sizeof($array[$index])  ==  2) {

									$this->ConditionString .= "{$array[$index][0]} = ? ";
									
									array_push($this->ConditionValue, $array[$index][1]);
								

								}else if (sizeof($array[$index]) > 2) {
								
									$this->ConditionString .= "{$array[$index][0]} {$array[$index][1]} ? ";
								
									array_push($this->ConditionValue, $array[$index][2]);


								}else{

	                                Logger::Log("Condition Child Array content must be at least 2");
									
									$this->ConditionString = null;
								
								}

							}else{

								$logicalOperatorsArray = ["OR","AND","NOT","XOR","NAND"];

								if (sizeof($array[$index])  ==  2) {

									$this->ConditionString .= "{$array[$index][0]} = ? AND ";
									
									array_push($this->ConditionValue, $array[$index][1]);

									$this->lastLogicalOfConditionString = $defaultLogicalOperator;
								
								
								}else if (sizeof($array[$index]) == 3) {
									
									if ($index != sizeof($array)) {
										
										if (!in_array($array[$index][2], $logicalOperatorsArray) ) {
											

											$this->ConditionString .= "{$array[$index][0]} {$array[$index][1]} ? $defaultLogicalOperator ";
											
											array_push($this->ConditionValue, $array[$index][2]);

											$this->lastLogicalOfConditionString = $defaultLogicalOperator;

										}else{

											$this->ConditionString .= "{$array[$index][0]} = ? {$array[$index][2]} ";
											
											array_push($this->ConditionValue, $array[$index][1]);
											
											$this->lastLogicalOfConditionString = $array[$index][2];

										}

									}else{

										if (in_array($array[$index][2], $logicalOperatorsArray) === true) {
											
											$this->ConditionString .= "{$array[$index][0]} = ? ";
											
											array_push($this->ConditionValue, $array[$index][1]);

											$this->lastLogicalOfConditionString = $array[$index][2];

										}else{
											
											$this->ConditionString = "{$array[$index][0]} ? {$array[$index][2]} ";
											
											array_push($this->ConditionValue, $array[$index][1]);

											$this->lastLogicalOfConditionString = $defaultLogicalOperator;

										}
									}

								}else if (sizeof($array[$index]) > 3) {
									
									if ($index != sizeof($array)) {
										
										$this->ConditionString .= "{$array[$index][0]} {$array[$index][1]} ? {$array[$index][3]} ";
	
										array_push($this->ConditionValue, $array[$index][2]);
										
										$this->lastLogicalOfConditionString = $array[$index][3];

									}else{

										$this->ConditionString .= "{$array[$index][0]} {$array[$index][1]} ? ";
										
										array_push($this->ConditionValue, $array[$index][2]);

										$this->lastLogicalOfConditionString = $defaultLogicalOperator;

									}
								}else{

									$this->ConditionString = null;
									
									Logger::Log("Condition Child Array content must be at least 2");
									
								}

							}/*Endif*/					
						}/*Endif*/	

					}/*Endif*/


					
					/* #increment index*/
					$index += 1;

				}/*Endwhile*/
				
				
				$this->ConditionString = rtrim(trim($this->ConditionString),$this->lastLogicalOfConditionString);
				
				
				$this->ConditionString .= ") {$this->lastLogicalOfConditionString} ";
				
			
			}/*Endif*/

				
		}/*EndextractCondition*/ 



		private function extractCondition($array,$index=0){
			
			$arr = [];

			$this->extractConditionEx($array,$index);

			$this->ConditionString = trim(rtrim(trim($this->ConditionString),"{$this->lastLogicalOfConditionString}"));

			$this->ConditionString[strlen($this->ConditionString)-1] = ' ';


			$arr['condition'] = $this->ConditionString;

			$arr['value'] = $this->ConditionValue;

            $this->ConditionString = null;

            $this->ConditionValue = [];


			return $arr;

		}


		private function checkArray($array){


			if (is_array($array) and sizeof($array) > 1) {


				$flag = 0;

				foreach($array as $in_array){

					if (is_array($in_array)) {

						$flag = 1;

						break;

					}

				}


				if ($flag == 1)
					return $array;
				else
					return false;


			}else{

				return false;

			}

		}


	}

	
?>
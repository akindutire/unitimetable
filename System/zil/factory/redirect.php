<?php

	namespace zil\factory;

	class Redirect{

		public function __construct($url){
			header("location:$url");
		}
	}

?>
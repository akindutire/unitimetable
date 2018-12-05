<?php

/*	
	Encryption Type: AES
	Author: Akindutire Ayomide Samuel
	Email: akinsamuel33@gmail.com or akindutire33@gmail.com
	Contact: 08107926083
	
*/

	
	include_once "{$_SERVER['DOCUMENT_ROOT']}/oautimetable/System/vendor/autoload.php";
	include_once "{$_SERVER['DOCUMENT_ROOT']}/oautimetable/vendor/autoload.php";
	include_once "{$_SERVER['DOCUMENT_ROOT']}/oautimetable/System/zil/main.php";	
		
	use zil\App;

	use src\timetable\config\config;

	$config = new config;

	$absPathUrl = $config->getAppUrl();
	
	/**
	 * @params
	 * WorkSpaceFolder, Database Parameters, special redirects ,Event Logging - false by default
	 */

		$WorkSpace = new App($config->getAppPath(), $config->getDatabaseParams(), $config->redirects(), false);
	
?>

<style type="text/css">
	
	@import "<?php echo "{$absPathUrl}view/asset/css/w3.css"; ?>";
	@import "<?php echo "{$absPathUrl}view/asset/css/oautimetable.css"; ?>";
	@import "<?php echo "{$absPathUrl}view/asset/@fortawesome/fontawesome-free/css/all.min.css"; ?>";

</style>

<script type="text/javascript" src="<?php echo "{$absPathUrl}view/asset/js/dependency/bower_components/angular/angular.min.js"; ?>"></script>
<script type="text/javascript" src="<?php echo "{$absPathUrl}view/asset/js/dependency/bower_components/angular-sanitize/angular-sanitize.min.js"; ?>"></script>

<?php
	
	/**
	 * @params
	 * 1- allow all, 0- deny all | live - true by default
	 */
    
	$WorkSpace->start();

	include_once "{$_SERVER['DOCUMENT_ROOT']}/oautimetable/System/zil/error.php";

?>
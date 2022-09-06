<?php
use \zil\factory\View;
$data = View::getInfo();

$absPath = $data[0];

$absPathForLinks = $data[2];
?>


<!DOCTYPE html>
<html>
<head>

	<title>home</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="icon" type="text/css" href="<?php echo "{$absPath}view/asset/img/ui1.jpg"; ?>">

</head>


<body>

hello world

</body>

<style type="text/css">
		
		@import "<?php echo "{$absPath}view/asset/css/w3.css" ?>";
		
</style>


</html>
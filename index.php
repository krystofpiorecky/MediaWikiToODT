<?php

	include_once('classes/odt.php');

	ODT::createFrom("
		== Heading ==
		<u>'''underline'''</u><ins>''inserted''</ins>
		====== Smaller Heading ======
		<s>'''struck out'''</s><del>''deleted''</del>
	");

?>
<!DOCTYPE html>
<html>
<head>
	<title>Tester</title>
</head>
<body>

</body>
</html>
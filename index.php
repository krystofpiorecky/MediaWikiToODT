<?php

	include_once('classes/odt.php');

	ODT::createFrom("
		== MediaWiki to ODT ==
		=== Nadpis 3 ===
		==== Nadpis 4 ====
		===== Nadpis 5 =====
		====== Nadpis 6 ======
		''italic'' '''bold''' '''''bold and italic'''''
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
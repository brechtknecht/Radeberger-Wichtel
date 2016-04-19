<?php
include("../shared/include/environment.inc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<form action="upload.php" method="post" enctype="multipart/form-data">
<input type="file" name="file" />
<input type="hidden" name="entry_id" value="99999" />
<input type="hidden" name="mode" value="image" />
<input type="hidden" name="cat1" value="module" />
<input type="hidden" name="cat2" value="testmodul" />
<input type="hidden" name="file_types" value="jpg" />
<input type="hidden" name="thumbnail" value="100" />
<button type="submit">ab gehts</button>

</form>
</body>
</html>

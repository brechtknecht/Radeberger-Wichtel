<?php
session_start();
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
?>
<xml>
	<upload_url val="upload.php?usid=<?php echo session_id();?>"></upload_url>
    <complete_url val="index.php"></complete_url>
</xml>

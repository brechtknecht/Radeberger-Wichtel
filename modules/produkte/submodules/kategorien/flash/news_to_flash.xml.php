<?php
//connect db
include("../../../../../shared/include/connect_db.inc.php");
include("../../../../../shared/include/functions.inc.php");
$result=mysqli_query($_SESSION['conn'], "SELECT * FROM _cms_modules_news_ WHERE NOW()>=news_start AND news_end>=NOW() AND news_show_on_startpage=1 ORDER BY news_date ASC");
$output_str="<xml>";
if(mysqli_num_rows($result)>0){
	while($row=mysqli_fetch_assoc($result)){
		$output_str.="<entry date=\"".formatDate($row['news_date']).($row['news_time']!=""?", ".$row['news_time']:"")."\" category='".$row['news_category']."'>";
		$output_str.="<headline>".stripslashes($row['news_headline'])."</headline>";
		$output_str.="<teaser><![CDATA[".stripslashes($row['news_teaser']);
		if(!empty($row['news_text'])){
			$output_str.="<br><br><a href=\"index.php?entry_id=215&amp;news_id=".$row['entry_id']."\"> ...weiterlesen</a>";
		}		
		$output_str.="]]></teaser>";
		$output_str.="<text>".$row['news_text']."</text>";
		$output_str.="</entry>";
	}
}
$output_str.="</xml>";
echo (str_replace("\r\n","\n",$output_str));

?>
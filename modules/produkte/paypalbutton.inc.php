<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" value="_cart" name="cmd" />
<input id="currency_code" type="hidden" value="EUR" name="currency_code" />
<input type="hidden" value="info@malermeister-pinkert.de" name="business"/>
<input type="image" src="https://www.paypal.com/de_DE/DE/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
<input type="hidden" value="1" name="upload" />
<?php
$i = 0;
foreach($_SESSION['modCart'] as $val){
$i+=1;
if(isset($_SESSION['page_user']['rabatt'])){
	$val['entry_preis'] = $val['entry_preis'] - ($val['entry_preis']*$_SESSION['page_user']['rabatt']/100);
}
?>
<input type="hidden" name="item_name_<?php echo $i;?>" value="<?php echo htmlspecialchars($val['entry_name']);?>">
<input type="hidden" name="quantity_<?php echo $i;?>" value="<?php echo htmlspecialchars($val['entry_count']);?>" />
<input type="hidden" name="amount_<?php echo $i;?>" value="<?php echo htmlspecialchars($val['entry_preis']);?>" />
<input type="hidden" name="shipping_<?php echo $i;?>" value="<?php echo $i == 1?htmlspecialchars($_POST['module_settings']['shipping']):"0";?>" />
<?php
}
?>
<img alt="" border="0" src="https://www.paypal.com/de_DE/i/scr/pixel.gif" width="1" height="1">
</form>



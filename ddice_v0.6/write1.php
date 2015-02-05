
<?
if($isAdmin == '1'){
print "<form name='write1' method='post' action='write_proc.php' style='margin:0; padding:0;'>";
print "<table width='100%' border=0 cellspacing='0' cellpadding='1' bgcolor='$co_w_tbcol'>";

?>
<tr><td width=100% colspan="3" align=left valign=bottom style='font-size:10px;'>
 MEMO. <input type="text" name="kd_memo" style="border:none; width:60%; background:transparent; " >
</td></tr>
<tr><td colspan="3">
 <textarea name="comment" style=" color:<?=$co_w_txfontcol?>; background:<?=$co_w_textbox?>; border:1px solid #ddd; width:100%; height:40px; overflow:visible;" ></textarea>
</td></tr>
<tr>
<td align=left bgcolor="<?=$co_w_tbcol?>" style='font-size:10px;' >
<input type="text" name="name" size="4" value="<?=$ckname?>" style="background:transparent; border:0px; border-bottom:1px solid #ccc; color:<?=$co_w_txfontcol?>;">
<input type="checkbox" name="usecookie" value="on" <?if($ckuse=="on")echo checked;?>> 쿠키
<input type="checkbox" name="kd_s"> 비밀글
<input type="checkbox" name="kd_m"> 접기
<span ddice="form"></span>
</td>
<td border='0' width="5%" align=right>
<input type="submit" name="Submit" value="WRITE" style="width:50px; font-size:7pt; font-weight:bold; color:#fff; border:1px solid <?=$co_w_submit?>; background-color:<?=$co_w_submit?>;">
<input type="hidden" name="number" value="<?=$num2?>">
<input type="hidden" name="chk_w" value="whoareyou">
<input type="checkbox" name="kd_col" checked style="display:none"></font>
</td>
    </tr>
</table>

<?
print "</form>";
}



else{

print "<form name='write2' method='post' action='write_proc.php' style='margin:0; padding:0;'>";

print "<table width='100%' border=0 cellspacing='0' cellpadding='1'  bgcolor='$co_w_tbcol'>";

?>
<tr><td width=100% colspan="3" align=left valign=bottom style='font-size:10px;' >
 MEMO. <input type="text" name="kd_memo" style="border:none; width:60%; background:transparent; ">
</td></tr>
<tr><td colspan="3">
 <textarea name="comment" style=" color:<?=$co_w_txfontcol?>; background:<?=$co_w_textbox?>; border:1px solid #ddd; width:100%; height:40px; overflow:visible;" ></textarea>
</td></tr>
<tr>
<td align=left bgcolor="<?=$co_w_tbcol?>" style='font-size:10px;' >
<input type="text" name="name" size="4" value="<?=$ckname?>" style="background:transparent; border:0px; border-bottom:1px solid #ccc; color:<?=$co_w_txfontcol?>;">
<input type="password" name="passwd" size="2" value="<?=$ckpass?>" style="background:transparent; border:0px; border-bottom:1px solid #ccc; color:<?=$co_w_txfontcol?>;">
<input type="checkbox" name="usecookiepw" value="on" <?if($ckpass!="")echo checked;?>> 비번
<input type="checkbox" name="usecookie" value="on" <?if($ckuse=="on")echo checked;?>> 쿠키
<input type="checkbox" name="kd_s"> 비밀글
<input type="checkbox" name="kd_m"> 접기
<span ddice="form"></span>
</td>
<td border='0' width="5%" align=right>
<input type="submit" name="Submit" value="WRITE" style="width:50px; font-size:7pt; font-weight:bold; color:#fff; border:1px solid <?=$co_w_submit?>; background-color:<?=$co_w_submit?>;">
<input type="hidden" name="number" value="<?=$num2?>">
<input type="hidden" name="chk_w" value="whoareyou">
</font>
</td>
    </tr>
</table>



<?
print "</form>";
}
?>

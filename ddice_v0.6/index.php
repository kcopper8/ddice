<?
include "env.php";
include "lib.php";
include "config_data.php";
include "option_data.php";
include "mtype_plugin/extend_lib.php";
include "mtype_plugin/db_admin.php";
include "KDM_skin_data.php";
include "KDM_fontcol_data.php";
include "KDM_tb_data.php";

header ("Pragma: no-cache");

$ad_ico = "<img src='$ad_icon' border='0' onerror=\"this.style.display='none';\">";
$maxleng_w = strlen($max_width);
$maxleng_h = strlen($max_height);
$emowidth = $cfg_emolist*72; //����Ͻô� �̸�Ƽ���� ���� ����� Ŭ ��� ���� ���� �ø�����.

//����� �Խ��� ���
if($mem_login=='on')
	{
		if($memberlogin == $cfg_member_passwd);
		else
			{
			gourl("./admin.php?member=1");
			exit;
			}
	}

if($memberpasswd === $cfg_member_passwd)
{
  setcookie ("memberlogin",$memberpasswd,0);
  $isMember = 1;
}
else $isMember = 0;
	// ������ �н�������Ű�� �����鼭 �����ھ�ȣ�� ������ �����ڸ����
if($ckadminpasswd == $cfg_admin_passwd && $ckadminpasswd !="")
{
	$isAdmin = 1;
}

if($cfg_admin_passwd=="")
{
	print "������ �н����尡 �����Ǿ����� �ʰų� 'env.php' ������ �о����� �ʾҽ��ϴ�.";
	exit();
}

print "<!--MM BToolBBS Version $BBS_VERSION-->\n";

if(file_exists($dbindex)){
//------MŸ�� �������� ���

  $res=readlock(); //env. ���� �������� �ƴ� ���¿����� read.
  if($res != 1)
  {
	  print"�� ������ �����Ͽ����ϴ�.";
  	exit();
  }

  if($pagebar_type=="on"){
    $temp_to = 0;
    $fp = fopen ("$dbindex", "r");
    if($fp){
      while(!feof($fp)) {
        $buffer = trim(fgets($fp, 4096));
        if ($buffer!=""&&(!($temp_to%$cfg_pic_per_page) || $temp_to==0)) { $page_arr[]=$buffer; }
        $temp_to++;
      }
      $total = $page_arr[0];
      fclose($fp);
    }// �������������ɼ�����
  
    $num = intval($num);
    if($num==0)$num=$total;
    $topnum = 0;
  
    $page_bar = mmb_page_bar($num, $cfg_bar_per_page, $page_arr);
  }
//------mmb1 �������� ���
  else{
    $fp = fopen ("$dbindex", "r");
    if($fp){
      $buffer = chop(fgets($fp, 4096));
      $total = $buffer;
      fclose($fp);
    }// �������������ɼ�����

    $num = intval($num);
    if($num==0)$num=$total;
    $topnum = 0;
  }
}
?>

<html>
<head>
<title>Dango-Jelly</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<link rel=StyleSheet HREF=style.css type=text/css title=style>
<!-- lightbox ST -->
<script src="lightbox/js/jquery-1.7.2.min.js"></script>
<script src="lightbox/js/lightbox.js"></script>
<link href="lightbox/css/lightbox.css" rel="stylesheet" />
<!-- lightbox ED -->	
</head>

<script type="text/javascript" src="js/js_input.js" charset='utf-8'></script>
<script type="text/javascript" src="./js/FileButton.js"></script>
<script type="text/javascript">
var myFileButton = new FileButton("imageswap", "imagesrc");
window.onload = function () {
    //myFileButton.run();
}
</script>
<script language="javascript">
function selectok(tooltype){
  formdraw.choose.value = tooltype;
  formdraw.submit();
}
</script>
<script type='text/javascript' src='/ddice/ddice.btool.js'></script>
<body background="<?=$bgurl?>" bgcolor="<?=$bgcol?>" text="<?=$b_fo?>" link="<?=$li_fo?>" vlink="<?=$vi_fo?>" alink="<?=$ac_fo?>">

<!---------��ü ���̺� ����-->
<table width="<?=$alltb_w?>" border="0" cellpadding="0" cellspacing="0" align="center" valign="top" bgcolor='<?=$alltb_bgc?>' style="border:<?=$alltb_bor?> solid <?=$alltb_borc?>;" >
<? if($upteble =='on'){ // ����Ʈ�� ���� �ֱ淡 �� �״�  ?>
<? }  else { //��ܸ޴��� ���̺��� ���?>

<!---------������갡 ��ü���̺� �ӽ÷� ����1�� �Ʒ��� ������̺�ҽ�-->
	<tr><td bgcolor="<?=$toptb_bgc?>" align="center" valign="bottom" style="border:<?=$menutb_bor?> solid <?=$menutb_borc?>;">

<!----�������~������ ���ε� �κ��� �Ʒ��� �ҽ��� ���������μ� ���� ����� �� �� �ֽ��ϴ�. ����� �޴�td�� ���� �Ӽ� ������ :)-->

		<table width=100% border=0 cellpadding=0 cellspacing=0>
		<tr><td valign=top>
		
			<? notice($notice); ?>
		
		</td>
		<td align=right valign=top>
		
		
			<? if($img_mode=="off" || $isAdmin==1) { ?>

				<form name="form1" method=post action="upload.php" enctype="multipart/form-data" style="margin:0; padding:0;">
					<? if($upimg=="on") { ?>
						<input type="text" name="filevalue" style="width:<?=$menu_width?>px; height: 15px; font-size:9px; color:<?=$beu_te_fontcol?>; border:none;" readonly ><!--//�׸� ���� ���� ��ġ �����ִ� �ؽ�Ʈ�ڽ��Դϴ� :)-->
						<script type="text/javascript">
							myFileButton.write('<input type="file" name="userfile" imageswap="true" imagesrc="image/upl.jpg" onmousedown="this.parentNode.style.backgroundPosition=\'2px 2px\';" onmouseup="this.parentNode.style.backgroundPosition=\'0px 0px\';" onchange="this.form.filevalue.value=this.value;"/>');
						</script>
						<input type='submit' name='submit' value="UPLOAD" class=buttonMMB>
						<?
						if($isAdmin==1) print "<input type='password' name='passwd' value='$ckadminpasswd' style='display:none;' >";
						else print "<input type='password' name='passwd' size='2' style='font-size:7pt; color:$beu_te_fontcol; background:transparent; border:none; border-bottom:$beu_te_bordercol 1px solid; text-align:center; height: 15px;' value='$ckpass'>";
					 } else {?>
						<input type='file' size='2' name='userfile' style=" font-size:10px; color:<?=$beu_te_fontcol?>; border-style:none; text-align:center; background-color:<?=$beu_te_bgcol?>;">
						<?
						if($isAdmin==1) print "<input type='password' name='passwd' value='$ckadminpasswd' style='display:none;' >";
						else print "<input type='password' name='passwd' size='2' style='font-size:7pt; color:$beu_te_fontcol; background:transparent; border:none; border-bottom:$beu_te_bordercol 1px solid; height: 15px;' value='$ckpass'>";
						?>
						<input type='submit' name='submit' value="UPLOAD" class=buttonMMB>
					<? } ?> 
						<br>
							<input type='checkbox' name='loadFold' >FOLD
							<input type='checkbox' name='loadAdmin' >��
							<input type='checkbox' name='loadMember' >��
							<input type='checkbox' name='loadWidth' >��
							<input type='checkbox' name='loadWidthWide' >��
							<input type='checkbox' name='loadHeight' >��
					</form>
					
					<form name="search" method="get" action="./search.php" style="margin:0; padding:0;">
							<input type="text" name="keyw" size="8" style="color:#888; background-color:transparent; border:0px; border-bottom:1px solid #ccc;"><input type="submit" name="Submit3" value="SEARCH" class="buttonMMB">
					</form>
					
				<? if($isAdmin==1){ ?>
					<form name="dong" method=post action="mupload.php" enctype="multipart/form-data" style="margin:0; padding:0;">
						<input type="text" name="mov" style="width:140px; height:15px; color:<?=$beu_te_fontcol?>; font-size:10px; border:none; border-bottom:1px solid #ddd; background: transparent;">
						<?
						if($isAdmin==1) print "<input type='password' name='passwd' value='$ckadminpasswd' style='display:none;' >";
						else print "<input type='password' name='passwd' size='4' style='font-size:7pt; color:$beu_te_fontcol; BORDER-RIGHT:none; BORDER-LEFT:none; BORDER-TOP:none; BORDER-BOTTOM:$beu_te_bordercol 1px solid; height: 15px;' value='$ckpass'>&nbsp;";
						?>
						<input type='submit' name='submit' value="TAG" class=buttonMMB>
					</form>
				<? } ?>

			<? if ($isAdmin==0) {
				echo "<form class=login name='admin' method='post' action='admin.php' style='margin:0; padding:0;'>
				LOGIN
				<input type='password' name='adminpasswd'  size='2'  style='color:333333; background-color:$toptb_bgc; border-width:1pt; border-color:#333333; border-style:none; border-bottom-style:solid;'>
				 <input type='submit' name='Submit' value='Ȯ��' style='font-size:8pt; color:333333; background-color:fff7e7; border-width:1pt; border-color:#333333; border-style:solid; display:none;'>
				</form>";
				}
			?>		
			
	<? } else print "\n" ?>
	
	</td></tr>
	<tr><td colspan=2 align=center>
	
		<div style='border:4px solid transparent;'></div> <!-- �ڸ�Ʈ�� ���̿� �� �� �ֱ� -->
		<ul class=MMB>					
			<li class=menuMMB><a href = "./index.php">REFRESH</a> |</li>
			<? print "<li class=menuMMB><a href = 'recent.php'>RECENT$cfg_recent</a> |</li>"; ?>
		<?
			if ($isAdmin==1) {
				print "<li class=menuMMB><a href = 'admin_config.php'>OPT</a> |&nbsp;</li>";
				print "<li class=menuMMB><a href = 'kd_config.php'>KD</a> |&nbsp;</li>";
				print "<li class=menuMMB><a href = 'admin.php?logout=on'>LOGOUT</a> |&nbsp;</li>"; 	
				}
		else { }
		?>

			<li class=menuMMBPage><? print "$page_bar"; ?></li>	
		</ul>

	</td>
	</tr></table>
	<br>
	
</td></tr>
<?
}
//��ܸ޴� ����


//--------------------�������̺� ����
print "<td bgcolor='$maintb_bgc'style='border:$maintb_bor solid $maintb_borc;' style='padding:$maintb_padd;'>";//����(�α�+�ڸ�Ʈ)���̺�

if(!file_exists($dbindex)){
  die("MMB $BBS_VERSION �ű� ��ġ�� Ȯ���մϴ�. ������ �α��� ��, ȯ�漳���� ���� ������ �ּ���.");
}

$intbl = 0; // ���̺��� �����ִ��� ����
$cp = fopen("option_list.php", "r");
while(!feof($cp)) {
  $first_arg = trim(fgets($cp, 4096));
  $second_arg = trim(fgets($cp, 4096));
  $option_list[$first_arg] = $second_arg;
}
fclose($cp);

reset($option_list);
while($option_onff = each($option_list)){
  ${"img_".$option_onff["key"]} = $$option_onff["key"];
}

//--------dbindex���� pixcnt ����

$fp = fopen("$dbindex","r");
if($fp)

$page_count = 0;
$dbbrk=0;
$cnt=0;

while(!feof($fp))
{
  $buffer = fgets($fp, 4096);
  $lognum[$cnt++] = $buffer= chop($buffer);
  
  if($num==0) $num=$buffer;
  if($topnum==0)$topnum=$buffer;
	if($buffer > $num){
    $cnt--;
    continue;
  } // ���� ��ȣ�� �����������ϸ� ��ŵ

  $page_count++;
  if($page_count > $cfg_pic_per_page) break;
}
fclose($fp);


//--------dbdata ��� ����

for($cnt=0;$cnt<$cfg_pic_per_page;$cnt++){

  $dbnum = $lognum[$cnt]%100;
  $dbfile = "$datafo/$dbnum.dat";
  //dbfile ����

  if(!file_exists($dbfile)){  $dbbrk=1; continue;  }
  $fp = fopen("$dbfile","r");
  while(!feof($fp))
  {
	  $buffer = fgets($fp, 4096);
  	$buffer = chop($buffer);

	  if(substr($buffer,0,1)==">") // ������ ���� �տ� '>'�� ������ �׸���
  	{
	  	if($intbl==1)
  		{
			$intbl = 0;
		}
  		$buffer = substr($buffer,1);
	  	$data = explode("|", $buffer);
		  list($picno,$picfn,$pass,$rtime,$ip,$loadFold,$loadAdmin,$loadMember,$mov,$loadWidth,$loadHeight,$loadWidthWide) = $data;
  		// �׸���ȣ, ���ϸ�, ��ȣȭ���н�����, ��Ͻð�, ȣ��Ʈ����, IP

   		if($picno!=$lognum[$cnt])
		{
   		  continue; //lognum�� dbfile�� picno �� �ٸ��� ���������� ��ŵ
   		}
    	if(!file_exists("$picfo/$picfn"))continue; // �׸��� ������ ��ŵ

    	// �۾��ð��� �ú��� ������ ��ȯ
  		$strjtime = sprintf("%d�ð� %d�� %d��",$sec/3600,($sec/60)%60,$sec%60);
	  	if($sec<3600)$strjtime = sprintf("%d�� %d��",($sec/60)%60,$sec%60);
		if($sec<60)$strjtime = sprintf("%d��",$sec%60);
  		if($sec<=0)$strjtime = "�� �� ����";
	  	$vhchoice = @GetImageSize("$picfo/$picfn");

		//--------------------------�׸����̺�

if($isAdmin==1)
				{
				echo("
				<div align=right>
					<form name='mdel' method='post' action='multidel.php'>
					<input type='submit' name='Submit' value='delete' style=' font-family:Tahoma; font-size:8; color:$beu_bt_fontcol; background-color:eeeeee; border-width:1; border-style:solid; border-color:666666;' >
				</div>
					");
				}
	
print "<div class=numMMB style='background:$pic_number;'><a href='delete.php?num=$picno'><span style='color:#fff; font-size:$pic_numsize; class=numMMBspan;'>&nbsp;#$picno</span></a></div>";
print "<TABLE id=cellMMB bgcolor='$logtb_bgc' style='border:$logtb_bor solid $logtb_borc;' width='100%' CELLSPACING='0' CELLPADDING='0'>";

	if(!$mov){
  		if($vhchoice[0] < $max_width_comment) print "<tr><TD align=center valign='top' width=5% BGCOLOR='$pictb_bgc' rowspan='2' style='padding:12px 6px 12px 12px;'>\n";//�׸�td��, �� padding�� �׸��� ���ʿ� ���� ��
	  	else print "<tr><TD align='center' width='100%' BGCOLOR='$pictb_bgc' style='padding:12px 12px 6px 12px;'>\n";//���ϵ��� // �׸��� ����� �� ��
	} 	else print "<tr><TD align='center' width='100%' BGCOLOR='$pictb_bgc' style='padding:12px 12px 6px 12px;'>\n";//���ϵ���

		  print "<div align=right>";
  		/* //�̰� �������� ��� :D
		if($reple_mode=="off" || $isAdmin==1) {
	  		print "<a href='picmod.php?num=$picno'>M</a> ";
  		}*/
		if($restrict_del != "on"){
			print "";
	  	if($isAdmin!=1) print "";
		  else  print "<input type='checkbox' name='delpic[]' value='$picno'>\n";
		}


		  
      if($eca_replay=='on' && isSGTFile("$picfo/$picfn")){
     		print "<font size=1> <a href='continue.php?num=$picno&choose=es_replay'><span style='color:$pic_number;'>Replay</span></a></font> \n";
    		//print "<a href='continue.php?num=$picno&choose=es_repcont'>Continue</a></font></div>\n";
    	}//����ī���� ��������
    	else print "</div>";


  		
		reset($option_list);
		$crt = "&#13;";
		$optcnt=0;
		while($option_onff = each($option_list)) {
			if ($optcnt>2) break;
			$optcnt++;
			$option_key = explode("_", $option_onff["key"]);
			$alt = ($$option_onff["key"]=="on") ? $alt.$option_onff["value"]." : ".${$option_key[1]} : $alt;
			if($optcnt<3)    $alt = ($$option_onff["key"]=="on") ? $alt.$crt : $alt;
		}




	
	  if(!$mov) {
		  print "\n";
		  if($loadAdmin == 'on') // ����������
		  { 
		  	 if($isAdmin==1){
				print "<div style='width:100%; background:#333;color:#fff; text-align:center;'><b>ADMIN ONLY</b></div>";	

					if($loadWidth == 'on') // �ʺ�����
					  print "<div class=mouseOn><a href='$picfo/$picfn' rel=lightbox class=foldImage><img src='$picfo/$picfn' class=maxW></a></div>";
					elseif($loadWidthWide == 'on') // �ʺ� 600 ����
					  print "<div class=mouseOn><a href='$picfo/$picfn' rel=lightbox class=foldImage><img src='$picfo/$picfn' class=maxWide></a></div>";
					elseif($loadHeight == 'on') // ��������
					  print "<div class=mouseOn><a href='$picfo/$picfn' rel=lightbox class=foldImage><img src='$picfo/$picfn' class=maxH></a></div>";
					elseif($loadFold == 'on') // ����
					  { print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<img src=image/click.gif border=0>': '<img src=image/click.gif border=0>';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol; line-height:130%;'><img src=image/click.gif border='0'></span></a><div style=\"display: none;\">\n";
						print "<img src='$picfo/$picfn' border='0'></a>";
						print "</div>"; }
					else print "<img src='$picfo/$picfn' border='0'></a>";
				
				

			  }
			  else print "<div style='width:130px; padding-top:15px; text-align:center;'><img src=image/locked.gif border='0' style='padding-bottom:3px;'><br><b>ADMIN ONLY</b></div>\n";
		}
		  
		  elseif($loadMember == 'on') // �������
		  {  
			if($isAdmin==1 || $logout=="on" || $isMember == 1){
					print "<div style='width:100%; background:#cf2a19;color:#fff; text-align:center;'><b>MEMBERS ONLY</b></div>";
					
					if($loadWidth == 'on') // �ʺ�����
					  print "<div class=mouseOn><a href='$picfo/$picfn' rel=lightbox class=foldImage><img src='$picfo/$picfn' class=maxW></a></div>";
					elseif($loadWidthWide == 'on') // �ʺ� 600 ����
					  print "<div class=mouseOn><a href='$picfo/$picfn' rel=lightbox class=foldImage><img src='$picfo/$picfn' class=maxWide></a></div>";
					 elseif($loadHeight == 'on') // ��������
					  print "<div class=mouseOn><a href='$picfo/$picfn' rel=lightbox class=foldImage><img src='$picfo/$picfn' class=maxH></a></div>";
					elseif($loadFold == 'on') // ����
					  { print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<img src=image/click.gif border=0>': '<img src=image/click.gif border=0>';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol; line-height:130%;'><img src=image/click.gif border='0'></span></a><div style=\"display: none;\">\n";
						print "<img src='$picfo/$picfn' border='0'></a>";
						print "</div>"; }
					else print "<img src='$picfo/$picfn' border='0'></a>";
			  }
			  else {
				  print "</a>";
				  member_login();
				  print "\n";
			  }
		}
		  
		  elseif($loadFold == 'on') // ����
		  { print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<img src=image/click.gif border=0>': '<img src=image/click.gif border=0>';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol; line-height:130%;'><img src=image/click.gif border='0'></span></a><div style=\"display: none;\">\n";
			print "<img src='$picfo/$picfn' border='0'></a>";
			print "</div>"; }
		  
		  elseif($loadWidth == 'on') // �ʺ�����
		  {  print "<div class=mouseOn><a href='$picfo/$picfn' rel=lightbox class=foldImage><img src='$picfo/$picfn' class=maxW></a></div>"; }

		  elseif($loadHeight == 'on') //��������
		  {  print "<div class=mouseOn><a href='$picfo/$picfn' rel=lightbox class=foldImage><img src='$picfo/$picfn' class=maxH></a></div>"; }
	
		  elseif($loadWidthWide == 'on') // �ʺ�����
		  {  print "<div class=mouseOn><a href='$picfo/$picfn' rel=lightbox class=foldImage><img src='$picfo/$picfn' class=maxWide></a></div>"; }
				
		  else // �׳� �ε�
		  print "<img src='$picfo/$picfn' border='0'>"; 
		  
		} // $mov if�� ��
		
	  else { 
		  print "<div align=center>";
		  if($loadFold =='on') {
			  print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<img src=image/click.gif border=0>': '<img src=image/click.gif border=0>';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol; line-height:130%;'><img src=image/click.gif border='0'></span></a><div style=\"display: none;\">\n";
		  }
		  print "$mov";
		  if($loadFold =='on') print "</div>";
		  print "</div>";
	  }
	
		
  		// ���� �׸��� ����ũ�Ⱑ ���� ũ�� �̻��̸� ������ �׸� ������ ǥ���Ѵ�.
		if(!$mov){
	  	if($vhchoice[0] < $max_width_comment) 
		print "</td>
		<TD height=100% style='margin:0; padding: 8px 8px 8px 0;'> 
		<table width=100% height=100% style='background-color:$comtb_bgc;'><tr><td valign='top'>";//�׸�td �ݰ� �ڸ�Ʈtable ����,  // write1.php�� �־ ������-�Ʒ� �е��� ���� ���� �д�
		
		else print "</td></tr><tr><TD BORDER=0 CELLSPACING=0 CELLPADDING=0 width=100% style='background-color:$comtb_bgc; padding: 10px 10px 8px 10px;'>\n";//���ϵ��� // �αװ� ��������� �� 
		} 
		else print "</td></tr><tr><TD BORDER=0 CELLSPACING=0 CELLPADDING=0 width=100% style='background-color:$comtb_bgc; padding: 10px 10px 8px 10px;'>\n";//���ϵ���
		global $mov2;
		$mov2 = $mov;
  		$intbl = 1;
  	}
	  else //����
  	{
	  	if($intbl!=1)continue;
		  $data = explode("|", $buffer);
  		list($autname,$comment,$rtime,$ip,$passwd,$kd_s,$kd_m,$kd_memo,$kd_col,$kd_replt) = $data;
  		// �ۼ��ڸ�,�۳���,��Ͻð�,IP,�н�����
  		if($comment=="")continue;
		

		if($kd_col == 'on'){
			if($kd_replt == 'on') print "<div style='background:$repl_bgcol; padding:0 0 0 5px; border-left: 2px solid $replt_text;'><font style='color:$replt_text; font-size:$ad_namesize;'><b>RE: </b></font><font style='color:$comm_ad_namecol; font-size:$ad_namesize;'>";//��� ���̺��� �¿� ������ ���ְ� ������ margin�� 20�� 0���� ���ּ���:)
			else print "<div style='background:$comm_adbgcol; margin:0;'><font style='color:$comm_ad_namecol; font-size:$ad_namesize;'>";
		}
		else {
			if($kd_replt == 'on') print "<div style='background:$repl_bgcol; padding:0 0 0 5px; border-left: 2px solid #555;'><font style='color:$replt_text; font-size:$ad_namesize;'><b>RE: </b></font><font style='color:$comm_cu_namecol; font-size:$cu_namesize;'>";//��� ���̺��� �¿� ������ ���ְ� ������ margin�� 20�� 0���� ���ּ���:)
			else print"<div style='background:$comm_cuscol; padding-left:$cu_textpadding;'><font style='color:$comm_cu_namecol; font-size:$cu_namesize;'>";
		}
		$autname = emote_ev($autname, $emote_table);
		print "<b>$autname</b>&nbsp;</font>";
		
  		$comment = str_replace("%7C","|",$comment);
	  	//$comment = del_html($comment);
		$comment = autolink($comment);
  		$comment = emote_ev($comment, $emote_table);

	  	$altdate = date("Y�� m�� d�� H�� i�� s��",$rtime);

		if($kd_col == 'on'){ 
			print "<font title='$altdate'>";
			print "<span style='background-color:$comm_ad_datebgcol; color:$comm_ad_datecol; font-family:tahoma;font-size:8px;'>";
			print date("ymd*H:i",$rtime)."&nbsp;</span></font>\n";
		} else {
			print "<font title='$altdate'>";
			print "<span style='color:$comm_cu_datecol; font-family:tahoma; font-size:8px;'>";
			print date("ymd*H:i",$rtime)."&nbsp;&nbsp;</span></font>";
		}

		$autname=urlencode($autname);//�����ڵ� �ذ�

		if($restrict_del == "on" && $isAdmin !="1"){ print "<br>";}
		else{
			if($kdreply_mode != "on" || $isAdmin =="1"){if ($kd_replt != 'on'){
				echo "<a href=\"reply1.php?num=$picno&name=$autname&time=$rtime\">";
				if($kd_col == 'on') print "<span style='color:$comm_ad_datecol; font-size:8px;'>RE </span></a>";
				else print "<span style='color:$comm_cu_fontcol; font-size:8px;'>RE </span></a>";
				}
			}
			else {
			print "";	
			}
		
		echo "<a href=\"mod.php?num=$picno&name=$autname&time=$rtime\">";
		if($kd_col == 'on') {
			if($isAdmin==1) print "<span style='color:$comm_ad_datecol; font-size:8px;'>M </span></a>";
			else print "\n";
		}
		else print "<span style='color:$comm_cu_fontcol; font-size:8px;'>M </span></a>";
		if($isAdmin!=1){
			echo "<a href=\"delete.php?num=$picno&name=$autname&time=$rtime\">";
			if($kd_col == 'on') print "</a><br>\n";
			else print "<span style='color:$comm_cu_fontcol; font-size:11px;'>�� </span></a><br>\n";
		}
		else print "<input type='checkbox' name='delreply[]' value='$picno|$ip$rtime'><br>\n";
		}
		print "<div ddice=\"rolled\" ddice-id=\"$picno_$rtime\"></div>";

		if($kd_memo) print "<font style='font-size:11px; color:$kd_memocol;'><b>memo. </b>$kd_memo</font><br>";

		if($kd_s == 'on'){
			if($isAdmin==1)	{ 
				print "<span style='color:$kd_seccol;'><b>Secret message</b>��</span><br><span style='color:#888;'>";
				if($kd_m =='on') print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<span style=color:$kd_morecol;>Close ��</span>': '<span style=color:$kd_morecol;>Open ��</span>';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol;'>Open ��</span></a><div style=\"display: none;\">\n";
				
				print "<span style='line-height:16px !important;'>$comment</span>";
			
			if($kd_m =='on') print "</div>";
			}
			else print "<span style='color:$kd_seccol;'><b>Secret</b><br></span>";
			
		}

		else{
			if($kd_m =='on'){
				print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'<span style=color:$kd_morecol;>Close ��</span>': '<span style=color:$kd_morecol;>Open ��</span>';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol;'>Open ��</span></a><div style=\"display: none;\">\n";
			}
			if($kd_col == 'on') {
				if($kd_replt == 'on') print "<span style='color:$reply_text;'>";
				else print "<span style='color:$comm_ad_fontcol;'>";
			}
			else print "<span style='color:$comm_cu_fontcol;'>";
			print "<span style='line-height:16px !important;'>$comment</span>\n";
			print "</span>";
			
		if($kd_m =='on') 	print "</div>";

		
		if($option_ip == "on" && $kd_replt !='on') print "<div align=right style=font-family:Tahoma;font-size:7pt;color:$kd_ipcol;>$ip</div>\n";

			}


		

		print "</div>";

		print "<div style='border:4px solid transparent;'></div>"; // �ڸ�Ʈ�� ���̿� ������ �ֱ� 

  	}

$num2 = $num;
$num2 = $picno; }

if($isAdmin==1)
{
echo "</form>\n";
}

	

if(!$mov2){
if($vhchoice[0] < $max_width_comment) print "</td></tr><tr><td valign='bottom' align=right>";//�ڸ�Ʈ �ؿ� write1���Ժ� ���̺�
else print "</td></tr><tr><td valign='bottom' align=center style='background-color:$comtb_bgc; padding: 0 10px 10px 10px;'>";//�ڸ�Ʈ �ؿ� write1���Ժ� ���̺�
}
else print "</td></tr><tr><td valign='bottom' align=center style='background-color:$comtb_bgc; padding: 0 10px 10px 10px;'>";//�ڸ�Ʈ �ؿ� write1���Ժ� ���̺�

if($reple_mode=="off" || $isAdmin==1) {

if($reply_close =='on') print "<a class=\"more\" onclick=\"this.innerHTML=(this.nextSibling.style.display=='none')?'��': '��';this.nextSibling.style.display=(this.nextSibling.style.display== 'none')?'block':'none';\" href=\"javascript:void(0);\" onfocus=\"blur()\"><span style='color:$kd_morecol; line-height:130%;'>��</span></a><div style=\"display: none;\">\n";
include ("write1.php");
if($reply_close =='on') print "</div>";

}

if(!$mov2){
if($vhchoice[0] < $max_width_comment) print "</td></tr></table>";
}
print "</td></tr></table>";
print "<br>";

}
//------------------��⼭ ���� ���̺� ����


if($dbbrk==0 && $fp) fclose($fp);
else "echo $dbfile �� ������ �����ϴ�.<br>\n";
print "<script type='text/javascript'>js_input_checkboxs_skin_all(null,true);</script>";
//���� ���̺� ����

print "<table width='100%'><tr><td>";
  echo "<div align = 'center'>";
  if($topnum>$num)
  {
	  $prev=$num+$cfg_pic_per_page;
  	if($prev>$topnum)$prev=$topnum;
	  echo " <a href=\"./index.php?num=$prev\">�� PREV</a>&nbsp&nbsp&nbsp��&nbsp&nbsp;";
  }
  if($num>$cfg_pic_per_page)
  {
	  $next=$num-$cfg_pic_per_page;
  	echo " <a href=\"./index.php?num=$next\">NEXT ��</a> ";
  }
  echo "</div>";
//������ �� ǥ��

print "</td></tr></table></td></tr></table>";//�������� ������ ������ ���̺� ��� ����

?>

<div style="width:100%;margin:0 auto; margin-top:40px;text-align:center; padding:0px; clear:both; font-size:9px;">
MMB &copy;Madoka / &copy;tomCat / &copy;Bandi / SKIN &copy;kodama / Lightbox and Resizing Edition by Hatti
<iframe scrolling=no frameborder=0 width=0 height=0 marginwidth=0 marginheight=0 hspace=0 vspace=0 src="http://count-1.blueweb.co.kr/counter/counter20.php?dbname=kayzero0&img=1&bgcolor=����ȭ���"></iframe>
</div>


</body>
</html>
<?




function member_login()
{
echo "
<form name='member' method='post'>
  <table width='200' border='0' cellspacing='0' cellpadding='0' align='center' style='margin-top:5px'>
  <tr>
    <td height='24' bgcolor='$pic_bgcol' style='border:0px solid $menu_bordercol;'>
        <div align='center' style='font-size:11px;'><img src='image/locked.gif' style='padding-bottom:5px;'><br><b>MEMBERS ONLY</b></div>
    </td>
  </tr>
  <tr>
    <td  bgcolor='$comm_bgcol' style='border:0 solid $menu_bordercol;' valign='middle' align='center'>
      <div align='center'>
            <input type='password' name='memberpasswd' style='width:100px; background:transparent; border:none; border-bottom:1px solid #aaa;'>&nbsp;
           	<input type='submit' name='submit2' value='OK' style='font-family:tahoma; font-size:7pt; font-weight:bold; color:#fff; border:1px solid #666; background-color:#666;'>
	  </div>
    </td>
  </tr>
</table>
</form>";
}

function del_html($str)
{
	$str = str_replace( ">", "&gt;",$str );
	$str = str_replace( "<", "&lt;",$str );
	$str = str_replace( "\"", "&quot;",$str );
	$str = str_replace( "&lt;br&gt;","<br>",$str); //br���ǰ���
	return $str;
}

function autolink($str)
{
	// URL ġȯ
	$homepage_pattern = "/([^\"\=\>])(mms|http|HTTP|ftp|FTP|telnet|TELNET)\:\/\/(.[^ \n\<\"]+)/";
	$str = preg_replace($homepage_pattern,"\\1<a href=\\2://\\3 target=_blank>\\2://\\3</a>", " ".$str);
	return $str;
}

function alt($msg='') {
  echo "<script language='javascript'>";
  if($msg) echo 'alert("'.$msg.'");';
  echo "location.reload();</script>\n";
}

function gourl($url)
{
	echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
	echo"</head></html>";
}
?>
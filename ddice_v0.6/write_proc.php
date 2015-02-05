<?
include "env.php";
include "mtype_plugin/db_admin.php";
include "config_data.php";
include "option_data.php";

if($chk_w != "whoareyou" || $number== "")
{
  exit("������ ��Get out");  //�ҹ�����
}
else{
  $dbnum_chk = $number%100;
  $dbfile_chk = "$datafo/$dbnum_chk.dat";
  if(!file_exists($dbfile_chk))  exit("�ش� �α��� DB ������ ����ϴ�.");  //���� db ������ �ƿ� ���.
}

// �����������
$comment = stripslashes($comment);
$name    = stripslashes($name);
$spos = strpos($comment, "http:");

if($link_http=="off"){
  if($spos === false) {}
  else {
	  print "�� �Խ����� �ڸ�Ʈ�� http �ּ� ��ũ�� �����Ǿ���ϴ�.";
  	exit;
  }
}
if($en_num=="on"){
  $temp = eregi_replace("[[:alnum:]]+", "", $comment);
  $temp = str_replace(" ", "", $temp);
  $temp = str_replace("!", "", $temp);
  $temp = str_replace(".", "", $temp);
  $temp = str_replace("/", "", $temp);
  $temp = str_replace("=", "", $temp);
  $temp = str_replace(",", "", $temp);
  $temp = str_replace("~", "", $temp);
  $temp = str_replace(";", "", $temp);
  $temp = str_replace("\n", "", $temp);
  $temp = str_replace("\r", "", $temp);
  $temp = str_replace("\t", "", $temp);
  if(strlen($temp) == 0) {  die("�� �Խ��ǿ� ���� �� ���ڸ��� �ڸ�Ʈ�� ���� �����Ǿ���ϴ�."); }
}//http ���ܰ� ����,���� ���� ����

$blkdb = "$datafo/blockw_data.txt";
$fp = fopen("$blkdb","r");
while(!feof($fp))
{
  $blklist[] = chop(fgets($fp, 4096));
}
fclose($fp);
reset($blklist);

if($blklist[0]!=""){
  while(list ($key, $val) = each($blklist)){
    if(strstr($comment,$val)){
      showmsg("���� �ܾ ���ԵǾ� �ֽ��ϴ�.: $val");
      exit();
    }
  }
}
//Ư�� �ܾ� ���͸�

$cookiesexpire = 30*24*3600; // 30���� ����

function updatereplyorderlist($mrnum)
{
	global $cfg_recent;
	global $datafo;

	if(file_exists("$datafo/recent.txt"))//data�� �̸��� dat������ ����Ǵ� ��� �̸����� �ٲ��ּ���
	{
		$fp = fopen("$datafo/recent.txt","r");//data�� �̸��� dat������ ����Ǵ� ��� �̸����� �ٲ��ּ���
	}
	$ocnt=0;
	if($fp)
	{
		while(!feof($fp))
		{
			$olist[$ocnt] = fgets($fp, 4096);
			if($olist[$ocnt]==$mrnum."\n")continue;
			$ocnt++;
		}

		fclose($fp);
	}

	$ototal = $ocnt;
	$ocnt=0;

	$fp = fopen ("$datafo/recent.txt", "w");//data�� �̸��� dat������ ����Ǵ� ��� �̸����� �ٲ��ּ���
	fputs($fp, $mrnum."\n");
	while($ocnt<$ototal && $ocnt<($cfg_recent-1))
	{
		fputs($fp, $olist[$ocnt++]);
	}
	fclose($fp);

}

function gourl($url)
{
	echo"<meta http-equiv=\"refresh\" content=\"0; url=$url\">";
	echo"</head></html>";
}

function showmsg($msg)
{
	echo "</head>\n";
	echo "<body bgcolor=\"#FFFFFF\" text=\"#333333\">\n";
	echo $msg."\n";
	echo "</body><html>";
}
if($name!="" && $usecookie=="on"){
	setcookie ("ckname", $name,time()+$cookiesexpire);
	setcookie ("ckurl", $hpurl,time()+$cookiesexpire);
	setcookie ("ckmail", $email,time()+$cookiesexpire);
	setcookie ("ckuse", $usecookie,time()+$cookiesexpire);
	if($usecookiepw=="on")setcookie ("ckpass", $passwd,time()+$cookiesexpire);
	else setcookie ("ckpass", "",time()-3600);
}
if($usecookie!="on")
{
	setcookie ("ckname", "",time()-3600);
	setcookie ("ckurl", "",time()-3600);
	setcookie ("ckmail", "",time()-3600);
	setcookie ("ckuse", "",time()-3600);
	setcookie ("ckpass", "",time()-3600);
}

?>

<html>
<head>
<title>BTool �Խ���</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

<?

$foundit = 0;
$complete = 0;
$cnt = 0;

$passtmp = $passwd;
$passtmp = substr($passtmp,-2);
if(strlen($passtmp)==0)  $passtmp=$passwd;//��й�ȣ�� ���ڸ��� ��ü
$enc_pw = crypt($passwd,$passtmp);
if($passwd=="")$enc_pw = "";

$comment = str_replace("|","%7C",$comment);


if($comment=="")
{
	showmsg("�� ������ ����ּ���.");
	exit();
}

if($name=="")
{
	showmsg("�̸��� ����ּ���.");
	exit();
}

if(strstr($name,"'")||strstr($email,"'")||strstr($hpurl,"'"))
{
	showmsg("�̸�,����,Ȩ�������� ' �� ��� �����Դϴ�.");
	exit();
}

if(strstr($name,'"')||strstr($email,'"')||strstr($hpurl,'"'))
{
	showmsg('�̸�,����,Ȩ�������� " �� ��� �����Դϴ�.');
	exit();
}


$ret = proclock();//�� ����
if($ret==0)
{
	showmsg("�� �����Դϴ�. (".$ret.")");
	exit();
}

$dbnum = $number%100;
$dbfile = "$datafo/$dbnum.dat";
//dbfile ����


$fp = fopen("$dbfile","r");
while(!feof($fp))
{
	$record[$cnt++] = $buffer = fgets($fp, 4096);

	if(substr($buffer,0,1)==">"){ // ������ ���� �տ� '>'�� ������ �׸���
		$buffer = substr($buffer,1);
		$data = explode("|", $buffer);

		$outdata = array($name,$comment,time(),$REMOTE_ADDR,$enc_pw,$kd_s,$kd_m,$kd_memo,$kd_col);

		if($foundit == 1 && $complete == 0){
			$complete = 1;
			$record[$cnt] = $record[$cnt-1];

			$strtmp = join("|",$outdata);
			$strtmp = str_replace("\n","<br>",$strtmp);
			$strtmp = str_replace("\r","",$strtmp);
			$record[$cnt-1] = $strtmp."\n";
			if(strlen($strtmp)>4000)
			{
				showmsg("��� : �Էµ����Ͱ� 4000����Ʈ�� �Ѿ���ϴ�.");
				procunlock();
				exit();
			}

			$cnt++;
		}
		if($data[0]==$number)$foundit = 1;
	}

}

if($foundit == 1 && $complete == 0){
	$strtmp = join("|",$outdata);
	$strtmp = str_replace("\n","<br>",$strtmp);
	$strtmp = str_replace("\r","",$strtmp);
	$record[$cnt] = $strtmp."\n";
	$cnt++;
}

fclose($fp);
$totalrec = $cnt;
$cnt = 0;

$fp = fopen("$dbfile","w");
while($cnt<$totalrec )
{
	fputs($fp, $record[$cnt++]);
}
fclose($fp);


procunlock();//�� ����

updatereplyorderlist($number);
//gourl("./index.php?num=$number");

function goAndReturnDDice($back_to, $id, $types) {
	$now_uri_arr = explode("/", $_SERVER['REQUEST_URI']);
	array_pop($now_uri_arr);

	$now_uri_directory = implode("/", $now_uri_arr);

// require fix for use. maybe ... domain and ddiceKey?
	$ddiceKey = "someKey";
    $serviceUrlPrefix = "http://ddice.domain.org/ddice/";
	$gotourl = $serviceUrlPrefix."ddice.php?action=roll_redirect&ddiceKey=$ddiceKey&id=$id&types=$types&redirect="
	. urlencode("$now_uri_directory/$back_to");

// require fix for use. maybe ... domain and ddiceKey?

	gourl($gotourl);
}

if($ddice) {
	goAndReturnDDice("index.php?num=$number", "$number_".$outdata[2], $ddice);
} else {
	gourl("./index.php?num=$number");
}
?>
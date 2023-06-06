<?php
function connect() 
{ 
    $con = mysql_connect('db2659.1und1.de','dbo362507291','c52pass') or die(mysql_error()); 
     mysql_select_db('db362507291',$con) or die(mysql_error()); 
} 
connect();
$Sender_ID = $_GET['Sender'];

if ($Sender_ID == 2197) {
	$data = mysql_query("SELECT * FROM mediadaten"); 
	while ($temp = mysql_fetch_array($data)){ 
		$mediadaten[] = $temp;
	}
	$num_mediadaten = mysql_num_rows($data);
	$folder="media/";
} else {
	$data = mysql_query("SELECT * FROM mediadaten WHERE SenderID='".$Sender_ID."'"); 
	while ($temp = mysql_fetch_array($data)){ 
		$mediadaten[] = $temp;
	}
	$num_mediadaten = mysql_num_rows($data);
	$folder="media/".$Sender_ID."/";
}

$m_bilder = false;
$m_videos = false;
$m_audios = false;
$m_doks = false;

if ($num_mediadaten != 0) {

	for ($i=0; $i<$num_mediadaten; $i++) { 
	
		if ($mediadaten[$i]["Media_Typ"] == "bild") {
			$m_bilder = true;
			$mediadaten_bilder .= "<tr><td><a href=\"media/".$mediadaten[$i]["SenderID"]."/Mediadaten/".utf8_encode($mediadaten[$i]["Media_File"])."\" target=\"_blank\">".utf8_encode($mediadaten[$i]["Media_File"])."</a></td>"; 
			if ($mediadaten[$i]["Titel"] != "") {
				$mediadaten_bilder .= "<td>&nbsp;&nbsp;".utf8_encode($mediadaten[$i]["Titel"])."</td></tr>";
			} else $mediadaten_bilder .= "<td>&nbsp;&nbsp;-</td></tr>";
		}
		if ($mediadaten[$i]["Media_Typ"] == "video") {
			$m_videos = true;
			$mediadaten_video .= "<tr><td><a href=\"media/".$mediadaten[$i]["SenderID"]."/Mediadaten/".utf8_encode($mediadaten[$i]["Media_File"])."\" target=\"_blank\">".utf8_encode($mediadaten[$i]["Media_File"])."</a></td>"; 
			if ($mediadaten[$i]["Titel"] != "") {
				$mediadaten_video .= "<td>&nbsp;&nbsp;".utf8_encode($mediadaten[$i]["Titel"])."</td></tr>";
			} else $mediadaten_video .= "<td>&nbsp;&nbsp;-</td></tr>";
		}
		if ($mediadaten[$i]["Media_Typ"] == "audio") {
			$m_audios = true;
			$mediadaten_audio .= "<tr><td><a href=\"media/".$mediadaten[$i]["SenderID"]."/Mediadaten/".utf8_encode($mediadaten[$i]["Media_File"])."\" target=\"_blank\">".utf8_encode($mediadaten[$i]["Media_File"])."</a></td>"; 		
			if ($mediadaten[$i]["Titel"] != "") {
				$mediadaten_audio .= "<td>&nbsp;&nbsp;".utf8_encode($mediadaten[$i]["Titel"])."</td></tr>";
			} else $mediadaten_audio .= "<td>&nbsp;&nbsp;-</td></tr>";
		
		}
		if ($mediadaten[$i]["Media_Typ"] == "dokument") {
			$m_doks = true;
			$mediadaten_dokumente .= "<tr><td><a href=\"media/".$mediadaten[$i]["SenderID"]."/Mediadaten/".utf8_encode($mediadaten[$i]["Media_File"])."\" target=\"_blank\">".utf8_encode($mediadaten[$i]["Media_File"])."</a></td>"; 
			if ($mediadaten[$i]["Titel"] != "") {
				$mediadaten_dokumente .= "<td>&nbsp;&nbsp;".utf8_encode($mediadaten[$i]["Titel"])."</td></tr>";
			} else $mediadaten_dokumente .= "<td>&nbsp;&nbsp;-</td></tr>";
		}
	}
}
echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<meta http-equiv="content-type" content="text/html; charset=UTF-8"> 
<html>
<head>
	<style type="text/css">
		a:link { 
			color:#006DB7; 
			text-decoration:none;
		}    
		a:visited {
			color:#002aB7;
			text-decoration:none;
		} 
		a:hover { 
			color:#03adf4; 
			text-decoration:underline;
		}	
			
 		.basic-accordian{
			border:5px solid #EEE;
			padding:5px;
			z-index:2;
		}
		.accordion_headings{
			padding:5px;
			background:#a1c4db;
			color:#ffffff;
			border:1px solid #FFF;
			cursor:pointer;
			font-weight:bold;
		}
		.accordion_headings:hover{
			background:#006DB7;
			color:#00FFFF;
		}
		.accordion_child{
			font-size: 1.1em;
			color:#888888;
			vertical-align: top;
			text-align:left;
			padding-left:15px;
			background:#ffffff;
		}
		.header_highlight{
			background:#006db7;
		}	
		.tab_container *{
			float:left;
			width:100px;
		}
	</style>
	 <script type="text/javascript" src="acc/accordian-src.js"></script>
</head>
<body style="margin:0" onload="new Accordian(\'basic-accordian\',5,\'header_highlight\');">
<table width="98%" border="0" cellspacing="0" style="align:left; font-size: 0.7em; font-family: Verdana; color:#006DB7">
 <tr>
 <td>
	<div id="basic-accordian" ><!--Parent of the Accordion-->
		<div class="tab_container">';
			if ($m_bilder) echo '<div id="test1-header" class="accordion_headings" >Bilder</div>';
			if ($m_videos) echo '<div id="test2-header" class="accordion_headings" >Videos</div>';
			if ($m_audios) echo '<div id="test3-header" class="accordion_headings" >Audios</div>';
			if ($m_doks) echo '<div id="test4-header" class="accordion_headings" >Dokumente</div>'; echo '
		</div>

		<div style="float:left;">';
			if ($m_bilder) echo '
			<div id="test1-content">
				<div class="accordion_child"><table border="0" cellspacing="0">'.$mediadaten_bilder.'</table>
				</div>
			</div>';
			if ($m_videos) echo '
			<div id="test2-content">
				<div class="accordion_child"><table border="0" cellspacing="0">'.$mediadaten_video.'</table>
				</div>
			</div>';
			if ($m_audios) echo '
			<div id="test3-content">
				<div class="accordion_child"><table border="0" cellspacing="0">'.$mediadaten_audio.'</table>
				</div>
			</div>';
			if ($m_doks) echo '
			<div id="test4-content">
				<div class="accordion_child"><table border="0" cellspacing="0">'.$mediadaten_dokumente.'</table>
				</div>
			</div>';
			echo '
		</div>
	</div><!--End of accordion parent-->
 </td>
 </tr>
 </table>
</body>
</html>
';
?>
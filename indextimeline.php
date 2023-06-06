<?php

function connect() 
{ 
    $con = mysql_connect('db2659.1und1.de','dbo362507291','c52pass') or die(mysql_error()); 
     mysql_select_db('db362507291',$con) or die(mysql_error()); 
} 

function umwandeln($string)
{
	list ($y, $m, $d) = explode('-', $string);
	switch ($m) {
		case "01" : $m = "Jan"; break; 
		case "02" : $m = "Feb"; break;
		case "03" : $m = "Mar"; break;
		case "04" : $m = "Apr"; break;
		case "05" : $m = "May"; break;
		case "06" : $m = "Jun"; break;
		case "07" : $m = "Jul"; break;
		case "08" : $m = "Aug"; break;
		case "09" : $m = "Sep"; break;
		case "10" : $m = "Oct"; break;
		case "11" : $m = "Nov"; break;
		case "12" : $m = "Dec"; break;
	}
	$datum = $m." ".$d." ".$y;
return $datum; 
}
 
function escape($text){
$text = str_replace("&","&amp;",$text);
$text = str_replace("<","&lt;",$text);
$text = str_replace(">","&gt;",$text);
$text = str_replace("\"","&quot;",$text);
$text = str_replace("ä","ae",$text);
$text = str_replace("Ä","Ae",$text);
$text = str_replace("ö","oe",$text);
$text = str_replace("Ö","Oe",$text);
$text = str_replace("ü","ue",$text);
$text = str_replace("ï¿½","ue",$text);
$text = str_replace("á","a",$text);
$text = str_replace("’","'",$text);
$text = str_replace("é","e",$text);
$text = str_replace("„","\"",$text);
$text = str_replace("“","\"",$text);
$text = str_replace("–","-",$text);
$text = str_replace("Ü","Ue",$text);
$text = str_replace("ß","ss",$text);
return $text;
}

if ( !empty($_GET["ev"]) ) { 
	if ($_GET["ev"] == "1" ) {
		$showEV = "0"; 
	}
	else {$showEV = "1";}
} 
else {$showEV = "1";}

if ( !empty($_GET["ms"]) ) { 
	if ($_GET["ms"] == "1" ) {
		$showMS = "0";
	}		
	else {$showMS = "1";}
}
else {$showMS = "1";}

if ($showEV == "1") {
	$checkEV = " checked=\"checked\">Events anzeigen&nbsp;[<img style=\"vertical-align:middle;\" src=\"timeline_js/images/red_circle2.png\" />]&nbsp;&nbsp;";
} else $checkEV = "\">Events anzeigen&nbsp;[<img style=\"vertical-align:middle;\" src=\"timeline_js/images/grau.png\" />]&nbsp;&nbsp;";

if ($showMS == "1") {
	$checkMS = " checked=\"checked\">Meilensteine anzeigen&nbsp;[<img style=\"vertical-align:middle;\" src=\"timeline_js/images/dull-blue-circle2.png\" />]&nbsp;&nbsp;";
} else $checkMS = ">Meilensteine anzeigen&nbsp;[<img style=\"vertical-align:middle;\" src=\"timeline_js/images/grau.png\" />]&nbsp;&nbsp;";

$data = mysql_query("SELECT * FROM sender ORDER BY SenderName"); 
while ($temp = mysql_fetch_array($data, MYSQL_ASSOC)){ 
	$sender[] = $temp;
}
$num_sender = mysql_num_rows($data);

if ( !empty($_GET["sid"]) ) { 
	$SenderID = $_GET["sid"];
} else $SenderID = $sender[0]["SenderID"];

if ( isset($_POST['sender']) ) {
		$SenderID = $_POST['sender'];
	}

if ($SenderID == 2197) {
	$hoehe = "450px";
	$prev = "../";
	if ( $showMS == "1") {
		$data = mysql_query("SELECT * FROM meilensteine ORDER BY MS_Datum"); 
		while ($temp = mysql_fetch_array($data, MYSQL_ASSOC)){ 
			$meilensteine[] = $temp;
		}
		$num_meilensteine = mysql_num_rows($data);
	}
	if ( $showEV == "1") {
		$data = mysql_query("SELECT * FROM events"); 
		while ($temp = mysql_fetch_array($data)){ 
			$events[] = $temp;
		}
		$num_events = mysql_num_rows($data);
	}
	$folder="media/";
} else {
	$hoehe = "280px";
	$prev = "../../";
	if ( $showMS == "1" ) {
		$data = mysql_query("SELECT * FROM meilensteine WHERE SenderID='".$SenderID."' ORDER BY MS_Datum"); 
		while ($temp = mysql_fetch_array($data, MYSQL_ASSOC)){ 
			$meilensteine[] = $temp;
		}
		$num_meilensteine = mysql_num_rows($data);
	}
	if ( $showEV == "1") {
		$data = mysql_query("SELECT * FROM events WHERE SenderID='".$SenderID."' ORDER BY EV_Datum"); 
		while ($temp = mysql_fetch_array($data)){ 
			$events[] = $temp;
		}
		$num_events = mysql_num_rows($data);
	}
	//XML Datei erstellen

	$folder="http://www.pink-monk-records.de/wurmzeichen/timeline/media/".$SenderID."/";
}
$fname = "zeitleiste.xml";

//printf($folder.$fname);

if(file_exists($folder.$fname)){
	unlink($folder.$fname);
}	

$newfile = fopen($folder.$fname,"a");

fwrite($newfile, "<data>\n");

if ($num_meilensteine != 0) {
	foreach ($meilensteine[0] as $key => $value) {
		$old_ms[$key] = $value;
	}
	$first_ms = umwandeln($meilensteine[0]["MS_Datum"])." 12:00:00 GMT";
	list ($y, $m, $d) = explode('-', $meilensteine[0]["MS_Datum"]);
	if ($SenderID == 2197) { 
		$intervallMonat = 170;
		$intervall = 150; 
	}	else {
			$intervallMonat = 80;
			$intervall = round(((-20/3)*(2009-$y))+200);
		}
	
	for ($i=0; $i<$num_meilensteine; $i++) {
		if ($meilensteine[$i]["SI_Logo"] != "") 
			//$logo = "\nimage=\"".$folder."Senderinformationen/".$meilensteine[$i]["SI_Logo"]."\"\n";
			$logo = "\nimage=\"Senderinformationen/".$meilensteine[$i]["SI_Logo"]."\"\n";
		else
			$logo = "";

		$col_Anschrift =  "style=\"color:#888888\"";
		
		foreach ($meilensteine[$i] as $key => $value) {
			if ($i == 0) {
				$color[$key] = "style=\"color:#006DB7\"";
				$col_Anschrift =  "style=\"color:#006DB7\"";
			} else {
				if ($old_ms[$key] != $value) {
					$color[$key] = "style=\"font-style:italic; color:#006DB7\"";	
					if ( ($key == "SI_Strasse") || ($key == "SI_Hausnummer") || ($key == "SI_PLZ") || ($key == "SI_Ort") ) 
						$col_Anschrift = "style=\"font-style:italic; color:#006DB7\"";
				}
				else $color[$key] = "style=\"color:#888888\"";		
			}
		}
		
		if ($meilensteine[$i]["SI_Logo"] != "")
			$logoundsende = "<tr><td valign=\"top\" ".$color["SI_Logo"]."><b>Logo: </b></td><td align=\"right\" valign=\"top\" ".$color["SI_Logo"]."><a href=\"".$folder."Senderinformationen/".$meilensteine[$i]["SI_Logo"]."\" rel=\"lightbox\">".$meilensteine[$i]["SI_Logo"]."</a></td></tr>";
		else
			$logoundsende = "";
			
		if ($meilensteine[$i]["SI_Sendegebiet"] != "")
			$logoundsende .= "<tr><td valign=\"top\" ".$color["SI_Sendegebiet"]."><b>Sendegebiet: </b></td><td align=\"right\" valign=\"top\" ".$color["SI_Logo"]."><a href=\"".$folder."Senderinformationen/".$meilensteine[$i]["SI_Sendegebiet"]."\" rel=\"lightbox\">".$meilensteine[$i]["SI_Sendegebiet"]."</a></td></tr>";
					
		$event .= "<event\n start=\"".umwandeln($meilensteine[$i]["MS_Datum"])." 12:00:00 GMT\"\n title=\"".escape($meilensteine[$i]["MS_Name"])."\"".$logo." icon=\"".$prev."timeline_js/images/dull-blue-circle.png\"\n color=\"#ffffff\">";
		$inhalt = "<br /><div style=\"color:#006DB7; font-size: 0.7em; font-family: Verdana;\"><b>".$meilensteine[$i]["SI_Bemerkungen"]."</b></div><br />
					<table border=\"0\" width=\"100%\" cellspacing=\"0\" style=\"color:#888888; font-size: 0.7em; font-family: Verdana;\">					
					
						<tr><td valign=\"top\" ".$color["SI_Name"]."><b>Name: </b></td><td align=\"right\" valign=\"top\" ".$color["SI_Name"].">".$meilensteine[$i]["SI_Name"]."</td></tr>
						<tr><td valign=\"top\" ".$color["SI_Geschaeftsfuehrer"]."><b>Gesch&auml;ftsf&uuml;hrer: </b></td><td align=\"right\" valign=\"top\" ".$color["SI_Geschaeftsfuehrer"].">".$meilensteine[$i]["SI_Geschaeftsfuehrer"]."</td></tr>
						<tr><td valign=\"top\" ".$color["SI_Mitarbeiter"]."><b>Mitarbeiter: </b></td><td align=\"right\" valign=\"top\" ".$color["SI_Mitarbeiter"].">".$meilensteine[$i]["SI_Mitarbeiter"]."</td></tr>
						<tr><td valign=\"top\" ".$color["SI_Programmformat"]."><b>Programmformat: </b></td><td align=\"right\" valign=\"top\" ".$color["SI_Programmformat"].">".$meilensteine[$i]["SI_Programmformat"]."</td></tr>
						<tr><td valign=\"top\" ".$color["SI_Sendezeiten"]."><b>Sendezeiten: </b></td><td align=\"right\" valign=\"top\" ".$color["SI_Sendezeiten"].">".$meilensteine[$i]["SI_Sendezeiten"]."</td></tr>
						<tr><td></td><td></td></tr>
					</table>
					<table border=\"0\" width=\"100%\" cellspacing=\"0\" style=\"color:#888888; font-size: 0.7em; font-family: Verdana;\">
						<tr><td valign=\"top\" ".$col_Anschrift."><b>Anschrift: </b></td><td align=\"right\" valign=\"top\" ".$color["SI_Strasse"].">".$meilensteine[$i]["SI_Strasse"]." ".$meilensteine[$i]["SI_Hausnummer"]."</td></tr>
						<tr><td></td><td align=\"right\" valign=\"top\" ".$color["SI_PLZ"].">".$meilensteine[$i]["SI_PLZ"]." <div ".$color["SI_Ort"].">".$meilensteine[$i]["SI_Ort"]."</div></td></tr>
						<tr><td></td><td align=\"right\" valign=\"top\" ".$color["SI_Homepage"].">".$meilensteine[$i]["SI_Homepage"]."</td></tr>
						<tr><td></td><td></td></tr>
						".$logoundsende."
					</table><br />
					";
		$inhalt = escape($inhalt);
		$event .= $inhalt."</event>\n";

		foreach ($meilensteine[$i] as $key => $value) {
			$old_ms[$key] = $value;
		}	
	}
} else {$intervallMonat = 170;
		$intervall = 150; }
if ($num_events != 0) {

	//############################################################################
	for ($i=0; $i<$num_events; $i++) {
		
		$data = mysql_query("SELECT ev_mediafile FROM eventmedia WHERE ((EV_ID='".$events[$i]["EV_ID"]."') AND (ev_mediatyp='bild'))"); 
		while ($temp = mysql_fetch_array($data, MYSQL_ASSOC)){ 
			$event_bilder = $temp;
		}
		$data = mysql_query("SELECT ev_mediafile FROM eventmedia WHERE ((EV_ID='".$events[$i]["EV_ID"]."') AND (ev_mediatyp='video'))"); 
		while ($temp = mysql_fetch_array($data, MYSQL_ASSOC)){ 
			$event_videos = $temp;
		}
		$data = mysql_query("SELECT ev_mediafile FROM eventmedia WHERE ((EV_ID='".$events[$i]["EV_ID"]."') AND (ev_mediatyp='audio'))"); 
		while ($temp = mysql_fetch_array($data, MYSQL_ASSOC)){ 
			$event_audios = $temp;
		}
		$data = mysql_query("SELECT ev_mediafile FROM eventmedia WHERE ((EV_ID='".$events[$i]["EV_ID"]."') AND (ev_mediatyp='dok'))"); 
		while ($temp = mysql_fetch_array($data, MYSQL_ASSOC)){ 
			$event_dokumente = $temp;
		}
		if ($event_bilder != NULL) {
			$dateien = "<b>Bilder: </b><br />";
			foreach ($event_bilder as $value) {
				$dateien .= "<a href=\"media/".$events[$i]["SenderID"]."/Events/".$value."\" target=\"_blank\">".$value."</a><br />";
			}
		}
		if ($event_videos != NULL) {
			$dateien .= "<br /><b>Videos: </b><br />";
			foreach ($event_videos as $value) {
				$dateien .= "<a href=\"media/".$events[$i]["SenderID"]."/Events/".$value."\" target=\"_blank\">".$value."</a><br />";
			}
		}
		if ($event_audios != NULL) {
			$dateien .= "<br /><b>Audiodateien: </b><br />";
			foreach ($event_audios as $value) {
				$dateien .= "<a href=\"media/".$events[$i]["SenderID"]."/Events/".$value."\" target=\"_blank\">".$value."</a><br />";
			}
		}
		if ($event_dokumente != NULL) {
			$dateien .= "<br /><b>Dokumente: </b><br />";
			foreach ($event_dokumente as $value) {
				$dateien .= "<a href=\"media/".$events[$i]["SenderID"]."/Events/".$value."\" target=\"_blank\">".$value."</a><br />";
			}
		}
		$event .= "<event\n start=\"".umwandeln($events[$i]["EV_Datum"])." 12:00:00 GMT\"\n title=\"".escape($events[$i]["EV_Name"])."\"\n icon=\"".$prev."timeline_js/images/red_circle.png\"\n color=\"#ffffff\">";
		$inhalt = "<div width=\"100%\" style=\"color:#006DB7; font-size: 0.7em; font-family: Verdana;\">".$events[$i]["EV_Info"]."<br /><br />";
		$inhalt .= $dateien."<br /></div>";
		$inhalt = escape($inhalt);
		$event .= $inhalt."</event>\n";
		if ($num_meilensteine == 0) $first_ms = umwandeln($events[0]["EV_Datum"])." 12:00:00 GMT";
	}
}
if (($num_meilensteine == 0) && ($num_events == 0)) {
	$event .= "<event\n start=\"Jan 01 2009 00:00:00 GMT\"\n title=\"Keine Meilensteine oder Events vorhanden\"\n icon=\"".$prev."timeline_js/images/red-circle2.png\"\n color=\"#FF0000\">";
	$inhalt = "<table border=\"0\" width=\"100%\" cellspacing=\"0\" style=\"font-size: 0.7em; font-family: Verdana;\">
				<tr><td valign=\"top\"><b>
				Es sind keine Meilensteine oder Events vorhanden</b><br />Bitte wählen Sie einen anderen Sender<br /><br />
				</td>
				</tr>
				</table>";
	$inhalt = escape($inhalt);
	$event .= $inhalt."</event>\n";
	$intervall = 80; $intervallMonat = 120;
	$first_ms = "Jan 01 2009 00:00:00 GMT";
}

fwrite($newfile, $event); 
fwrite($newfile, "</data>");
fclose($newfile);

$dropdown = "<select name=\"sender\" onChange=\"document.forms['senderform'].submit()\">
			";
	if ($SenderID == 2197) $dropdown .= "<option value=\"2197\">Alle</option>";	
	
	for ( $i=0; $i<$num_sender; $i++) {
		if ($sender[$i]["SenderID"] != $SenderID) {
			$dropdown_2 .= "<option value=\"".$sender[$i]["SenderID"]."\">".escape($sender[$i]["SenderName"])."</option>"; 
		} 
		else { 
			$dropdown .= "<option value=\"".$sender[$i]["SenderID"]."\">".escape($sender[$i]["SenderName"])."</option>"; 
			$Akt_Sender = $sender[$i]["SenderName"];
		}
	}

if ($SenderID != 2197) { 
	$dropdown .= $dropdown_2."<option value=\"2197\">Alle</option></select>"; 
}
else  { 
	$Akt_Sender = "aller Sender"; 
	$dropdown .= $dropdown_2."</select>";
}

$timestamp = time();

echo '
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<html> <!-- http://www.dezinerfolio.com/2007/07/19/simple-javascript-accordions/ -->
<head>
<title>Chronik</title>
 <script>
      Timeline_urlPrefix=\'timeline_js/\';       
      Timeline_parameters=\'bundle=true\';
 </script>
 <script src="timeline_js/timeline-api.js"  type="text/javascript"></script>
 
 <script type="text/javascript" src="timeline_js/js/prototype.js"></script>
 <script type="text/javascript" src="timeline_js/js/scriptaculous.js?load=effects,builder"></script>
 <script type="text/javascript" src="timeline_js/js/lightbox.js"></script>
 
 <script type="text/javascript">
	var tl;
	
	function onLoad() {
		
		var eventSource = new Timeline.DefaultEventSource();
				
		var bandInfos = [ 
			Timeline.createBandInfo({
				eventSource:    eventSource,
				date:           "'.$first_ms.'",
				width:          "75%",
				intervalUnit:   Timeline.DateTime.MONTH,          
				intervalPixels: '.$intervallMonat.'		}),
				
			Timeline.createBandInfo({
				overview:		true,
				eventSource:    eventSource,
				date:           "'.$first_ms.'",
				width:          "25%",
				intervalUnit:   Timeline.DateTime.YEAR,
				intervalPixels: '.$intervall.'	})   
		];
		
		bandInfos[1].syncWith = 0;   
		bandInfos[1].highlight = true;
		
		tl = Timeline.create(document.getElementById("my-timeline"), bandInfos); 
		Timeline.loadXML("'.$folder.'zeitleiste.xml", function(xml, url) { eventSource.loadXML(xml, url); });
	} 
	var resizeTimerID = null;

	function onResize() {
		if (resizeTimerID == null) {
			resizeTimerID = window.setTimeout(function() {
				resizeTimerID = null;
				tl.layout();         
			},500);
		} 
	}
	</script>
		<style type="text/css">
						
			a:link { color:#006DB7; text-decoration:none;}    
			a:visited { color:#002aB7; text-decoration:none;} 
			a:hover { color:#03adf4; text-decoration:underline;}		
					
			select {
			font-family:Verdana;
			font-size: 1.0em;
			color:#006DB7;
			}

		</style>
	
		<link rel="stylesheet" href="timeline_js/css/lightbox.css" type="text/css" media="screen" />
		
</head> 

 <body onload="onLoad();" onresize="onResize();">
 
 <div align="center"><br /><br />
	<table width="98%" border="0" cellspacing="0" style="font-size: 0.8em; font-family: Verdana; color:#006db7">
		<tr>
			<td style="vertical-align:bottom;" align="left"><img src="bilder/dieneuewelle300.gif">
			<td style="vertical-align:top;" align="right"><img src="timeline_js/images/zumlogin.jpg" />&nbsp;<a style="vertical-align:top; color:#006db7;" href="http://chronik.kanal8.de"><b>login</b></a>&nbsp;</td>
		</tr>
		<tr><td colspan="2"><br /><br /><hr><br /></td>
		</tr>
		<tr style="background-color:#FFFFFF; color:#006db7; height:30px;">
			<td align="left" style="vertical-align:top"><b>Chronik '.$Akt_Sender.'</b></td>
			<td align="right" style="vertical-align:top"><form name="senderform" method="post"><input style="vertical-align:middle;" onClick="location.href=\'indextimeline.php?sid='.$SenderID.'&ms='.$showMS.'&t='.$timestamp.'\'" name="checkb_MS" type="checkbox" value="MS_an"'.$checkMS.'<input style="vertical-align:middle;" onClick="location.href=\'indextimeline.php?sid='.$SenderID.'&ev='.$showEV.'&t='.$timestamp.'\'" name="checkb_EV" type="checkbox" value="Ev_an"'.$checkEV.$dropdown.'</form></td>
		</tr>
		<tr>
			<td style="vertical-align:top;" colspan="2">
				<div id="my-timeline" style="font-family:Verdana; color:#006DB7; font-size:1.0em; height: '.$hoehe.'; border: 0px solid #006DB7"></div>
				<noscript>Die Darstellung der Zeitleiste erfordert Javascript. Bitte schalten Sie Javascript in Ihrem Browser ein. Danke.
				</noscript>
			</td>
		</tr>
	</table><br />
  </div>
  <div align="center">
  <table width="98%" border="0" cellspacing="0" style="font-size: 0.8em; font-family: Verdana; color:#ffffff">
  <tr>
	<td>
		<iframe src="menu.php?Sender='.$SenderID.'" width="100%" height="300px" frameborder="0" style="border: 0; margin: 0; padding: 0;"></iframe>
	</td>
  </tr>
  </table>
 
 </div>
 </body>
</html>

';

?>
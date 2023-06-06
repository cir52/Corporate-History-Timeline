<?php

function connect() 
{ 
    $con = mysql_connect('db2659.1und1.de','dbo362507291','c52pass') or die(mysql_error()); 
     mysql_select_db('db362507291',$con) or die(mysql_error()); 
} 



$data = mysql_query("SELECT SenderID FROM sender"); 
while ($temp = mysql_fetch_array($data)){ 
	$Sender[] = $temp[0];
}

foreach ($Sender as $value) {
	$SenderID = $value;
	$folder =  

$temp = "SELECT * FROM meilensteine WHERE SenderID='".$SenderID."'"; 
$result = mysql_query($temp);
$num = mysql_num_rows($result);

// die xml-datei benennen
$fname = $SenderID.".xml";

if(file_exists($folder.$fname)){
	unlink($folder.$fname);

}	
// xml-datei öffnen
$newfile = fopen($dateiname,"a+");
//
// neuen xml-string anhängen
$add = stripslashes($_POST['str']."\n");
$status = fwrite($newfile, $add); 
	// rückmeldung an flash
	if($status)
	{
	echo "&daten=ok";
	}

	fclose($newfile);
?>
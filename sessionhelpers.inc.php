<?php
function connect() 
{ 
    $con = mysql_connect('db2659.1und1.de','dbo362507291','c52pass') or die(mysql_error()); 
     mysql_select_db('db362507291',$con) or die(mysql_error()); 
} 

function check_user($name, $pass) 
{ 
    $sql="SELECT SenderID 
    FROM sender 
    WHERE SenderNick='".$name."' AND SenderPass=MD5('".$pass."') 
    LIMIT 1"; 
    $result= mysql_query($sql) or die(mysql_error()); 
    if ( mysql_num_rows($result)==1) 
    { 
        $sender=mysql_fetch_assoc($result); 
		return $sender['SenderID']; 
    } 
    else 
        return false; 
} 

function login($senderid) 
{ 
	$path = "http://www.pink-monk-records.de/wurmzeichen/timeline/media/";
	if(!file_exists("$path\\$senderid"))
	{
		$rs = mkdir( "$path\\$senderid", 0777 );
	}
	if(!file_exists("$path\\$senderid\\Events"))
	{
		$rs = mkdir( "$path\\$senderid\\Events", 0777 );
	}
	if(!file_exists("$path\\$senderid\\Mediadaten"))
	{
		$rs = mkdir( "$path\\$senderid\\Mediadaten", 0777 );
	}
	if(!file_exists("$path\\$senderid\\Senderinformationen"))
	{
		$rs = mkdir( "$path\\$senderid\\Senderinformationen", 0777 );
	}

	if($_SESSION[adminmode]==true && trim($_SESSION[showsender])!='') return;

	$sql="UPDATE sender 
	SET SenderSession='".session_id()."' 
	WHERE SenderID=".$senderid; 
    mysql_query($sql); 

	$timestamp = time();
	$temp="UPDATE sender SET LastLogin=FROM_UNIXTIME(".$timestamp.") WHERE SenderSession='".session_id()."'"; 
	mysql_query($temp);	 
	
	$temp="UPDATE sender SET LoginCount = (LoginCount + 1) WHERE SenderSession='".session_id()."'"; 
	mysql_query($temp);
} 

function check_logg() 
{ 
    if($_SESSION[adminmode]==true && trim($_SESSION[showsender])!='') return true;

	$sql="SELECT SenderID 
    FROM sender 
    WHERE SenderSession='".session_id()."' 
    LIMIT 1"; 
    $result = mysql_query($sql);
    
	if (mysql_num_rows($result) == 1) {
	
		$timestamp = time();
		
		//letzter Login ?
		$temp = "SELECT (UNIX_TIMESTAMP(LastLogin)) FROM sender WHERE SenderSession='".session_id()."'"; 
		$data = mysql_query($temp);
		$row = mysql_fetch_row($data);
		$lastlogin = $row[0];
		
		if ( ($timestamp - $lastlogin) > 900 )  { 
			$temp="UPDATE sender SET SenderSession=NULL WHERE SenderSession='".session_id()."'";
			mysql_query($temp);
			return false;
		  }
		else {		
			$temp="UPDATE sender SET LastLogin=FROM_UNIXTIME(".$timestamp.") WHERE SenderSession='".session_id()."'"; 
			mysql_query($temp);
			return true;
		}
	}
	else
		return false;
} 

function logged_in() 
{ 
    if($_SESSION[adminmode]==true) 
    {
		if(trim($_GET[showsender])!='')
		{
			$_SESSION[showsender]=$_GET[showsender];
		}
		if(trim($_SESSION[showsender])!='')
		{
			return $_SESSION[showsender];
		}
    }
	$sql="SELECT SenderID FROM sender WHERE SenderSession='".session_id()."' LIMIT 1"; 
    $data = mysql_query($sql); 
	$row = mysql_fetch_row($data);
	$result = $row[0];
	return ($result); 
} 

function logout() 
{ 
    if($_SESSION[showsender]==true) 
    {
		$_SESSION[showsender] = "";
		$_SESSION[adminmode] = false;
		return;
    }    
    if($_SESSION[adminmode]==true) 
    {
		$_SESSION[adminmode] = false;
    }    
    $sql="UPDATE sender SET SenderSession=NULL WHERE SenderSession='".session_id()."'"; 
	mysql_query($sql); 
    $_SESSION[loggedin] = false;
} 

function datum_ok($datum)
{
	list ($d, $m, $y) = explode('.', $datum); 

	if (is_numeric($d) && is_numeric($m) && is_numeric($y) && ( (int)$y > 1950 ))
	{ 
		if ( checkdate( $m, $d, $y) ) 
			return true;
	}
	else
		return false;
}

//meilenstein array füllen
function fill() {
	$row[0]=$_POST['MS_Datum']; $row[2]=$_POST['MS_Name']; $row[4]=$_POST['SI_Name'];
	$row[6]=$_POST['SI_Geschaeftsfuehrer']; $row[7]=$_POST['SI_Mitarbeiter']; $row[8]=$_POST['SI_Programmformat']; $row[9]=$_POST['SI_Sendezeiten'];
	$row[10]=$_POST['SI_Bemerkungen']; $row[11]=$_POST['SI_Strasse']; $row[12]=$_POST['SI_Hausnummer']; $row[13]=$_POST['SI_PLZ'];
	$row[14]=$_POST['SI_Ort']; $row[15]=$_POST['SI_Telefon']; $row[16]=$_POST['SI_FAX']; $row[17]=$_POST['SI_eMail'];
	$row[18]=$_POST['SI_Homepage'];
	return $row;
}

//Löschen
function delete($place, $file) {

	list ($pl_src, $pl_id) = explode('_', $place);

	switch ($pl_src) {
		case "EV": 	$sql=("DELETE FROM eventmedia WHERE (EV_ID='".$pl_id."' AND ev_mediafile='".$file."')");
					$datei = 'media\\'.logged_in().'\\'.'Events\\'.$file;
					mysql_query($sql);
					@unlink(str_replace("\\","/",$datei));
			break;
		case "MSLogo": 	
					$sql=("UPDATE meilensteine SET SI_Logo='' WHERE (MS_ID='".$pl_id."' AND SI_Logo='".$file."')");
					mysql_query($sql);
					$sql = mysql_query("SELECT COUNT(*) FROM meilensteine WHERE (SI_Logo='".$file."' AND SenderID='".logged_in()."')");
					$row = mysql_fetch_row($sql);
					$result = $row[0];					
					if ($result == 0) {
						$datei = 'media\\'.logged_in().'\\'.'Senderinformationen\\'.$file;
					@unlink(str_replace("\\","/",$datei));
					}
			break;
		case "MSSende": 	
					$sql=("UPDATE meilensteine SET SI_Sendegebiet='' WHERE (MS_ID='".$pl_id."' AND SI_Sendegebiet='".$file."')");
					mysql_query($sql);
					$sql = mysql_query("SELECT COUNT(*) FROM meilensteine WHERE (SI_Sendegebiet='".$file."' AND SenderID='".logged_in()."')");
					$row = mysql_fetch_row($sql);
					$result = $row[0];					
					if ($result == 0) {
						$datei = 'media\\'.logged_in().'\\'.'Senderinformationen\\'.$file;
					@unlink(str_replace("\\","/",$datei));
					}
			break;			
		case "MD":  $sql=("DELETE FROM mediadaten WHERE (SenderID='".logged_in()."' AND Media_File='".$file."')");
					$datei = 'media\\'.logged_in().'\\'.'Mediadaten\\'.$file;
					mysql_query($sql);
					@unlink(str_replace("\\","/",$datei));
			break;
	}
}

//****************  DATEI UPLOAD *********************************+
function random($laenge) { 
    $signs = "abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789";
    $name_new = ""; 
    mt_srand ((double) microtime() * 1000000); 
    for ($i = 0; $i < $laenge; $i++) $name_new .= $signs{mt_rand (0,strlen($signs))}; 
    return $name_new; 
}

function createName($pname, $ftype){
    global $folder;
    $pname .= random(3);
    if(file_exists($folder.$pname.".".$ftype)) return createName($pname, $ftype);
    else return $pname.".".$ftype;
}

function fileupload($med_typ, $para){

	global $folder;
    global $max_filesize;
    global $extensions;
	global $SenderID;
    if(!empty($_FILES[$med_typ]['name'])){
        $fname = $_FILES[$med_typ]['name'];
        $split = explode(".", $fname);
        $pname = $split[0];
        $ftype = $split[1];
        if(!in_array(strtolower($ftype), $extensions)) {
			foreach ($extensions as $value)
				$stringext.= $value.", ";
			return "Die Datei hat keine zulässige Dateiendung. (erlaubt: $stringext)";
        }
		if($_FILES[$med_typ]['size'] > $max_filesize) return "Die von Ihnen ausgewählte Datei ist zu groß.";
        if(file_exists($folder.$fname)){
            $fname = createName($pname, $ftype);
            $info = "<br /><strong>Die Datei musste unbenannt werden, weil eine Datei mit gleichem Dateinamen schon auf dem Server existiert.</strong>";
        }
        if(!move_uploaded_file($_FILES[$med_typ]['tmp_name'], str_replace("\\","/",$folder.$fname))) return "Der Upload ist fehlgeschlagen, bitte versuchen Sie es erneut.";
		chmod(str_replace("\\","/",$folder.$fname),0777);        
		//erfolgreicher Upload: in Datenbanktabelle eintragen:
		list ($wo, $id) = explode('_', $para);
		switch ($wo) {
			case "EV": 
				$sql = "INSERT INTO eventmedia SET EV_ID='".$id."', ev_mediatyp='".$med_typ."', ev_mediafile='".$fname."'";
				break;
			case "MS": 
				$sql = "UPDATE meilensteine SET ".$med_typ."='".$fname."' WHERE MS_ID='".$id."'";
				break;
			case "MD": 
				$sql = "INSERT INTO mediadaten SET SenderID='".$SenderID."', Media_Typ='".$med_typ."', Media_File='".$fname."', Titel='".$id."'";
				break;
		}
		mysql_query($sql);
	
		return "Die Datei \"$fname\" wurde erfolgreich hochgeladen.$info";
    }
    else return "Sie haben noch keine Datei ausgewählt!";
}
connect(); 
?>
<?php
require_once(realpath(dirname(__FILE__) . "/constants.php"));

ini_set("memory_limit", "2048M");
ini_set('max_execution_time', 300);

function connectCPB($db = 'compbrowser') {
	$mysqli = new mysqli(CPB_HOST, CPB_USER, CPB_PASS, $db);
	if($mysqli->connect_errno) {
		die("Connect failed:" . $mysqli->connect_error);
	}
	return $mysqli;
}

function connectGB($db) {
	$mysqli = new mysqli(GB_HOST, GB_USER, GB_PASS, $db);
	if($mysqli->connect_errno) {
		die("Connect failed:" . $mysqli->connect_error);
	}
	return $mysqli;
}


function byteSwap32($data) {
	$arr = unpack("V", pack("N", $data));
	return $arr[1];
}

function cmpTwoBits($aHigh, $aLow, $bHigh, $bLow) {
	// return -1 if b < a, 0 if equal, +1 else
	if($aHigh < $bHigh) {
		return 1;
	} else if($aHigh > $bHigh) {
		return -1;
	} else {
		return (($aLow < $bLow)? 1: (($aLow > $bLow)? -1: 0));
	}
}

function rangeIntersection($start1, $end1, $start2, $end2) {
	$s = max($start1, $start2);
	$e = min($end1, $end2);
	return $e - $s;
}

function getSpeciesDbNames() {
	// return a full list of species db names
	$mysqli = connectCPB();
	$result = array();
	$species = $mysqli->query("SELECT dbname FROM species");
	while($spcitor = $species->fetch_assoc()) {
		$result[] = $spcitor["dbname"];
	}
	$species->free();
	$mysqli->close();
	return $result;
}

function getSpeciesInfoFromArray($spcDbNameList = NULL) {
	// return everything about species from db indicated by spcDbNameList
	$mysqli = connectCPB();
	$spcinfo = array();
	$sqlstmt = "SELECT * FROM species";
	if(!empty($spcDbNameList)) {
		$sqlstmt .= " WHERE dbname = 'hg19'";
		for($i = 0; $i < count($spcDbNameList); $i++) {
			$sqlstmt .= " OR dbname = '" . $mysqli->real_escape_string($spcDbNameList[$i]) . "'";
		}
	}		
	$species = $mysqli->query($sqlstmt);
	while($spcitor = $species->fetch_assoc()) {
		$spcinfo[] = $spcitor;
	}	
	$species->free();
	$mysqli->close();
	return $spcinfo;
}

function getSpeciesDatabaseFromGapInfo($gap, $spcDbName) {
	// return the database name according the gap value from each species
	$mysqli = connectCPB();
	$spcinfo = array();
	$spcinfo = $mysqli->query("SELECT * FROM $spcDbName WHERE gap<=$gap ORDER BY gap DESC LIMIT 1");
	while($specdb = $spcinfo->fetch_assoc()){
		$bb = $specdb['databaseName'];
	}
	$spcinfo->free();
	$mysqli->close();
	return $bb;
}

?>

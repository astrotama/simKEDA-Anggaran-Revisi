<?php

function kegiatanlt_autocomplete($tag='', $arg='') {
    $matches = array();
    if ($arg) {
        switch($tag) {
            case 'uraian' :
                $qsql = 'select kegid as fld1, kegiatan as fld2 from {kegiatanlt} where lower(kegiatan) like lower(\'%%%s%%\')';
                break;
            case 'kode' :
                $qsql = 'select kegid as fld2, kegiatan as fld1 from {kegiatanlt} where lower(kegid) like lower(\'%%%s%%\')';
                break;
        }
        $result = db_query_range($qsql, array($arg), 0,10 );
        while ($data = db_fetch_object($result)) {
            $matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
        }
    }
    drupal_json($matches);
}

function kegiatanlt_utilitas($arg1="", $arg2="") {
    switch($arg1) {
        case 'getnk':
            $nk = getnk($arg2);
            echo $nk;            
            break;
        case 'getprogram':
            echo getprogram($arg2);
            break;
    }
}
function getprogram($kodeu) {
	$q = sprintf("select kodepro, program from program where kodeu='%s' order by program", db_escape_string($kodeu));
	$r = db_query($q);
	while ($d = db_fetch_object($r)) {
		echo "<option value='" . $d->kodepro . "'>" . $d->program . "</option>\n";
	}
    
}
function getnk($kodepro) {
    $v = '001';
    if (strlen($kodepro)>0) {
        $query = sprintf("select nk from kegiatanlt where kodepro='%s' order by nk desc", db_escape_string($kodepro));
        $pres = db_query($query);
        if ($data=db_fetch_object($pres)) {
            $v = $data->nk;
            $iv = intval($v);
            $iv++;
            $v = '000' . $iv;
            $v = substr($v, strlen($v)-3);
            return $v;
        } 	        
    } 
    return $v;
}
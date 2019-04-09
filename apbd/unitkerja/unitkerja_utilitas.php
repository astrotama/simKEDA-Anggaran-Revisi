<?php

function unitkerja_autocomplete($tag='', $arg='') {
    $matches = array();
    if ($arg) {
        switch($tag) {
            case 'uraian' :
                $qsql = 'select kodeuk as fld1, namauk as fld2 from {unitkerja} where lower(namauk) like lower(\'%%%s%%\')';
                break;
            case 'kode' :
                $qsql = 'select kodeuk as fld2, namauk as fld1 from {unitkerja} where lower(kodeuk) like lower(\'%%%s%%\')';
                break;
        }
        $result = db_query_range($qsql, array($arg), 0,10 );
        while ($data = db_fetch_object($result)) {
            $matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
        }
    }
    drupal_json($matches);
}
function unitkerja_utilitas($arg1, $arg2) {
    
}
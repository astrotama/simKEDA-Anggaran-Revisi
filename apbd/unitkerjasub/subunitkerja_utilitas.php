<?php

function subunitkerja_autocomplete($tag='', $arg='') {
    $matches = array();
    if ($arg) {
        switch($tag) {
            case 'uraian' :
                $qsql = 'select kodesuk as fld1, namasuk as fld2 from {subunitkerja} where lower(namasuk) like lower(\'%%%s%%\')';
                break;
            case 'kode' :
                $qsql = 'select kodesuk as fld2, namasuk as fld1 from {subunitkerja} where lower(kodesuk) like lower(\'%%%s%%\')';
                break;
        }
        $result = db_query_range($qsql, array($arg), 0,10 );
        while ($data = db_fetch_object($result)) {
            $matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
        }
    }
    drupal_json($matches);
}
function subunitkerja_utilitas ($arg1, $arg2) {

}
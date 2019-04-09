<?php

function dinas_autocomplete($tag='', $arg='') {
    $matches = array();
    if ($arg) {
        switch($tag) {
            case 'uraian' :
                $qsql = 'select id as fld1, nama as fld2 from {ukurusan} where lower(nama) like lower(\'%%%s%%\')';
                break;
            case 'kode' :
                $qsql = 'select id as fld2, nama as fld1 from {ukurusan} where lower(id) like lower(\'%%%s%%\')';
                break;
        }
        $result = db_query_range($qsql, array($arg), 0,10 );
        while ($data = db_fetch_object($result)) {
            $matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
        }
    }
    drupal_json($matches);
}
function dinas_utilitas ($arg1, $arg2) {

}
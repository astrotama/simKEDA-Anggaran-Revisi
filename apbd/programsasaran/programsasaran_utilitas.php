<?php

function bidang_autocomplete($tag='', $arg='') {
    $matches = array();
    if ($arg) {
        switch($tag) {
            case 'uraian' :
                $qsql = 'select kodebid as fld1, bidang as fld2 from {bidang} where lower(bidang) like lower(\'%%%s%%\')';
                break;
            case 'kode' :
                $qsql = 'select kodebid as fld2, bidang as fld1 from {bidang} where lower(kodebid) like lower(\'%%%s%%\')';
                break;
        }
        $result = db_query_range($qsql, array($arg), 0,10 );
        while ($data = db_fetch_object($result)) {
            $matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
        }
    }
    drupal_json($matches);
}
function bidang_utilitas ($arg1, $arg2) {

}
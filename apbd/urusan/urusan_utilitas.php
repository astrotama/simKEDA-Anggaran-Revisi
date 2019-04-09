<?php

function urusan_autocomplete($tag='', $arg='') {
    $matches = array();
    if ($arg) {
        switch($tag) {
            case 'uraian' :
                $qsql = 'select kodeu as fld1, urusansingkat as fld2 from {urusan} where lower(urusansingkat) like lower(\'%%%s%%\')';
                break;
            case 'kode' :
                $qsql = 'select kodeu as fld2, urusansingkat as fld1 from {urusan} where lower(kodeu) like lower(\'%%%s%%\')';
                break;
        }
        $result = db_query_range($qsql, array($arg), 0,10 );
        while ($data = db_fetch_object($result)) {
            $matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
        }
    }
    drupal_json($matches);
}
function urusan_utilitas($arg1, $arg2) {
    switch ($arg1) {
        case 'listurusan':
            
            break;
    }
}
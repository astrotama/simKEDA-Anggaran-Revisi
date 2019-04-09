<?php

function apbdop_autocomplete($tag='', $arg='') {
    $matches = array();
    if ($arg) {
        switch($tag) {
            case 'uraian' :
                $qsql = 'select username as fld1, nama as fld2 from {apbdop} where lower(nama) like lower(\'%%%s%%\')';
                break;
            case 'kode' :
                $qsql = 'select username as fld2, nama as fld1 from {apbdop} where lower(username) like lower(\'%%%s%%\')';
                break;
        }
        $result = db_query_range($qsql, array($arg), 0,10 );
        while ($data = db_fetch_object($result)) {
            $matches[$data->fld2] = $data->fld2. ' (' . $data->fld1. ')';
        }
    }
    drupal_json($matches);
}
<?php

function kegiatanlt_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    $kegid = arg(3);
    if (isset($kegid)) {
        $sql = 'select kegid,u1,u2,np,nk,kegiatan,kodepro,kodeu from {kegiatanlt} where kegid=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($kegid));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Data Kegiatan',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['kegid'] = array('#type' => 'value', '#value' => $data->kegid);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->kegiatan (Kode: $data->kegid)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/kegiatanlt',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function kegiatanlt_delete_form_validate($form, &$form_state) {
}
function kegiatanlt_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kegid = $form_state['values']['kegid'];
        $sql = 'DELETE FROM {kegiatanlt} WHERE kegid=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($kegid));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/kegiatanlt');
        }
        
    }
}
?>
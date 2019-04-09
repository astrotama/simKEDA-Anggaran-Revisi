<?php

function desa_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    
    $kodedesa = arg(3);
    if (isset($kodedesa)) {
        $sql = 'select kodedesa,namadesa,kodeuk from {desa} where kodedesa=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($kodedesa));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Data Desa',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['kodedesa'] = array('#type' => 'value', '#value' => $data->kodedesa);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->namadesa (Kode: $data->kodedesa)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/desa',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function desa_delete_form_validate($form, &$form_state) {
}
function desa_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodedesa = $form_state['values']['kodedesa'];
        $sql = 'DELETE FROM {desa} WHERE kodedesa=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($kodedesa));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/desa');
        }
        
    }
}
?>
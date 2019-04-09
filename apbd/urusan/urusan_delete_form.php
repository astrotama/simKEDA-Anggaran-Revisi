<?php

function urusan_delete_form() {
    drupal_set_title('Data Urusan Pemerintahan');
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    $kodeu = arg(3);
    if (isset($kodeu)) {
        $sql = 'select kodeu,sifat,urusan,urusansingkat from {urusan} where kodeu=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($kodeu));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Data Urusan',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['kodeu'] = array('#type' => 'value', '#value' => $data->kodeu);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->urusan (Kode: $data->kodeu)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/urusan',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function urusan_delete_form_validate($form, &$form_state) {
}
function urusan_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodeu = $form_state['values']['kodeu'];
        $sql = 'DELETE FROM {urusan} WHERE kodeu=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($kodeu));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/urusan');
        }
        
    }
}
?>
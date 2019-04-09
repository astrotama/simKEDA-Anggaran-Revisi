<?php

function download_delete_form() {
    drupal_set_title('Data Download');
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    $nomor = arg(3);
    if (isset($nomor)) {
        $sql = 'select nomor,topik,uraian from {download} where nomor=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($nomor));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Data Download',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['nomor'] = array('#type' => 'value', '#value' => $data->nomor);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->topik ($data->uraian)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/download',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function download_delete_form_validate($form, &$form_state) {
}
function download_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $nomor = $form_state['values']['nomor'];
        $sql = 'DELETE FROM {download} WHERE nomor=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($nomor));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/download');
        }
        
    }
}
?>
<?php

function subkegiatancam_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    $id = arg(5);
    if (isset($id)) {
        $sql = 'select id,kodekeg,uraian,lokasi,total,totalsebelum,totalsesudah from {kegiatankecsub} where id=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($id));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus subkegiatancam',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['id'] = array('#type' => 'value', '#value' => $data->id);
                $form['formdata']['kodekeg'] = array('#type' => 'value', '#value' => $data->kodekeg);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->uraian (Lokasi: $data->lokasi)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/kegiatancam/subkegiatan/' . $data->kodekeg,
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function subkegiatancam_delete_form_validate($form, &$form_state) {
}
function subkegiatancam_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $id = $form_state['values']['id'];
        $kodekeg = $form_state['values']['kodekeg'];
        
        $sql = 'DELETE FROM {kegiatankecsub} WHERE id=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($id));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/kegiatancam/subkegiatan/' . $kodekeg);
        }
        
    }
}
?>
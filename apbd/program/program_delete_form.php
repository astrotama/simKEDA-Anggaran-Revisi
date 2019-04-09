<?php

function program_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    $kodepro = arg(3);
    if (isset($kodepro)) {
        $sql = 'select kodepro,kodeu,tahun,program,sifat,sasaran,target,np from {program} where kodepro=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($kodepro));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus program',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['kodepro'] = array('#type' => 'value', '#value' => $data->kodepro);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->program (Kode: $data->kodepro)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/program',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function program_delete_form_validate($form, &$form_state) {
}
function program_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodepro = $form_state['values']['kodepro'];
        $sql = 'DELETE FROM {program} WHERE kodepro=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($kodepro));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/program');
        }
        
    }
}
?>
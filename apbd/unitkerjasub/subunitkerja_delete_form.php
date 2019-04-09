<?php

function subunitkerja_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');

    $kodesuk = arg(3);
    if (isset($kodesuk)) {
        $sql = 'select kodesuk,kodeuk,namasuk from {subunitkerja} where kodesuk=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($kodesuk));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Sub SKPD',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['kodesuk'] = array('#type' => 'value', '#value' => $data->kodesuk);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->namasuk (Kode: $data->kodesuk)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/subunitkerja',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function subunitkerja_delete_form_validate($form, &$form_state) {
}
function subunitkerja_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodesuk = $form_state['values']['kodesuk'];
        $sql = 'DELETE FROM {subunitkerja} WHERE kodesuk=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($kodesuk));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/subunitkerja');
        }
        
    }
}
?>
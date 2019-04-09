<?php

function kegiatanskpd_perpanjangan_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    $kodekeg = arg(3);
    if (isset($kodekeg)) {
        $customwhere = '';
        if (!isSuperuser()) {
            $customwhere .= sprintf(' and kodeuk=\'%s\' ', apbd_getuseruk());	
        }	
        
        $sql = 'select kodekeg, kegiatan, plafon from {kegiatanskpd} where kodekeg=\'%s\'' . $customwhere;
        $res = db_query(db_rewrite_sql($sql), array($kodekeg));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Kegiatan',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['kodekeg'] = array('#type' => 'value', '#value' => $data->kodekeg);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->kegiatan</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/kegiatanskpd',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function kegiatanskpd_perpanjangan_form_validate($form, &$form_state) {
}
function kegiatanskpd_perpanjangan_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodekeg = $form_state['values']['kodekeg'];
        $sql = 'DELETE FROM {kegiatanskpd} WHERE kodekeg=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($kodekeg));
        if ($res) {
            $query = sprintf("DELETE FROM {kegiatanskpd} where kodekeg='%s'", db_escape_string($kodekeg));
            db_query($query);
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/kegiatanskpd');
        }
        
    }
}
?>
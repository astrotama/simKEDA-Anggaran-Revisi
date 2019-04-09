<?php

function bidang_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    drupal_set_title('Data Bidang');
    $kodebid = arg(3);
    if (isset($kodebid)) {
        $sql = 'select kodebid,bidang from {bidang} where kodebid=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($kodebid));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus bidang',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['kodebid'] = array('#type' => 'value', '#value' => $data->kodebid);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->bidang (Kode: $data->kodebid)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/bidang',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function bidang_delete_form_validate($form, &$form_state) {
}
function bidang_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodebid = $form_state['values']['kodebid'];
        $sql = 'DELETE FROM {bidang} WHERE kodebid=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($kodebid));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/bidang');
        }
        
    }
}
?>
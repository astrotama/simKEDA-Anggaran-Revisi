<?php

function unitkerja_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    $kodeuk = arg(3);    
    if (isset($kodeuk)) {
        $sql = 'select kodeuk, namauk, namasingkat, pimpinannama, pimpinanjabatan, pimpinanpangkat, pimpinannip, iskecamatan from {unitkerja} where kodeuk=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($kodeuk));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Unit Kerja',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['kodeuk'] = array('#type' => 'value', '#value' => $data->kodeuk);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->namauk (Kode: $data->kodeuk)</span>",
                            '#weight' => 1,
                            );
                
                $theForm = confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/unitkerja',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
                //print_r ($theForm['actions']['submit']);
                //$theForm['actions']['cancel']['#attributes'] = array('class' => 'btn_blue');
                return $theForm;
            }
        }
    }
}
function unitkerja_delete_form_validate($form, &$form_state) {
}
function unitkerja_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodeuk = $form_state['values']['kodeuk'];
        $sql = 'DELETE FROM {unitkerja} WHERE kodeuk=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($kodeuk));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/unitkerja');
        }
        
    }
}

?>
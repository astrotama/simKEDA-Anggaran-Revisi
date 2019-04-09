<?php

function apbdop_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    $username = arg(3);
    if (isset($username)) {
        $sql = 'select username,nama,kodeuk from {apbdop} where username=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($username));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus apbdop',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['username'] = array('#type' => 'value', '#value' => $data->username);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->nama (Kode: $data->username)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/manageuser',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function apbdop_delete_form_validate($form, &$form_state) {
}
function apbdop_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $username = $form_state['values']['username'];
        $user = user_load(array('name'=>$username));
        if ($user) {
            user_delete(null, $user->uid);
        }
        $sql = 'DELETE FROM {apbdop} WHERE username=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($username));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/manageuser');
        }
        
    }
}
?>
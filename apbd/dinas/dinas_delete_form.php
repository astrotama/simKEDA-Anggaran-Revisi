<?php

function dinas_delete_form() {
    $id = arg(3);
    if (isset($id)) {
        $sql = 'select id,kodeuk,kodeu,nourut,nama from {ukurusan} where id=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($id));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus dinas',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['id'] = array('#type' => 'value', '#value' => $data->id);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->nama (Kodeu/KodeUK: $data->kodeu $data->kodeuk)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/dinas',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function dinas_delete_form_validate($form, &$form_state) {
}
function dinas_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $id = $form_state['values']['id'];
        $sql = 'DELETE FROM {ukurusan} WHERE id=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($id));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/dinas');
        }
        
    }
}
?>
<?php

function programsasaran_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    drupal_set_title('Target Program');
    
    $kodepro = arg(3);
    $tahun = arg(4);
    $nomor = arg(5);
    
    if (isset($kodepro)) {
		$sql = 'select ktarget,satuan,rtarget from {programsasaran} where kodepro=\'%s\' and tahun=\'%s\' and nomor=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($kodepro, $tahun, $nomor));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Target',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['kodepro'] = array('#type' => 'value', '#value' => $kodepro);
                $form['formdata']['tahun'] = array('#type' => 'value', '#value' => $tahun);
                $form['formdata']['nomor'] = array('#type' => 'value', '#value' => $nomor);
                
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->ktarget (Plafon : $data->rtarget)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/programsasaran/'  . $kodepro . $tahun,
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function programsasaran_delete_form_validate($form, &$form_state) {
}

function programsasaran_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodepro = $form_state['values']['kodepro'];
        $tahun = $form_state['values']['tahun'];
        $nomor = $form_state['values']['nomor'];
        
        $sql = 'DELETE FROM {programsasaran} WHERE kodepro=\'%s\' and tahun=\'%s\' and nomor=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($kodepro, $tahun, $nomor));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/programsasaran/' . $kodepro . $tahun );
        }
        
    }
}
?>

<?php

function setupapp_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');	
    $tahun = arg(3);
    if (isset($tahun)) {
        $sql = 'select tahun, wilayah from {setupapp} where tahun=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($tahun));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus setupapp',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['tahun'] = array('#type' => 'value', '#value' => $data->tahun);
                $form['formdata']['e_tahun']= array(
					'#type'        => 'item', 
					'#title'       => 'Tahun', 
					'#value'		=> $data->tahun, 
				);
				$form['formdata']['e_wilayah']= array(
					'#type'        => 'item', 
					'#title'       => 'Nama Wilayah', 
					'#value'		=> $data->wilayah, 
				);
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/setupapp',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function setupapp_delete_form_validate($form, &$form_state) {
}
function setupapp_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $tahun = $form_state['values']['tahun'];
        $sql = 'DELETE FROM {setupapp} WHERE tahun=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($tahun));
        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/setupapp');
        }
        
    }
}
?>
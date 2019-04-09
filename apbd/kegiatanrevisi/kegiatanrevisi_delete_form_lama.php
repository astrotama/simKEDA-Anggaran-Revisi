<?php

function kegiatanrevisi_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    $id = arg(3);
	//drupal_set_message($id);
    if (isset($id)) {

        $sql = 'select id, tipe, kegiatanlama, kegiatanbaru from {kegiatanrevisi} where id=\'%s\'';
		//$sql = "select id, tipe, kegiatanlama, kegiatanbaru from {kegiatanrevisi} where id=\'%s\'";
        $res = db_query(db_rewrite_sql($sql), array($id));
        if ($res) {
			//drupal_set_message('res');
            $data = db_fetch_object($res);
            if ($data) {
                //drupal_set_message('data');
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Usulan Revisi Kegiatan',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
				
				if ($data->tipe==0) 
					$keterangan = $data->kegiatanlama;
				else
					$keterangan = $data->kegiatanbaru;
				
                $form['formdata']['id'] = array('#type' => 'value', '#value' => $data->id);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$keterangan</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/kegiatanrevisiperubahan',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function kegiatanrevisi_delete_form_validate($form, &$form_state) {
}
function kegiatanrevisi_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $id = $form_state['values']['id'];
        $sql = 'DELETE FROM {kegiatanrevisi} WHERE id=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($id));
        if ($res) {
            $query = sprintf("DELETE FROM {kegiatanrevisi} where id='%s'", db_escape_string($id));
            db_query($query);
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/kegiatanrevisiperubahan');
        }
        
    }
}
?>
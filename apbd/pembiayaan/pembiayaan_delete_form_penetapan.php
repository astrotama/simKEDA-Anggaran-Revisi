<?php

function pembiayaan_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
	
	$kodero = arg(3);
	//drupal_set_message($kodeuk);
	
	$tahun = variable_get('apbdtahun', 0);
    if (isset($kodero)) {
        $sql = 'select tahun,kodero,uraian,jumlah from {anggperda} where tahun=\'%s\' and kodero=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($tahun, $kodero));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Rekening',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
				// '#value' => "<span>$data->kegiatan (Kode: $data->kodekeg)</span>",

                $form['formdata']['tahun'] = array('#type' => 'value', '#value' => $data->tahun);
				$form['formdata']['kodero'] = array('#type' => 'value', '#value' => $data->kodero);
				$form['formdata']['jumlah'] = array('#type' => 'value', '#value' => $data->jumlah);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->uraian (Kode : $data->kodero)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini?",
                                    'apbd/pembiayaan/',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function pembiayaan_delete_form_validate($form, &$form_state) {
}

function pembiayaan_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodero = $form_state['values']['kodero'];
		$tahun = $form_state['values']['tahun'];
		$jumlah = $form_state['values']['jumlah'];
        	
        $sql = 'DELETE FROM {anggperda} where tahun=\'%s\' and kodero=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($tahun, $kodero));
        if ($res) {
			
			//Delete detilnya
			$sql = 'DELETE FROM {anggperdadetil} WHERE tahun=\'%s\' and kodero=\'%s\'';
			$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero));
			
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/pembiayaan/');
        }
        
    }
}
?>
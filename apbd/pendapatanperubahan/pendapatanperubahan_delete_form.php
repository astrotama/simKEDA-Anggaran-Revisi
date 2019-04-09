<?php

function pendapatanperubahan_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
	
    $kodeuk = arg(3);
	$kodero = arg(4);
	//drupal_set_message($kodeuk);
	//drupal_set_message($kodero);
	
	$tahun = variable_get('apbdtahun', 0);
	//drupal_set_message($tahun);
    if (isset($kodero)) {
        $sql = 'select kodeuk,tahun,kodero,uraian,jumlah from {anggperukperubahan} where tahun=\'%s\' and kodeuk=\'%s\' and kodero=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($tahun, $kodeuk, $kodero));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Rekening Perubahan',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
				// '#value' => "<span>$data->kegiatan (Kode: $data->kodekeg)</span>",

                $form['formdata']['tahun'] = array('#type' => 'value', '#value' => $data->tahun);
				$form['formdata']['kodero'] = array('#type' => 'value', '#value' => $data->kodero);
                $form['formdata']['kodeuk'] = array('#type' => 'value', '#value' => $data->kodeuk);
				$form['formdata']['jumlah'] = array('#type' => 'value', '#value' => $data->jumlah);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->uraian (Kode : $data->kodero)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini?",
                                    'apbd/pendapatanperubahan/',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function pendapatanperubahan_delete_form_validate($form, &$form_state) {
}
function pendapatanperubahan_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodero = $form_state['values']['kodero'];
        $kodeuk = $form_state['values']['kodeuk'];
		$tahun = $form_state['values']['tahun'];
		$jumlah = $form_state['values']['jumlah'];
        	
        $sql = 'DELETE FROM {anggperukperubahan} where tahun=\'%s\' and kodeuk=\'%s\' and kodero=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($tahun, $kodeuk, $kodero));
        if ($res) {
			
			//Delete detilnya
			$sql = 'DELETE FROM {anggperukdetilperubahan} WHERE tahun=\'%s\' and kodeuk=\'%s\' and kodero=\'%s\'';
			$res = db_query(db_rewrite_sql($sql), array($tahun, $kodeuk, $kodero));
			
			if ($res) {
				drupal_set_message('Penghapusan X berhasil dilakukan');
				drupal_goto('apbd/pendapatanperubahan/');
			} else 
				drupal_set_message('Penghapusan gagal');
        }
        
    }
}
?>
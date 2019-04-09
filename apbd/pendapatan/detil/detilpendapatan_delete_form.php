<?php

function detilpendapatan_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
		
	$kodeuk=arg(4);
    $kodero = arg(5);
	$iddetil = arg(6);
	
	//drupal_set_message($iddetil);
    if (isset($iddetil)) {
        $sql = 'select kodeuk,kodero,iddetil,uraian from {anggperukdetil} where iddetil=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($iddetil));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Detil Rekening',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
				// '#value' => "<span>$data->kegiatan (Kode: $data->kodeuk)</span>",

                $form['formdata']['kodero'] = array('#type' => 'value', '#value' => $data->kodero);
                $form['formdata']['kodeuk'] = array('#type' => 'value', '#value' => $data->kodeuk);
				$form['formdata']['iddetil'] = array('#type' => 'value', '#value' => $data->iddetil);
				$form['formdata']['total'] = array('#type' => 'value', '#value' => $data->total);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->uraian</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/pendapatan/detil/' . $data->kodeuk . '/' . $data->kodero,
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function detilpendapatan_delete_form_validate($form, &$form_state) {
}
function detilpendapatan_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodero = $form_state['values']['kodero'];
        $kodeuk = $form_state['values']['kodeuk'];
		$iddetil = $form_state['values']['iddetil'];
		$total = $form_state['values']['total'];
        
        $sql = 'DELETE FROM {anggperukdetil} WHERE iddetil=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($iddetil));
        if ($res) {
		
			$sql = sprintf("select sum(total) as jumlahrek from {anggperukdetil} where kodeuk='%s' and kodero='%s'",
				   $kodeuk, $kodero);
			$result = db_query($sql);
			if ($data = db_fetch_object($result)) {		
				$jumlahrek = $data->jumlahrek;
				
				$sql = sprintf("update {anggperuk} set jumlah='%s' where kodeuk='%s' and kodero='%s'",
						db_escape_string($jumlahrek),
						db_escape_string($kodeuk),
						$kodero);		
				$res = db_query($sql);
				
			}
			
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/pendapatan/detil/' . $kodeuk . '/' . $kodero);
        }
        
    }
}
?>
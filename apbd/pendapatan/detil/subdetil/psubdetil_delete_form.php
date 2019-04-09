<?php

function psubdetil_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
	$kodeuk=arg(5);
    $kodero = arg(6);
	$iddetil = arg(7);
	$idsub = arg(8);
	 
	//drupal_set_message(arg(7));
	//drupal_set_message(arg(8));
	//drupal_set_message($kodeuk);
    if (isset($idsub)) {
        $sql = 'select iddetil,idsub,uraian,total from {anggperukdetilsub} where idsub=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($idsub));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Sub Detil Rekening',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
				// '#value' => "<span>$data->kegiatan (Kode: $data->kodeuk)</span>",

                $form['formdata']['kodero'] = array('#type' => 'value', '#value' => $kodero);
                $form['formdata']['kodeuk'] = array('#type' => 'value', '#value' => $kodeuk);
				$form['formdata']['iddetil'] = array('#type' => 'value', '#value' => $data->iddetil);
				$form['formdata']['idsub'] = array('#type' => 'value', '#value' => $data->idsub);
				$form['formdata']['total'] = array('#type' => 'value', '#value' => $data->total);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->uraian, sejumlah ("  . apbd_fn($data->total) .")</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/pendapatan/detil/subdetil/' . $kodeuk . '/' . $kodero . '/' . $iddetil,
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function psubdetil_delete_form_validate($form, &$form_state) {
}
function psubdetil_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodero = $form_state['values']['kodero'];
        $kodeuk = $form_state['values']['kodeuk'];
		$iddetil = $form_state['values']['iddetil'];
		$idsub = $form_state['values']['idsub'];
		$total = $form_state['values']['total'];
        
        $sql = 'DELETE FROM {anggperukdetilsub} WHERE idsub=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($idsub));
        if ($res) {
		
			$sql = sprintf("select sum(total) as jumlahrek from {anggperukdetilsub} where iddetil='%s'",
				   $iddetil);
			$result = db_query($sql);
			if ($data = db_fetch_object($result)) {		
				$jumlahrek = $data->jumlahrek;
				
				$sql = sprintf("update {anggperukdetil}} set total='%s' where iddetil='%s'",
						db_escape_string($jumlahrek),
						$iddetil);		
				$res = db_query($sql);
				
			}


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
            drupal_goto('apbd/pendapatan/detil/subdetil/' . $kodeuk . '/' . $kodero . '/' . $iddetil);
        }
        
    }
}
?>
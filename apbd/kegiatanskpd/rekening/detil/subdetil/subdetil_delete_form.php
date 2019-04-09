<?php

function subdetil_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
	$kodekeg=arg(6);
    $kodero = arg(7);
	$iddetil = arg(8);
	$idsub = arg(9);
	
	//drupal_set_message($iddetil);
	//drupal_set_message($idsub);
    if (isset($iddetil)) {
        $sql = 'select iddetil,idsub,uraian,total from {anggperkegdetilsub} where idsub=\'%s\'';
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
				// '#value' => "<span>$data->kegiatan (Kode: $data->kodekeg)</span>",

                $form['formdata']['kodero'] = array('#type' => 'value', '#value' => $kodero);
                $form['formdata']['kodekeg'] = array('#type' => 'value', '#value' => $kodekeg);
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
                                    'apbd/kegiatanskpd/rekening/detil/subdetil/' . $kodekeg . '/' . $kodero . '/' . $iddetil,
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function subdetil_delete_form_validate($form, &$form_state) {
}
function subdetil_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodero = $form_state['values']['kodero'];
        $kodekeg = $form_state['values']['kodekeg'];
		$iddetil = $form_state['values']['iddetil'];
		$idsub = $form_state['values']['idsub'];
		$total = $form_state['values']['total'];
        
        $sql = 'DELETE FROM {anggperkegdetilsub} WHERE idsub=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($idsub));
        if ($res) {
		
			$sql = sprintf("select sum(total) as jumlahrek from {anggperkegdetilsub} where iddetil='%s'",
				   $iddetil);
			$result = db_query($sql);
			if ($data = db_fetch_object($result)) {		
				$jumlahrek = $data->jumlahrek;
				
				$sql = sprintf("update {anggperkegdetil}} set total='%s' where iddetil='%s'",
						db_escape_string($jumlahrek),
						$iddetil);		
				$res = db_query($sql);
				
			}


			$sql = sprintf("select sum(total) as jumlahrek from {anggperkegdetil} where kodekeg='%s' and kodero='%s'",
				   $kodekeg, $kodero);
			$result = db_query($sql);
			if ($data = db_fetch_object($result)) {		
				$jumlahrek = $data->jumlahrek;
				
				$sql = sprintf("update {anggperkeg} set jumlah='%s' where kodekeg='%s' and kodero='%s'",
						db_escape_string($jumlahrek),
						db_escape_string($kodekeg),
						$kodero);		
				$res = db_query($sql);
				
			}

			//UPDATE JUMLAH KEGIATAN
			$sql = sprintf("select sum(jumlah) as jumlahsub from {anggperkeg} where kodekeg='%s'", $kodekeg);
			$result = db_query($sql);
			if ($data = db_fetch_object($result)) {		
				$jumlahsub = $data->jumlahsub;
				
				$sql = sprintf("update {kegiatanskpd} set total='%s' where kodekeg='%s'", db_escape_string($jumlahsub), $kodekeg);		
				$res = db_query($sql);
				
			}
			
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbd/kegiatanskpd/rekening/detil/subdetil/' . $kodekeg . '/' . $kodero . '/' . $iddetil);
        }
        
    }
}
?>
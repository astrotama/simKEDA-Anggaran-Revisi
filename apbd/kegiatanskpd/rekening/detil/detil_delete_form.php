<?php

function detil_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
	$kodekeg=arg(5);
    $kodero = arg(6);
	$iddetil = arg(7);
	
	//drupal_set_message($iddetil);
    if (isset($iddetil)) {
        $sql = 'select kodekeg,kodero,iddetil,uraian,total from {anggperkegdetil} where iddetil=\'%s\'';
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
				// '#value' => "<span>$data->kegiatan (Kode: $data->kodekeg)</span>",

                $form['formdata']['kodero'] = array('#type' => 'value', '#value' => $data->kodero);
                $form['formdata']['kodekeg'] = array('#type' => 'value', '#value' => $data->kodekeg);
				$form['formdata']['iddetil'] = array('#type' => 'value', '#value' => $data->iddetil);
				$form['formdata']['total'] = array('#type' => 'value', '#value' => $data->total);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->uraian, sejumlah ("  . apbd_fn($data->total) .")</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/kegiatanskpd/rekening/detil/' . $data->kodekeg . '/' . $data->kodero,
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function detil_delete_form_validate($form, &$form_state) {
}
function detil_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodero = $form_state['values']['kodero'];
        $kodekeg = $form_state['values']['kodekeg'];
		$iddetil = $form_state['values']['iddetil'];
		$total = $form_state['values']['total'];
        
        $sql = 'DELETE FROM {anggperkegdetil} WHERE iddetil=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($iddetil));
        if ($res) {
		
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
            drupal_goto('apbd/kegiatanskpd/rekening/detil/' . $kodekeg . '/' . $kodero);
        }
        
    }
}
?>
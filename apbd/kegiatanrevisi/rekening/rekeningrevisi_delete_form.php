<?php

function rekeningrevisi_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    $kodekeg = arg(4);
	$kodero = arg(5);
	$id = arg(6);

	if ($id=='0')
		$ret_page = 'apbd/kegiatanrevisiperubahan/rekening/' . $kodekeg;
	else
		$ret_page = 'apbd/kegiatanrevisi/rekening/' . $kodekeg;
	
    if (isset($kodero)) {
        $sql = 'select kodekeg,kodero,uraian,jumlah from {anggperkegrevisi} where kodekeg=\'%s\' and kodero=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($kodekeg, $kodero));
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

                $form['formdata']['kodero'] = array('#type' => 'value', '#value' => $data->kodero);
                $form['formdata']['kodekeg'] = array('#type' => 'value', '#value' => $data->kodekeg);
				$form['formdata']['jumlah'] = array('#type' => 'value', '#value' => $data->jumlah);
                $form['formdata']['id'] = array('#type' => 'value', '#value' => $id);
				
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$data->uraian (Kode : $data->kodero)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus rekening berikut ini?",
                                    $ret_page,
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
		
    } else {
		
        $sql = 'select kodekeg,kegiatan from {kegiatanrevisi} where kodekeg=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($kodekeg));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Semua Rekening',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
				// '#value' => "<span>$data->kegiatan (Kode: $data->kodekeg)</span>",

                $form['formdata']['kodero'] = array('#type' => 'value', '#value' => 'xx');
                $form['formdata']['kodekeg'] = array('#type' => 'value', '#value' => $data->kodekeg);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>Semua rekening(Kegiatan : $data->kegiatan)</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus semua rekening?",
                                    $ret_page,
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }		
	}
}
function rekeningrevisi_delete_form_validate($form, &$form_state) {
}
function rekeningrevisi_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodero = $form_state['values']['kodero'];
        $kodekeg = $form_state['values']['kodekeg'];
		$jumlah = $form_state['values']['jumlah'];
        $id = $form_state['values']['id'];
		
		if ($kodero=='xx') {
			$sql = 'DELETE FROM {anggperkegrevisi} WHERE kodekeg=\'%s\'';
			$res = db_query(db_rewrite_sql($sql), array($kodekeg));
			if ($res) {
				
				//nolkan anggarannya;
				$sql = 'UPDATE {kegiatanrevisi} SET total=0 WHERE kodekeg=\'%s\'';
				$res = db_query(db_rewrite_sql($sql), array($kodekeg));
				
				//Delete detilnya
				if ($res) {
					$sql = 'DELETE FROM {anggperkegdetil} WHERE kodekeg=\'%s\'';
					$res = db_query(db_rewrite_sql($sql), array($kodekeg));
				}
				if ($res) 
					drupal_set_message('Penghapusan berhasil dilakukan');
				
			}			

		} else {
			$sql = 'DELETE FROM {anggperkegrevisi} WHERE kodekeg=\'%s\' and kodero=\'%s\'';
			$res = db_query(db_rewrite_sql($sql), array($kodekeg, $kodero));
			if ($res) {
				
				//Kurangi anggarannya
				$totalbaru = 0;
				$sql = 'select sum(jumlah) jumlahx from {anggperkegrevisi} where kodekeg=\'%s\'';
				$res = db_query(db_rewrite_sql($sql), array($kodekeg));
				if ($res) {
					$data = db_fetch_object($res);
					if ($data) {
						$totalbaru = $data->jumlahx;
					}
				}
				
				//drupal_set_message($totalbaru);
				
				$sql = 'UPDATE {kegiatanrevisi} SET total=\'%s\' WHERE kodekeg=\'%s\'';
				$res = db_query(db_rewrite_sql($sql), array($totalbaru, $kodekeg));
				
				//Delete detilnya
				if ($res) {
					$sql = 'DELETE FROM {anggperkegdetil} WHERE kodekeg=\'%s\' and kodero=\'%s\'';
					$res = db_query(db_rewrite_sql($sql), array($kodekeg, $kodero));
				}
				if ($res) 
					drupal_set_message('Penghapusan berhasil dilakukan');
				
			}
        }
		if ($id=='0')
			drupal_goto('apbd/kegiatanrevisiperubahan/rekening/' . $kodekeg);
		else
			drupal_goto('apbd/kegiatanrevisi/rekening/' . $kodekeg);
    }
}
?>
<?php

function kegiatanrevisi_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    $id = arg(3);
	////drupal_set_message($id);
    if (isset($id)) {

        $sql = 'select k.id,keg.kodekeg,keg.kegiatan from {kegiatanrevisiperubahan} k inner join {kegiatanrevisi} keg on k.kodekeg=keg.kodekeg where k.id=\'%s\'';
		//$sql = "select id, tipe, kegiatanlama, kegiatanbaru from {kegiatanrevisi} where id=\'%s\'";
        $res = db_query(db_rewrite_sql($sql), array($id));
        if ($res) {
			////drupal_set_message('res');
            $data = db_fetch_object($res);
            
			if ($data) {
                ////drupal_set_message('data');
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Usulan Revisi Kegiatan',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
				
				$keterangan = $data->kegiatan;
				
                $form['formdata']['id'] = array('#type' => 'value', '#value' => $data->id);
                $form['formdata']['kodekeg'] = array('#type' => 'value', '#value' => $data->kodekeg);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => "<span>$keterangan</span>",
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbd/kegiatanrevisi',
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
		$kodekeg = $form_state['values']['kodekeg'];
		
        $sql = 'delete from {kegiatanrevisiperubahan} WHERE id=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($id));
        if ($res) {
			$emsg= '0';
			
			$ada = false;
			$sql = 'select id from {kegiatanrevisiperubahan} WHERE kodekeg=\'%s\' ';
			$res = db_query(db_rewrite_sql($sql), array($kodekeg));
			if ($res) {
				if ($data = db_fetch_object($res)) {
					$ada = true;
				}
			}
			
			//DELETE EXISTING
			/*
			if ($ada==false) {
				$query = sprintf("delete from {kegiatanrevisi} where kodekeg='%s'", db_escape_string($kodekeg));
				$res = db_query($query);
				if ($res == false) $emsg .= '1';
				
				$query = sprintf("delete from {anggperkegrevisi} where kodekeg='%s'", db_escape_string($kodekeg));
				$res = db_query($query);
				if ($res == false) $emsg .= '2';

				$res = $query = sprintf("delete from {anggperkegdetilrevisi} where kodekeg='%s'", db_escape_string($kodekeg));
				$res = db_query($query);
				if ($res == false) $emsg .= '3';
				
				//REINSERT
				//R1
				
				$query = sprintf("insert into {kegiatanrevisi} select * from {kegiatanskpd} where kodekeg='%s'", db_escape_string($kodekeg));
				$res = db_query($query);
				if ($res == false) $emsg .= '4';
				
				$query = sprintf("insert into {anggperkegrevisi} select * from {anggperkeg} where kodekeg='%s'", db_escape_string($kodekeg));
				$res = db_query($query);
				if ($res == false) $emsg .= '5';

				$query = sprintf("insert into {anggperkegdetilrevisi} select * from {anggperkegdetil} where kodekeg='%s'", db_escape_string($kodekeg));
				$res = db_query($query);
				}
				
				*/
				//R2
				/*
				$query = sprintf("insert into {kegiatanrevisi} (
					kodekeg,nomorkeg,jenis,tahun,kodepro,kodeuk,kegiatan,lokasi,totalsebelum,totalsesudah,
					total,plafon,targetsesudah,kodesuk,sumberdana1,sumberdana2,sumberdana1rp,sumberdana2rp,
					periode,programsasaran,programtarget,masukansasaran,masukantarget,keluaransasaran,keluarantarget,
					hasilsasaran,hasiltarget,waktupelaksanaan,latarbelakang,kelompoksasaran,tw1,tw2,tw3,tw4,
					adminok,inaktif,isgaji,isppkd,plafonlama,dispensasi,edit)
					select kodekeg,nomorkeg,jenis,tahun,kodepro,kodeuk,kegiatan,lokasi,totalsebelum,totalsesudah,
					totalp,plafon,targetsesudah,kodesuk,sumberdana1,sumberdana2,sumberdana1rp,sumberdana2rp,
					periode,programsasaran,programtarget,masukansasaran,masukantarget,keluaransasaran,keluarantarget,
					hasilsasaran,hasiltarget,waktupelaksanaan,latarbelakang,kelompoksasaran,tw1p,tw2p,tw3p,tw4p,
					adminok,inaktif,isgaji,isppkd,plafonlama,dispensasi,edit
					from {kegiatanperubahan where kodekeg='%s'", db_escape_string($kodekeg));
				$res = db_query($query);
				if ($res == false) $emsg .= '4';
				
				$query = sprintf("INSERT INTO {anggperkegrevisi} (kodero,kodekeg,uraian,jumlah,jumlahsesudah,jumlahsebelum)
					SELECT kodero,kodekeg,uraian,jumlahp,jumlahsesudah,jumlahsebelum FROM {anggperkegperubahan} where kodekeg='%s'", db_escape_string($kodekeg));
				$res = db_query($query);
				if ($res == false) $emsg .= '5';

				$query = sprintf("INSERT INTO {anggperkegdetilrevisi} (iddetil,kodero,kodekeg,pengelompokan,uraian,unitjumlah,
					unitsatuan,volumjumlah,volumsatuan,harga,total,nourut)
					SELECT iddetil,kodero,kodekeg,pengelompokan,uraian,unitjumlah,unitsatuan,volumjumlah,
					volumsatuan,harga,total,nourut FROM {anggperkegdetilperubahan} where kodekeg='%s'", db_escape_string($kodekeg));
				$res = db_query($query);
				if ($res == false) $emsg .= '6';

				$query = sprintf("INSERT INTO {anggperkegdetilsubrevisi} (idsub,iddetil,uraian,unitjumlah,
					unitsatuan,volumjumlah,volumsatuan,harga,total,nourut)
					SELECT idsub,iddetil,uraian,unitjumlah,
					unitsatuan,volumjumlah,volumsatuan,harga,total,nourut FROM {anggperkegdetilsubperubahan} 
					where iddetil in (select iddetil from {anggperkegdetilperubahan} where kodekeg='%s')", db_escape_string($kodekeg));
				//drupal_set_message($query);
				$res = db_query($query);
				if ($res == false) $emsg .= '7';
				*/
				
			//}
			
			
            drupal_set_message($emsg . ' - Penghapusan berhasil dilakukan');
            drupal_goto('apbd/kegiatanrevisi');
        }
        
    }
}
?>
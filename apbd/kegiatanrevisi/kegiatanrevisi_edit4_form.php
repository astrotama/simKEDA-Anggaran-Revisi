<?php
function kegiatanrevisi_edit4_form() {
	drupal_add_css('files/css/kegiatancam.css');
	
	drupal_set_title('Usulan Revisi - Langkah #4, Isi Kelengkapan');
	
	$id = arg(3);
	$kodeuk = arg(4);
	
	$jenisrevisi = arg(5);
	$subjenisrevisi = arg(6);
	
	if (($jenisrevisi=='3') and ($subjenisrevisi==2)) {
		$noback = true;
		$geserblokir = '0';
		$geserrincian = '0';
		$geserobyek = '0';
		
		$lokasi = '0';
		$sumberdana = '0';
		$kinerja = '0';
		$sasaran = '0';
		$detiluraian = '0';
		$rab = '0';
		$triwulan = '0';
		$lainnya = '0';
		
		$kodekeg = arg(7);
		$e_kodekeg = $kodekeg;
		
	} else {
		$noback = false;
		$geserblokir = arg(7);
		$geserrincian = arg(8);
		$geserobyek = arg(9);
		
		$lokasi = arg(10);
		$sumberdana = arg(11);
		$kinerja = arg(12);
		$sasaran = arg(13);
		$detiluraian = arg(14);
		$rab = arg(15);
		$triwulan = arg(16);
		$lainnya = arg(17);
		$kodekeg = arg(18);
		$e_kodekeg = $kodekeg;
	}
	if (isset($id) and ($id != '0')) {
        $sql = 'select id,alasan1, jenisrevisi, subjenisrevisi, alasan2, alasan3, nosurat, tglsurat,dokumen,kodekeg from {kegiatanrevisiperubahan} where id=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($id));
		if ($res) {
			if ($data = db_fetch_object($res)) {
				$id = $data->id;
				$alasan1 = $data->alasan1;
				$alasan2 = $data->alasan2;
				$alasan3 = $data->alasan3;
				$nosurat = $data->nosurat;
				$tglsurat = $data->tglsurat;
				$dokumen = $data->dokumen;
				$e_kodekeg = $data->kodekeg;
				
				if (isSuperuser()==false) {
					$jenisrevisi = $data->jenisrevisi;
					$subjenisrevisi = $data->subjenisrevisi;
					$kodekeg = $data->kodekeg;
				}
			}
		} else
			$id = '0';
	} else 
		$id = '0';
	
	$form['id']= array(
		'#type' => 'value', 
		'#value' => $id, // changed
	);
	$form['kodeuk']= array(
		'#type' => 'value', 
		'#value' => $kodeuk, // changed
	);

	$form['jenisrevisi']= array(
		'#type' => 'value', 
		'#value' => $jenisrevisi, // changed
	);
	$form['subjenisrevisi']= array(
		'#type' => 'value', 
		'#value' => $subjenisrevisi, // changed
	);
	$form['geserrincian']= array(
		'#type' => 'value', 
		'#value' => $geserrincian, // changed
	);
	$form['geserblokir']= array(
		'#type' => 'value', 
		'#value' => $geserblokir, // changed
	);
	$form['geserobyek']= array(
		'#type' => 'value', 
		'#value' => $geserobyek, // changed
	);	
	$form['lokasi']= array(
		'#type' => 'value', 
		'#value' => $lokasi, // changed
	);
	$form['sumberdana']= array(
		'#type' => 'value', 
		'#value' => $sumberdana, // changed
	);
	$form['kinerja']= array(
		'#type' => 'hidden', 
		'#default_value' => $kinerja, // changed
	);
	$form['sasaran']= array(
		'#type' => 'value', 
		'#value' => $sasaran, // changed
	);
	$form['detiluraian']= array(
		'#type' => 'value', 
		'#value' => $detiluraian, // changed
	);
	$form['rab']= array(
		'#type' => 'value', 
		'#value' => $rab, // changed
	);
	$form['triwulan']= array(
		'#type' => 'hidden', 
		'#default_value' => $triwulan, // changed
	);
	$form['lainnya']= array(
		'#type' => 'value', 
		'#value' => $lainnya, // changed
	);	
	/*
	if (isSuperuser()) {
		if ($jenisrevisi==1) {				//GESER
			
		} else if ($jenisrevisi==2) {		//ADMIN
		
		} else if ($jenisrevisi==3) {		//TRANSFER
		
		}
	} 
	*/
	
	drupal_set_message('id : ' . $id);
	drupal_set_message('kodekeg : ' . $kodekeg);
	drupal_set_message('kodekeg lama : ' . $e_kodekeg);
	$form['kodekeg']= array(
		'#type' => 'value', 
		'#value' => $kodekeg, // changed
	);
	$form['e_kodekeg']= array(
		'#type' => 'value', 
		'#value' => $e_kodekeg, // changed
	);
	
	$form['alasan1'] = array(
		'#type' => 'textfield',
		'#title' => 'Alasan Revisi #1',
		'#maxlength'    => 255, 
		'#size'         => 120, 
		'#default_value' => $alasan1,
		'#required' => true,
	);

	$form['alasan2'] = array(
		'#type' => 'textfield',
		'#title' => 'Alasan Revisi #2',
		'#maxlength'    => 255, 
		'#size'         => 120, 
		'#default_value' => $alasan2,
	);

	$form['alasan3'] = array(
		'#type' => 'textfield',
		'#title' => 'Alasan Revisi #3',
		'#maxlength'    => 255, 
		'#size'         => 120, 
		'#default_value' => $alasan3,
	);

	$form['dokumen'] = array(
		'#type' => 'textfield',
		'#title' => 'Dokumen Pendukung',
		'#maxlength'    => 255, 
		'#size'         => 120, 
		'#default_value' => $dokumen,
	);

	$form['nosurat'] = array(
		'#type' => 'textfield',
		'#title' => 'No. Surat',
		'#maxlength'    => 255, 
		'#size'         => 60, 
		'#default_value' => $nosurat,
		'#required' => true,
	);

	$form['tglsurat'] = array(
		'#type' => 'textfield',
		'#title' => 'Tgl. Surat',
		'#maxlength'    => 255, 
		'#size'         => 60, 
		'#default_value' => $tglsurat,
		'#required' => true,
	);
	
	if ($noback==false) {
		$form['kembali'] = array (
			'#type' => 'submit',
			'#value' => '< Kembali',
			//'#weight' => 6,
		);
	}
	
	$form['lanjut'] = array(
		'#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Batal</a>",
		'#value' => 'Simpan',
	);
	return $form;
	
}

function kegiatanrevisi_edit4_form_validate($form, &$form_state) {
}
 
function kegiatanrevisi_edit4_form_submit($form, &$form_state) {
	
	$jenisrevisi = $form_state['values']['jenisrevisi'];
	$id = $form_state['values']['id'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodekeg = $form_state['values']['kodekeg'];
	$e_kodekeg = $form_state['values']['e_kodekeg'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['kembali']) {
		$form_state['redirect'] = 'apbd/kegiatanrevisi/edit2/' . $id . '/' . $jenisrevisi ;
	
	} else {
		$subjenisrevisi = 0;
		$geserblokir = 0;
		$geserrincian = 0;
		$geserobyek = 0;
		$lokasi = 0;
		$sumberdana = 0;
		$kinerja = 0;
		$sasaran = 0;
		$detiluraian = 0;
		$rab = 0;
		$triwulan = 0;
		$lainnya = 0;
				
		switch($jenisrevisi) {
			case '1':
				$geserblokir =$form_state['values']['geserblokir'];
				$geserrincian =$form_state['values']['geserrincian'];
				$geserobyek =$form_state['values']['geserobyek'];
				break;

			case '2':
				$lokasi = $form_state['values']['lokasi'];
				$sumberdana = $form_state['values']['sumberdana'];
				$kinerja = $form_state['values']['kinerja'];
				$sasaran = $form_state['values']['sasaran'];
				$detiluraian = $form_state['values']['detiluraian'];
				$rab = $form_state['values']['rab'];
				$triwulan = $form_state['values']['triwulan'];
				$lainnya = $form_state['values']['lainnya'];
				break;
				
			case '3':
				$subjenisrevisi = $form_state['values']['subjenisrevisi'];
				break;
			
		}

		$alasan1 = $form_state['values']['alasan1'];
		$alasan2 = $form_state['values']['alasan2'];
		$alasan3 = $form_state['values']['alasan3'];
		 
		$nosurat = $form_state['values']['nosurat'];
		$tglsurat = $form_state['values']['tglsurat'];	
		
		$dokumen = $form_state['values']['dokumen'];
		
		$tahun = variable_get('apbdtahun', 0);
  
		if ($id=='0') {
			$sql =  sprintf("insert into {kegiatanrevisiperubahan} (jenisrevisi, subjenisrevisi, tahun, kodeuk, kodekeg, geserblokir, geserrincian, geserobyek, lokasi, sumberdana, kinerja, sasaran, detiluraian, rab, triwulan, lainnya, alasan1, alasan2, alasan3, nosurat, tglsurat, dokumen) values('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $jenisrevisi, $subjenisrevisi, $tahun, $kodeuk, $kodekeg, $geserblokir, $geserrincian, $geserobyek, $lokasi, db_escape_string($sumberdana), db_escape_string($kinerja), db_escape_string($sasaran), db_escape_string($detiluraian), db_escape_string($rab), db_escape_string($triwulan), db_escape_string($lainnya), db_escape_string($alasan1), db_escape_string($alasan2), db_escape_string($alasan3), db_escape_string($nosurat), db_escape_string($tglsurat), db_escape_string($dokumen)); 
			
			
		} else {
			if (isSuperuser())
				$sql = sprintf("update {kegiatanrevisiperubahan} set jenisrevisi='%s', subjenisrevisi='%s', geserblokir='%s', geserrincian='%s', geserobyek='%s', lokasi='%s', sumberdana='%s', kinerja='%s', sasaran='%s', detiluraian='%s', rab='%s', triwulan='%s', lainnya='%s', alasan1='%s', alasan2='%s', alasan3='%s', nosurat='%s', tglsurat='%s', dokumen='%s' where id='%s'", db_escape_string($jenisrevisi), db_escape_string($subjenisrevisi), db_escape_string($geserblokir), db_escape_string($geserrincian), db_escape_string($geserobyek), db_escape_string($lokasi), db_escape_string($sumberdana), db_escape_string($kinerja), db_escape_string($sasaran), db_escape_string($detiluraian), db_escape_string($rab), db_escape_string($triwulan), db_escape_string($lainnya), db_escape_string($alasan1), db_escape_string($alasan2), db_escape_string($alasan3), db_escape_string($nosurat), db_escape_string($tglsurat), db_escape_string($dokumen), db_escape_string($id));
			else
				$sql = sprintf("update {kegiatanrevisiperubahan} set alasan1='%s', alasan2='%s', alasan3='%s', nosurat='%s', tglsurat='%s', dokumen='%s' where id='%s'",  db_escape_string($alasan1), db_escape_string($alasan2), db_escape_string($alasan3), db_escape_string($nosurat), db_escape_string($tglsurat), db_escape_string($dokumen), db_escape_string($id));
		}
		//drupal_set_message($sql);
		$res = db_query($sql);
		
		//cek exixstig	
		$ada = false;
		$query = sprintf("select count(*) as ada from {kegiatanrevisi} where kodekeg='%s'", db_escape_string($kodekeg));
		$res = db_query($query);
		if ($res) {
			if ($data = db_fetch_object($result)) {
				$ada = ($data->ada>0);
			}
		}
		
		//if (($e_kodekeg!='') and ($e_kodekeg != $kodekeg)) {
		if ($ada==false) {	
			//DELETE EXISTING
			/*
            $query = sprintf("delete from {kegiatanrevisi} where kodekeg='%s'", db_escape_string($e_kodekeg));
            $res = db_query($query);
			if ($res == false) $emsg .= '1';
			
            $query = sprintf("delete from {anggperkegrevisi} where kodekeg='%s'", db_escape_string($e_kodekeg));
            $res = db_query($query);
			if ($res == false) $emsg .= '2';

            $res = $query = sprintf("delete from {anggperkegdetilsubrevisi} where iddetil in (select iddetil from anggperkegdetilrevisi kodekeg='%s')", db_escape_string($e_kodekeg));
            $res = db_query($query);
			if ($res == false) $emsg .= '3';

            $res = $query = sprintf("delete from {anggperkegdetilrevisi} where kodekeg='%s'", db_escape_string($e_kodekeg));
            $res = db_query($query);
			if ($res == false) $emsg .= '4';
			*/
			
			//REINSERT
            //$query = sprintf("insert into {kegiatanrevisi} (kodekeg, nomorkeg, jenis, tahun, kodepro, kodeuk, kegiatan, lokasi, totalsebelum, totalsesudah, total, plafon, targetsesudah, kodesuk, sumberdana1, sumberdana2, sumberdana1rp, sumberdana2rp, periode, programsasaran, programtarget, masukansasaran, masukantarget, keluaransasaran, keluarantarget, hasilsasaran, hasiltarget, waktupelaksanaan, latarbelakang, kelompoksasaran, tw1, tw2, tw3, tw4, adminok, inaktif, isgaji, isppkd, plafonlama, dispensasi, edit, plafonpenetapan, totalpenetapan) select kodekeg, nomorkeg, jenis, tahun, kodepro, kodeuk, kegiatan, lokasi, totalsebelum, totalsesudah, totalp total, plafon, targetsesudah, kodesuk, sumberdana1, sumberdana2, sumberdana1rp, sumberdana2rp, periode, programsasaran, programtarget, masukansasaran, masukantarget, keluaransasaran, keluarantarget, hasilsasaran, hasiltarget, waktupelaksanaan, latarbelakang, kelompoksasaran, tw1p, tw2p, tw3p, tw4p, adminok, inaktif, isgaji, isppkd, plafonlama, dispensasi, edit, plafonlama plafonpenetapan, total totalpenetapan from {kegiatanperubahan} where kodekeg='%s'", db_escape_string($e_kodekeg));
			
			/*
            $query = sprintf("insert into {kegiatanrevisi} (kodekeg, nomorkeg, jenis, tahun, kodepro, kodeuk, kegiatan, lokasi, totalsebelum, totalsesudah, total, plafon, targetsesudah, kodesuk, sumberdana1, sumberdana2, sumberdana1rp, sumberdana2rp, periode, programsasaran, programtarget, masukansasaran, masukantarget, keluaransasaran, keluarantarget, hasilsasaran, hasiltarget, waktupelaksanaan, latarbelakang, kelompoksasaran, tw1, tw2, tw3, tw4, adminok, inaktif, isgaji, isppkd, plafonlama, dispensasi, edit, plafonpenetapan, totalpenetapan) select kodekeg, nomorkeg, jenis, tahun, kodepro, kodeuk, kegiatan, lokasi, totalsebelum, totalsesudah, total, plafon, targetsesudah, kodesuk, sumberdana1, sumberdana2, sumberdana1rp, sumberdana2rp, periode, programsasaran, programtarget, masukansasaran, masukantarget, keluaransasaran, keluarantarget, hasilsasaran, hasiltarget, waktupelaksanaan, latarbelakang, kelompoksasaran, tw1, tw2, tw3, tw4, adminok, inaktif, isgaji, isppkd, plafonlama, dispensasi, edit, plafon plafonpenetapan, total totalpenetapan from {kegiatanskpd} where kodekeg='%s'", db_escape_string($kodekeg));
			 
			////drupal_set_message($query);
			
            $res = db_query($query);
			if ($res == false) $emsg .= '5';
			
            //$query = sprintf("insert into {anggperkegrevisi} (kodero, kodekeg, uraian, jumlah, jumlahsesudah, jumlahsebelum) select kodero, kodekeg, uraian, jumlahp jumlah, jumlahsesudah, jumlahsebelum from {anggperkegperubahan} where kodekeg='%s'", db_escape_string($kodekeg));
            $query = sprintf("insert into {anggperkegrevisi} (kodero, kodekeg, uraian, jumlah, jumlahsesudah, jumlahsebelum) select kodero, kodekeg, uraian, jumlah, jumlahsesudah, jumlahsebelum from {anggperkeg} where kodekeg='%s'", db_escape_string($kodekeg));
			//drupal_set_message($query);
            $res = db_query($query);
			if ($res == false) $emsg .= '6';

            //$query = sprintf("insert into {anggperkegdetilrevisi} (iddetil, kodero, kodekeg, pengelompokan, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, nourut) select iddetil, kodero, kodekeg, pengelompokan, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, nourut from {anggperkegdetilperubahan} where kodekeg='%s'", db_escape_string($kodekeg));
            $query = sprintf("insert into {anggperkegdetilrevisi} (iddetil, kodero, kodekeg, pengelompokan, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, nourut) select iddetil, kodero, kodekeg, pengelompokan, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, nourut from {anggperkegdetil} where kodekeg='%s'", db_escape_string($kodekeg));
			//drupal_set_message($query);
            $res = db_query($query);
			if ($res == false) $emsg .= '7';		
			
            //$query = sprintf("insert into {anggperkegdetilsubrevisi} (idsub, iddetil, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, nourut) select idsub, iddetil, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, nourut from {anggperkegdetilsubperubahan} where iddetil in (select iddetil from anggperkegdetilperubahan kodekeg='%s')", db_escape_string($kodekeg));
			
			$query = sprintf("insert into {anggperkegdetilsubrevisi} (idsub, iddetil, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, nourut) select idsub, iddetil, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, nourut from {anggperkegdetilsub} where iddetil in (select iddetil from anggperkegdetilsub where kodekeg='%s')", db_escape_string($kodekeg));
			
			//drupal_set_message($query);
			
            $res = db_query($query);
			if ($res == false) $emsg .= '8';		
			*/
		}
		
		
		drupal_set_message($emsg);
		
		//UPDATE ALASAN PERUBAHAN
		$sql = sprintf("update {kegiatanrevisi} set latarbelakang='%s' where kodekeg='%s'", db_escape_string($alasan1), db_escape_string($kodekeg));
		//drupal_set_message($sql);
		$res = db_query($sql);
		if ($res == false) $emsg .= '7';		
  
		if ($res) 
			drupal_set_message('Penyimpanan data berhasil dilakukan');
		else
			drupal_set_message('Penyimpanan data tidak berhasil dilakukan');	

		drupal_goto('apbd/kegiatanrevisi');
		
		
	}
}

?>
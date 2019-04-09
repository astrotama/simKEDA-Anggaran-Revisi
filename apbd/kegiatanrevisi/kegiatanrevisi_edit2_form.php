<?php
function kegiatanrevisi_edit2_form() {
	drupal_add_css('files/css/kegiatancam.css');
	
	drupal_set_title('Usulan Revisi - Langkah #2, Pilih Kategori');
	
	$id = arg(3);
	$kodeuk = arg(5);
	$subjenisrevisi = '1';

	if (isset($id) and ($id != '0')) {
        $sql = 'select kp.id, kp.kodeuk, kp.jenisrevisi, kp.subjenisrevisi, kp.geserblokir, kp.geserrincian, kp.geserobyek, kp.lokasi, kp.sumberdana, kp.kinerja, kp.sasaran, kp.detiluraian, kp.rab, kp.triwulan, kp.lainnya, kr.kegiatan, kr.kodekeg from {kegiatanrevisiperubahan} kp left join {kegiatanrevisi} kr on kp.kodekeg=kr.kodekeg where id=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($id));
		if ($res) {
			if ($data = db_fetch_object($res)) {
				$id = $data->id;
				$kodeuk = $data->kodeuk;
				$jenisrevisi = $data->jenisrevisi;
				$subjenisrevisi = $data->subjenisrevisi;
				$geserblokir = $data->geserblokir;
				$geserrincian = $data->geserrincian;
				$geserobyek = $data->geserobyek;
				$lokasi = $data->lokasi;
				$sumberdana = $data->sumberdana;
				$kinerja = $data->kinerja;
				$sasaran = $data->sasaran;
				$detiluraian = $data->detiluraian;
				$rab = $data->rab;
				$triwulan = $data->triwulan;
				$lainnya = $data->lainnya;	

				$kodekeg = 	$data->kodekeg;
				$labelkegiatan = ' -> [' . $data->kegiatan . ']';
			}
		} else
			$id = 0;
	} else 
		$id = 0;
	$jenisrevisi = arg(4);

	
	$form['id']= array(
		'#type' => 'value', 
		'#value' => $id, // changed
	);
	$form['kodekeg']= array(
		'#type' => 'value', 
		'#value' => $kodekeg, // changed
	);
	$form['jenisrevisi']= array(
		'#type' => 'value', 
		'#value' => $jenisrevisi, // changed
	);
	
	if (isSuperuser()) {
		if ($id==0) {
			$pquery = "select kodeuk, namasingkat from {unitkerja} where aktif=1 order by namauk" ;
			$pres = db_query($pquery);
			$skpd = array();
			//$dinas[''] = '--- pilih dinas teknis---';
			while ($data = db_fetch_object($pres)) {
				$skpd[$data->kodeuk] = $data->namasingkat;
			}
			
			if (strlen($kodeuk)==0) $kodeuk = '81';
			$form['formdata']['kodeuk']= array(
				'#type'         => 'select', 
				'#title'        => 'SKPD',
				'#options'		=> $skpd,
				'#description'  => 'SKPD pelaksana kegiatan', 
				//'#maxlength'    => 60, 
				//'#size'         => 20, 
				//'#required'     => !$disabled, 
				//'#disabled'     => $disabled, 
				'#default_value'=> $kodeuk, 
			);  	
		}	else {
			$form['formdata']['kodeuk']= array(
				'#type'         => 'hidden', 
				'#default_value'=> $kodeuk, 
			);  			
		}
	
	} else {
		$kodeuk = apbd_getuseruk();
		$form['formdata']['kodeuk']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $kodeuk, 
		);  			
	}

	$subjenisrevisitype = 'hidden';	
	switch($jenisrevisi) {
		case '1':
			$subjenisrevisitype = 'checkbox';
			$form['geserblokir']= array(
				'#type' => $subjenisrevisitype, 
				'#title' => t('Pembukaan Blokir'), 
				'#default_value' => $geserblokir, // changed
			);
			$form['geserrincian']= array(
				'#type' => $subjenisrevisitype, 
				'#title' => t('Pergeseran Rekening Rincian Obyek'), 
				'#default_value' => $geserrincian, // changed
			);
			$form['geserobyek']= array(
				'#type' => $subjenisrevisitype, 
				'#title' => t('Pergeseran Rekening Obyek'), 
				'#default_value' => $geserobyek, // changed
			);
			break;

		case '2':
			$subjenisrevisitype = 'checkbox';

			$form['lokasi']= array(
				'#type' => $subjenisrevisitype, 
				'#title' => t('Ralat/kesalahan penulisan lokasi'), 
				'#default_value' => $lokasi, // changed
			);
			$form['sumberdana']= array(
				'#type' => $subjenisrevisitype, 
				'#title' => t('Ralat/kesalahan penulisan sumber dana'), 
				'#default_value' => $sumberdana, // changed
			);
			$form['kinerja']= array(
				'#type' => $subjenisrevisitype, 
				'#title' => t('Ralat/kesalahan tolok ukur dan/atau target kinerja'), 
				'#default_value' => $kinerja, // changed
			);
			$form['sasaran']= array(
				'#type' => $subjenisrevisitype, 
				'#title' => t('Ralat/kesalahan penulisan kelompok sasaran kegiatan'), 
				'#default_value' => $sasaran, // changed
			);
			$form['detiluraian']= array(
				'#type' => $subjenisrevisitype, 
				'#title' => t('Ralat/kesalahan penulisan uraian dalam rincian obyek belanja'), 
				'#default_value' => $detiluraian, // changed
			);
			$form['rab']= array(
				'#type' => $subjenisrevisitype, 
				'#title' => t('Ralat/kesalahan penulisan satuan, volume dan harga satuan dalam rincian obyek belanja'), 
				'#default_value' => $rab, // changed
			);
			$form['triwulan']= array(
				'#type' => $subjenisrevisitype, 
				'#title' => t('Ralat/kesalahan pagu anggaran triwulan'), 
				'#default_value' => $triwulan, // changed
			);
			$form['lainnya']= array(
				'#type' => $subjenisrevisitype, 
				'#title' => t('Ralat/kesalahan administrasi lainnya yang tidak bertentangan dengan ketentuan perundang-undangan'), 
				'#default_value' => $lainnya, // changed
			);
			break;
		
		case '3':
			$form['formdata']['subjenisrevisi']= array(
				'#type' => 'radios', 
				'#title' => t('Kategori'), 
				'#default_value' => $subjenisrevisi,
				'#options' => array(	
					 '1' => t('[1] Geser/Ubah Kegiatan pada Anggaran Penetapan'), 	
					 '2' => t('[2] Kegiatan Baru dari DAK/BANPROV/BOS/DBH CHT' . $labelkegiatan ),
				   ),
			);	

			//$qlike = " and sumberdana1 in ('BANPROV','DAK')";
			break;
		
		case '4':
			$form['subjenisrevisi']= array(
				'#type' => $subjenisrevisitype, 
				//'#description'  => 'Jenis belanja',
				'#default_value' => 0, // changed
			);
			break;
	}
	
	$form['kembali'] = array (
		'#type' => 'submit',
		'#value' => '< Kembali',
		//'#weight' => 6,
	);

	$form['lanjut'] = array(
	'#type' => 'submit',
	'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisiperubahan' class='btn_green' style='color: white'>Batal</a>",
	'#value' => 'Lanjut >',
	);
	return $form;
	
}

function kegiatanrevisi_edit2_form_validate($form, &$form_state) {

	$kodeuk = $form_state['values']['kodeuk'];
	
	$jenisrevisi = $form_state['values']['jenisrevisi'];
	$subjenisrevisi = $form_state['values']['subjenisrevisi'];

	if (($jenisrevisi=='3') and ($subjenisrevisi=='1')) {
		$sql = sprintf('select count(kodekeg) jumlah from {kegiatanskpd} where kodeuk=\'%s\'', $kodeuk);
		$sql .= " and sumberdana1 in ('DAK', 'BANPROV', 'BOS', 'DBH CHT')";	
		$res = db_query($sql);
		if ($res) {
			if ($data = db_fetch_object($res)) {

			  if ($data->jumlah==0) {
				form_set_error('subjenisrevisi', t('SKPD tidak punya kegiatan dari DAK/BANPROV/BOS/DBH CHT.'));
			  }
			
			}
		}
	}
	
}
 
function kegiatanrevisi_edit2_form_submit($form, &$form_state) {
	
	$jenisrevisi = $form_state['values']['jenisrevisi'];
	$id = $form_state['values']['id'];
	$kodekeg = $form_state['values']['kodekeg'];
	$kodeuk = $form_state['values']['kodeuk'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['kembali']) {
		$form_state['redirect'] = 'apbd/kegiatanrevisi/edit1/' . $id . '/' . $jenisrevisi ;
	
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
		
		
		if (($jenisrevisi == '3') and ($subjenisrevisi =='2') )
			if ($kodekeg=='') 
				$form_state['redirect'] = 'apbd/kegiatanrevisi/editnew/' .  $kodeuk . '/' .	$jenisrevisi . '/' . $subjenisrevisi;
			else
				$form_state['redirect'] = 'apbd/kegiatanrevisi/edit4/' . $id . '/' . $kodeuk . '/' . $jenisrevisi . '/' . $subjenisrevisi . '/' . $kodekeg ;
				

		else
			$form_state['redirect'] = 'kegiatanrevisi2/' .  $id . '/' . $kodeuk . '/' .	$jenisrevisi . '/' . $subjenisrevisi . '/' . $geserblokir . '/' . $geserrincian . '/' . $geserobyek . '/' . $lokasi . '/' . $sumberdana . '/' . $kinerja . '/' . $sasaran . '/' . $detiluraian . '/' . $rab . '/' . $triwulan . '/' . $lainnya;
	}
}

?>
<?php
function kegiatanrevisi_editnew_form() {
    $form['formdata'] = array (
        '#type' => 'fieldset',
        //'#title'=> 'Edit Data Kegiatan',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    
    //$kodekeg = arg(3);
	
	$kodeuk = arg(3);
	if ($kodeuk=='') $kodeuk = apbd_getuseruk();
	
	
	$jenisrevisi = arg(4);
	$subjenisrevisi = arg(5);
	
	$perubahan = arg(6);

	$jenis = 2;
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);

	$total = 0;
	$plafon = 0;
	
	//drupal_add_js('files/js/common.js');
	drupal_add_js('files/js/kegiatanrev.js');
	drupal_add_css('files/css/kegiatancam.css');
 
 
	//drupal_set_title($kegiatan);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
	
	//Jam,Menit,Detik,Bulan,Hari,Tahun
	//'$batas = mktime(20, 0, 0, 3, 8, variable_get('apbdtahun', 0)) ;
	$batas = mktime(20, 0, 0, 9, 9, 2016) ;
	$sekarang = time () ;
	$selisih =($batas-$sekarang) ;
	$allowedit = true;		// (($selisih>0) || (isSuperuser()));
	
	//TIDAK BOLEH MENGEDIT BILA BUKAN TAHUN AKTIF
	$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
	
	$form['jenisrevisi']= array(
		'#type' => 'hidden', 
		'#default_value' => $jenisrevisi, // changed
	);
	$form['subjenisrevisi']= array(
		'#type' => 'hidden', 
		'#default_value' => $subjenisrevisi, // changed
	);

	
	$form['formdata']['kodekeg']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodekeg', 
		//'#description'  => 'kodekeg', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodekeg, 
	);

	$form['formdata']['tahun']= array( 
		'#type'         => 'hidden', 
		'#title'        => 'tahun',  
		//'#description'  => 'tahun', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $tahun, 
	); 
	$form['formdata']['kodepro']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodepro', 
		//'#description'  => 'kodepro', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodepro, 
	);

	$form['formdata']['perubahan']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodepro', 
		//'#description'  => 'kodepro', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perubahan, 
	);
	
	$kegiatan = 'Usulan kegiatan baru, isikan disini';
	$form['formdata']['kegiatanx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kegiatan', 
		'#description'  => 'Nama kegiatan', 
		'#maxlength'    => 255, 
		'#size'         => 100, 
		'#required'     => true, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kegiatan, 
	);

	
	/*
	$form['formdata']['nk']= array(
		'#type'         => $tipenomorkeg, 
		'#title'        => 'nomorkeg', 
		//'#description'  => 'kodekeg', 
		'#maxlength'    => 3, 
		'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $nomorkeg, 
	);
	*/
	

	$form['formdata']['program'] = array (
		'#type'		=> 'textfield',
		'#title'	=> 'Program',
		'#maxlength'    => 255, 
		'#size'         => 100, 
		'#default_value' => $program,
	);
	
	$form['formdata']['program-val']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $program, 
	);
	$form['formdata']['kegiatanpro'] = array (
		'#type' => 'markup',
		'#value' => "<span><font size='1.5'>Program kegiatan, isikan dengan program yang sesuai melalui tombol Program disamping</font></span>",
	);		

	$form['formdata']['ss0'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);	
		

		
	$pquery = "select kodeuk, namasingkat from {unitkerja} where aktif=1 order by namauk" ;
	$pres = db_query($pquery);
	$skpd = array();
	//$dinas[''] = '--- pilih dinas teknis---';
	while ($data = db_fetch_object($pres)) {
		$skpd[$data->kodeuk] = $data->namasingkat;
	}
	$skpdtype = 'hidden';
	if (isSuperuser() || isAdministrator())
		$skpdtype='select';

	$form['formdata']['kodeuk']= array(
		'#type'         => $skpdtype, 
		'#title'        => 'SKPD',
		'#options'		=> $skpd,
		'#description'  => 'SKPD pelaksana kegiatan', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	); 
	
 	
	$form['formdata']['plafon']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Usulan Anggaran',
		//'#description'  => 'Alokasi plafon belanja kegiatan', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#disabled'     => false, 
		'#default_value'=> $plafon, 
	); 

	
	if ($perubahan=='1')		//UNTUK PERUBAHAN
		$pquery = "select sumberdana from {sumberdanalt} order by nomor" ;
	else						//REVISI
		$pquery = "select sumberdana from {sumberdanalt} where sumberdana in ('DAK', 'BANPROV', 'BOS', 'DBH CHT') order by nomor" ;
	
	$pres = db_query($pquery);
	$sumberdana = array();
	while ($data = db_fetch_object($pres)) {
		$sumberdana[$data->sumberdana] = $data->sumberdana;
	}
	
	$form['formdata']['sumberdana1']= array(
		'#type'         => 'select', 
		'#title'        => 'Sumber Dana', 
		'#options'		=> $sumberdana,
		'#width'         => 30, 
		'#default_value'=> $sumberdana1, 
	); 
	/*
	$form['formdata']['sumberdana']['sumberdana2']= array(
		'#type'         => 'select', 
		'#options'		=> $sumberdana,
		'#title'        => 'Sumber Dana #2', 
		'#width'         => 30, 
		'#default_value'=> $sumberdana2, 
	); 
	$form['formdata']['sumberdana']['sumberdana2rp']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Sumber Dana #2 Rp',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#default_value'=> $sumberdana2rp, 
	); 
	*/


	
    $form['formdata']['e_kodekeg']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodekeg, 
    ); 
	
    $form['formdata']['e_kodepro']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodepro, 
    ); 

    $form['formdata']['e_kodeuk']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodeuk, 
    ); 
    $form['formdata']['e_nomorkeg']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $nomorkeg, 
    ); 

	if ($perubahan=='1') {						//PERUBAHAN
		$form['formdata']['submit'] = array (
			'#type' => 'submit',
			'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpdperubahan' class='btn_blue' style='color: white'>Batal</a>",
			'#value' => 'Simpan'
		);
	} else {									//REVISI
		$form['formdata']['submit'] = array (
			'#type' => 'submit',
			'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_blue' style='color: white'>Batal</a>",
			'#value' => 'Simpan'
		);
	}	
    return $form;
	  
}
function kegiatanrevisi_editnew_form_validate($form, &$form_state) {

$kodepro = $form_state['values']['kodepro']; 
if ($kodepro == '') {
	form_set_error('program', 'Program tidak diisi/dipilih dengan benar');
}
	
}

function kegiatanrevisi_editnew_form_submit($form, &$form_state) {

	$kodeuk = $form_state['values']['kodeuk'];
	
	$jenisrevisi = $form_state['values']['jenisrevisi'];
	$subjenisrevisi =$form_state['values']['subjenisrevisi'];

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


	$kodepro = $form_state['values']['kodepro'];
	
	$kodekeg = $form_state['values']['kodekeg'];
	
	$perubahan = $form_state['values']['perubahan'];

	$kegiatan = $form_state['values']['kegiatanx'];
	$jenis = 2;
	$isppkd = 0;
	$plafon = $form_state['values']['plafon'];
	$inaktif = 0;
	$dispensasi = 0;
	
	$sumberdana1 = $form_state['values']['sumberdana1'];
	$sumberdana2 = '';		//$form_state['values']['sumberdana2'];
	$sumberdana1rp = $form_state['values']['sumberdana1rp'];
	$sumberdana2rp = 0;		//$form_state['values']['sumberdana2rp'];
	
	$total = $form_state['values']['total'];
	
	$tahun = variable_get('apbdtahun', 0);
	$periode = variable_get('apbdrevisi', 0);
	$kodekeg = $tahun . $kodeuk . $kodepro ;
	
	/*
	if ($perubahan=='1') {		//PERUBAHAN
		
		$nomorkeg =apbd_getcounterskpd($kodekeg);
		$kodekeg .= apbd_getcounterkegiatan($kodekeg);
		
	} else {
	*/
		$nomorkeg =apbd_getcounterskpdrevisi($kodekeg);
		//$kodekeg .= apbd_getcounterkegiatan($kodekeg);
		$kodekeg .= $nomorkeg;
	//}
	
	$sql = sprintf("insert into {kegiatanrevisi} (kodekeg, nomorkeg, tahun, kodepro, kodeuk, kegiatan, jenis, plafon, sumberdana1, sumberdana1rp, sumberdana2, sumberdana2rp, inaktif, isppkd, dispensasi, periode) values('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
					$kodekeg, $nomorkeg, $tahun, $kodepro, $kodeuk,
					db_escape_string($kegiatan),
					db_escape_string($jenis),
					db_escape_string($plafon),
					db_escape_string($sumberdana1),					  
					db_escape_string($sumberdana1rp),
					db_escape_string($sumberdana2),					  
					db_escape_string($sumberdana2rp),
					db_escape_string($inaktif),
					$isppkd,
					$dispensasi,
					$periode
				  );
	
	
	//drupal_set_message($sql);

	$res = db_query($sql);

	if ($res) {
			drupal_set_message('ok 1');
			
			$sql =  sprintf("insert into {kegiatanrevisiperubahan} (jenisrevisi, subjenisrevisi, tahun, kodeuk, kodekeg, geserblokir, geserrincian, geserobyek, lokasi, sumberdana, kinerja, sasaran, detiluraian, rab, triwulan, lainnya, alasan1, alasan2, alasan3, nosurat, tglsurat) values('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $jenisrevisi, $subjenisrevisi, $tahun, $kodeuk, $kodekeg, $geserblokir, $geserrincian, $geserobyek, $lokasi, db_escape_string($sumberdana), db_escape_string($kinerja), db_escape_string($sasaran), db_escape_string($detiluraian), db_escape_string($rab), db_escape_string($triwulan), db_escape_string($lainnya), db_escape_string($alasan1), db_escape_string($alasan2), db_escape_string($alasan3), db_escape_string($nosurat), db_escape_string($tglsurat)); 
			
			//drupal_set_message($sql);
			//c
			$res = db_query($sql);
		
	}
	
	if ($res) {
		drupal_set_message('ok 2');
		drupal_set_message('Penyimpanan data berhasil dilakukan');		
	}
	else {
		//drupal_set_message($kodekeg);		
		//drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
	}
	//$form_state['redirect'] = 'apbd/kegiatanrevisi/edit4/' . $id . '/' . $jenisrevisi . '/' . $subjenisrevisi . '/' . $geserblokir . '/' . $geserrincian . '/' . $geserobyek . '/' . $lokasi . '/' . $sumberdana . '/' . $kinerja . '/' . $sasaran . '/' . $detiluraian . '/' . $rab . '/' . $triwulan . '/' . $lainnya . '/' . $kodekeg ;    
	//drupal_goto('apbd/kegiatanrevisiperubahan');
	
	drupal_goto('apbd/kegiatanrevisi');
}
?>
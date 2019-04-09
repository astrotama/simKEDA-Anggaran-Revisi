<?php
function kegiatanskpd_edit_form() {
    $form['formdata'] = array (
        '#type' => 'fieldset',
        //'#title'=> 'Edit Data Kegiatan',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    
    $kodekeg = arg(3);
	$kodeuk = apbd_getuseruk();
	if (isSuperuser()) {
		$kodeuk = $_SESSION['kodeuk'];
		$adminok = true;
	} else
		$adminok = false;
	
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);
	$jenis = 2;
	
	//drupal_add_js('files/js/common.js');
	drupal_add_js('files/js/kegiatancam.js');
	drupal_add_css('files/css/kegiatancam.css');
    $disabled = FALSE;
    if (isset($kodekeg))
    {
        if (!user_access('kegiatanskpd edit'))
            drupal_access_denied();
		

			
        $sql = 'select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.jenis, k.kodesuk, k.kegiatan, k.lokasi, 
				k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget, k.keluaransasaran,
				k.keluarantarget, k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, k.totalsebelum, 
				k.totalsesudah, k.waktupelaksanaan, k.sumberdana1, k.sumberdana2, k.sumberdana1rp, 
				k.sumberdana2rp, k.latarbelakang, k.dispensasi, k.kelompoksasaran, k.adminok, p.program 
				from {kegiatanskpd} k left join {program} p on (k.kodepro = p.kodepro) where k.kodekeg=\'%s\'' ;
				
		//drupal_set_message($sql . $kodekeg);
        $res = db_query(db_rewrite_sql($sql), array ($kodekeg));
        if ($res) {
			$data = db_fetch_object($res);
			if ($data) {    
				$kodekeg = $data->kodekeg;
				$nomorkeg = $data->nomorkeg;
				$tahun = $data->tahun;
				$kodepro = $data->kodepro;
				$program = $data->program;
				$kodeuk = $data->kodeuk;
				$kodesuk = $data->kodesuk;
				$kegiatan = $data->kegiatan ;
				$lokasi = $data->lokasi;
				$jenis = $data->jenis;

				$programsasaran = $data->programsasaran;
				$programtarget = $data->programtarget;
				$masukansasaran = $data->masukansasaran;
				$masukantarget = $data->masukantarget;
				$hasilsasaran = $data->hasilsasaran;
				$hasiltarget = $data->hasiltarget;
				$keluaransasaran = $data->keluaransasaran;
				$keluarantarget = $data->keluarantarget;

				
				$total = $data->total;
				$plafon = $data->plafon;
				$totalsebelum = $data->totalsebelum;
				$totalsesudah = $data->totalsesudah;
				
				$sumberdana1 = $data->sumberdana1;
				$sumberdana2 = $data->sumberdana2;
				$sumberdana1rp = $data->sumberdana1rp;
				$sumberdana2rp = $data->sumberdana2rp;

				$waktupelaksanaan = $data->waktupelaksanaan;
				$latarbelakang  = $data->latarbelakang;
				$kelompoksasaran = $data->kelompoksasaran;
				
				$adminok = ($adminok or $data->adminok);
				
				$dispensasi = $data->dispensasi;
				
				$disabled =TRUE;
			} else {
				$kodekeg = '';
				$kegiatan = 'Kegiatan Baru';
			}
        } else {
			$kodekeg = '';
			$kegiatan = 'Kegiatan Baru';
		}
    } else {
		//$form['formdata']['#title'] = 'Tambah Data Kegiatan Renja SKPD';
		$kodekeg = '';
		$kegiatan = 'Kegiatan Baru';
	
		if (!user_access('kegiatanskpd tambah'))
			drupal_access_denied();
    }

	drupal_set_title($kegiatan);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
	
	//Jam,Menit,Detik,Bulan,Hari,Tahun
	//'$batas = mktime(20, 0, 0, 3, 8, variable_get('apbdtahun', 0)) ;
	
	$allowedit = batastgl() || isSuperuser();//(($selisih>0) || (isSuperuser()));
	
	//if ($kodeuk == '33') $allowedit = true;
	
	if ($allowedit==false) $allowedit = $dispensasi;
	if ($allowedit==false) {
		//dispensasirenja
        $sql = 'select dispensasirenja from {unitkerja} where kodeuk=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($kodeuk));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {  		
				$allowedit = $data->dispensasirenja;
			}
		}
	}
	
	//TIDAK BOLEH MENGEDIT BILA BUKAN TAHUN AKTIF
	//$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
	
	
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
	
	/*
	$form['formdata']['kegiatanx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kegiatan', 
		//'#description'  => 'kegiatanx', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kegiatan, 
	);
	*/

	if (isSuperuser()) {
		$form['formdata']['program'] = array (
			'#type'		=> 'textfield',
			'#title'	=> 'Program',
			'#maxlength'    => 255, 
			'#size'         => 90, 
			//'#maxlength'    => 255, 
			//'#size'         => 60, 
			//'#disabled'     => true, 
			'#default_value' => $program,
		);
		$form['formdata']['program-val']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $program, 
		);
	
	} else {
		$form['formdata']['programx']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Program', 
			'#description'  => 'Program kegiatan, ditentukan saat penyusunan RKPD dan KUA/PPA', 
			'#maxlength'    => 255, 
			'#size'         => 90, 
			//'#required'     => !$disabled, 
			//'#disabled'     => true, 
			'#default_value'=> $program, 
	);
	}
		
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
	
	if (isSuperuser()){
		$form['formdata']['kodesuk']= array(
			'#type'         => 'hidden', 
			'#title'        => 'Sub SKPD',
			'#default_value'=> $kodesuk, 
		); 			
		
	} else {
		
		//$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
		$pquery = sprintf('select kodesuk, namasuk from {subunitkerja} where kodeuk=\'%s\' order by kodesuk', $kodeuk);
		
		//drupal_set_message($pquery);
		
		$pres = db_query($pquery);
		$subskpd = array();
		$subskpd[''] = '- Pilih Bidang -';
		while ($data = db_fetch_object($pres)) {
			$subskpd[$data->kodesuk] = $data->namasuk;
		}
		
		$form['formdata']['kodesuk']= array(
			'#type'         => 'select', 
			'#title'        => 'Bidang/Bagian',
			'#options'		=> $subskpd,
			//'#description'  => 'kodesuk', 
			//'#maxlength'    => 60, 
			//'#size'         => 20, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $kodesuk, 
		); 		
	}

	if ($jenis==1) {
		//drupal_set_message('x');
		//$tuvisibe = 'fieldset';
		
		$tuvisibe = 'hidden';
		$tureq=false;
		
		$programsasaran = '-';
		$programtarget = $programsasaran;
		
		$masukantarget = $programsasaran;
		$masukansasaran = $programsasaran;
		
		$keluarantarget = $programsasaran;
		$keluaransasaran = $programsasaran;

		$hasiltarget = $programsasaran;
		$hasilsasaran = $programsasaran;
		
	} else {
		$tuvisibe = 'fieldset';
		$tureq = true;
	}
	//TUK Program
	$form['formdata']['tukprogram'] = array (
		'#type' => $tuvisibe,
		'#title'=> 'Program',
		'#collapsible' => true,
		'#collapsed' => true,     
	);
	$form['formdata']['tukprogram']['programsasaran']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tolok Ukur Kinerja', 
		'#description'  => 'Tolok ukur kinerja program, diisi untuk mendukung kinerja program sesuai dengan RPJMD',
		'#maxlength'    => 255, 
		'#size'         => 89, 
		'#required'     => $tureq, 
		'#default_value'=> $programsasaran, 
	); 
	$form['formdata']['tukprogram']['programtarget']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target Kinerja', 
		'#description'  => 'Target kinerja program, diisi untuk memenuhi target pencapaian RPJMD',
		//attributes'	=> array('style' => 'text-align: right'),
		//size'         => 30, 
		'#maxlength'    => 255, 
		'#size'         => 89, 
		'#required'     => $tureq, 
		'#default_value'=> $programtarget, 
	); 
	//TUK Masukan
	$form['formdata']['tukmasukan'] = array (
		'#type' => $tuvisibe,
		'#title'=> 'Masukan (Input)',
		'#collapsible' => true,
		'#collapsed' => true,     
	);
	$form['formdata']['tukmasukan']['masukansasaran']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tolok Ukur Kinerja', 
		'#maxlength'    => 255, 
		'#size'         => 89, 
		'#required'     => $tureq, 
		'#default_value'=> $masukansasaran, 
	); 
	$form['formdata']['tukmasukan']['masukantarget']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target Kinerja', 
		//attributes'	=> array('style' => 'text-align: right'),
		//size'         => 30, 
		'#maxlength'    => 255, 
		'#size'         => 89, 
		'#required'     => $tureq, 
		'#default_value'=> $masukantarget, 
	); 
	//TUK Keluaran
	$form['formdata']['tukkeluaran'] = array (
		'#type' => $tuvisibe,
		'#title'=> 'Keluaran (Output)',
		'#collapsible' => true,
		'#collapsed' => true,     
	);
	$form['formdata']['tukkeluaran']['keluaransasaran']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tolok Ukur Kinerja', 
		'#maxlength'    => 255,
		'#size'         => 89, 
		'#required'     => $tureq, 
		'#default_value'=> $keluaransasaran, 
	); 
	$form['formdata']['tukkeluaran']['keluarantarget']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target Kinerja', 
		//attributes'	=> array('style' => 'text-align: right'),
		//size'         => 30, 
		'#maxlength'    => 255, 
		'#size'         => 89, 
		'#required'     => $tureq, 
		'#default_value'=> $keluarantarget, 
	); 
	//TUK hasil
	$form['formdata']['tukhasil'] = array (
		'#type' => $tuvisibe,
		'#title'=> 'Hasil (Outcome)',
		'#collapsible' => true,
		'#collapsed' => true,     
	);
	$form['formdata']['tukhasil']['hasilsasaran']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tolok Ukur Kinerja', 
		'#maxlength'    => 255, 
		'#size'         => 89, 
		'#required'     => $tureq, 
		'#default_value'=> $hasilsasaran, 
	); 
	$form['formdata']['tukhasil']['hasiltarget']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target Kinerja', 
		//attributes'	=> array('style' => 'text-align: right'),
		//size'         => 30, 
		'#maxlength'    => 255, 
		'#size'         => 89, 
		'#required'     => $tureq, 
		'#default_value'=> $hasiltarget, 
	); 
	
	$form['formdata']['waktupelaksanaan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Waktu Pelaksanaan', 
		'#description'  => 'Waktu pelaksana kegiatan, misalnya 3 bulan, 6 bulan atau 1 tahun',
		'#maxlength'    => 255, 
		'#size'         => 90, 		
		'#required'     => true, 
		'#default_value'=> $waktupelaksanaan, 
	);	
	$form['formdata']['latarbelakang']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Latar Belakang', 
		'#maxlength'    => 255, 
		'#size'         => 90, 		
		'#required'     => false, 
		'#default_value'=> $latarbelakang, 
	);	
	$form['formdata']['kelompoksasaran']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kelompok Sasaran', 
		'#maxlength'    => 255, 
		'#size'         => 90, 		
		'#required'     => true, 
		'#default_value'=> $kelompoksasaran, 
	);	

	$form['formdata']['lokasi'] = array (
		'#type'	=>'hidden',
		'#default_value'=>$lokasi
	);
	$form['formdata']['lokasilabel']= array(
		'#type'         => 'item', 
		'#title'        => 'Lokasi',
		'#description'  => 'Lokas kegiatan, anda bisa mengisi beberapa lokasi untuk satu kegiatan',
		'#value'		=> "<div id='lokasi' style='float:left'><span id='lokasilabel'></span><div id='btnTambah' style='float:left;'><a href='#bds' class='btn_blue' style='color:#ffffff'>Tambah Lokasi</a></div></div><div style='clear:both'></div>"
		//'#cols'		=> '40',
		//'#rows'		=> '3',
		//'#disabled'     => true, 
		//'#description'  => 'lokasi', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		//'#default_value'=> $lokasi, 
	);

	$form['formdata']['anggaran'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Isian Anggaran',
		'#collapsible' => true,
		'#collapsed' => false,        
	);
	
	$form['formdata']['anggaran']['totalx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jumlah', 
		'#description'  => 'Jumlah anggaran tahun ini, akan terisi otomatis pada saat pengisian rekening',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		//'#disabled'     => true, 
		'#default_value'=> $total, 
	); 
	$form['formdata']['anggaran']['total']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $total, 
	); 
	$form['formdata']['anggaran']['plafon']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Alokasi',
		'#description'  => 'Alokasi plafon yang sediakan untuk kegiatan ini, anggaran yang disusun tidak boleh melebihi batas plafon',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		//'#disabled'     => true, 
		'#default_value'=> $plafon, 
	); 
	 $form['formdata']['anggaran']['totalsebelum']= array(
		 '#type'         => 'textfield', 
		 '#title'        => 'Jumlah Tahun ' . ($tahun-1), 
		 '#description'  => 'Jumlah anggaran tahun lalu, bila ada',
		 '#attributes'	=> array('style' => 'text-align: right'),
		 '#size'         => 30, 
		 '#default_value'=> $totalsebelum, 
	 );
	 $form['formdata']['anggaran']['totalsesudah']= array(
		 '#type'         => 'textfield', 
		 '#title'        => 'Jumlah Tahun ' . ($tahun+1), 
		 '#description'  => 'Perkiraan jumlah anggaran tahun depan, bila diperkirakan ada',
		 '#attributes'	=> array('style' => 'text-align: right'),
		 '#size'         => 30, 
		 '#default_value'=> $totalsesudah, 
	 );

	$form['formdata']['sumberdana'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Sumber Dana',
		'#collapsible' => true,
		'#collapsed' => false,        
	);

	$pquery = "select sumberdana from {sumberdanalt} order by nomor" ;
	$pres = db_query($pquery);
	$sumberdana = array();
	$sumberdana[''] = '-Pilih Sumber Dana-';
	while ($data = db_fetch_object($pres)) {
		$sumberdana[$data->sumberdana] = $data->sumberdana;
	}
	
	$form['formdata']['sumberdana']['sumberdana1']= array(
		'#type'         => 'select', 
		'#title'        => 'Sumber Dana', 
		'#description'  => 'Sumber dana kegiatan',
		'#options'		=> $sumberdana,
		'#width'         => 30, 
		'#default_value'=> $sumberdana1, 
	); 
	$form['formdata']['sumberdana']['sumberdana1rp']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jumlah',
		'#description'  => 'Nomnal (nilai rupiah) sumber dana kegiatan',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#default_value'=> $sumberdana1rp, 
	); 
	/*
	$form['formdata']['sumberdana']['sumberdana2']= array(
		'#type'         => 'select', 
		'#options'		=> $sumberdana,
		'#description'  => 'Sumber dana kedua, untuk kegiatan yang didanai oleh dua sumber dana',
		'#title'        => 'Sumber Dana #2', 
		'#width'         => 30, 
		'#default_value'=> $sumberdana2, 
	); 
	$form['formdata']['sumberdana']['sumberdana2rp']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Sumber Dana #2 Rp',
		'#description'  => 'Nomnal (nilai rupiah) sumber dana kedua',
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

	if ($adminok) {
		$form['formdata']['submitrek'] = array (
			'#type' => 'submit',
			'#value' => 'Rekening',
			//'#weight' => 23,
		);

		$form['formdata']['submitprint'] = array (
			'#type' => 'submit',
			'#value' => 'Preview RKA',
			//'#weight' => 23,
		);
	}
	
	if ($allowedit) 
		$form['formdata']['submit'] = array (
			'#type' => 'submit',
			'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd' class='btn_blue' style='color: white'>Tutup</a>",
			'#value' => 'Simpan'
		);
	
    return $form;
	
}
function kegiatanskpd_edit_form_validate($form, &$form_state) {

    //if ($form_state['values']['nk']=='') {
	//	form_set_error('', 'Kegiatan harus dipilih dari daftar kegiatan yang telah disediakan.');
    //}

	
	$e_kodekeg = $form_state['values']['e_kodekeg'];
	
	/*
	if ($e_kodekeg <> '') {

		$sql = sprintf("select sum(total) as totalsub from {kegiatanskpdsub} where kodekeg='%s'",
					   $e_kodekeg
					   );
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			
			$totalsub = $data->totalsub;
			$totalsub 	= is_numeric($totalsub) ? $totalsub : 0;
			if (($total <> $totalsub) and ($totalsub > 0)) {
				form_set_error('kegiatanx', 'Jumlah usulan sub kegiatan tidak sama [' . $total . ' : ' . $totalsub . ']' );
			}
			


		}
	}
	//END VALIDATE KODE
	*/
	
	$lokasi = $form_state['values']['lokasi'];
    if ($lokasi == '') {
		form_set_error('lokasi', 'Lokasi belum diisi' );
	}

	/*
	$total = $form_state['values']['total'];
	$sumberdana1rp = $form_state['values']['sumberdana1rp'];
	$sumberdana2rp = $form_state['values']['sumberdana2rp'];
	if ($total != ($sumberdana1rp+$sumberdana2rp)) {
		form_set_error('sumberdana1rp', 'Isian sumber dana tidak sama dengan jumlah anggaran' );
	}
	*/
	$field = array('programsasaran','lokasi', 'programtarget', 'masukansasaran', 'masukantarget', 'hasilsasaran', 'hasiltarget', 'keluaransasaran', 'keluarantarget');
	$error = false;
	foreach($field as $data){
		if(preg_match('/[<@>]/', $form_state['values'][$data])){
			$error=true;
		}
	}
	if($error == false){
		form_set_error("akses",'Tidak boleh menggunakan < @ >');
	}
}

function kegiatanskpd_edit_form_submit($form, &$form_state) {
    if($form_state['clicked_button']['#value'] == $form_state['values']['submitrek']) {
       $e_kodekeg = $form_state['values']['e_kodekeg'];
	   $form_state['redirect'] = 'apbd/kegiatanskpd/rekening/' . $e_kodekeg ;
		//drupal_set_message('Next');
		
    } elseif ($form_state['clicked_button']['#value'] == $form_state['values']['submitprint']) {
       $e_kodekeg = $form_state['values']['e_kodekeg'];
	   $form_state['redirect'] = 'apbd/kegiatanskpd/print/' . $e_kodekeg  ;
		//drupal_set_message('Next');
		
	} else {
		$e_kodekeg = $form_state['values']['e_kodekeg'];
		$e_kodeuk = $form_state['values']['e_kodeuk'];
		$e_kodepro = $form_state['values']['e_kodepro'];

		$kodeuk = $form_state['values']['kodeuk'];
		$kodesuk = $form_state['values']['kodesuk'];
		$kodepro = $form_state['values']['kodepro'];
		
		$kodekeg = $form_state['values']['kodekeg'];

		$lokasi = $form_state['values']['lokasi'];
		//drupal_set_message($lokasi);

		$programsasaran = $form_state['values']['programsasaran'];
		$programtarget = $form_state['values']['programtarget'];
		$masukansasaran = $form_state['values']['masukansasaran'];
		$masukantarget = $form_state['values']['masukantarget'];
		$hasilsasaran = $form_state['values']['hasilsasaran'];
		$hasiltarget = $form_state['values']['hasiltarget'];
		$keluaransasaran = $form_state['values']['keluaransasaran'];
		$keluarantarget = $form_state['values']['keluarantarget'];

		
		//$total = $form_state['values']['total'];
		//$plafon = $form_state['values']['plafon'];
		$totalsebelum = $form_state['values']['totalsebelum'];
		$totalsesudah = $form_state['values']['totalsesudah'];
		
		$sumberdana1 = $form_state['values']['sumberdana1'];
		$sumberdana2 = '';		//$form_state['values']['sumberdana2'];
		$sumberdana1rp = $form_state['values']['sumberdana1rp'];
		$sumberdana2rp = 0;		//$form_state['values']['sumberdana2rp'];

		//drupal_set_message($sumberdana1rp);
		//drupal_set_message($sumberdana2rp);
		//drupal_set_message('ini total : ' . $total);
		
		if (($sumberdana1 . $sumberdana2) == '') $sumberdana1 = 'DAU';
		if (($sumberdana1rp + $sumberdana2rp) == '') $sumberdana1rp = $total;
		
		$waktupelaksanaan = $form_state['values']['waktupelaksanaan'];
		$latarbelakang  = $form_state['values']['latarbelakang'];
		$kelompoksasaran = $form_state['values']['kelompoksasaran'];


		if ($e_kodekeg=='')
		{

			$res = db_query($sql);
		} else {
 
			$kodeberubah=false;
			if (($e_kodepro == $kodepro) && ($e_kodeuk == $kodeuk)) {
				$kodekeg = $e_kodekeg;
				
			} else {
				$tahun = variable_get('apbdtahun', 0);
				$kodekeg = $tahun . $kodeuk . $kodepro;			
				$kodekeg .= apbd_getcounterkegiatan($kodekeg);
				
				$kodeberubah=true;
			}		
			
			
			$sql = sprintf("update {kegiatanskpd} set adminok=1, lokasi='%s', programsasaran='%s', programtarget='%s', masukansasaran='%s', 		
					masukantarget='%s', hasilsasaran='%s', hasiltarget='%s', keluaransasaran='%s', keluarantarget='%s', totalsebelum='%s', 
					totalsesudah='%s', sumberdana1='%s', sumberdana1rp='%s', sumberdana2='%s', sumberdana2rp='%s', waktupelaksanaan='%s', 
					latarbelakang='%s', kelompoksasaran='%s', kodeuk='%s', kodesuk='%s', kodepro='%s', kodekeg='%s' where kodekeg='%s'",
							db_escape_string($lokasi),
							db_escape_string($programsasaran),
							db_escape_string($programtarget),					  
							db_escape_string($masukansasaran),					  
							db_escape_string($masukantarget), 				
							db_escape_string($hasilsasaran),
							db_escape_string($hasiltarget),
							db_escape_string($keluaransasaran),
							db_escape_string($keluarantarget),					  
							db_escape_string($totalsebelum),					  
							db_escape_string($totalsesudah),					  
							db_escape_string($sumberdana1),					  
							db_escape_string($sumberdana1rp),
							db_escape_string($sumberdana2),					  
							db_escape_string($sumberdana2rp),
							db_escape_string($waktupelaksanaan),
							db_escape_string($latarbelakang),
							db_escape_string($kelompoksasaran),
							db_escape_string($kodeuk),
							db_escape_string($kodesuk),
							db_escape_string($kodepro),
							db_escape_string($kodekeg),
							$e_kodekeg);		
			$res = db_query($sql);
		}
		if ($res) {
			
			if ($kodeberubah) {
				//Update anggperkeg
				$sql = sprintf("update {anggperkeg} set kodekeg='%s' where kodekeg='%s'",
								db_escape_string($kodekeg),
								$e_kodekeg);		
				$res = db_query($sql);

				//Update anggperkegdetil
				$sql = sprintf("update {anggperkegdetil} set kodekeg='%s' where kodekeg='%s'",
								db_escape_string($kodekeg),
								$e_kodekeg);		
				$res = db_query($sql);
				
			}
			
			drupal_set_message('Penyimpanan data berhasil dilakukan');		
		}
		else {
			drupal_set_message($kodekeg);		
			drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
		}
	}
}
?>
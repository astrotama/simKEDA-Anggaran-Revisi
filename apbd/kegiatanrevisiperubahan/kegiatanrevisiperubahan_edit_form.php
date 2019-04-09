<?php
function kegiatanrevisiperubahan_edit_form() {
    $form['formdata'] = array (
        '#type' => 'fieldset',
        //'#title'=> 'Edit Data Kegiatan',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );

    $kodekeg = arg(3);
	$id = arg(4);
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
		
		//PENETAPAN
        $sql = 'select total, plafon from {kegiatanskpd} where kodekeg=\'%s\'' ;
		$res = db_query(db_rewrite_sql($sql), array ($kodekeg));
		if ($res) {
			if ($data = db_fetch_object($res)) {
				$total_pen = $data->total;
				$plafon_pen = $data->plafon;				
			}
		}
		
		
        $sql = 'select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.jenis, k.kodesuk, k.kegiatan, k.lokasi, 
				k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget, k.keluaransasaran,
				k.keluarantarget, k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, k.totalsebelum, 
				k.totalsesudah, k.waktupelaksanaan, k.latarbelakang, k.dispensasi, k.kelompoksasaran, k.adminok, p.program 
				from {kegiatanrevisi} k left join {program} p on (k.kodepro = p.kodepro) where k.kodekeg=\'%s\'' ;
				
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
				
				$waktupelaksanaan = $data->waktupelaksanaan;
				
				$kelompoksasaran = $data->kelompoksasaran;
				
				$adminok = ($adminok or $data->adminok);
				
				$dispensasi = $data->dispensasi;
				
				$disabled =TRUE;
				
				//INFO Perubahan
				$sql = 'select id,nosurat, tglsurat,dokumen,alasan1 from {kegiatanrevisiperubahan} where kodekeg=\'%s\' and id=\'%s\'';
				$res = db_query(db_rewrite_sql($sql), array ($kodekeg, $id));
				if ($res) {
					if ($data = db_fetch_object($res)) {
						$id = $data->id;
						$nosurat = $data->nosurat;
						$tglsurat = $data->tglsurat;
						$dokumen = $data->dokumen;
						
						$latarbelakang  = $data->alasan1;
					}
				}
				
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
	//$batas = mktime(0, 0, 0, 8, 16, 2016) ;
	//$sekarang = time () ;
	//$selisih =($batas-$sekarang) ;
	$allowedit = batastgl() || isSuperuser();
	
	//if ($kodeuk == '33') $allowedit = true;
	
	if ($allowedit==false) $allowedit = $dispensasi;
	if ($allowedit==false) {
		//dispensasirevisi
        $sql = 'select dispensasirevisi from {unitkerja} where kodeuk=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($kodeuk));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {  		
				$allowedit = $data->dispensasirevisi;
			}
		}
	}

	if ($allowedit==false) {
		//dispensasirenja
		//$sqluk = sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
        $sql = sprintf('select dispensasi from {kegiatanrevisi} where kodekeg=\'%s\'', $kodekeg);
		$res = db_query($sql);
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {  		
				$allowedit = $data->dispensasi;
			}
		}
	}	
	
	//VERIFIKATOR
	$allowedit = ($allowedit and !isVerifikator());
	
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
	$form['formdata']['id']= array(
		'#type'         => 'hidden', 
		'#title'        => 'id', 
		//'#description'  => 'kodekeg', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $id, 
	);

	$form['formdata']['id']= array(
		'#type'         => 'hidden', 
		'#title'        => 'ID', 
		//'#description'  => 'kodekeg', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $id, 
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
		'#collapsed' => true,        
	);
	
	$form['formdata']['anggaran']['totalx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jumlah', 
		'#description'  => 'Jumlah anggaran perubahan, anggaran penetapannya adalah ' . apbd_fn($total_pen),
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
		'#description'  => 'Alokasi plafon anggara perubahan, plafon penetapannya adalah ' . apbd_fn($plafon_pen),
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

	$form['formdata']['perubahan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Perubahan',
		'#collapsible' => true,
		'#collapsed' => false,        
	);

	$form['formdata']['perubahan']['latarbelakang']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Latar Belakang',
		'#description'  => 'Latar belakang perubahan',
		'#required'     => 'true', 
		'#size'         => 90, 
		'#default_value'=> $latarbelakang, 
	); 
	$form['formdata']['perubahan']['dokumen'] = array(
		'#type' => 'textfield',
		'#title' => 'Dokumen Pendukung',
		'#maxlength'    => 255, 
		'#size'         => 120, 
		'#default_value' => $dokumen,
	);

	$form['formdata']['perubahan']['nosurat'] = array(
		'#type' => 'textfield',
		'#title' => 'No. Surat',
		'#maxlength'    => 255, 
		'#size'         => 60, 
		'#default_value' => $nosurat,
		'#required' => true,
	);

	$form['formdata']['perubahan']['tglsurat'] = array(
		'#type' => 'textfield',
		'#title' => 'Tgl. Surat',
		'#maxlength'    => 255, 
		'#size'         => 60, 
		'#default_value' => $tglsurat,
		'#required' => true,
	);	
	/*
	$form['formdata']['perubahan']['nosurat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'No. Surat',
		'#description'  => 'No. Surat permohonan perubahan',
		'#size'         => 30, 
		'#default_value'=> $nosurat, 
	); 
	$form['formdata']['perubahan']['tglsurat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tgl. Surat',
		'#description'  => 'Tgl. Surat permohonan perubahan',
		'#size'         => 30, 
		'#default_value'=> $tglsurat, 
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

	/*
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
	*/
	
	if ($allowedit) {
		if (isSuperuser()) {
			$form['formdata']['sahkan'] = array (
				'#type' => 'submit',
				'#value' => 'Sahkan'
			);
			
		}
		$form['formdata']['submit'] = array (
			'#type' => 'submit',
			'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisiperubahan' class='btn_blue' style='color: white'>Tutup</a>",
			'#value' => 'Simpan'
		);
		
	}
    return $form;
	
}
function kegiatanrevisiperubahan_edit_form_validate($form, &$form_state) {

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
}

function kegiatanrevisiperubahan_edit_form_submit($form, &$form_state) {
    if($form_state['clicked_button']['#value'] == $form_state['values']['submitrek']) {
       $e_kodekeg = $form_state['values']['e_kodekeg'];
	   $form_state['redirect'] = 'apbd/kegiatanskpdperubahan/rekening/' . $e_kodekeg ;
		//drupal_set_message('Next');

    } else if($form_state['clicked_button']['#value'] == $form_state['values']['tutup']) {
		drupal_goto('apbd/kegiatanrevisiperubahan');
		
    } else if($form_state['clicked_button']['#value'] == $form_state['values']['sahkan']) {
		$e_kodekeg = $form_state['values']['e_kodekeg'];
		$id = $form_state['values']['id'];
		pengesahanrevisi_x($e_kodekeg, $id);
		

	} elseif ($form_state['clicked_button']['#value'] == $form_state['values']['submitprint']) {
       $e_kodekeg = $form_state['values']['e_kodekeg'];
	   $form_state['redirect'] = 'apbd/kegiatanskpdperubahan/print/' . $e_kodekeg  ;
		//drupal_set_message('Next');
		
	} else {
		$e_kodekeg = $form_state['values']['e_kodekeg'];
		$e_kodeuk = $form_state['values']['e_kodeuk'];
		$e_kodepro = $form_state['values']['e_kodepro'];

		$id = $form_state['values']['id'];

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
		
		$waktupelaksanaan = $form_state['values']['waktupelaksanaan'];
		$latarbelakang  = $form_state['values']['latarbelakang'];
		$kelompoksasaran = $form_state['values']['kelompoksasaran'];

		$nosurat = $form_state['values']['nosurat'];
		$tglsurat = $form_state['values']['tglsurat'];	
		
		$dokumen = $form_state['values']['dokumen'];
		

		if ($e_kodekeg=='')
		{

			$res = db_query($sql);
		} else {
			
			$tahun = variable_get('apbdtahun', 0);
			
			$kodeberubah=false;
			if (($e_kodepro == $kodepro) && ($e_kodeuk == $kodeuk)) {
				$kodekeg = $e_kodekeg;
				
			} else {
				
				$kodekeg = $tahun . $kodeuk . $kodepro;			
				$kodekeg .= apbd_getcounterkegiatan($kodekeg);
				
				$kodeberubah=true;
			}		
			
			
			$sql = sprintf("update {kegiatanrevisi} set adminok=1, lokasi='%s', programsasaran='%s', programtarget='%s', masukansasaran='%s', 		
					masukantarget='%s', hasilsasaran='%s', hasiltarget='%s', keluaransasaran='%s', keluarantarget='%s', totalsebelum='%s', 
					totalsesudah='%s', waktupelaksanaan='%s', 
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
			
			//UPDATE REVISI
			if ($id=='0') 
				$sql =  sprintf("insert into {kegiatanrevisiperubahan} (jenisrevisi, subjenisrevisi, tahun, kodeuk, kodekeg, alasan1, nosurat, tglsurat, dokumen) values('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", 9, 9, $tahun, $kodeuk, $kodekeg, db_escape_string($latarbelakang), db_escape_string($nosurat), db_escape_string($tglsurat), db_escape_string($dokumen));			
			else
				$sql = sprintf("update {kegiatanrevisiperubahan} set alasan1='%s', nosurat='%s', tglsurat='%s', dokumen='%s' where id='%s'",  db_escape_string($latarbelakang), db_escape_string($nosurat), db_escape_string($tglsurat), db_escape_string($dokumen), db_escape_string($id));
			$res = db_query($sql);		
			
			drupal_set_message('Penyimpanan data berhasil dilakukan');	
			drupal_goto('apbd/kegiatanrevisiperubahan');	
		}
		else {
			//drupal_set_message($kodekeg);		
			drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
		}
	}
}

function pengesahanrevisi_x($kodekeg, $id) {	
	$sql = "select id, kodekeg, jenisrevisi from {kegiatanrevisiperubahan} where kodekeg='" . $kodekeg . "' and id=" . $id;			
	$res = db_query($sql); 
	while ($data = db_fetch_object($res)) {
		$jenisrevisi = $data->jenisrevisi;		
	}	
	
	$kegiatan = 'Pengesahan...';
	$revisi = variable_get('apbdrevisi', 1);
	$periode = $revisi +1;
	switch ($jenisrevisi) {
		case 1:		//Geser2
			
			drupal_set_message('4.2. Pengesahan kegiatan `' . $kegiatan . '` -> GESER...');
			
			$str = '4.2.0 Aktifkan kegiatan';
			$sql = sprintf('update kegiatanperubahan set inaktif=0, r_geser=1, periode=\'%s\' where kodekeg=\'%s\'', $periode, $kodekeg) ;
			$res = db_query($sql);
			if ($res) $str .= ' ok';
			drupal_set_message($str);
			
			reset_rekening_kegiatan($kodekeg);
			insert_rek_kegiatan($kodekeg);
			break;
			
		case 2:		//Admin
			
			drupal_set_message('4.2. Pengesahan kegiatan `' . $kegiatan . '` -> ADMIN...');
			$str = '4.2.0 Aktifkan kegiatan';
			$sql = sprintf('update kegiatanperubahan set inaktif=0, r_admin=1, periode=\'%s\' where kodekeg=\'%s\'', $periode, $kodekeg) ;
			$res = db_query($sql);
			if ($res) $str .= ' ok';
			drupal_set_message($str);

			$sql = sprintf('select lokasi, geserblokir, geserrincian, geserobyek, sumberdana, kinerja, sasaran, detiluraian, rab, triwulan from {kegiatanrevisiperubahan} where id=\'%s\'', $id) ;		
			$reskeg = db_query($sql);
			if ($datakeg = db_fetch_object($reskeg)) {
				update_kegiatan_admin($kodekeg, $datakeg->lokasi, $datakeg->geserblokir, $datakeg->geserrincian, $datakeg->geserobyek, $datakeg->sumberdana, $datakeg->kinerja, $datakeg->sasaran, $datakeg->detiluraian, $datakeg->rab, $datakeg->triwulan);
			}
			reset_rekening_kegiatan($kodekeg);
			insert_rek_kegiatan($kodekeg);
			break;
		
		case 3:		//DAK
			
			drupal_set_message('4.2. Pengesahan kegiatan `' . $kegiatan . '` -> TRANSFER...');
			update_kegiatan_dak($kodekeg);
			break;

		case 9:		//MURNI
			
			drupal_set_message('4.2. Pengesahan kegiatan `' . $kegiatan . '` -> PERUBAHAN...');
			update_kegiatan_dak($kodekeg);
			break;

		case 0:		//KEGIATAN BARU
			
			drupal_set_message('4.2. Pengesahan kegiatan `' . $kegiatan . '` -> PERUBAHAN...');
			update_kegiatan_dak($kodekeg);
			break;
			
	}
		
}

function reset_rekening_kegiatan_x($kodekeg) {
	//I. DELETE FIRST
	
	//1. Delete Sub Detil
	$str = '4.2.1 Reset sub detil perubahan';
	$sql_e = sprintf('delete from {anggperkegdetilsubperubahan} where iddetil in (select iddetil from {anggperkegdetilperubahan} where kodekeg=\'%s\')', $kodekeg);
	////drupal_set_message($sql_e);
	$res_e = db_query($sql_e);
	if ($res_e) $str .= ' ok';
	drupal_set_message($str);
	
	//2. Delete Detil
	$str = '4.2.2 Reset detil perubahan';
	$sql_e = sprintf('delete from {anggperkegdetilperubahan} where kodekeg=\'%s\'', $kodekeg);
	$res_e = db_query($sql_e);
	if ($res_e) $str .= ' ok';
	drupal_set_message($str);
	
	//3. Delete Rekening
	$str = '4.2.2 Reset rekening perubahan';
	$sql_e = sprintf('delete from {anggperkegperubahan} where kodekeg=\'%s\'', $kodekeg);
	$res_e = db_query($sql_e);
	if ($res_e) $str .= ' ok';
	drupal_set_message($str);	
}

function insert_rek_kegiatan_x($kodekeg) {
	//2. Rekening
	//drupal_set_message('4.4.1 Membaca rekening revisi...');
	
	$revisi = variable_get('apbdrevisi', 1);
	$periode = $revisi+1;
	
	$sebelumnya_inaktif = 0;
	$sql_e = sprintf('select kodekeg,inaktif from {kegiatanskpd} kodekeg=\'%s\'', $kodekeg);
	$res_r = db_query($sql_e);
	if ($data_r = db_fetch_object($res_r)) {
		$sebelumnya_inaktif = $data->inaktif;
	}
	
	//
	$sql_e = sprintf('select p.kodero, p.kodekeg, p.uraian, p.jumlah jumlahp, p.jumlahsesudah, p.jumlahsebelum, t.jumlah, p.anggaran anggaranp, t.anggaran from {anggperkegrevisi} p left join {anggperkeg} t on p.kodekeg=t.kodekeg and p.kodero=t.kodero where p.kodekeg=\'%s\'', $kodekeg);
	$res_r = db_query($sql_e);
	while ($data_r = db_fetch_object($res_r)) {
	
		$str = '4.4.1.1 Memasukkan rekening ' . $data_r->kodero . ' - ' . $data_r->uraian;
		 
		$jumlah = $data_r->jumlah;
		if ($jumlah=='') {				//DIPENETAPAN TIDAK ADA
			$jumlah = 0;
			$anggaranp = $data_r->anggaranp;
		
		} else {						//ADA DI PENETAPAN
			if ($data_r->anggaranp == 0)
				$anggaranp = $data_r->anggaran;
			else 
				$anggaranp = $data_r->anggaranp;
		}
		
		
		$str .= '(' . $anggaranp . ')';
		
		if (($jumlah+$data_r->jumlahp)>0) {
			$sql_x = sprintf("insert into {anggperkegperubahan} (kodero, kodekeg, uraian, jumlah, jumlahsesudah, jumlahsebelum, jumlahp, periode, anggaran) values ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')", $data_r->kodero, $data_r->kodekeg, $data_r->uraian, $jumlah, $data_r->jumlahsesudah, $data_r->jumlahsebelum, $data_r->jumlahp, $periode, $anggaranp);
			//drupal_set_message($sql_x);
			$res_x = db_query($sql_x);
			if ($res_x) $str .= ' ok';
			drupal_set_message($str);

			//3. Detil
			$str = '4.4.1.2 Memasukkan detil rekening';
			$sql_x = sprintf("insert into {anggperkegdetilperubahan} (iddetil, kodero, kodekeg, pengelompokan, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, nourut, periode, anggaran) select iddetil, kodero, kodekeg, pengelompokan, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, nourut, '%s', anggaran from {anggperkegdetilrevisi} where kodekeg='%s' and kodero='%s'", $periode, $kodekeg, $data_r->kodero);	
			//drupal_set_message($sql_x);
			$res_x = db_query($sql_x);
			if ($res_x) $str .= ' ok';
			drupal_set_message($str);		

			//4. Detil Sub
			$str = '4.4.1.3 Memasukkan sub detil rekening';
			$sql_x = sprintf("insert into {anggperkegdetilsubperubahan} (idsub, iddetil, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, nourut, periode, anggaran) select idsub, iddetil, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, nourut, '%s', anggaran from anggperkegdetilsubrevisi where iddetil in (select iddetil from {anggperkegdetilrevisi} where kodekeg='%s' and kodero='%s')", $periode, $kodekeg, $data_r->kodero);	
			$res_x = db_query($sql_x);
			if ($res_x) $str .= ' ok';
			drupal_set_message($str);	
			
		}	
	}

	
	if ($sebelumnya_inaktif==1) {
		$sql_x = sprintf('update {anggperkegperubahan} set jumlah=0 where kodekeg=\'%s\'', $kodekeg);
		////drupal_set_message($sql_x);
		$res_x = db_query($sql_x);
		if ($res_x) $str = 'ok';

		$sql_x = sprintf('delete from {anggperkegdetilperubahan} where kodekeg=\'%s\'', $kodekeg);
		////drupal_set_message($sql_x);
		if ($res_x) $str = 'ok';

		drupal_set_message('RESET INAKTIF');	
	}
}		

function update_kegiatan_admin_x($kodekeg, $lokasi, $geserblokir, $geserrincian, $geserobyek, $sumberdana, $kinerja, $sasaran, $detiluraian, $rab, $triwulan) {
	
	//Lokasi
	$str = '4.2.1 Update lokasi';
	if ($lokasi) {
		$sql = sprintf("update {kegiatanperubahan} kp, {kegiatanrevisi} kr set kp.lokasi=kr.lokasi where kp.kodekeg=kr.kodekeg and kp.kodekeg='%s'", $kodekeg);
		$res = db_query($sql);
		
		$str .= ' [x]';
		if ($res) $str .= ' ok';
	}
	drupal_set_message($str);

	//sumberdana
	$str = '4.2.2 Update sumber dana';
	if ($sumberdana) {
		$sql = sprintf("update {kegiatanperubahan} kp, {kegiatanrevisi} kr set kp.sumberdana1=kr.sumberdana1 where kp.kodekeg=kr.kodekeg and kp.kodekeg='%s'", $kodekeg);
		$res = db_query($sql);
		
		$str .= ' [x]';
		if ($res) $str .= ' ok';
	}
	drupal_set_message($str);

	//sasaran
	$str = '4.2.3 Update sasaran';
	if ($sasaran) {
		$sql = sprintf("update {kegiatanperubahan} kp, {kegiatanrevisi} kr set kp.kelompoksasaran=kr.kelompoksasaran where kp.kodekeg=kr.kodekeg and kp.kodekeg='%s'", $kodekeg);
		$res = db_query($sql);
		
		$str .= ' [x]';
		if ($res) $str .= ' ok';
	}
	drupal_set_message($str);

	//triwulan
	$str = '4.2.4 Update triwulan';
	if ($triwulan) {
		$sql = sprintf("update {kegiatanperubahan} kp, {kegiatanrevisi} kr set kp.tw1p=kr.tw1, kp.tw2p=kr.tw2, kp.tw3p=kr.tw3, kp.tw4p=kr.tw4 where kp.kodekeg=kr.kodekeg and kp.kodekeg='%s'", $kodekeg);

		//drupal_set_message($sql);
		$res = db_query($sql);
		
		$str .= ' [x]';
		if ($res) $str .= ' ok';
	}
	drupal_set_message($str);

	//kinerja
	$str = '4.2.5 Update kinerja';
	if ($kinerja) {
		$sql = sprintf("update {kegiatanperubahan} kp, {kegiatanrevisi} kr set kp.programsasaran=kr.programsasaran, kp.programtarget=kr.programtarget, kp.masukansasaran=kr.masukansasaran, kp.masukantarget=kr.masukantarget, kp.keluaransasaran=kr.keluaransasaran, kp.keluarantarget=kr.keluarantarget, kp.hasilsasaran=kr.hasilsasaran, kp.hasiltarget=kr.hasiltarget where kp.kodekeg=kr.kodekeg and kp.kodekeg='%s'", $kodekeg);
		$res = db_query($sql);
		
		$str .= ' [x]';
		if ($res) $str .= ' ok';
	}
	drupal_set_message($str);
	
	//rekening
	if (($geserobyek) or ($geserrincian)) {
		//drupal_set_message('4.4 Memasukkan rekening kegiatan revisi ke perubahan...');
		reset_rekening_kegiatan($kodekeg);
		insert_rek_kegiatan($kodekeg);
		
	} else {

		if (($detiluraian) or ($rab)) {
			//drupal_set_message('4.4 Memasukkan rekening kegiatan revisi ke perubahan...');
			reset_rekening_kegiatan($kodekeg);
			insert_rek_kegiatan_admin($kodekeg);
		} 	
	}	
	
}

function update_kegiatan_dak_x($kodekeg) { 

	$revisi = variable_get('apbdrevisi', 1);
	$periode = $revisi +1;
	
	reset_rekening_kegiatan($kodekeg);
	
	//3. Delete Kegiatan
	$str = '4.2.3 Reset kegiatan perubahan';
	$sql_e = sprintf('delete from kegiatanperubahan where kodekeg=\'%s\'', $kodekeg);
	$res_e = db_query($sql_e);
	if ($res_e) $str .= ' ok';
	drupal_set_message($str);

	//II. REINSERT
	//1. kegiatanperubahan
	$str = '4.2.4 Baca kegiatan di penetapan...';
	$total = 0;		//total lalu
	$tw1 = 0; $tw2 = 0; $tw3 = 0; $tw4 = 0; 
	$sql_e = sprintf('select total, tw1, tw2, tw3, tw4 from {kegiatanskpd} where kodekeg=\'%s\'', $kodekeg);	
	$res_x = db_query($sql_e);
	if ($data_x = db_fetch_object($res_x)) {
		$total = $data_x->total;
		$tw1 = $data_x->tw1;
		$tw2 = $data_x->tw2;
		$tw3 = $data_x->tw3;
		$tw4 = $data_x->tw4;
		
		$str .= 'Ada, dengan anggaran ' . apbd_fn($total);
		
	} else {
		$str .= 'Tidak Ada';
	}
	drupal_set_message($str);

	//CEK USULAN PERUBAHAN
	$totalrevisi = 0;
	$sql_e = sprintf('select total from {kegiatanrevisi} where kodekeg=\'%s\'', $kodekeg);	
	$res_x = db_query($sql_e);
	if ($data_x = db_fetch_object($res_x)) {
		$totalrevisi = $data_x->total;
		
	}	
	
	//MASUK KE DPA KALO PENETAPAN ADA DAN/ATAU PERUBAHAN ADA
	if (($total+$totalrevisi)>0 ) {
		//drupal_set_message('HOHOHO...');
		//drupal_set_message($total);
		//drupal_set_message($totalrevisi);
		$str = '4.3 Memasukkan kegiatan revisi ke perubahan';
		$sql_e = sprintf("insert into kegiatanperubahan (kodekeg, nomorkeg, jenis, tahun, kodepro, kodeuk, kegiatan, lokasi, totalsebelum, totalsesudah, total, plafon, targetsesudah, kodesuk, sumberdana1, sumberdana2, sumberdana1rp, sumberdana2rp, programsasaran, programtarget, masukansasaran, masukantarget, keluaransasaran, keluarantarget, hasilsasaran, hasiltarget, waktupelaksanaan, latarbelakang, kelompoksasaran, tw1, tw2, tw3, tw4, adminok, inaktif, isgaji, isppkd, plafonlama, dispensasi, edit, totalp, tw1p, tw2p, tw3p, tw4p, periode, r_transfer, anggaran) select kodekeg, nomorkeg, jenis, tahun, kodepro, kodeuk, kegiatan, lokasi, totalsebelum, totalsesudah, '%s', plafon, targetsesudah, kodesuk, sumberdana1, sumberdana2, sumberdana1rp, sumberdana2rp, programsasaran, programtarget, masukansasaran, masukantarget, keluaransasaran, keluarantarget, hasilsasaran, hasiltarget, waktupelaksanaan, latarbelakang, kelompoksasaran, '%s', '%s', '%s', '%s', adminok, 0 inaktif, isgaji, isppkd, plafonlama, dispensasi, edit, total totalp, tw1 tw1p, tw2 tw2p, tw3 tw3p, tw4 tw4p, '%s', 1 r_transfer, anggaran from kegiatanrevisi where kodekeg='%s'", $total, $tw1, $tw2, $tw3, $tw4, $periode, $kodekeg);
		//drupal_set_message($sql_e);
		$res_e = db_query($sql_e);
		if ($res_e) $str .= ' ok';
		drupal_set_message($str);

		//2. Rekening
		//drupal_set_message('4.4 Memasukkan rekening kegiatan revisi ke perubahan...');
		insert_rek_kegiatan($kodekeg);
	}
}


?>
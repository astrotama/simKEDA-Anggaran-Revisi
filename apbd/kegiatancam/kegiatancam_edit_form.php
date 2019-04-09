<?php
function kegiatancam_edit_form() {
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Edit Data Kegiatan Kecamatan',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $kodekeg = arg(3);
	$kodeuk = apbd_getuseruk();
	if (isSuperuser())
		$kodeuk = $_SESSION['kodeuk'];
	//drupal_add_js('files/js/common.js');
	drupal_add_js('files/js/kegiatancam.js');
	drupal_add_css('files/css/kegiatancam.css');
	drupal_set_title('Kegiatan Kecamatan');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
    $disabled = FALSE;
	$sifat=0;
	$asal = 1;
	$dekon = 0;
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);
    $allowedit = true;
    if (isset($kodekeg))
    {
        if (!user_access('kegiatancam edit'))
            drupal_access_denied();
		
		$customwhere = '';

		if (isUserKecamatan()) {
			$customwhere .= sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
		}	
			
        $sql = 'select k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodeuktujuan, k.sifat, k.kegiatan, k.lokasi, k.sasaran, k.target, k.target1, k.catatan, k.totalsebelum, k.total, k.targetsesudah, k.nilai, k.lolos, k.asal, k.kodekec, k.apbdkab, k.apbdprov, k.apbdnas, k.kodebid, k.isbantuan, k.dekon, k.apbp, k.apbn, k.kodesuk, k.totalsebelum2, k.totalsebelum3, k.totalpenetapan, k.sumberdana, k.pnpm, k.addadk, p.program from {kegiatankec} k left join {program} p on (k.kodepro = p.kodepro) where k.kodekeg=\'%s\'' . $customwhere;
        
        $res = db_query(db_rewrite_sql($sql), array ($kodekeg));
        if ($res) {
			//drupal_set_message('1');
            $data = db_fetch_object($res);
			if ($data) {    
				//drupal_set_message('2');
				$kodekeg = $data->kodekeg;
				$nomorkeg = $data->nomorkeg;
				$tahun = $data->tahun;
				$kodepro = $data->kodepro;
				$kodeuk = $data->kodeuk;
				$kodeuktujuan = $data->kodeuktujuan;
				$sifat = $data->sifat;
				$kegiatan = $data->kegiatan ;
				$lokasi = $data->lokasi;
				$sasaran = $data->sasaran;
				$target = $data->target;
				$target1 = $data->target1;
				$catatan = $data->catatan;
				$totalsebelum = $data->totalsebelum;
				$total = $data->total;
				$targetsesudah = $data->targetsesudah;
				$nilai = $data->nilai;
				$lolos = $data->lolos;
				$asal = $data->asal;
				$kodekec = $data->kodekec;
				$apbdkab = $data->apbdkab;
				$apbdprov = $data->apbdprov;
				$apbdnas = $data->apbdnas;
				$isbantuan = $data->isbantuan;
				$dekon = $data->dekon;
				$apbp = $data->apbp;
				$apbn = $data->apbn;
				$kodesuk = $data->kodesuk;
				$totalsebelum2 = $data->totalsebelum2;
				$totalsebelum3 = $data->totalsebelum3;
				$totalpenetapan = $data->totalpenetapan;
				$sumberdana = $data->sumberdana;
				$pnpm = $data->pnpm;
				$addadk = $data->addadk;
				$program = $data->program;
				$disabled =TRUE;			
			} else {
				//drupal_set_message('3');
				$kodekeg = '';
			}
        } else {
			//drupal_set_message('4');
			$kodekeg = '';
		}
    } else {
		//drupal_set_message('5');
		$form['formdata']['#title'] = 'Tambah Data Kegiatan Kecamatan';
		$kodekeg = '';
	
		if (!user_access('kegiatancam tambah'))
			drupal_access_denied();
    }
    
	//TIDAK BOLEH MENGEDIT BILA BUKAN TAHUN AKTIF
	$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
     
    //$isikegiatanpolicy = 0;
	$form['formdata']['isikegiatanpolicy']= array(
		'#type'         => 'hidden', 
		//'#title'        => 'kodekeg', 
		//'#description'  => 'kodekeg', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> variable_get("apbdkegiatan",0), 
		//'#default_value'=> $isikegiatanpolicy, 
	);
	
	$form['formdata']['formjenis']= array(
		'#type'         => 'hidden', 
		//'#title'        => 'kodekeg', 
		//'#description'  => 'kodekeg', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> 'kegiatancam', 
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
	$tipenomorkeg='hidden';
	//if ($disabled)
	//	$tipenomorkeg="textfield";

	//if (variable_get("apbdkegiatan",0)==1)			
	//	$tipenomorkeg='hidden';	
	
	$form['formdata']['nomorkeg']= array(
		'#type'         => $tipenomorkeg, 
		'#title'        => 'Nomor', 
		//'#description'  => 'kodekeg', 
		'#maxlength'    => 3, 
		'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $nomorkeg, 
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
	
	$form['formdata']['kegiatancam']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kegiatan', 
		//'#description'  => 'kegiatan', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kegiatan, 
	);
	$form['formdata']['program'] = array (
		'#type'		=> 'textarea',
		'#title'	=> 'Program',
		'#cols'		=> '80',
		'#rows'		=> '2',
		'#disabled'     => true,
		//'#attributes'	=> array('style' => 'text-align: right'),
		'#default_value' => $program,
	);
	$form['formdata']['program-val']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $program, 
	);

	$pquery = "select kodeuk, namasingkat from {unitkerja} where aktif=1 and iskecamatan=1 order by namasingkat" ;
	$pres = db_query($pquery);
	$skpd = array();
	//$dinas[''] = '--- pilih dinas teknis---';
	while ($data = db_fetch_object($pres)) {
		$skpd[$data->kodeuk] = $data->namasingkat;
	}
	$skpdtype = 'hidden';
	if (isSuperuser())
		$skpdtype='select';

	$form['formdata']['kodeuk']= array(
		'#type'         => $skpdtype, 
		'#title'        => 'SKPD',
		'#options'		=> $skpd,
		//'#description'  => 'kodeuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	); 
	
	$tipesuk = 'hidden';
	if (apbd_getuseruk() =='03')
		$tipesuk = 'select';

	$pquery = "select kodesuk, namasuk from {subunitkerja} order by namasuk" ;
	$pres = db_query($pquery);
	$subskpd = array();
	$subskpd[''] = '----Pilih Sub SKPD----';
	while ($data = db_fetch_object($pres)) {
		$subskpd[$data->kodesuk] = $data->namasuk;
	}
	
	$form['formdata']['kodesuk']= array(
		'#type'         => $tipesuk, 
		'#title'        => 'Sub SKPD',
		'#options'		=> $subskpd,
		//'#description'  => 'kodesuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodesuk, 
	); 
	
	$form['formdata']['sifat']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Sifat', 
		//'#description'  => 'sifat', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $sifat, 
	); 
	$form['formdata']['sasaran']= array(
		'#type'         => 'textarea', 
		'#title'        => 'Sasaran', 
		'#cols'		=> '40',
		'#rows'		=> '2',
		//'#disabled'     => true, 
		//'#description'  => 'sasaran', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $sasaran, 
	); 
	$form['formdata']['target']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target/Volume', 
		//'#attributes'	=> array('style' => 'text-align: right'),
		//'#size'         => 30, 
		'#default_value'=> $target, 
	);  
	//$form['formdata']['targetdes']= array(
	//	'#type'         => 'textfield', 
	//	'#title'        => 'Uraian Target', 
		//'#cols'		=> '40',
		//'#rows'		=> '2',
		//'#disabled'     => true, 
		//'#description'  => 'target', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
	//	'#default_value'=> $targetdes, 
	//); 
	$form['formdata']['lokasi'] = array (
		'#type'	=>'hidden',
		'#default_value'=>$lokasi
	);
	$form['formdata']['lokasilabel']= array(
		'#type'         => 'item', 
		'#title'        => 'Lokasi',
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
	$form['formdata']['foo1']= array(
		'#type'         => 'hidden', 
		'#default_value'=> '-', 
	);	
	$form['formdata']['nilai']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Volume', 
		//'#description'  => 'nilai', 
		//'#maxlength'    => 60, 
		'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $nilai, 
	);
	$pquery = "select kodeuk, namasingkat from {unitkerjaskpd} order by namasingkat" ;
	$pres = db_query($pquery);
	$dinas = array();
	//$dinas[''] = '--- pilih dinas teknis---';
	while ($data = db_fetch_object($pres)) {
		$dinas[$data->kodeuk] = $data->namasingkat;
	}
	
	$form['formdata']['kodeuktujuan']= array(
		'#type'         => 'select', 
		'#title'        => 'Dinas Teknis',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuktujuan, 
	); 
	
	//$form['formdata']['totalsebelum']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'totalsebelum', 
	//	'#description'  => 'totalsebelum', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $totalsebelum, 
	//); 
	//$form['formdata']['total']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'total', 
	//	'#description'  => 'total', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $total, 
	//); 
	//$form['formdata']['targetsesudah']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'targetsesudah', 
	//	'#description'  => 'targetsesudah', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $targetsesudah, 
	//); 
	$form['formdata']['lolos']= array(
		'#type'         => 'hidden', 
		'#title'        => 'lolos', 
		'#description'  => 'lolos', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $lolos, 
	); 
	$form['formdata']['asal']= array(
		'#type'         => 'hidden', 
		'#title'        => 'asal', 
		'#description'  => 'asal', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $asal, 
	); 
	$form['formdata']['kodekec']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodekec', 
		'#description'  => 'kodekec', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodekec, 
	); 
	//$form['formdata']['apbdkab']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'apbdkab', 
	//	'#description'  => 'apbdkab', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $apbdkab, 
	//); 
	//$form['formdata']['apbdprov']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'apbdprov', 
	//	'#description'  => 'apbdprov', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $apbdprov, 
	//); 
	//$form['formdata']['apbdnas']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'apbdnas', 
	//	'#description'  => 'apbdnas', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $apbdnas, 
	//);
	$form['formdata']['catatan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Catatan Penting', 
		//'#disabled'     => true, 
		//'#description'  => 'sasaran', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $catatan, 
	);	
	$options = array('0' => t('Non Bantuan'), '1' => t('Bantuan'));
	$form['formdata']['isbantuan']= array(
		'#type'         => 'select', 
		'#title'        => 'Bantuan ',
		'#options'		=> $options,
		//'#description'  => 'isbantuan', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $isbantuan, 
	); 
	$form['formdata']['dekon']= array(
		'#type'         => 'hidden', 
		'#title'        => 'dekon', 
	//	'#description'  => 'dekon', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
		'#default_value'=> $dekon, 
	); 
	//$form['formdata']['apbp']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'apbp', 
	//	'#description'  => 'apbp', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $apbp, 
	//); 
	//$form['formdata']['apbn']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'apbn', 
	//	'#description'  => 'apbn', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $apbn, 
	//); 
	//$form['formdata']['totalsebelum2']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'totalsebelum2', 
	//	'#description'  => 'totalsebelum2', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $totalsebelum2, 
	//); 
	//$form['formdata']['totalsebelum3']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'totalsebelum3', 
	//	'#description'  => 'totalsebelum3', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $totalsebelum3, 
	//); 
	//$form['formdata']['totalpenetapan']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'totalpenetapan', 
	//	'#description'  => 'totalpenetapan', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $totalpenetapan, 
	//); 
	//$form['formdata']['sumberdana']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'sumberdana', 
	//	'#description'  => 'sumberdana', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $sumberdana, 
	//); 
	//$form['formdata']['pnpm']= array(
	//	'#type'         => 'hidden', 
	//	'#title'        => 'pnpm', 
	//	'#description'  => 'pnpm', 
	//	//'#maxlength'    => 60, 
	//	//'#size'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $pnpm, 
	//);
	    $form['formdata']['anggaran'] = array (
		    '#type' => 'fieldset',
		    '#title'=> 'ISIAN ANGGARAN',
		    '#collapsible' => true,
		    '#collapsed' => false,        
	    );
		
		$totaltype = 'textfield';
	    if (isSuperuser())
		     $totaltype = 'hidden';
	    $form['formdata']['anggaran']['total']= array(
		    '#type'         => $totaltype, 
		    '#title'        => 'Anggaran Tahun ' . $tahun,
		    '#attributes'	=> array('style' => 'text-align: right'),
		    '#size'         => 30, 
		    '#default_value'=> $total, 
	    ); 
	    $totalpenetapantype = 'hidden';
		// HIDE PENETAPAN 
	     if (isSuperuser())
		     $totalpenetapantype = 'textfield';
	     $form['formdata']['anggaran']['totalpenetapan']= array(
		     '#type'         => $totalpenetapantype, 
		     '#title'        => 'Anggaran Tahun ' . $tahun, 
		     '#attributes'	=> array('style' => 'text-align: right'),
		     '#size'         => 30, 
		     '#default_value'=> $totalpenetapan, 
	     );
	    //END HIDE PENETAPAN

	    $form['formdata']['anggaran']['totalsebelum']= array(
		    '#type'         => 'textfield', 
		    '#title'        => 'Anggaran Tahun ' . ($tahun-1) , 
		    '#attributes'	=> array('style' => 'text-align: right'),
		    '#size'         => 30, 
		    '#default_value'=> $totalsebelum, 
	    ); 	    

	    $form['formdata']['sumberdana'] = array (
		    '#type' => 'fieldset',
		    '#title'=> 'SUMBER DANA APBD',
		    '#collapsible' => true,
		    '#collapsed' => false,        
	    );
	    $form['formdata']['sumberdana']['apbdkab']= array(
		    '#type'         => 'textfield', 
		    '#title'        => 'DAU/PAD/Lainnya', 
		    '#attributes'	=> array('style' => 'text-align: right'),
		    '#size'         => 30, 
		    '#default_value'=> $apbdkab, 
	    ); 
	    $form['formdata']['sumberdana']['pnpm']= array(
		    '#type'         => 'textfield', 
		    '#title'        => 'PNPM', 
		    '#attributes'	=> array('style' => 'text-align: right'),
		    '#size'         => 30, 
		    '#default_value'=> $pnpm, 
	    ); 	
	    $form['formdata']['sumberdana']['addadk']= array(
		    '#type'         => 'textfield', 
		    '#title'        => 'ADD/ADK (Alokasi Dana Desa/Kecamatan)', 
		    '#attributes'	=> array('style' => 'text-align: right'),
		    '#size'         => 30, 
		    '#default_value'=> $addadk,
			'#suffix' => "<div style='clear:left;'></div><div id='cekapbdcam'></div>",
	    ); 	
	    $form['formdata']['sumberdanadekon'] = array (
		    '#type' => 'fieldset',
		    '#title'=> 'SUMBER DANA Non APBD',
		    '#collapsible' => true,
		    '#collapsed' => false,        
	    );
	    $form['formdata']['sumberdanadekon']['apbp']= array(
		    '#type'         => 'textfield', 
		    '#title'        => 'APBD Provinsi/Sektoral', 
		    '#attributes'	=> array('style' => 'text-align: right'),
		    '#size'         => 30, 
		    '#default_value'=> $apbp, 
	    ); 
	    $form['formdata']['sumberdanadekon']['apbn']= array(
		    '#type'         => 'textfield', 
		    '#title'        => 'APBN/TP-Dekon', 
		    '#attributes'	=> array('style' => 'text-align: right'),
		    '#size'         => 30, 
		    '#default_value'=> $apbn,
			'#suffix' => "<div id='ceknonapbd'></div>",
	    ); 	

		//N+1
	    $form['formdata']['targetdepan'] = array (
		    '#type' => 'fieldset',
		    '#title'=> 'PRAKIRAAN RENCANA '  . ($tahun+1) , 
		    '#collapsible' => true,
		    '#collapsed' => false,        
	    );
	    $form['formdata']['targetdepan']['target1']= array(
		    '#type'         => 'textfield', 
		    '#title'        => 'Target/Volume', 
		    //'#size'         => 30, 
			//'#attributes'	=> array('style' => 'text-align: right'),
		    '#default_value'=> $target1, 
	    ); 
	    $form['formdata']['targetdepan']['totalsebelum2']= array(
		    '#type'         => 'textfield', 
		    '#title'        => 'Anggaran Tahun ' . ($tahun+1) , 
		    '#attributes'	=> array('style' => 'text-align: right'),
		    '#size'         => 30, 
		    '#default_value'=> $totalsebelum2, 
	    );	    //$form['formdata']['targetdepan']['targetdes1']= array(
		//    '#type'         => 'textfield', 
		//    '#title'        => 'Uraian Target', 
		//    //'#size'         => 30, 
		//    '#default_value'=> $targetdes1, 
	    //); 	

		
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

	//if (isSuperuser() or isUserKecamatan()) {
	if (isSuperuser() or $allowedit) {
		$form['formdata']['submit'] = array (
			'#type' => 'submit',
			'#suffix' => "&nbsp;<a href='/apbd/kegiatancam' class='btn_blue' style='color: white'>Batal</a>",
			'#value' => 'Simpan'
		);
	}
	
    return $form;
	
}
function kegiatancam_edit_form_validate($form, &$form_state) {

	if ($form_state['values']['kegiatancam'] == '') {
		form_set_error('', 'Nama kegiatan tidak boleh kosong');
	}

	if ($form_state['values']['catatan'] == '') {
		form_set_error('', 'Catatan harus diisi');
    }

	if ($form_state['values']['target']=='') {
		form_set_error('', 'Target harus diisi');
    }
	
	//VALIDATE JUMLAH
	$apbdkab = $form_state['values']['apbdkab'];
	$apbdprov = $form_state['values']['apbdprov'];
	$apbdnas = $form_state['values']['apbdnas'];
	
	$apbp = $form_state['values']['apbp'];
	$apbn = $form_state['values']['apbn'];

	$apbdkab 	= is_numeric($apbdkab) ? $apbdkab : 0;
	$pnpm = $form_state['values']['pnpm'];
	$addadk = $form_state['values']['addadk'];
	
	$apbp = is_numeric($apbp) ? $apbp : 0;
	$apbn = is_numeric($apbn) ? $apbn : 0;

	//$total = $form_state['values']['total'];
	//$totalpenetapan = $form_state['values']['totalpenetapan'];
	//if ($totalpenetapan == 0)
	//	$totalpenetapan = $total;
	if (isSuperuser()) {
		$totalpenetapan = $form_state['values']['totalpenetapan'];
		$total = $totalpenetapan;
	} else {
		$total = $form_state['values']['total'];
		$totalpenetapan = $total;
	}
	
	$lolos = $form_state['values']['lolos'];
	
	if ((($apbdkab + $pnpm + $addadk) * ($apbp + $apbn)) > 0) {
		form_set_error('', 'Anda harus memisahkan Kegiatan yang didanai APBD dengan Kegiatan Non APBD');
    }
	
	if (($apbdkab + $pnpm + $addadk) > 0 ) {
		if (($apbdkab + $pnpm  + $addadk) <> $totalpenetapan) {
			form_set_error('', 'Jumlah sumber dana tidak sama dengan nilai anggaran kegiatan');
		}
	}  
	if (($apbp + $apbn) > 0 ) {
		if (($apbp + $apbn) <> $totalpenetapan) {
			form_set_error('', 'Jumlah sumber dana tidak sama dengan nilai anggaran yang kegiatan');
		}
	}
	
	
	$e_kodekeg = $form_state['values']['e_kodekeg'];
	
	if ($e_kodekeg <> '') {

		$sql = sprintf("select sum(total) as totalsub from {kegiatankecsub} where kodekeg='%s'",
					   $e_kodekeg
					   );
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			
			$totalsub = $data->totalsub;
			$totalsub 	= is_numeric($totalsub) ? $totalsub : 0;
			if (($total <> $totalsub) and ($totalsub > 0)) {
				form_set_error('kegiatan', 'Jumlah usulan sub kegiatan tidak sama [' . $total . ' : ' . $totalsub . ']' );
			}
			
			//if (isSuperuser()) {
			//	if ($totalpenetapan <> $totalpenetapansub) {
			//		form_set_error('kegiatan', 'Jumlah usulan sub kegiatan tidak sama [' . $totalpenetapan . ' : ' . $totalpenetapansub . ']' );
			//	}			
			//}

		}
	}
	//END VALIDATE JUMLAH

    if ($form_state['values']['kodeuk']=='') {
		form_set_error('', 'User Id anda tidak terhubung dengan Unit Kerja/SKPD, hubungilah admin untuk mengisi kode SKPD anda.');
    }
	if ($form_state['values']['isikegiatanpolicy']==0) {
		if ($form_state['values']['kodepro']=='')
			form_set_error('', 'Program belum di isi, silahkan diisi terlebih dahulu');
	} else {
		//if ($form_state['values']['kodepro']=='')
		//	form_set_error('kegiatan', 'Kegiatan harus dipilih dari pilihan (tekanlah tombol cari), tidak boleh diisi sendiri');
		////cek di database tahun, kodeu, kodepro, kodeuk, nomorkeg tidak boleh sama
		//$sql = sprintf("select count(*) as jml from {kegiatankec} where tahun=%s and kodepro='%s' and kodeuk='%s' and nomorkeg='%s'",
		//			   $form_state['values']['tahun'],
		//			   $form_state['values']['kodepro'],
		//			   $form_state['values']['kodeuk'],
		//			   $form_state['values']['nomorkeg']
		//			   );		
		//$result = db_query($sql);
		
		//if ($data = db_fetch_object($result)) {
		//	$old = $form_state['values']['e_kodeuk'] . $form_state['values']['e_kodepro'] . $form_state['values']['e_nomorkeg'];
		//	$new = $form_state['values']['kodeuk'] . $form_state['values']['kodepro'] . $form_state['values']['nomorkeg'];

		//	$isModify = $old != $new;
		//	
		//	$num = $data->jml;
		//	if ($isModify) {
		//		if ($num>0) {
		//			form_set_error('kegiatan', 'Kegiatan ini sudah dibuat untuk SKPD tersebut' . $old . "|" . $new);
		//		}
		//	}
		//}
	}
    
}

function kegiatancam_edit_form_submit($form, &$form_state) {
    $e_kodekeg = $form_state['values']['e_kodekeg'];
    $e_kodeuk = $form_state['values']['e_kodeuk'];
    $e_kodepro = $form_state['values']['e_kodepro'];
	$isikegiatanpolicy = $form_state['values']['isikegiatanpolicy'];
    
	$kodekeg = $form_state['values']['kodekeg'];
	$nomorkeg = $form_state['values']['nomorkeg'];
	$tahun = $form_state['values']['tahun'];
	$kodepro = $form_state['values']['kodepro'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodesuk = $form_state['values']['kodesuk'];
	$kodeuktujuan = $form_state['values']['kodeuktujuan'];
	$sifat = $form_state['values']['sifat'];
	$kegiatan = $form_state['values']['kegiatancam'];
	$lokasi = $form_state['values']['lokasi'];
	$sasaran = $form_state['values']['sasaran'];
	$target = $form_state['values']['target'];
	$target1 = $form_state['values']['target1'];
	$catatan = $form_state['values']['catatan'];
	$asal = $form_state['values']['asal'];
	$kodebid = '00';
	$isbantuan = $form_state['values']['isbantuan'];
	

	$totalsebelum = $form_state['values']['totalsebelum'];
	$totalsebelum2 = $form_state['values']['totalsebelum2'];

	if (isSuperuser()) {
		$totalpenetapan = $form_state['values']['totalpenetapan'];
		$total = $totalpenetapan;
	} else {
		$total = $form_state['values']['total'];
		$totalpenetapan = $total;
	}
	
	$lolos = $form_state['values']['lolos'];

	$apbdkab = $form_state['values']['apbdkab'];
	$pnpm = $form_state['values']['pnpm'];
	$addadk = $form_state['values']['addadk'];
	
	$apbp = $form_state['values']['apbp'];
	$apbn = $form_state['values']['apbn'];
	
	$dekon = 0;
	if (($apbp + $apbn)> 0)
		$dekon = 1;
	
	$sumberdana ='';
	if ((is_numeric($apbdkab)) && ($apbdkab>0))
		$sumberdana = 'APBD+';
	if ((is_numeric($pnpm)) && ($pnpm>0))
		$sumberdana .= 'PNPM+';		
	if ((is_numeric($addadk)) && ($addadk>0))
		$sumberdana .= 'ADD/ADK+';

	if (strlen($sumberdana)>0)
		$sumberdana = substr($sumberdana, 0, strlen($sumberdana)-1);
		    
    if ($e_kodekeg=='')
    {
		//$kodeuk = apbd_getuseruk();		
		//$tahun = '2012';
		$kodekeg = $tahun . $kodeuk . $kodepro ;
		//if ($isikegiatanpolicy==0)			
		$nomorkeg =apbd_getcounterkec($kodekeg);
		$kodekeg .= apbd_getcounterkegiatan($kodekeg);
		$sql = sprintf("insert into {kegiatankec} (kodekeg, nomorkeg, tahun, kodepro, kodeuk, kodekec, kodeuktujuan, kodesuk, sifat, kegiatan, lokasi, sasaran, target, target1, catatan, total, totalsebelum, totalsebelum2, totalpenetapan, lolos, asal, apbdkab, pnpm,addadk, apbp, apbn, dekon, sumberdana, kodebid, isbantuan) values('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
						$kodekeg, $nomorkeg, $tahun, $kodepro, $kodeuk, $kodeuk, $kodeuktujuan, $kodesuk, $sifat,
						db_escape_string($kegiatan),
						db_escape_string($lokasi),
						db_escape_string($sasaran),
						db_escape_string($target),
						db_escape_string($target1),
						db_escape_string($catatan),
						db_escape_string($total),
						db_escape_string($totalsebelum),
						db_escape_string($totalsebelum2),
						db_escape_string($totalpenetapan),					  
						db_escape_string($lolos),
						db_escape_string($asal),
						db_escape_string($apbdkab),					  
						db_escape_string($pnpm),
						db_escape_string($addadk),
						db_escape_string($apbp),
						db_escape_string($apbn),
						db_escape_string($dekon),
						db_escape_string($sumberdana),
						db_escape_string($kodebid),
						$isbantuan
					  );
		//drupal_set_message($sql);
		$res = db_query($sql);
    } else {
		
		//$tahun='2012';
		if (($e_kodepro == $kodepro) && ($e_kodeuk == $kodeuk)) {
			$kodekeg = $e_kodekeg;
		} else {
			$kodekeg = $tahun . $kodeuk . $kodepro;
			$kodekeg .= apbd_getcounterkegiatan($kodekeg);
			
		}
		$sql = sprintf("update {kegiatankec} set kodekeg='%s', nomorkeg='%s', kodepro='%s', kodeuk='%s', kodeuktujuan='%s', kodesuk='%s', kegiatan='%s', lokasi ='%s', sasaran='%s', target='%s', target1='%s', catatan='%s', total='%s', totalsebelum ='%s', totalsebelum2='%s', totalpenetapan='%s', lolos='%s', apbdkab='%s', pnpm='%s',addadk='%s', apbp='%s', apbn='%s', sumberdana='%s', kodebid='%s', isbantuan='%s' where kodekeg='%s'",
					   $kodekeg,
					   db_escape_string($nomorkeg),					   
					   $kodepro,
					   $kodeuk,
						$kodeuktujuan,
						$kodesuk,
						db_escape_string($kegiatan),
						db_escape_string($lokasi),
						db_escape_string($sasaran),
						db_escape_string($target),					  
						db_escape_string($target1),					  
						db_escape_string($catatan),
						db_escape_string($total),
						db_escape_string($totalsebelum),
						db_escape_string($totalsebelum2),
						db_escape_string($totalpenetapan),					  
						db_escape_string($lolos),					  
						db_escape_string($apbdkab),					  
						db_escape_string($pnpm),
						db_escape_string($addadk),
						db_escape_string($apbp),
						db_escape_string($apbn),
						db_escape_string($sumberdana),
						db_escape_string($kodebid),
						db_escape_string($isbantuan),
						$e_kodekeg);		
		$res = db_query($sql);
    }
    if ($res) {
        drupal_set_message('Penyimpanan data berhasil dilakukan');		
    }
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');

	$_SESSION['kodeuk'] = $kodeuk;
    drupal_goto('apbd/kegiatancam');    
}
?>
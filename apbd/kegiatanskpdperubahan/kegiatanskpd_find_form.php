<?php
// $Id$

function kegiatanskpd_find_form() {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);	
    drupal_add_css('files/css/kegiatancam.css');
	//drupal_add_js('files/js/kegiatancam.js');
    drupal_set_title('Cari Kegiatan');

	$sumberdana = $_SESSION['carisumberdana'];
	$statusisi = $_SESSION['caristatusisi'];
	$statustw = $_SESSION['caristatustw'];
	$statusinaktif = $_SESSION['caristatusinaktif'];
	$jenis = $_SESSION['carijenis'];
	$plafon = $_SESSION['cariplafon'];

	$kegiatan = $_SESSION['carikegiatan'];
	$rekening = $_SESSION['carirekening'];
	$rincian = $_SESSION['caririncian'];
	
	if (!isSuperuser()) {
		
		$kodesuk = $_SESSION['carikodesuk'];
		
		$typeuk = 'hidden';
		$kodeuk = apbd_getuseruk();
		
		$typesuk ='select';

		$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
		$pquery = sprintf('select kodesuk, namasuk from {subunitkerja} where kodeuk=\'%s\' order by kodesuk', $kodeuk);
		
		//drupal_set_message($pquery);
		
		$pres = db_query($pquery);
		$subskpd = array();
		$subskpd[''] = '- Pilih Bidang -';
		while ($data = db_fetch_object($pres)) {
			$subskpd[$data->kodesuk] = $data->namasuk;
		}

		if (isUserKecamatan()) {
			$typesuk='hidden';
			$kodesuk = apbd_getusersuk();
		} else
			$typesuk='select';
		
	} else {
		
		$kodeuk = $_SESSION['carikodeuk'];
		
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		$dinas['00'] ='00000 - SEMUA SKPD';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}
		
		$typeuk='select';
		$typesuk='hidden';
	}
	 
	$form['kodeuk']= array(
		'#type'         => $typeuk, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
		'#weight' => 0,
	);

	$form['kodesuk']= array(
		'#type'         => $typesuk, 
		'#title'        => 'Bidang/Bagian',
		'#options'		=> $subskpd,
		//'#description'  => 'kodesuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodesuk, 
		'#weight' => 1,
	); 

    $form['kegiatan'] = array (
        '#type' => 'textfield',
        '#title' => 'Nama Kegiatan',
		'#size' => 70, 
        '#description' => 'kegiatan yang akan dicari',
        '#autocomplete_path' => 'apbd/kegiatanskpd/utils_auto/uraian',
		'#default_value'=> $kegiatan, 
		'#weight' => 2,
    );
	
	$pquery = "select sumberdana from {sumberdanalt} order by nomor" ;
	$pres = db_query($pquery);
	$sumberdanaotp = array();
	$sumberdanaotp[''] = '- SEMUA -';
	while ($data = db_fetch_object($pres)) {
		$sumberdanaotp[$data->sumberdana] = $data->sumberdana;
	}
	$form['sumberdana']= array(
		'#type'         => 'select', 
		'#title'        => 'Sumber Dana', 
		'#options'		=> $sumberdanaotp,
		'#width'         => 30, 
		'#default_value'=> $sumberdana, 
		'#weight' => 3,
	);

	$form['jenis']= array(
		'#type' => 'radios', 
		'#title' => t('Jenis'), 
		'#default_value' => '',
		'#options' => array(	
			 '' => t('Semua'), 	
			 'gaji' => t('Gaji'), 	
			 'langsung' => t('Langsung'),
			 'ppkd' => t('PPKD'),	
		   ),
		'#default_value'=> $jenis, 
		'#weight' => 4,		
	);	
	
	$form['ssj'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 5,
	);		
 	
	$form['statusisi']= array(
		'#type' => 'radios', 
		'#title' => t('Pengisian'), 
		'#default_value' => '',
		'#options' => array(	
			 '' => t('Semua'), 	
			 'sudah' => t('Selesai'), 	
			 'sebagian' => t('Sebagian'),
			 'belum' => t('Belum'),	
			 'lebih' => t('Lebih Plafon'),	
		   ),
		'#default_value'=> $statusisi, 		   
		'#weight' => 6,		
	);	

	$form['ss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 7,
	);		
	$form['plafon']= array(
		'#type' => 'radios', 
		'#title' => t('Alokasi Anggaran'), 
		'#default_value' => $plafon,
		'#options' => array(	
			 '' => t('Semua'), 	
			 'new' => t('Usulan Baru'), 	
			 'up' => t('Naik'),
			 'down' => t('Turun'),	
			 'still' => t('Tetap'),	
		   ),
		'#weight' => 8,		
	);	

	$form['ss1p'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 9,
	);		
	$form['statustw']= array(
		'#type' => 'radios', 
		'#title' => t('Tri Wulan'), 
		'#default_value' => '',
		'#options' => array(	
			 '' => t('Semua'), 	
			 'sudah' => t('Sudah'), 	
			 'belum' => t('Belum'),	
		   ),
		'#default_value'=> $statustw, 		   
		'#weight' => 10,		
	);		
	$form['ss2'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 11,
	);		
	
	if (isSuperuser()) {
		$statusinaktiftype = 'radios';
	} else {
		$statusinaktiftype = 'hidden';
		$statusinaktif = '0';
	}
	
	$form['statusinaktif']= array(
		'#type' => $statusinaktiftype, 
		'#title' => t('Status'), 
		'#default_value' => 0,
		'#options' => array(	
			 '' => t('Semua'), 	
			 '0' => t('Aktif'),	
			 '1' => t('Inaktif'), 	
			 '2' => t('Perpanjang'),
		   ),
		'#default_value'=> $statusinaktif, 		   
		'#weight' => 12,		
	);		
	
	$form['sskeg'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 13,
	);	 

	$form['rekening']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Rekening', 
		//'#description'  => 'Rekening belanja', 
		'#maxlength'    => 255, 
		'#size'         => 70, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraian',
		'#default_value'=> $rekening, 
		'#weight' => 14,
	); 
	
	$form['rincian']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Rincian Belanja', 
		//'#description'  => 'Rekening belanja', 
		'#maxlength'    => 255, 
		'#size'         => 70, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraian',
		'#default_value'=> $rincian, 
		'#weight' => 15,
	); 
	
    $form['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpdperubahan' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Cari',
		'#weight' => 16,
    );
    return $form;
}
function kegiatanskpd_find_form_validate($form, &$form_state) {

}
function kegiatanskpd_find_form_submit($form, &$form_state) {
	$sumberdana = $form_state['values']['sumberdana'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodesuk = $form_state['values']['kodesuk'];
	$kegiatan = $form_state['values']['kegiatan'];
	$statusisi = $form_state['values']['statusisi'];
	$statustw = $form_state['values']['statustw'];
	$statusinaktif = $form_state['values']['statusinaktif'];
	$jenis = $form_state['values']['jenis'];
	$plafon = $form_state['values']['plafon'];
	
	$tahun= $form_state['values']['tahun'];

	$rekening = $form_state['values']['rekening'];
	$rincian = $form_state['values']['rincian'];

	$_SESSION['carisumberdana'] = $sumberdana;
	$_SESSION['caristatusisi'] = $statusisi;
	$_SESSION['caristatustw'] = $statustw;
	$_SESSION['caristatusinaktif'] = $statusinaktif;
	$_SESSION['carijenis'] = $jenis;
	$_SESSION['cariplafon'] = $plafon;

	$_SESSION['carikegiatan'] = $kegiatan;
	$_SESSION['carirekening'] = $rekening;
	$_SESSION['caririncian'] = $rincian;
	
	if (isSuperuser()) 
		$_SESSION['carikodeuk'] = $kodeuk;
	else
		$_SESSION['carikodesuk'] = $kodesuk;
	

	//$uri = 'apbd/kegiatanskpdperubahan/filter/' . $kodeuk . '/' . $sumberdana . '/' . $statusisi . '/' . $kodesuk . '/'. $statustw . '/' . $statusinaktif . '/' . $jenis;
	//drupal_goto($uri);
	
	$uri = 'apbd/kegiatanskpdperubahan/filter/' . $kodeuk . '/' . $sumberdana . '/' . $statusisi . '/' . $kodesuk . '/'. $statustw . '/' . $statusinaktif . '/' . $jenis . '/' . $plafon . '/' . $kegiatan . '/' . $rekening . '/' . $rincian ;
	drupal_goto($uri);
}

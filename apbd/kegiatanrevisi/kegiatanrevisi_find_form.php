<?php
// $Id$

function kegiatanrevisi_find_form() {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);

	$jenisrevisi = $_SESSION['jenisrevisicari'];
	$status = $_SESSION['statusrevisicari'];
	$sumberdana = $_SESSION['sumberdana'];
	$kegrevisicari = $_SESSION['kegrevisicari'];
	
	drupal_add_css('files/css/kegiatancam.css');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Pencarian Revisi',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	
	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		
	} else {
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		$dinas['00'] ='00000 - SEMUA SKPD';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}
		$type='select';
		
		$kodeuk = $_SESSION['kodeukrevisicari'];

	}
	 
	$form['formdata']['kodeuk']= array(
		'#type'         => $type, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	);
	

	$form['formdata']['jenisrevisi']= array(
		'#type' => 'radios', 
		'#title' => t('Jenis Revisi'), 
		'#default_value' => $jenisrevisi,
		'#options' => array(	
			 '0' => t('Semua'), 	
			 '1' => t('[1] Pergeseran'), 	
			 '2' => t('[2] Administrasi'), 	
			 '3' => t('[3] Dana Transfer'),	
			 '4' => t('[4] Darurat'),	
		   ),
	);	

	$form['formdata']['ss'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);	
	$form['formdata']['status']= array(
		'#type' => 'radios', 
		'#title' => t('Status'), 
		'#default_value' => $status,
		'#options' => array(	
			 '100' => t('Semua'),
			 '0' => t('Usulan'), 	
			 '1' => t('Disetujui'), 	
			 '9' => t('Ditolak'),	
			 '999' => t('Perpanjang'),
		   ),
	);		
	$form['formdata']['ss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);		
		
	$pquery = "select sumberdana from {sumberdanalt} order by nomor" ;
	$pres = db_query($pquery);
	$sumberdanaotp = array();
	$sumberdanaotp[''] = '- SEMUA -';
	while ($data = db_fetch_object($pres)) {
		$sumberdanaotp[$data->sumberdana] = $data->sumberdana;
	}
	$form['formdata']['sumberdana']= array(
		'#type'         => 'select', 
		'#title'        => 'Sumber Dana', 
		'#options'		=> $sumberdanaotp,
		'#width'         => 30, 
		'#default_value'=> $sumberdana, 
	);
    $form['formdata']['kegiatan'] = array (
        '#type' => 'textfield',
        '#title' => 'Revisi Dicari',
        '#description' => 'Revisi kegiatan yang akan dicari',
        //'#autocomplete_path' => 'apbd/kegiatancam/utils_auto/uraian',
		'#default_value' => $kegrevisicari,
    );
	
	$form['formdata']['ss2'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);		
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
        '#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Batal</a>",
        '#value' => 'Cari',
    );
    return $form;
}
function kegiatanrevisi_find_form_validate($form, &$form_state) {

}
function kegiatanrevisi_find_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$jenisrevisi = $form_state['values']['jenisrevisi'];
	$status = $form_state['values']['status'];	
	$sumberdana = $form_state['values']['sumberdana'];	
    $kegrevisicari =$form_state['values']['kegiatan'];
	
	$_SESSION['jenisrevisicari'] = $jenisrevisi;
	$_SESSION['statusrevisicari'] = $status;
	$_SESSION['kegrevisicari'] = $kegrevisicari;
	$_SESSION['sumberdana'] = $sumberdana;
	
	if (isSuperuser()) 
		$_SESSION['kodeukrevisicari'] = $kodeuk;
	
	
    drupal_goto('apbd/kegiatanrevisi/show/'  . $kodeuk . '/' . $jenisrevisi . '/' . $status . '/' . $sumberdana .'/'. $kegrevisicari);
}

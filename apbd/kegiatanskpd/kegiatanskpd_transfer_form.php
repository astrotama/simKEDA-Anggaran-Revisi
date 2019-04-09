<?php

function kegiatanskpd_transfer($arg=NULL) {
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
	$tahun = 2016;
	
	if ($arg) {
		
	}
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/transfer.js');

	//if (isSuperuser())
	$filterform = drupal_get_form('kegiatanskpd_transfer_filter_form');
		
	$output = "<div id='dv_transferform'>" .$filterform . "</div>". "<div id='dv_transferdata'>" . $output . "</div>";
    return $output;
}
function kegiatanskpd_transfer_filter_form() {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Filter Data Kegiatan',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	$form['formdata']['kind'] = array (
		'#type'		=> 'hidden',
		'#default_value' => "kegiatancam",
	);
	
	$filter = arg(2);
	if (isset($filter) && ($filter=='filter')) {
		$kodeuk = arg(3);
		$sumberdana = arg(4);
	}
  
	//Kecamatan
	$pquery = "select kodeuk, namasingkat from {unitkerja} where aktif=1 and iskecamatan=1 order by namasingkat" ;
	$pres = db_query($pquery);
	$dinas = array();
	$dinas['00'] ='--- DARI SEMUA MUSRENBANGCAM ---';
	while ($data = db_fetch_object($pres)) {
		$dinas[$data->kodeuk] = $data->namasingkat;
	}	
	$form['formdata']['kodeuk']= array(
		'#type'         => 'select', 
		'#title'        => 'Musrenbangcam',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> '00',
		'#weight' => 1,
	);

	//Teknis
	$pquery = "select kodeuk, namasingkat from {unitkerjaskpd} where aktif=1 order by namasingkat" ;
	$pres = db_query($pquery);
	$skpd = array();
	$skpd['00'] = '-------- KE SEMUA SKPD --------';
	while ($data = db_fetch_object($pres)) {
		$skpd[$data->kodeuk] = $data->namasingkat;
	}		
	$type='select';
	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		//drupal_set_message('user kec');
	}
	$form['formdata']['kodeuktujuan']= array(
		'#type'         => $type, 
		'#title'        => 'SKPD Tujuan',
		'#options'	=> $skpd,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk,  
		'#weight' => 2,
	);

	$form['formdata']['filterdata'] = array(
		'#type' => 'radios', 
		'#title' => t('Filter Data'), 
		'#default_value' => 0, 
		'#options' => array(t('Data Musrenbangcam yang belum ditransfer'), t('Semua Data (Retransfer)')),
		'#weight' => 3,
	);	
	$form['formdata']['ss'] = array (
		'#weight' => 4,
		'#value' => "<div style='clear:both;'></div>",
	);
	
	//$form['formdata']['btntransfer'] = array (
	//	'#weight' => 5,
	//	'#value' => "<a href='#transfermusrenbangcam' class='btn_blue' style='color: white;'>Transfer Data</a>",
	//);
	$form['formdata']['indicator'] = array (
		'#weight' => 6,
		'#value' => "<div id='indicator' style='display:none;'></div>",
	);

	$form['formdata']['submit'] = array (
		'#type' => 'hidden',
		'#value' => 'Proses',
		'#weight' => 5,
	);
	
	return $form;
}

?>
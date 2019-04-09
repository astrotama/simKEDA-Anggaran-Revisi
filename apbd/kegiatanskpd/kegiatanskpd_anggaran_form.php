<?php
function kegiatanskpd_anggaran_form(){

    $kodekeg = arg(3);
	if (!isset($kodekeg))
		drupal_access_denied();

	if (!user_access('kegiatanskpd anggaran'))
		drupal_access_denied();

	
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Data Kegiatan Renja SKPD - [' . $kodekeg . ']',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );

	drupal_add_js('files/js/kegiatancam.js');
	drupal_add_css('files/css/kegiatancam.css');
	drupal_set_title('Kegiatan Renja SKPD - Isian Anggaran Kegiatan');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
	
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);

	$customwhere = sprintf(' and k.tahun=%s ', $tahun);	
	if (!isSuperuser()) {
		$customwhere .= sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
	}	
	$sql = 'select k.kodekeg, k.tahun, k.kodepro, k.kodeuk, k.kodeuktujuan, k.sifat, k.kegiatan, k.lokasi, k.sasaran, k.target, k.totalsebelum, k.total, k.targetsesudah, k.nilai, k.lolos, k.asal, k.kodekec, k.apbdkab, k.apbdprov, k.apbdnas, k.kodebid, k.dekon, k.apbp, k.apbn, k.kodesuk, k.totalsebelum2, k.totalsebelum3, k.totalpenetapan, k.sumberdana, k.pnpm, p.program, concat_ws(\' \', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi   from {kegiatanskpd} k left join {program} p on (k.kodepro = p.kodepro) left join {unitkerja} u on (k.kodeuk=u.kodeuk) where k.kodekeg=\'%s\'' . $customwhere;
	$res = db_query(db_rewrite_sql($sql), array ($kodekeg));
	if ($res) {
		$data = db_fetch_object($res);
		if ($data) {
			
			$kodekeg = $data->kodekeg;
			$tahun = $data->tahun;
			$kodepro = $data->kodepro;
			$kodeuk = $data->kodeuk;
			$kodeuktujuan = $data->kodeuktujuan;
			$sifat = $data->sifat;
			$kegiatan = $data->kegiatan . '&nbsp;&nbsp;' . l('Sub Kegiatan', 'apbd/kegiatanskpd/subkegiatan/' . $kodekeg, array('html'=>TRUE, 'attributes'=>array('class'=>'btn_blue', 'style'=>'color:#ffffff;')));
			$lokasi = $data->lokasi;
			$sasaran = $data->sasaran;
			$target = $data->target;
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
			$kodebid = $data->kodebid;
			$dekon = $data->dekon;
			$apbp = $data->apbp;
			$apbn = $data->apbn;
			$kodesuk = $data->kodesuk;
			$totalsebelum2 = $data->totalsebelum2;
			$totalsebelum3 = $data->totalsebelum3;
			$totalpenetapan = $data->totalpenetapan;
			$sumberdana = $data->sumberdana;
			$pnpm = $data->pnpm;
			$program = $data->program;
			$disabled =TRUE;
			
			$form['formdata']['#title'] = 'Data Kegiatan Renja SKPD - [' . $data->koderesmi . ']';
			$form['formdata']['kodekeg']= array(
				'#type'         => 'hidden', 
				'#title'        => 'kodekeg', 
				'#default_value'=> $kodekeg, 
			);
			
			$form['formdata']['kegiatan']= array(
				'#type'         => 'item', 
				'#title'        => 'Kegiatan', 
				'#value'=> $kegiatan, 
			);
			$form['formdata']['program'] = array (
				'#type'		=> 'item',
				'#title'	=> 'Program',
				'#value' => $program,
			);
			$form['formdata']['sasaran']= array(
				'#type'         => 'item', 
				'#title'        => 'Sasaran', 
				'#value'=> $sasaran . '&nbsp;', 
			); 
			$form['formdata']['target']= array(
				'#type'         => 'item', 
				'#title'        => 'Target/Volume', 
				'#value'=> $target . '&nbsp;', 
			);
			$pquery = sprintf("select kodeuk, namasingkat from unitkerja where kodeuk='%s'", db_escape_string($kodeuktujuan)) ;
			$pres = db_query($pquery);
			//$dinas = array();
			if($data = db_fetch_object($pres)) {
				$dinas = $data->namasingkat;
			}
			$form['formdata']['kodeuktujuan']= array(
				'#type'         => 'item', 
				'#title'        => 'Dinas Teknis',
				'#value'=> $dinas . '&nbsp;', 
			); 
			$form['formdata']['anggaran'] = array (
				'#type' => 'fieldset',
				'#title'=> 'ISIAN ANGGARAN',
				'#collapsible' => true,
				'#collapsed' => false,        
			);
			$form['formdata']['anggaran']['total']= array(
				'#type'         => 'textfield', 
				'#title'        => 'Usulan Tahun ' . $tahun,
				'#attributes'	=> array('style' => 'text-align: right'),
				'#size'         => 30, 
				'#default_value'=> $total, 
			); 
			$form['formdata']['anggaran']['totalsebelum']= array(
				'#type'         => 'textfield', 
				'#title'        => 'Anggaran Tahun N-1(' . ($tahun-1) . ')', 
				'#attributes'	=> array('style' => 'text-align: right'),
				'#size'         => 30, 
				'#default_value'=> $totalsebelum, 
			); 
			$form['formdata']['anggaran']['totalsebelum2']= array(
				'#type'         => 'textfield', 
				'#title'        => 'Anggaran Tahun N+1(' . ($tahun+1) . ')', 
				'#attributes'	=> array('style' => 'text-align: right'),
				'#size'         => 30, 
				'#default_value'=> $totalsebelum2, 
			);
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
			
			$totalpenetapantype = 'hidden';
			//HIDE PENETAPAN
			if (isSuperuser())
				$totalpenetapantype = 'textfield';			
			$form['formdata']['anggaran']['totalpenetapan']= array(
				'#type'         => $totalpenetapantype, 
				'#title'        => 'Anggaran Disetujui', 
				'#attributes'	=> array('style' => 'text-align: right'),
				'#size'         => 30, 
				'#default_value'=> $totalpenetapan, 
			);
			//END HIDE PENETAPAN
			
			$form['formdata']['sumberdanaapbd'] = array (
				'#type' => 'fieldset',
				'#title'=> 'SUMBER DANA APBD',
				'#collapsible' => true,
				'#collapsed' => false,        
			);
			$form['formdata']['sumberdanaapbd']['apbdkab']= array(
				'#type'         => 'textfield', 
				'#title'        => 'DAU/PAD/Lainnya', 
				'#attributes'	=> array('style' => 'text-align: right'),
				'#size'         => 30, 
				'#default_value'=> $apbdkab, 
			); 
			$form['formdata']['sumberdanaapbd']['apbdprov']= array(
				'#type'         => 'textfield', 
				'#title'        => 'Banprov', 
				'#attributes'	=> array('style' => 'text-align: right'),
				'#size'         => 30, 
				'#default_value'=> $apbdprov, 
			); 	
			$form['formdata']['sumberdanaapbd']['apbdnas']= array(
				'#type'         => 'textfield', 
				'#title'        => 'DAK', 
				'#attributes'	=> array('style' => 'text-align: right'),
				'#size'         => 30, 
				'#default_value'=> $apbdnas,
				'#suffix' => "<div id='cekapbd'></div>",
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

			$form['formdata']['e_kodekeg']= array( 
				'#type'         => 'hidden', 
				'#default_value'=> $kodekeg, 
			); 
			
			$form['formdata']['submit'] = array (
				'#type' => 'submit',
				'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd' class='btn_blue' style='color: white'>Batal</a>",
				'#value' => 'Simpan'
			);
		}
	}
	return $form;
}
function kegiatanskpd_anggaran_form_validate($form, &$form_state) {
	$total = $form_state['values']['total'];
	$totalsebelum = $form_state['values']['totalsebelum'];
	$totalsebelum2 = $form_state['values']['totalsebelum2'];
	$totalpenetapan = $form_state['values']['totalpenetapan'];
	$apbd = $form_state['values']['apbdkab'];
	$pnpm = $form_state['values']['pnpm'];
	
	//if ($total != $apbd + $pnpm) {            
		//form_set_error('', 'Total Usualan harus sama dengan jumlah dari APBD + PNPM');
	//}            
}
function kegiatanskpd_anggaran_form_submit($form, &$form_state) {
    $e_kodekeg = $form_state['values']['e_kodekeg'];	    
	$kodekeg = $form_state['values']['kodekeg'];
	$total = $form_state['values']['total'];
	$totalsebelum = $form_state['values']['totalsebelum'];
	$totalsebelum2 = $form_state['values']['totalsebelum2'];
	$totalpenetapan = $form_state['values']['totalpenetapan'];
	$lolos = $form_state['values']['lolos'];
	$apbdkab = $form_state['values']['apbdkab'];
	$apbdprov = $form_state['values']['apbdprov'];
	$apbdnas = $form_state['values']['apbdnas'];
	
	$apbp = $form_state['values']['apbp'];
	$apbn = $form_state['values']['apbn'];

	$apbdkab 	= is_numeric($apbdkab) ? $apbdkab : 0;
	$apbdprov 	= is_numeric($apbdprov) ? $apbdprov : 0;
	$apbdnas 	= is_numeric($apbdnas) ? $apbdnas : 0;
	
	$apbp = is_numeric($apbp) ? $apbp : 0;
	$apbn = is_numeric($apbn) ? $apbn : 0;

	$dekon = 0;
	if (($apbp + $apbn)> 0)
		$dekon = 1;
	
	$sumberdana ='';
	if ($dekon==1) {
		if ($apbp > 0)
			$sumberdana .= 'APBD Provinsi+';
		if ($apbn >0)
			$sumberdana .= 'APBN+';
	} else {
		if ($apbdkab>0)
			$sumberdana .= 'APBD+';
		if ($apbdprov>0)
			$sumberdana .= 'Banprov+';
		if ($apbdnas>0)
			$sumberdana .= 'DAK+';
	}
	if (strlen($sumberdana)>0)
		$sumberdana = substr($sumberdana, 0, strlen($sumberdana)-1);



	$sql = sprintf("update {kegiatanskpd} set total='%s', totalsebelum ='%s', totalsebelum2='%s', totalpenetapan='%s', lolos='%s', apbdkab='%s', apbdprov='%s', apbdnas='%s', apbp='%s', apbn='%s', dekon='%s', sumberdana='%s'  where kodekeg='%s'",
				  db_escape_string($total),
				  db_escape_string($totalsebelum),
				  db_escape_string($totalsebelum2),
				  db_escape_string($totalpenetapan),					  
				  db_escape_string($lolos),					  
				  db_escape_string($apbdkab),					  
				  db_escape_string($apbdprov),
				  db_escape_string($apbdnas),
				  db_escape_string($apbp),
				  db_escape_string($apbn),
				  $dekon,
				  $sumberdana,
				   $e_kodekeg);		
	$res = db_query($sql);
    if ($res) {
        drupal_set_message('Penyimpanan data berhasil dilakukan');		
    }
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');



	drupal_goto('apbd/kegiatanskpd');  
}
?>
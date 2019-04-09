<?php
    
function subdetil_edit_form(){
    drupal_add_css('files/css/kegiatancam.css');	
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Mengisi Sub Detil Rekening',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	$kodekeg = arg(6);
	$kodero = arg(7);
	$iddetil = arg(8);
	$idsub = arg(9);
	
	//drupal_set_message($iddetil);
	//drupal_set_message($kodero);
	
	$title = 'Sub Detil Rincian Rekening ';
	if (isset($iddetil)) {
        $sql = 'select uraian from {anggperkegdetil} where {iddetil}=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($iddetil));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) 
				$title .= $data->uraian;
		
		}
		
	}
	
	$title =l($title, 'apbd/kegiatanskpd/rekening/detil/subdetil/' . $kodekeg . '/' . $kodero . '/' . $iddetil, array('html'=>true));	
	drupal_set_title($title);
	
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
    $disabled = FALSE;
	$totalsubdetillama=0;
    if (isset($idsub))
    {
        if (!user_access('kegiatanskpd edit'))
            drupal_access_denied();
			
        $sql = 'select idsub,iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,
		volumsatuan,harga,total from {anggperkegdetilsub} where idsub=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($idsub));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$iddetil = $data->iddetil;
				$idsub = $data->idsub;
				$uraian = $data->uraian;
				
				$unitjumlah = $data->unitjumlah;
				$unitsatuan = $data->unitsatuan;
				$volumjumlah = $data->volumjumlah;
				$volumsatuan = $data->volumsatuan;
				$harga = $data->harga;
					
				$totalsubdetillama = $unitjumlah * $volumjumlah * $harga;
				
                $disabled =TRUE;
			} else 
				$idsub = '';
			
        } else 
			$idsub = '';
		
    } else {
		if (!user_access('kegiatanskpd tambah'))
			drupal_access_denied();
		//$form['formdata']['#title'] = 'Tambah Detil Rekening Kegiatan';
		$idsub = '';
	}
    
	$form['formdata']['kodekeg']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodekeg', 
		'#default_value'=> $kodekeg, 
	); 
	$form['formdata']['kodero']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodero', 
		'#default_value'=> $kodero, 
	); 
	$form['formdata']['iddetil']= array(
		'#type'         => 'hidden', 
		'#title'        => 'iddetil', 
		'#default_value'=> $iddetil, 
	); 
	$form['formdata']['idsub']= array(
		'#type'         => 'hidden', 
		'#title'        => 'idsub', 
		'#default_value'=> $idsub, 
	); 
	$form['formdata']['totalsubdetillama']= array(
		'#type'         => 'hidden', 
		'#title'        => 'totalsubdetillama', 
		'#default_value'=> $totalsubdetillama, 
	); 

	if ($idsub == '') {
		$form['formdata']['detil1'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Sub Detil Rekening',
			'#collapsible' => true,
			'#collapsed' => false,        
		);

		$form['formdata']['detil1']['uraian1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 70, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraianbelanja',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil1']['unitjumlah1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil1']['unitsatuan1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil1']['volumjumlah1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil1']['volumsatuan1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		
		$form['formdata']['detil1']['harga1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '', 
		); 

		$form['formdata']['detil2'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Detil Rekening',
			'#collapsible' => true,
			'#collapsed' => false,        
		);

		$form['formdata']['detil2']['uraian2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 70, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraianbelanja',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil2']['unitjumlah2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil2']['unitsatuan2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil2']['volumjumlah2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil2']['volumsatuan2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		
		$form['formdata']['detil2']['harga2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '', 
		); 		

		$form['formdata']['detil3'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Detil Rekening',
			'#collapsible' => true,
			'#collapsed' => false,        
		);

		$form['formdata']['detil3']['uraian3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 70, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraianbelanja',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil3']['unitjumlah3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil3']['unitsatuan3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil3']['volumjumlah3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil3']['volumsatuan3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		
		$form['formdata']['detil3']['harga3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '', 
		); 	
		
		$form['formdata']['detil4'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Detil Rekening',
			'#collapsible' => true,
			'#collapsed' => true,        
		);

		$form['formdata']['detil4']['uraian4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 70, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraianbelanja',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil4']['unitjumlah4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil4']['unitsatuan4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil4']['volumjumlah4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil4']['volumsatuan4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		
		$form['formdata']['detil4']['harga4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '', 
		); 		

		$form['formdata']['detil5'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Detil Rekening',
			'#collapsible' => true,
			'#collapsed' => true,        
		);

		$form['formdata']['detil5']['uraian5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 70, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraianbelanja',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil5']['unitjumlah5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil5']['unitsatuan5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil5']['volumjumlah5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil5']['volumsatuan5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		
		$form['formdata']['detil5']['harga5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '', 
		); 		
	
	//Sudah ada isinyam edit mode satu record
	} else {

		$form['formdata']['uraian1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 70, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraianbelanja',
			'#default_value'=> $uraian, 
		); 
		$form['formdata']['unitjumlah1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> $unitjumlah, 
		); 
		$form['formdata']['unitsatuan1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> $unitsatuan, 
		); 
		$form['formdata']['volumjumlah1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> $volumjumlah, 
		); 
		$form['formdata']['volumsatuan1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> $volumsatuan, 
		); 
		
		$form['formdata']['harga1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> $harga, 
		); 	
		
		//PREV
		$sql = 'select idsub from {anggperkegdetilsub} where iddetil=\'%s\' and idsub<\'%s\' order by iddetil desc limit 1';
		$res = db_query(db_rewrite_sql($sql), array ($iddetil, $idsub));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {
				$prevkode=$data->idsub;

				$form['formdata']['prevkode']= array(
					'#type'         => 'hidden', 
					'#default_value'=> $prevkode, 
				);				
				
				
				$form['formdata']['submitprev'] = array (
					'#type' => 'submit',
					'#value' => '<<',
				); 
				
			}
		}	
		
		//NEXT
		$sql = 'select idsub from {anggperkegdetilsub} where iddetil=\'%s\' and idsub>\'%s\' order by idsub limit 1';
		$res = db_query(db_rewrite_sql($sql), array ($iddetil, $idsub));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {
				$nextkode=$data->idsub;

				$form['formdata']['nextkode']= array(
					'#type'         => 'hidden', 
					'#default_value'=> $nextkode, 
				);				

				$form['formdata']['submitnext'] = array (
					'#type' => 'submit',
					'#value' => '>>',
				);
				
			}
		}		
	}

	$form['formdata']['submitlist'] = array (
		'#type' => 'submit',
		'#value' => 'Daftar Sub Detil',
	);	
	
	
    $form['formdata']['e_idsub']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $idsub, 
    ); 

	$form['formdata']['submitnew'] = array (
		'#type' => 'submit',
		'#value' => 'Tambah Sub Detil',
	);	
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd/rekening/detil/subdetil/" . $kodekeg . '/' . $kodero . '/' . $iddetil . "' class='btn_blue' style='color: white'>Tutup</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function subdetil_edit_form_validate($form, &$form_state) {
	$kodekeg = $form_state['values']['kodekeg'];
	$kodero = $form_state['values']['kodero'];
	$iddetil = $form_state['values']['iddetil'];
	$e_idsub = $form_state['values']['e_idsub'];	
	$seribu = 1000;
		 
	if ($e_idsub=='') {
		
	
		//Hitung detilnya dulu
		$uraian1 = $form_state['values']['uraian1'];
		if ($uraian1 != '') {
			$unitjumlah1 = $form_state['values']['unitjumlah1'];
			$volumjumlah1 = $form_state['values']['volumjumlah1'];
			$harga1 = $form_state['values']['harga1'];
			
			$total1 = $unitjumlah1 * $volumjumlah1 * $harga1;
			$totalrekeningbaru = $total1;
		}
		//CEK PER 1000
		//if (($total1 % $seribu)>0) form_set_error('', 'Isian detil rekening #1 tidak bulat per seribu');
		
		$uraian2 = $form_state['values']['uraian2'];
		if ($uraian2 != '') {
			$unitjumlah2 = $form_state['values']['unitjumlah2'];
			$volumjumlah2 = $form_state['values']['volumjumlah2'];
			$harga2 = $form_state['values']['harga2'];
			
			$total2 = $unitjumlah2 * $volumjumlah2 * $harga2;
			$totalrekeningbaru += $total2;
		}
		//CEK PER 1000
		//if (($total2 % $seribu)>0) form_set_error('', 'Isian detil rekening #2 tidak bulat per seribu');

		$uraian3 = $form_state['values']['uraian3'];
		if ($uraian3 != '') {
			$unitjumlah3 = $form_state['values']['unitjumlah3'];
			$volumjumlah3 = $form_state['values']['volumjumlah3'];
			$harga3 = $form_state['values']['harga3'];
			
			$total3 = $unitjumlah3 * $volumjumlah3 * $harga3;
			$totalrekeningbaru += $total3;
		}
		//CEK PER 1000
		//if (($total3 % $seribu)>0) form_set_error('', 'Isian detil rekening #3 tidak bulat per seribu');

		$uraian4 = $form_state['values']['uraian4'];
		if ($uraian4 != '') {
			$unitjumlah4 = $form_state['values']['unitjumlah4'];
			$volumjumlah4 = $form_state['values']['volumjumlah4'];
			$harga4 = $form_state['values']['harga4'];
			
			$total4 = $unitjumlah4 * $volumjumlah4 * $harga4;
			$totalrekeningbaru += $total4;
		}
		//CEK PER 1000
		//if (($total4 % $seribu)>0) form_set_error('', 'Isian detil rekening #4 tidak bulat per seribu');

		$uraian5 = $form_state['values']['uraian5'];
		if ($uraian5 != '') {
			$unitjumlah5 = $form_state['values']['unitjumlah5'];
			$volumjumlah5 = $form_state['values']['volumjumlah5'];
			$harga5 = $form_state['values']['harga5'];
			
			$total5 = $unitjumlah5 * $volumjumlah5 * $harga5;
			$totalrekeningbaru += $total5;
		}
		//CEK PER 1000
		//if (($total5 % $seribu)>0) form_set_error('', 'Isian detil rekening #5 tidak bulat per seribu');

		$jumlahrek =0;
		$sql = sprintf("select sum(total) as jumlahrek from {anggperkegdetil} where kodekeg='%s' and kodero='%s'",
			   $kodekeg, $kodero);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {		
			$jumlahrek = $data->jumlahrek;
		}
		//CEK PER 1000
		if ((($jumlahrek+$totalrekeningbaru) % $seribu)>0) form_set_error('', 'Jumlah isian detil rekening dari sebelumnya ' . apbd_fn($jumlahrek) . ' ditambah dengan ' . apbd_fn($totalrekeningbaru) . ' menjadi tidak bulat per seribu');
		
		//CEK Plafon
		/*
		$sql = sprintf("select total,plafon from {kegiatanskpd} where kodekeg='%s'", $kodekeg);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {		
			$total = $data->total;
			$plafon = $data->plafon;
		}
		
		if (($total+$totalrekeningbaru)>$plafon) {		
			form_set_error('', 'Isian rekening melebihi plafon, Plafon : ' . apbd_fn($plafon) . 
							   ', Sudah Masuk : ' . apbd_fn($total) . ', Isian Baru : ' . apbd_fn($totalrekeningbaru) );
		}		
		*/
		
	} else {

		$totalsubdetillama = $form_state['values']['totalsubdetillama'];

		$unitjumlah = $form_state['values']['unitjumlah1'];
		$volumjumlah = $form_state['values']['volumjumlah1'];
		$harga = $form_state['values']['harga1'];
		
		$totaldetilbaru  = $unitjumlah * $volumjumlah * $harga;

		$jumlahrek =0;
		$sql = sprintf("select sum(total) as jumlahrek from {anggperkegdetil} where kodekeg='%s' and kodero='%s'",
			   $kodekeg, $kodero);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {		
			$jumlahrek = $data->jumlahrek;
		}
		//CEK PER 1000
		//drupal_set_message($jumlahrek . ' - ' . $totaldetilbaru . ' - ' . $totaldetillama);
		//drupal_set_message($jumlahrek+$totaldetilbaru-$totaldetillama);
		if ((($jumlahrek+$totaldetilbaru-$totaldetillama) % $seribu)>0) form_set_error('', 'Jumlah isian detil rekening setelah diganti dengan ' . apbd_fn($totaldetilbaru) . ', menjadi ' . apbd_fn($jumlahrek+$totaldetilbaru-$totaldetillama) . '  yang tidak bulat per seribu');	
		
		//CEK Plafon
		/*
		$sql = sprintf("select total,plafon,jenis from {kegiatanskpd} where kodekeg='%s'", $kodekeg);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {		
			$total = $data->total;
			$plafon = $data->plafon;
			$jenis = $data->jenis;
		}		
		
		if (($total-$totalsubdetillama+$totaldetilbaru)>$plafon) {		
			form_set_error('', 'Isian rekening melebihi plafon, Plafon : ' . apbd_fn($plafon) . 
							   ', Mengganti : ' . apbd_fn($totalsubdetillama) . ' menjadi ' . apbd_fn($totaldetilbaru) . ' menyebabkan total anggaran menjadi ' . apbd_fn($total-$totalsubdetillama+$totaldetilbaru));
		}			
		*/
	}

}
function subdetil_edit_form_submit($form, &$form_state) {

    $kodekeg = $form_state['values']['kodekeg'];
	$kodero = $form_state['values']['kodero'];
    $iddetil = $form_state['values']['iddetil'];
    
    if($form_state['clicked_button']['#value'] == $form_state['values']['submitnext']) {
		$nextkode = $form_state['values']['nextkode'];
        $form_state['redirect'] = 'apbd/kegiatanskpd/rekening/detil/subdetil/edit/' . $kodekeg . '/' . $kodero . '/' . $iddetil . '/' . $nextkode ;
		//drupal_set_message('Next');

	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitprev']) {
		$prevkode = $form_state['values']['prevkode'];
        $form_state['redirect'] = 'apbd/kegiatanskpd/rekening/detil/subdetil/edit/' . $kodekeg . '/' . $kodero . '/' . $iddetil . '/' . $prevkode ;
		//drupal_set_message('Next');

	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitlist']) {
        $form_state['redirect'] = 'apbd/kegiatanskpd/rekening/detil/subdetil/' . $kodekeg . '/' . $kodero . '/' . $iddetil;
		//drupal_set_message('Next');		

		
    } else {

		$e_idsub = $form_state['values']['e_idsub'];	
		$idsub = $form_state['values']['idsub'];
		
	   
		if ($e_idsub=='')
		{
			
			//Prepare sql
			$sql = 'insert into {anggperkegdetilsub} (iddetil, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        

			$uraian1 = $form_state['values']['uraian1'];
			if ($uraian1 != '') {
				$unitjumlah1 = $form_state['values']['unitjumlah1'];
				$unitsatuan1 = $form_state['values']['unitsatuan1'];
				$volumjumlah1 = $form_state['values']['volumjumlah1'];
				$volumsatuan1 = $form_state['values']['volumsatuan1'];
				$harga1 = $form_state['values']['harga1'];
				
				$res = db_query(db_rewrite_sql($sql), array($iddetil, $uraian1, $unitjumlah1, $unitsatuan1, $volumjumlah1, $volumsatuan1, $harga1, 
						$unitjumlah1 * $volumjumlah1 * $harga1));	
				
			}
			
			//1 sukses, simpan 2
			if ($res) {
				$uraian2 = $form_state['values']['uraian2'];
				if ($uraian2 != '') {
					$unitjumlah2 = $form_state['values']['unitjumlah2'];
					$unitsatuan2 = $form_state['values']['unitsatuan2'];
					$volumjumlah2 = $form_state['values']['volumjumlah2'];
					$volumsatuan2 = $form_state['values']['volumsatuan2'];
					$harga2 = $form_state['values']['harga2'];
					
					$res = db_query(db_rewrite_sql($sql), array($iddetil, $uraian2, $unitjumlah2, $unitsatuan2, $volumjumlah2, $volumsatuan2, $harga2, 
							$unitjumlah2 * $volumjumlah2 * $harga2));	
					
				}
			}

			//2 sukses, simpan 3
			if ($res) {
				$uraian3 = $form_state['values']['uraian3'];
				if ($uraian3 != '') {
					$unitjumlah3 = $form_state['values']['unitjumlah3'];
					$unitsatuan3 = $form_state['values']['unitsatuan3'];
					$volumjumlah3 = $form_state['values']['volumjumlah3'];
					$volumsatuan3 = $form_state['values']['volumsatuan3'];
					$harga3 = $form_state['values']['harga3'];
					
					$res = db_query(db_rewrite_sql($sql), array($iddetil, $uraian3, $unitjumlah3, $unitsatuan3, $volumjumlah3, $volumsatuan3, $harga3, 
							$unitjumlah3 * $volumjumlah3 * $harga3));	
					
				}
			}

			//3 sukses, simpan 4
			if ($res) {
				$uraian4 = $form_state['values']['uraian4'];
				if ($uraian4 != '') {
					$unitjumlah4 = $form_state['values']['unitjumlah4'];
					$unitsatuan4 = $form_state['values']['unitsatuan4'];
					$volumjumlah4 = $form_state['values']['volumjumlah4'];
					$volumsatuan4 = $form_state['values']['volumsatuan4'];
					$harga4 = $form_state['values']['harga4'];
					
					$res = db_query(db_rewrite_sql($sql), array($iddetil, $uraian4, $unitjumlah4, $unitsatuan4, $volumjumlah4, $volumsatuan4, $harga4, 
							$unitjumlah4 * $volumjumlah4 * $harga4));	
					
				}
			}

			//4 sukses, simpan 5
			if ($res) {
				$uraian5 = $form_state['values']['uraian5'];
				if ($uraian5 != '') {
					$unitjumlah5 = $form_state['values']['unitjumlah5'];
					$unitsatuan5 = $form_state['values']['unitsatuan5'];
					$volumjumlah5 = $form_state['values']['volumjumlah5'];
					$volumsatuan5 = $form_state['values']['volumsatuan5'];
					$harga5 = $form_state['values']['harga5'];
					
					$res = db_query(db_rewrite_sql($sql), array($iddetil, $uraian5, $unitjumlah5, $unitsatuan5, $volumjumlah5, $volumsatuan5, $harga5, 
							$unitjumlah5 * $volumjumlah5 * $harga5));	
					
				}
			}

			
		} else {
			
			//Edit satu detil
			$uraian = $form_state['values']['uraian1'];
			$unitjumlah = $form_state['values']['unitjumlah1'];
			$unitsatuan = $form_state['values']['unitsatuan1'];
			$volumjumlah = $form_state['values']['volumjumlah1'];
			$volumsatuan = $form_state['values']['volumsatuan1'];
			$harga = $form_state['values']['harga1'];
			
			$total  = $unitjumlah * $volumjumlah * $harga;
			
			$sql = 'update {anggperkegdetilsub} set  uraian=\'%s\', unitjumlah=\'%s\', unitsatuan=\'%s\', volumjumlah=\'%s\', volumsatuan=\'%s\', harga=\'%s\', total=\'%s\' 
			where idsub=\'%s\' ';
			$res = db_query(db_rewrite_sql($sql), array($uraian, $unitjumlah, $unitsatuan, $volumjumlah, $volumsatuan, $harga, $total, $e_idsub));

		}
		
		//UPDATE JUMLAH DETIL
		if ($res) {
			
			$sql = sprintf("select sum(total) as jumlahrek from {anggperkegdetilsub} where iddetil='%s'",
				   $iddetil);
			$result = db_query($sql);
			if ($data = db_fetch_object($result)) {		
				$jumlahrek = $data->jumlahrek;
				
				$sql = sprintf("update {anggperkegdetil} set total='%s' where iddetil='%s'",
						db_escape_string($jumlahrek),
						$iddetil);		
				$res = db_query($sql);
				
			}
		}
	
		//UPDATE JUMLAH REKENING DETIL
		if ($res) {
			
			$sql = sprintf("select sum(total) as jumlahrek from {anggperkegdetil} where kodekeg='%s' and kodero='%s'",
				   $kodekeg, $kodero);
			$result = db_query($sql);
			if ($data = db_fetch_object($result)) {		
				$jumlahrek = $data->jumlahrek;
				
				$sql = sprintf("update {anggperkeg} set jumlah='%s' where kodekeg='%s' and kodero='%s'",
						db_escape_string($jumlahrek),
						db_escape_string($kodekeg),
						$kodero);		
				$res = db_query($sql);
				
			}
		}	
		//UPDATE JUMLAH KEGIATAN
		$sql = sprintf("select sum(jumlah) as jumlahsub from {anggperkeg} where kodekeg='%s'", $kodekeg);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {		
			$jumlahsub = $data->jumlahsub;
			
			$sql = sprintf("update {kegiatanskpd} set total='%s' where kodekeg='%s'", db_escape_string($jumlahsub), $kodekeg);		
			$res = db_query($sql);
			
		}
		
		if($form_state['clicked_button']['#value'] == $form_state['values']['submitnew']) {
			$form_state['redirect'] = 'apbd/kegiatanskpd/rekening/detil/subdetil/edit/' . $kodekeg . '/' . $kodero . '/' . $iddetil ;

		} else {
			if ($res)
				drupal_set_message('Penyimpanan data berhasil dilakukan');
			else
				drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
			drupal_goto('apbd/kegiatanskpd/rekening/detil/subdetil/' . $kodekeg . '/' . $kodero . '/' . $iddetil);    
		}
	}
}
?>
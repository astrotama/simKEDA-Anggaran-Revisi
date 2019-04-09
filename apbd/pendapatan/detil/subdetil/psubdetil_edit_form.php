<?php
     
function psubdetil_edit_form(){
    drupal_add_css('files/css/kegiatancam.css');	
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Mengisi Sub Detil Rekening',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    ); 
	$kodeuk = arg(5);
	$kodero = arg(6);
	$iddetil = arg(7);
	$idsub = arg(8);
	
	//drupal_set_message($iddetil);
	//drupal_set_message($idsub);
	
	$title = 'Sub Detil Rincian Rekening ';
	if (isset($iddetil)) {
        $sql = 'select uraian from {anggperukdetil} where {iddetil}=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($iddetil));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) 
				$title .= $data->uraian;
		
		}
		
	}

	
	$allowedit = (batastgl() || (isSuperuser()));	

	if ($allowedit==false) {
		//dispensasippas
		//$sqluk = sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
        $sql = sprintf('select dispensasippas from {unitkerja} where kodeuk=\'%s\'', apbd_getuseruk());
		$res = db_query($sql);
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {  		
				$allowedit = $data->dispensasippas;
			}
		}
	}
	
	$title =l($title, 'apbd/pendapatan/detil/subdetil/' . $kodeuk . '/' . $kodero . '/' . $iddetil, array('html'=>true));	
	drupal_set_title($title);
	
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
    $disabled = FALSE;
	$totalsubdetillama=0;
    if (isset($idsub))
    {
        if (!user_access('kegiatanskpd edit'))
            drupal_access_denied();
			
        $sql = 'select idsub,iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,
		volumsatuan,harga,total from {anggperukdetilsub} where idsub=\'%s\'';
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
    
	$form['formdata']['kodeuk']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodeuk', 
		'#default_value'=> $kodeuk, 
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
		$sql = 'select idsub from {anggperukdetilsub} where iddetil=\'%s\' and idsub<\'%s\' order by iddetil desc limit 1';
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
		$sql = 'select idsub from {anggperukdetilsub} where iddetil=\'%s\' and idsub>\'%s\' order by idsub limit 1';
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

	if ($allowedit) {
		$form['formdata']['submitnew'] = array (
			'#type' => 'submit',
			'#value' => 'Tambah Sub Detil',
		);	
		
		$form['formdata']['submit'] = array (
			'#type' => 'submit',
			'#suffix' => "&nbsp;<a href='/apbd/pendapatan/detil/subdetil/" . $kodeuk . '/' . $kodero . '/' . $iddetil . "' class='btn_blue' style='color: white'>Tutup</a>",
			'#value' => 'Simpan'
		);
	}
	
    return $form;
}
function psubdetil_edit_form_validate($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$kodero = $form_state['values']['kodero'];
	$iddetil = $form_state['values']['iddetil'];
	$e_idsub = $form_state['values']['e_idsub'];	
		 
	$seribu = 1000;

	//Hitung detilnya dulu
	$uraian1 = $form_state['values']['uraian1'];
	if ($uraian1 != '') {
		$unitjumlah1 = $form_state['values']['unitjumlah1'];
		$volumjumlah1 = $form_state['values']['volumjumlah1'];
		$harga1 = $form_state['values']['harga1'];
		
		$total1 = $unitjumlah1 * $volumjumlah1 * $harga1;
	}
	//CEK PER 1000
	if (($total1 % $seribu)>0) form_set_error('', 'Isian detil rekening #1 tidak bulat per seribu');
	
	$uraian2 = $form_state['values']['uraian2'];
	if ($uraian2 != '') {
		$unitjumlah2 = $form_state['values']['unitjumlah2'];
		$volumjumlah2 = $form_state['values']['volumjumlah2'];
		$harga2 = $form_state['values']['harga2'];
		
		$total2 = $unitjumlah2 * $volumjumlah2 * $harga2;
	}
	//CEK PER 1000
	if (($total2 % $seribu)>0) form_set_error('', 'Isian detil rekening #2 tidak bulat per seribu');

	$uraian3 = $form_state['values']['uraian3'];
	if ($uraian3 != '') {
		$unitjumlah3 = $form_state['values']['unitjumlah3'];
		$volumjumlah3 = $form_state['values']['volumjumlah3'];
		$harga3 = $form_state['values']['harga3'];
		
		$total3 = $unitjumlah3 * $volumjumlah3 * $harga3;
	}
	//CEK PER 1000
	if (($total3 % $seribu)>0) form_set_error('', 'Isian detil rekening #3 tidak bulat per seribu');

	$uraian4 = $form_state['values']['uraian4'];
	if ($uraian4 != '') {
		$unitjumlah4 = $form_state['values']['unitjumlah4'];
		$volumjumlah4 = $form_state['values']['volumjumlah4'];
		$harga4 = $form_state['values']['harga4'];
		
		$total4 = $unitjumlah4 * $volumjumlah4 * $harga4;
	}
	//CEK PER 1000
	if (($total4 % $seribu)>0) form_set_error('', 'Isian detil rekening #4 tidak bulat per seribu');

	$uraian5 = $form_state['values']['uraian5'];
	if ($uraian5 != '') {
		$unitjumlah5 = $form_state['values']['unitjumlah5'];
		$volumjumlah5 = $form_state['values']['volumjumlah5'];
		$harga5 = $form_state['values']['harga5'];
		
		$total5 = $unitjumlah5 * $volumjumlah5 * $harga5;
	}
	//CEK PER 1000
	if (($total5 % $seribu)>0) form_set_error('', 'Isian detil rekening #5 tidak bulat per seribu');
		
}
function psubdetil_edit_form_submit($form, &$form_state) {

    $kodeuk = $form_state['values']['kodeuk'];
	$kodero = $form_state['values']['kodero'];
    $iddetil = $form_state['values']['iddetil'];
    
    if($form_state['clicked_button']['#value'] == $form_state['values']['submitnext']) {
		$nextkode = $form_state['values']['nextkode'];
        $form_state['redirect'] = 'apbd/pendapatan/detil/subdetil/edit/' . $kodeuk . '/' . $kodero . '/' . $iddetil . '/' . $nextkode ;
		//drupal_set_message('Next');

	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitprev']) {
		$prevkode = $form_state['values']['prevkode'];
        $form_state['redirect'] = 'apbd/pendapatan/detil/subdetil/edit/' . $kodeuk . '/' . $kodero . '/' . $iddetil . '/' . $prevkode ;
		//drupal_set_message('Next');

	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitlist']) {
        $form_state['redirect'] = 'apbd/pendapatan/detil/subdetil/' . $kodeuk . '/' . $kodero . '/' . $iddetil;
		//drupal_set_message('Next');		

		
    } else {

		$e_idsub = $form_state['values']['e_idsub'];	
		$idsub = $form_state['values']['idsub'];		
	   
		if ($e_idsub=='')
		{
			
			//Prepare sql
			$sql = 'insert into {anggperukdetilsub} (iddetil, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        

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
			
			$sql = 'update {anggperukdetilsub} set  uraian=\'%s\', unitjumlah=\'%s\', unitsatuan=\'%s\', volumjumlah=\'%s\', volumsatuan=\'%s\', harga=\'%s\', total=\'%s\' 
			where idsub=\'%s\' ';
			$res = db_query(db_rewrite_sql($sql), array($uraian, $unitjumlah, $unitsatuan, $volumjumlah, $volumsatuan, $harga, $total, $e_idsub));

		}
		
		//UPDATE JUMLAH DETIL
		if ($res) {
			
			$sql = sprintf("select sum(total) as jumlahrek from {anggperukdetilsub} where iddetil='%s'",
				   $iddetil);
			$result = db_query($sql);
			if ($data = db_fetch_object($result)) {		
				$jumlahrek = $data->jumlahrek;
				
				$sql = sprintf("update {anggperukdetil} set total='%s' where iddetil='%s'",
						db_escape_string($jumlahrek),
						$iddetil);		
				$res = db_query($sql);
				
			}
		}
	
		//UPDATE JUMLAH REKENING DETIL
		if ($res) {
			
			$sql = sprintf("select sum(total) as jumlahrek from {anggperukdetil} where kodeuk='%s' and kodero='%s'",
				   $kodeuk, $kodero);
			$result = db_query($sql);
			if ($data = db_fetch_object($result)) {		
				$jumlahrek = $data->jumlahrek;
				
				$sql = sprintf("update {anggperuk} set jumlah='%s' where kodeuk='%s' and kodero='%s'",
						db_escape_string($jumlahrek),
						db_escape_string($kodeuk),
						$kodero);		
				$res = db_query($sql);
				
			}
		}	

		if($form_state['clicked_button']['#value'] == $form_state['values']['submitnew']) {
			$form_state['redirect'] = 'apbd/pendapatan/detil/subdetil/edit/' . $kodeuk . '/' . $kodero . '/' . $iddetil ;

		} else {
			if ($res)
				drupal_set_message('Penyimpanan data berhasil dilakukan');
			else
				drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
			drupal_goto('apbd/pendapatan/detil/subdetil/' . $kodeuk . '/' . $kodero . '/' . $iddetil);    
		}
	}
}
?>
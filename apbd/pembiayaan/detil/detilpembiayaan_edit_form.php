<?php
    
function detilpembiayaan_edit_form(){
    drupal_add_css('files/css/kegiatancam.css');	
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Mengisi Detil Rekening',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $kodero = arg(4);
	$iddetil = arg(5);

	//drupal_set_message($iddetil);
	$tahun = variable_get('apbdtahun', 0);
	
	$title = 'Detil Rekening Pembiayaan ';
	if (isset($kodero)) {
        $sql = 'select uraian from {rincianobyek} where {kodero}=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($kodero));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) 
				$title .= $data->uraian;
		
		}
		
	}
	
	$title =l($title, 'apbd/pembiayaan/detil/' . $kodero, array('html'=>true));	
	drupal_set_title($title);
	
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($iddetil))
    {
        if (!user_access('kegiatanrkpd edit'))
            drupal_access_denied();
			
        $sql = 'select iddetil,tahun,kodeuk,kodero,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total 
		   from {anggperdadetil} where iddetil=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($iddetil));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$iddetil = $data->iddetil;
				$kodeuk = $data->kodeuk;
				$kodero = $data->kodero;
				
				$uraian = $data->uraian;
				$unitjumlah = $data->unitjumlah;
				$unitsatuan = $data->unitsatuan;
				$volumjumlah = $data->volumjumlah;
				$volumsatuan = $data->volumsatuan;
				$harga = $data->harga;
				
                $disabled =TRUE;
			} else 
				$iddetil = '';
			
        } else 
			$iddetil = '';
		
    } else {
		if (!user_access('kegiatanrkpd tambah'))
			drupal_access_denied();
		//$form['formdata']['#title'] = 'Tambah Detil Rekening Kegiatan';
		$iddetil = '';
	}
    
	
	$form['formdata']['iddetil']= array(
		'#type'         => 'hidden', 
		'#title'        => 'iddetil', 
		'#default_value'=> $iddetil, 
	); 
	$form['formdata']['kodero']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodero', 
		'#default_value'=> $kodero, 
	); 
	$form['formdata']['kodeuk']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodeuk', 
		'#default_value'=> $kodeuk, 
	); 
	$form['formdata']['tahun']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodeuk', 
		'#default_value'=> $tahun, 
	); 
	
	if ($iddetil == '') {

		$form['formdata']['detil1'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Detil Rekening',
			'#collapsible' => true,
			'#collapsed' => false,        
		);

		$form['formdata']['detil1']['uraian1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 60, 
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
			'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
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
			'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
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
			'#size'         => 60, 
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
			'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
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
			'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
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
			'#size'         => 60, 
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
			'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
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
			'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',			
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
			'#size'         => 60, 
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
			'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
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
			'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
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
			'#size'         => 60, 
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
			'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
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
			'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
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

		$form['formdata']['uraian']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 60, 
			'#default_value'=> $uraian, 
		); 
		$form['formdata']['unitjumlah']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> $unitjumlah, 
		); 
		$form['formdata']['unitsatuan']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#size'         => 30, 
			'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> $unitsatuan, 
		); 
		$form['formdata']['volumjumlah']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> $volumjumlah, 
		); 
		$form['formdata']['volumsatuan']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#size'         => 30, 
			'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> $volumsatuan, 
		); 
		
		$form['formdata']['harga']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> $harga, 
		); 	
		
		//PREV
		$sql = 'select iddetil from {anggperdadetil} where tahun=\'%s\' and kodero=\'%s\' and iddetil<\'%s\' order by iddetil desc limit 1';
		$res = db_query(db_rewrite_sql($sql), array ($tahun, $kodero, $iddetil));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {
				$prevkode=$data->iddetil;

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
		$sql = 'select iddetil from {anggperdadetil} where tahun=\'%s\' and kodero=\'%s\' and iddetil>\'%s\' order by iddetil limit 1';
		$res = db_query(db_rewrite_sql($sql), array ($tahun, $kodero, $iddetil));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {
				$nextkode=$data->iddetil;

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
	
	
    $form['formdata']['e_iddetil']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $iddetil, 
    ); 

	$form['formdata']['submitnew'] = array (
		'#type' => 'submit',
		'#value' => 'Tambah Detil',
	);	
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/pembiayaan/detil/" . $kodero . "' class='btn_blue' style='color: white'>Tutup</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function detilpembiayaan_edit_form_validate($form, &$form_state) {
//$id = arg(3);
//    if (!isset($id)) {
//        if (strlen($form_state['values']['id']) < 8 ) {
//            form_set_error('', 'id harus terdiri atas 8 karakter');
//        }            
//    }
}
function detilpembiayaan_edit_form_submit($form, &$form_state) {

	$tahun = $form_state['values']['tahun'];
	$kodero = $form_state['values']['kodero'];
    
    if($form_state['clicked_button']['#value'] == $form_state['values']['submitnext']) {
		$nextkode = $form_state['values']['nextkode'];
        $form_state['redirect'] = 'apbd/pembiayaan/detil/edit/' . $kodero . '/' . $nextkode ;
		//drupal_set_message('Next');

	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitprev']) {
		$prevkode = $form_state['values']['prevkode'];
        $form_state['redirect'] = 'apbd/pembiayaan/detil/edit/' . $kodero . '/' . $prevkode ;
		//drupal_set_message('Next');

    } else {

		$e_iddetil = $form_state['values']['e_iddetil'];	
		$iddetil = $form_state['values']['iddetil'];
		
	   
		if ($e_iddetil=='')
		{
			
			//Prepare sql
			$sql = 'insert into {anggperdadetil} (tahun, kodero, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        

			$uraian1 = $form_state['values']['uraian1'];
			if ($uraian1 != '') {
				$unitjumlah1 = $form_state['values']['unitjumlah1'];
				$unitsatuan1 = $form_state['values']['unitsatuan1'];
				$volumjumlah1 = $form_state['values']['volumjumlah1'];
				$volumsatuan1 = $form_state['values']['volumsatuan1'];
				$harga1 = $form_state['values']['harga1'];
				
				$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero,$uraian1, $unitjumlah1, $unitsatuan1, $volumjumlah1, $volumsatuan1, $harga1, 
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
					
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero,$uraian2, $unitjumlah2, $unitsatuan2, $volumjumlah2, $volumsatuan2, $harga2, 
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
					
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero,$uraian3, $unitjumlah3, $unitsatuan3, $volumjumlah3, $volumsatuan3, $harga3, 
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
					
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero,$uraian4, $unitjumlah4, $unitsatuan4, $volumjumlah4, $volumsatuan4, $harga4, 
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
					
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero,$uraian5, $unitjumlah5, $unitsatuan5, $volumjumlah5, $volumsatuan5, $harga5, 
							$unitjumlah5 * $volumjumlah5 * $harga5));	
					
				}
			}

			
		} else {
			
			//Edit satu detil
			$uraian = $form_state['values']['uraian'];
			$unitjumlah = $form_state['values']['unitjumlah'];
			$unitsatuan = $form_state['values']['unitsatuan'];
			$volumjumlah = $form_state['values']['volumjumlah'];
			$volumsatuan = $form_state['values']['volumsatuan'];
			$harga = $form_state['values']['harga'];
			
			$total  = $unitjumlah * $volumjumlah * $harga;
			
			$sql = 'update {anggperdadetil} set  uraian=\'%s\', unitjumlah=\'%s\', unitsatuan=\'%s\', volumjumlah=\'%s\', volumsatuan=\'%s\', 
				   harga=\'%s\', total=\'%s\' where iddetil=\'%s\' ';
			$res = db_query(db_rewrite_sql($sql), array($uraian, $unitjumlah, $unitsatuan, $volumjumlah, $volumsatuan, $harga, $total, $e_iddetil));
		}
		
		//UPDATE JUMLAH REKENING
		if ($res) {
			
			
			$sql = sprintf("select sum(total) as jumlahrek from {anggperdadetil} where tahun='%s' and kodero='%s'",				   $tahun, $kodero);
			$result = db_query($sql);
			if ($data = db_fetch_object($result)) {		
				$jumlahrek = $data->jumlahrek;
				
				$sql = sprintf("update {anggperda} set jumlah='%s' where tahun='%s' and kodero='%s'",
						db_escape_string($jumlahrek),
						db_escape_string($tahun),
						$kodero);		
				$res = db_query($sql);
				
			}
		}
		
		if($form_state['clicked_button']['#value'] == $form_state['values']['submitnew']) {
			$form_state['redirect'] = 'apbd/pembiayaan/detil/edit/' . $kodero  ;

		} else {
			if ($res)
				drupal_set_message('Penyimpanan data berhasil dilakukan');
			else
				drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
			drupal_goto('apbd/pembiayaan/detil/' .  $kodero);    
		}
	}
}
?>
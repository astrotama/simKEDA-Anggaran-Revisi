<?php
    
function pembiayaan_edit_form(){
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatanpb.js');
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Rekening Pembiayaan',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	
	$kodero = arg(3);
	//drupal_set_message($kodero);
	
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);
	//drupal_set_message($tahun);

	$title = 'Rekening Pembiayaan';

	$jumlah = 0;
	$jumlahsebelum = 0;
	$jumlahsesudah = 0;
	
	//$title =l($title, 'apbd/kegiatanskpd/rekening/' . $kodeuk, array('html'=>true));
	drupal_set_title($title);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($kodero))
    {
        if (!user_access('kegiatanrkpd edit'))
            drupal_access_denied();
			
        $sql = 'select tahun,kodero,uraian,jumlah,jumlahsebelum,jumlahsesudah from {anggperda} 
			   where tahun=\'%s\' and kodero=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($tahun, $kodero));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$tahun = $data->tahun;
				$kodero = $data->kodero;
				$uraian = $data->uraian;
				$jumlah = $data->jumlah;
				$jumlahsebelum = $data->jumlahsebelum;
				$jumlahsesudah = $data->jumlahsesudah;
                $disabled =TRUE;
			} else {
				$kodero = '';
			}
        } else {
			$kodero = '';
		}
    } else {
		if (!user_access('kegiatanrkpd tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Rekening Pembiayaan';
		$kodero = '';
	}
    
	$form['formdata']['tahun']= array( 
		'#type'         => 'hidden', 
		'#title'        => 'tahun',  
		'#default_value'=> $tahun, 
	); 	
	$form['formdata']['nk']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodero', 
		'#default_value'=> $kodero, 
	); 
	$form['formdata']['e_kodero']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodero', 
		'#default_value'=> $kodero, 
	); 

	$form['formdata']['kegiatan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Rekening', 
		//'#description'  => 'uraian', 
		'#maxlength'    => 255, 
		'#size'         => 100, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraianpembiayaan',
		'#default_value'=> $uraian, 
	); 
	$form['formdata']['keterangan'] = array (
		'#type' => 'markup',
		'#value' => "<span><font size='1'>Isi rekening dengan memilih menggunakan tombol Pilih</font></span>",
	);	
	$form['formdata']['jumlah']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jumlah',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#disabled'     => true, 
		'#description'  => 'Jumlah anggaran pembiayaan, akan terisi saat detil rekening diisikan',
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jumlah, 
	); 
	$form['formdata']['jumlahsebelum']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Tahun Lalu',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Jumlah anggaran pembiayaan tahun lalu, seandainya ada', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jumlahsebelum, 
	); 
	$form['formdata']['jumlahsesudah']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tahun Depan',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Jumlah perkiraan anggaran pembiayaan tahun depan, seandainya ada', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jumlahsesudah, 
	); 
	
	//drupal_set_message($kodero); 
	if ($kodero == '') {

		$form['formdata']['detil1'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Detil Rekening #1',
			'#collapsible' => true,
			'#collapsed' => false,        
		);

		$form['formdata']['detil1']['uraian1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 100, 
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
			'#title'=> 'Detil Rekening #2',
			'#collapsible' => true,
			'#collapsed' => false,        
		);

		$form['formdata']['detil2']['uraian2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 100, 
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
			'#title'=> 'Detil Rekening #3',
			'#collapsible' => true,
			'#collapsed' => false,        
		);

		$form['formdata']['detil3']['uraian3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 100, 
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
			'#title'=> 'Detil Rekening #4',
			'#collapsible' => true,
			'#collapsed' => true,        
		);

		$form['formdata']['detil4']['uraian4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 100, 
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
			'#title'=> 'Detil Rekening #5',
			'#collapsible' => true,
			'#collapsed' => true,        
		);

		$form['formdata']['detil5']['uraian5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 100, 
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
	
	//Edit lama, dikasih Next dan Prev
	} else {

		//PREV
		$sql = 'select kodero from {anggperda} where tahun=\'%s\' and kodero<\'%s\' order by kodero desc limit 1';
		$res = db_query(db_rewrite_sql($sql), array ($tahun, $kodero));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {
				$prevkode=$data->kodero;

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
		$sql = 'select kodero from {anggperda} where tahun=\'%s\' and kodero>\'%s\' order by kodero limit 1';
		$res = db_query(db_rewrite_sql($sql), array ($tahun, $kodero));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {
				$nextkode=$data->kodero;

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
		
		//Detil RAB
		$form['formdata']['submitrab'] = array (
			'#type' => 'submit',
			'#value' => 'Detil',
		);		
	
	}
	
	$form['formdata']['submitnewdetil'] = array (
		'#type' => 'submit',
		'#value' => 'Tambah Detil',
	);
	
	$form['formdata']['submitnew'] = array (
		'#type' => 'submit',
		'#value' => 'Tambah Rekening',
	);
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/pembiayaan/' class='btn_blue' style='color: white'>Tutup</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function pembiayaan_edit_form_validate($form, &$form_state) {
	$uraian = $form_state['values']['kegiatan'];
	$kodero = $form_state['values']['nk'];

	$tahun = $form_state['values']['tahun'];
	$e_kodero = $form_state['values']['e_kodero'];

	if ($kodero=='') {		
		if ($uraian =='') {
			form_set_error('', 'Rekening belum diisi');
			
		} else {			//Rekening diisi dari mengetik
			$sql = 'select kodero from {rincianobyek} where uraian=\'%s\'';
			$res = db_query(db_rewrite_sql($sql), array ($uraian));
			if ($res) {
				$data = db_fetch_object($res);
				if ($data) {
					$kodero = $data->kodero;
				} else {
					form_set_error('', 'Rekening tidak diisi/dipilih dengan benar');
				}
			} 
		}
	}
	
	if ($e_kodero != $kodero) {		//Rekening baru
		$sql = 'select kodero from {anggperda} where tahun=\'%s\' and and kodero=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($tahun, $kodero));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {		
				form_set_error('', 'Rekening sudah digunakan');
			}
		}
	} else {
		
	}
	
}

function pembiayaan_edit_form_submit($form, &$form_state) {
	
    if($form_state['clicked_button']['#value'] == $form_state['values']['submitnext']) {
		$nextkode = $form_state['values']['nextkode'];
        $form_state['redirect'] = 'apbd/pembiayaan/edit/' . $nextkode ;
		//drupal_set_message('Next');

	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitprev']) {
		$prevkode = $form_state['values']['prevkode'];
        $form_state['redirect'] = 'apbd/pembiayaan/edit/' . $prevkode ;
		//drupal_set_message('Next');

    } else if($form_state['clicked_button']['#value'] == $form_state['values']['submitrab']) {
		$kodero = $form_state['values']['nk'];
        $form_state['redirect'] = 'apbd/pembiayaan/detil/' . $kodero;
		//drupal_set_message('Next');

	} else {
		
		$e_kodero = $form_state['values']['e_kodero'];			

		$tahun = $form_state['values']['tahun'];
		
		$uraian = $form_state['values']['kegiatan'];
		$kodero = $form_state['values']['nk'];

		$jumlah = $form_state['values']['jumlah'];
		$jumlahsebelum = $form_state['values']['jumlahsebelum'];
		$jumlahsesudah = $form_state['values']['jumlahsesudah'];

		if (($kodero=='') and ($uraian !='')) {		//Rekening diisi dari mengetik
			$sql = 'select kodero from {rincianobyek} where uraian=\'%s\'';
			$res = db_query(db_rewrite_sql($sql), array ($uraian));
			if ($res) {
				$data = db_fetch_object($res);
				if ($data) {
					
					$kodero = $data->kodero;
				} else {
					$kodero = '';
				}
			} else {
				$kodero = '';
			}
		}
		
		if ($e_kodero=='')
		{
			
			//Hitung detilnya dulu
			$uraian1 = $form_state['values']['uraian1'];
			if ($uraian1 != '') {
				$unitjumlah1 = $form_state['values']['unitjumlah1'];
				$unitsatuan1 = $form_state['values']['unitsatuan1'];
				$volumjumlah1 = $form_state['values']['volumjumlah1'];
				$volumsatuan1 = $form_state['values']['volumsatuan1'];
				$harga1 = $form_state['values']['harga1'];
				
				$total1 = $unitjumlah1 * $volumjumlah1 * $harga1;
				$totalrekening = $total1;
			}
			$uraian2 = $form_state['values']['uraian2'];
			if ($uraian2 != '') {
				$unitjumlah2 = $form_state['values']['unitjumlah2'];
				$unitsatuan2 = $form_state['values']['unitsatuan2'];
				$volumjumlah2 = $form_state['values']['volumjumlah2'];
				$volumsatuan2 = $form_state['values']['volumsatuan2'];
				$harga2 = $form_state['values']['harga2'];
				
				$total2 = $unitjumlah2 * $volumjumlah2 * $harga2;
				$totalrekening += $total2;
			}
			$uraian3 = $form_state['values']['uraian3'];
			if ($uraian3 != '') {
				$unitjumlah3 = $form_state['values']['unitjumlah3'];
				$unitsatuan3 = $form_state['values']['unitsatuan3'];
				$volumjumlah3 = $form_state['values']['volumjumlah3'];
				$volumsatuan3 = $form_state['values']['volumsatuan3'];
				$harga3 = $form_state['values']['harga3'];
				
				$total3 = $unitjumlah3 * $volumjumlah3 * $harga3;
				$totalrekening += $total3;
			}
			$uraian4 = $form_state['values']['uraian4'];
			if ($uraian4 != '') {
				$unitjumlah4 = $form_state['values']['unitjumlah4'];
				$unitsatuan4 = $form_state['values']['unitsatuan4'];
				$volumjumlah4 = $form_state['values']['volumjumlah4'];
				$volumsatuan4 = $form_state['values']['volumsatuan4'];
				$harga4 = $form_state['values']['harga4'];
				
				$total4 = $unitjumlah4 * $volumjumlah4 * $harga4;
				$totalrekening += $total4;
			}
			$uraian5 = $form_state['values']['uraian5'];
			if ($uraian5 != '') {
				$unitjumlah5 = $form_state['values']['unitjumlah5'];
				$unitsatuan5 = $form_state['values']['unitsatuan5'];
				$volumjumlah5 = $form_state['values']['volumjumlah5'];
				$volumsatuan5 = $form_state['values']['volumsatuan5'];
				$harga5 = $form_state['values']['harga5'];
				
				$total5 = $unitjumlah5 * $volumjumlah5 * $harga5;
				$totalrekening += $total5;
			}

			$sql = 'insert into {anggperda} (tahun,kodero,uraian,jumlah,jumlahsebelum,jumlahsesudah) 
				   values (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
			$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero,$uraian, $totalrekening, $jumlahsebelum, $jumlahsesudah));
			
			
			//Simpan detilnya
			$sql = 'insert into {anggperdadetil} (tahun, kodero, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
			if ($res) 
				if ($uraian1 != '') {
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero, $uraian1, $unitjumlah1, $unitsatuan1, $volumjumlah1, $volumsatuan1, $harga1, 
							$unitjumlah1 * $volumjumlah1 * $harga1));	
				}
			
			if ($res) 
				if ($uraian2 != '') {
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero, $uraian2, $unitjumlah2, $unitsatuan2, $volumjumlah2, $volumsatuan2, $harga2, 
							$unitjumlah2 * $volumjumlah2 * $harga2));	
				}

			if ($res) 
				if ($uraian3 != '') {
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero, $uraian3, $unitjumlah3, $unitsatuan3, $volumjumlah3, $volumsatuan3, $harga3, 
							$unitjumlah3 * $volumjumlah3 * $harga3));	
				}

			if ($res) 	
				if ($uraian4 != '') {
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero, $uraian4, $unitjumlah4, $unitsatuan4, $volumjumlah4, $volumsatuan4, $harga4, 
							$unitjumlah4 * $volumjumlah4 * $harga4));	
				}
			
			if ($res) 
				if ($uraian5 != '') {
					$res = db_query(db_rewrite_sql($sql), array($tahun, $kodero, $uraian5, $unitjumlah5, $unitsatuan5, $volumjumlah5, $volumsatuan5, $harga5, 
							$unitjumlah5 * $volumjumlah5 * $harga5));	
				}	
			
			
		} else {
			$sql = 'update {anggperda} set uraian=\'%s\', kodero=\'%s\', jumlahsebelum=\'%s\', jumlahsesudah=\'%s\' where tahun=\'%s\' and kodero=\'%s\'';
			$res = db_query(db_rewrite_sql($sql), array($uraian, $kodero, $jumlahsebelum, $jumlahsesudah, $tahun, $e_kodero));
		}
		
		if($form_state['clicked_button']['#value'] == $form_state['values']['submitnew']) {
			$nextkode = $form_state['values']['nextkode'];
			$form_state['redirect'] = 'apbd/pembiayaan/edit/' ;

		} else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitnewdetil']) {
			$form_state['redirect'] = 'apbd/pembiayaan/detil/edit/' . $kodero  ;
			
		} else {
			
			if ($res)
				drupal_set_message('Penyimpanan data berhasil dilakukan');
			else
				drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
			drupal_goto('apbd/pembiayaan');    
		}
	}
}
?>
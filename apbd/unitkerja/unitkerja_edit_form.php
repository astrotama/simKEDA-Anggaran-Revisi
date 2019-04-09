<?php
    
function unitkerja_edit_form(){
    /*
	$form = array (
        '#type' => 'fieldset',
        //'#title'=> 'Edit Data',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,        
    );
	*/

	//FORM NAVIGATION	
	//$current_url = url(current_path(), array('absolute' => TRUE));
	$referer = $_SERVER['HTTP_REFERER'];
	
	if (strpos($referer, '?')>0)
		$_SESSION["unitkerjalastpage"] = $referer;
	else
		$referer = $_SESSION["unitkerjalastpage"];
	
	
    drupal_add_css('files/css/kegiatancam.css');	
	drupal_set_title('Data SKPD');
    $kodeuk = arg(3);
	$kodeu = $_SESSION['kodeu'];
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $disabled = FALSE;
	$iskecamatan = 0;
    if (isset($kodeuk))
    {
        if (!user_access('unitkerja edit'))
            drupal_access_denied();
		if (!isSuperuser())
			if ($kodeuk != apbd_getuseruk())
				drupal_access_denied();
		
        $sql = 'select kodeuk, header1, header2, namauk, namasingkat, pimpinannama, pimpinanjabatan, pimpinanpangkat, pimpinannip, kodedinas, kodeu, iskecamatan, dispensasipendapatan,dispensasibelanja,dispensasirevisi from {unitkerja} where kodeuk=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($kodeuk));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {                
				$kodeuk = $data->kodeuk;
				$namauk = $data->namauk;
				$kodeu = $data->kodeu;
				$namasingkat = $data->namasingkat;
				$pimpinannama = $data->pimpinannama;
				$pimpinanjabatan = $data->pimpinanjabatan;
				$pimpinanpangkat = $data->pimpinanpangkat;
				$pimpinannip = $data->pimpinannip;
				$iskecamatan = $data->iskecamatan;
				$kodedinas = $data->kodedinas;
				$dispensasipendapatan = $data->dispensasipendapatan;
				$dispensasibelanja = $data->dispensasibelanja;
				$dispensasirevisi = $data->dispensasirevisi;
				
				$header1 = $data->header1;
				$header2 = $data->header2;

                $disabled =TRUE;
			} else {
				$kodeuk = '';
			}
        } else {
			$kodeuk = '';
		}
    } else {
		if (!user_access('unitkerja tambah'))
			drupal_access_denied();
		$kodeuk = '';
		$form['#title'] = 'Tambah Data';
	}
    $query = sprintf("select kodeu, urusansingkat from urusan order by urusansingkat");
	$uresult = db_query($query);
	$urusan = array();
	while ($udata = db_fetch_object($uresult)) {
		$urusan[$udata->kodeu] = $udata->urusansingkat;
	}
	
	$selecttype = 'hidden';
	$texttype = 'hidden';
	if (isSuperuser()) {
		$selecttype = 'radios';
		$texttype = 'textfield';	
	}
	
	$form['kodeu']= array(
		'#type'         => 'select', 
		'#title'        => 'Urusan',
		'#options'		=> $urusan,
		//'#description'  => 'kodeuk', 
		//'#maxlength'    => 4, 
		//'#size'         => 8, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeu, 
		//'#weight' => 1,
	); 
	
	$form['kodeuk']= array(
		'#type'         => $texttype, 
		'#title'        => 'Kode', 
		//'#description'  => 'kodeuk', 
		'#maxlength'    => 4, 
		'#size'         => 8, 
		'#required'     => !$disabled, 
		'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
		//'#weight' => 2,
	); 
	
	if (isSuperuser())
		$uktype = 'textfield';
	else
		$uktype = 'hidden';
	
	$form['namauk']= array(
		'#type'         => $uktype, 
		'#title'        => 'Nama', 
		//'#description'  => 'namauk', 
		'#maxlength'    => 100, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $namauk, 
		//'#weight' => 3,
	); 
	$form['namasingkat']= array(
		'#type'         => $uktype, 
		'#title'        => 'Nama Singkat', 
		//'#description'  => 'namasingkat', 
		'#maxlength'    => 50, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $namasingkat, 
		//'#weight' => 4,
	); 
	
	$form['pimpinannama']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama Pimpinan', 
		//'#description'  => 'pimpinannama', 
		'#maxlength'    => 50, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $pimpinannama, 
		//'#weight' => 5,
	); 
	$form['pimpinanjabatan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jabatan Pimpinan', 
		//'#description'  => 'pimpinanjabatan', 
		'#maxlength'    => 60, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $pimpinanjabatan, 
		//'#weight' => 6,
	); 
	$form['pimpinanpangkat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Pangkat Pimpinan', 
		//'#description'  => 'pimpinanpangkat', 
		'#maxlength'    => 60, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $pimpinanpangkat, 
		//'#weight' => 7,
	); 
	$form['pimpinannip']= array(
		'#type'         => 'textfield', 
		'#title'        => 'NIP Pimpinan', 
		//'#description'  => 'pimpinannip', 
		'#maxlength'    => 21, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $pimpinannip, 
		//'#weight' => 8,
	);
	$form['kodedinas']= array(
		'#type'         => $texttype, 
		'#title'        => 'Kode Dinas', 
		//'#description'  => 'pimpinannip', 
		'#maxlength'    => 5, 
		'#size'         => 10, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodedinas, 
		//'#weight' => 9,
	);
	$form['formdata']['ss0'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		//'#weight' => 10,
	);
	$options = array('1' => t('Kecamatan'), '0' => t('Bukan Kecamatan'));
	$form['iskecamatan']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Kecamatan',
		'#options'		=> $options,
		//'#description'  => 'iskecamatan', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $iskecamatan, 
		//'#weight' => 11,
	); 
	$form['formdata']['ssKC'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		//'#weight' => 10,
	);
	$form['formdata']['ss2'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		//'#weight' => 14,
	);	
	
	//pengaturan skpd
	$pengaturantype = 'hidden';
	if (isSuperuser()) 
		$pengaturantype = 'fieldset';
	
	$form['kegiatan'] = array (
		'#type' => $pengaturantype,
		'#title'=> 'Dispensasi Penyusunan Anggaran',
		'#collapsible' => true,
		'#collapsed' => false,        
	);	
	$optiondispensasi = array('0' => t('Tidak'), '1' => t('Ya'));
	$form['kegiatan']['dispensasipendapatan']= array(
		'#type'         => $selecttype, 
		'#title'        => 'RKA Belanja',
		'#options'		=> $optiondispensasi,
		//'#description'  => 'iskecamatan', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dispensasipendapatan, 
		//'#weight' => 15,		
	);	
	
	$form['kegiatan']['ss3'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		//'#weight' => 14,
	);	
	
	$form['kegiatan']['dispensasibelanja']= array(
		'#type'         => $selecttype, 
		'#title'        => 'RKA Pendapatan',
		'#options'		=> $optiondispensasi,
		//'#description'  => 'iskecamatan', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dispensasibelanja, 
		//'#weight' => 17,
	); 	

	$form['kegiatan']['ss4'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		//'#weight' => 14,
	);	
	
	$form['kegiatan']['dispensasirevisi']= array(
		'#type'         => $selecttype, 
		'#title'        => 'Revisi/Perubahan',
		'#options'		=> $optiondispensasi,
		//'#description'  => 'iskecamatan', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dispensasirevisi, 
		//'#weight' => 17,
	); 	
	$form['kop'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Kop Surat',
		'#collapsible' => true,
		'#collapsed' => false,        
	);		
	$form['kop']['header1']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Header 1',
		'#maxlength'    => 100, 
		'#size'         => 60, 		
		'#default_value'=> $header1, 
		//'#weight' => 17,
	); 	
	$form['kop']['header2']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Header 2',
		'#maxlength'    => 100, 
		'#size'         => 60, 		
		'#default_value'=> $header2, 
		//'#weight' => 17,
	); 	
	
    $form['e_kodeuk']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodeuk, 
    ); 
	
	if (isSuperuser()) {
		$form['submit'] = array (
			'#type' => 'submit',
			'#value' => 'Simpan',
			'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn_blue' style='color: white'>Tutup</a>",
			//'#weight' => 19,
		);
	} else {
		$form['submit'] = array (
			'#type' => 'submit',
			'#value' => 'Simpan',
			'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn_blue' style='color: white'>Tutup</a>",
			//'#weight' => 20,
		);
	}
    return $form;
}
function unitkerja_edit_form_validate($form, &$form_state) {
//$kodeuk = arg(3);
//    if (!isset($kodeuk)) {
//        if (strlen($form_state['values']['kodeuk']) < 8 ) {
//            form_set_error('', 'kodeuk harus terdiri atas 8 karakter');
//        }            
//    }
}
function unitkerja_edit_form_submit($form, &$form_state) {
    
    $e_kodeuk = $form_state['values']['e_kodeuk'];
    $kodeu = $form_state['values']['kodeu'];
	
	$kodeuk = $form_state['values']['kodeuk'];
	$namauk = $form_state['values']['namauk'];
	$namasingkat = $form_state['values']['namasingkat'];
	$pimpinannama = $form_state['values']['pimpinannama'];
	$pimpinanjabatan = $form_state['values']['pimpinanjabatan'];
	$pimpinanpangkat = $form_state['values']['pimpinanpangkat'];
	$pimpinannip = $form_state['values']['pimpinannip'];
	$kodedinas = $form_state['values']['kodedinas'];
	$iskecamatan = $form_state['values']['iskecamatan'];
	$dispensasipendapatan = $form_state['values']['dispensasipendapatan'];
	$dispensasibelanja = $form_state['values']['dispensasibelanja'];
	$dispensasirevisi = $form_state['values']['dispensasirevisi'];	

	$header1 = $form_state['values']['header1'];
	$header2 = $form_state['values']['header2'];
		
    if ($e_kodeuk=='')
    {
        $sql = 'insert into {unitkerja} (kodeuk,namauk,namasingkat,pimpinannama,pimpinanjabatan,pimpinanpangkat,pimpinannip, kodedinas, kodeu, iskecamatan, dispensasipendapatan, dispensasibelanja, dispensasirevisi) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($kodeuk), strtoupper($namauk), strtoupper($namasingkat), $pimpinannama, $pimpinanjabatan, $pimpinanpangkat, strtoupper($pimpinannip), $kodedinas, $kodeu, strtoupper($iskecamatan), $dispensasipendapatan, $dispensasibelanja, $dispensasirevisi));
    } else {
        $sql = 'update {unitkerja} set namauk=\'%s\', namasingkat=\'%s\', pimpinannama=\'%s\', pimpinanjabatan=\'%s\', pimpinanpangkat=\'%s\', pimpinannip=\'%s\', kodedinas=\'%s\', kodeu=\'%s\', iskecamatan=\'%s\',  dispensasipendapatan=\'%s\', dispensasibelanja=\'%s\', 
		dispensasirevisi=\'%s\', header1=\'%s\', header2=\'%s\' where kodeuk=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($namauk), strtoupper($namasingkat), $pimpinannama, $pimpinanjabatan, $pimpinanpangkat, strtoupper($pimpinannip), $kodedinas, $kodeu, strtoupper($iskecamatan), 
		$dispensasipendapatan, $dispensasibelanja, $dispensasirevisi, $header1, $header2, $e_kodeuk));
    }
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
	
	if (isSuperuser()) {
		$_SESSION['kodeu'] = $kodeu;
		
		$referer = $_SESSION["unitkerjalastpage"];
		drupal_goto($referer);    
	} else {
		drupal_goto('');
	}
}
?>
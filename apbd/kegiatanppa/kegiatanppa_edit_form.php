<?php
function kegiatanppa_edit_form() {
    
    $kodeuk = arg(3);
	$tipe = arg(4);
	$revisi = arg(5);
	
	$_SESSION['dpano_revisi'] = $revisi;
	
	//drupal_set_message($kodeuk . ' = ' . $tipe);
	drupal_add_css('files/css/kegiatancam.css');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
    $disabled = FALSE;
	
	$title = 'Penomoran DPPA ';
    if (isset($kodeuk)) 
    {
        if (!user_access('kegiatanppa edit'))
            drupal_access_denied();
		
			
        $sql = 'select namasingkat, penno, pentgl, penok, btlno, btltgl, btlok, blno, bltgl from {dpanomor' . $revisi . '} d inner join {unitkerja} u on d.kodeuk=u.kodeuk where u.kodeuk=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($kodeuk));
        if ($res) {
			//drupal_set_message('OK');
			$data = db_fetch_object($res);
			if ($data) {    
				
				switch($tipe) {
					case 'pen':
						$title .= 'Pendapatan ';
						$nomor = $data->penno;
						$tanggal = $data->pentgl;
						break;

					case 'btl':
						$title .= 'Belanja Tidak Langsung ';
						$nomor = $data->btlno;
						$tanggal = $data->btltgl;
						break;
					
					case 'bl':
						$title .= 'Belanja Langsung ';
						$nomor = $data->blno;
						$tanggal = $data->bltgl;
						break;
				}
				
				$title .= $data->namasingkat;
				$disabled =TRUE;
				
			} 
        } 
		
    } else {
		$form['formdata']['#title'] = 'Data Tidak Ada';
		//$kodeuk = '';
	
		if (!user_access('kegiatanppa tambah'))
			drupal_access_denied();
    }

	
	drupal_set_title($title);
	

	$form['kodeuk']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $kodeuk, 
	);
	$form['tipe']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $tipe, 
	);

	
	$form['nomor']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nomor DPPA', 
		//'#description'  => 'kodeuk', 
		'#maxlength'    => 10, 
		'#size'         => 10, 
		'#default_value'=> $nomor, 
	); 
	$form['tanggal']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tanggal DPPA', 
		//'#description'  => 'kodeuk', 
		'#maxlength'    => 20, 
		'#size'         => 20, 
		'#default_value'=> $tanggal, 
	);
	
	

	if ($nomor=='') {
		$form['submitnew'] = array (
			'#type' => 'submit',
			'#value' => 'Beri Nomor'
		);
	}
	
	$form['submit'] = array (
		'#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanppa/" . $revisi . "' class='btn_blue' style='color: white'>Tutup</a>",
		'#value' => 'Simpan'
	);

	return $form;
}
function kegiatanppa_edit_form_validate($form, &$form_state) {
	$nomor = $form_state['values']['nomor'];
	
	$revisi = $_SESSION['dpano_revisi'];
	
	if ($nomor !='') {
		$nomor = sprintf("%03d", $nomor);
		$kodeuk = $form_state['values']['kodeuk'];
		$tipe = $form_state['values']['tipe'];
			switch($tipe) {
				case 'pen':
					$sql = 'select penno from {dpanomor' . $revisi . '} where kodeuk<>\'%s\' and penno=\'%s\'';
					break;
				case 'btl':
					$sql = 'select btlno from {dpanomor' . $revisi . '} where kodeuk<>\'%s\' and btlno=\'%s\'';
					break;
				case 'bl':
					$sql = 'select blno from {dpanomor' . $revisi . '} where kodeuk<>\'%s\' and blno=\'%s\'';
					break;
			}
			$res = db_query(db_rewrite_sql($sql), array ($kodeuk, $nomor));
			if ($res) {
				//drupal_set_message('OK');
				if ($data = db_fetch_object($res)) {
					form_set_error('nomor', 'Nomor DPA sudah dipakai');
				}
			}
	}
}

function kegiatanppa_edit_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$tipe = $form_state['values']['tipe'];
	
	$revisi = $_SESSION['dpano_revisi'];
	
    if($form_state['clicked_button']['#value'] == $form_state['values']['submitnew']) {
		
		//$form_state['redirect'] = 'apbd/kegiatanppa/edit/' . $data->kodeuk . '/new' ;
		switch($tipe) {
			case 'pen':
				$nomor = getnomorPEN();
				break;
			case 'btl':
				$nomor = getnomorBTL();
				break;
			case 'bl':
				$nomor = getnomorBL();
				break;
		}
		
		//strftime('%e %B %Y', time());
		$tanggal = strftime('%e %B %Y', time());
		$isnew = true;
	
	} else { 
		$nomor = $form_state['values']['nomor'];
		if ($nomor !='') $nomor = sprintf("%03d", $nomor);
		$tanggal = $form_state['values']['tanggal'];
		$isnew = false;
		
		//drupal_set_message ('Here...' . $nomor);
	}
		
	switch($tipe) {
		case 'pen':
			$sql = "update {dpanomor" . $revisi . "} set penno='%s', pentgl='%s' where kodeuk='%s'";
			break;
			
		case 'btl':
			$sql = "update {dpanomor" . $revisi . "} set btlno='%s', btltgl='%s' where kodeuk='%s'";
			break;
			
		case 'bl':
			$sql = "update {dpanomor" . $revisi . "} set blno='%s', bltgl='%s' where kodeuk='%s'";
			break;
	}
		
	$sql = sprintf($sql, $nomor, $tanggal, $kodeuk);	
	//drupal_set_message ($sql);
	$res = db_query($sql);

	if ($isnew == false) {
		if ($res) {
			drupal_set_message('Penyimpanan data berhasil dilakukan');		
		}
		else
			drupal_set_message('Penyimpanan data tidak berhasil dilakukan');

		drupal_goto('apbd/kegiatanppa/' . $revisi);    
	}
}

function getnomorBL() {
    
	$revisi = $_SESSION['dpano_revisi'];
	
	$query = "select max(blno) maxno from {dpanomor" . $revisi . "}";
	
	drupal_set_message($query);
	
	$pres = db_query($query);
	if ($data=db_fetch_object($pres)) {
		
		drupal_set_message('ok');
		
		$v = $data->maxno;
		$iv = intval($v);
		$iv++;
	} else {
		$iv=1;
	}	 
	
	drupal_set_message($iv);
	
	return sprintf("%03d", $iv);
}

function getnomorBTL() {
    
	$revisi = $_SESSION['dpano_revisi'];
	
	$query = "select max(btlno) maxno from {dpanomor" . $revisi . "}";
	
	$pres = db_query($query);
	if ($data=db_fetch_object($pres)) {
		$v = $data->maxno;
		$iv = intval($v);
		$iv++;
	} else {
		$iv=1;
	}	 
	return sprintf("%03d", $iv);
}

function getnomorPEN() { 
    
	$revisi = $_SESSION['dpano_revisi'];
	
	$query = "select max(penno) maxno from {dpanomor" . $revisi . "}";
	$pres = db_query($query);
	if ($data=db_fetch_object($pres)) {
		$v = $data->maxno;
		$iv = intval($v);
		$iv++;
	} else {
		$iv=1;
	}	 
	return sprintf("%03d", $iv);
}

?>
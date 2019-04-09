<?php
    
function upload_edit_form(){
    $nomor = arg(3);
    drupal_add_css('files/css/kegiatancam.css');		
	drupal_set_title('Data Donwload');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $disabled = FALSE;
   $username = arg(3);
	drupal_set_title('Pengelolaan User');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    drupal_add_css('files/css/kegiatancam.css');		

	$akses='';
	$lakses = array();
	$takses = array();
	$lakses[''] = '---Tentukan Hak Akses---';
	
	$roles = user_roles(TRUE);
	foreach( array_keys($roles) as $rid) {
		switch ($roles[$rid]) {
			case 'apbd admin' :
				//$lakses[$rid] = 'Super User';
				if (isSuperuser()) {
					$lakses[$rid] = 'Administrator';
					$takses['apbd admin'] = $rid;
				}
				break;
			case 'user kecamatan':
				//$lakses[$rid] = 'User Kecamatan (Musrenbangcam)';
				$lakses[$rid] = 'Bidang';
				$takses['user kecamatan'] = $rid;
				break;
			case 'user skpd non-kecamatan':
				//$lakses[$rid] = 'User SKPD (non Kecamatan)';
				$lakses[$rid] = 'SKPD';
				$takses['user skpd non-kecamatan'] = $rid;
				break;

			case 'user viewer':
				//$lakses[$rid] = 'User SKPD (non Kecamatan)';
				$lakses[$rid] = 'Viewer';
				$takses['user viewer'] = $rid;
				break;
				
		}
		
	}

	//............
	$sql = 'select ttd from {apbdop} where username=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($username));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$file = $data->ttd;
				
				$filetype = 'file';
				
                $disabled =TRUE;
			} else {
				$nomor = '';
				$filetype = 'file';
			}
        } else {
			$nomor = '';
			$filetype = 'file';
		}
		//....................
    $disabled = FALSE;
    if (isset($username))
    {
        if (!user_access('apbdop edit'))
            drupal_access_denied();
			
        $sql = 'select username,nama,kodeuk,kodesuk,ttd from {apbdop} where username=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array ($username));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$username = $data->username;
				$nama = $data->nama;
				$kodeuk = $data->kodeuk;
				$kodesuk = $data->kodesuk;
				$ttd = $data->ttd;
                $disabled =TRUE;
				$user = user_load(array('name' => $username));
				if (in_array('apbd admin', $user->roles)) {
					$akses = $takses['apbd admin'];
				} elseif(in_array('user kecamatan', $user->roles)){
					$akses = $takses['user kecamatan'];
				} elseif(in_array('user skpd non-kecamatan', $user->roles)){
					$akses = $takses['user skpd non-kecamatan'];
				} elseif(in_array('user viewer', $user->roles)){
					$akses = $takses['user viewer'];
				}
				//print_r($user);
			} else {
				$username = '';
			}
        } else {
			$username = '';
		}
    } else {
		if (!user_access('apbdop tambah'))
			drupal_access_denied();
		$username = '';
		$form['formdata']['#title'] = 'Tambah User';
		$akses = $takses['user skpd non-kecamatan'];
	}
	$form = array(
		'#attributes' => array('enctype' => "multipart/form-data"),
	);	
	$form['nomor']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Kode', 
		//'#description'  => 'nomor', 
		'#default_value'=> $nomor, 
	); 
	
	$form['formdata']['username']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Username', 
		//'#description'  => 'username', 
		'#maxlength'    => 60, 
		'#size'         => 40, 
		'#required'     => !$disabled, 
		'#disabled'     => $disabled, 
		//'#value'=> '',
		//'#default_value' => $username,
	);
	if ($disabled) {
		$form['formdata']['username']['#value'] = $username;
	} else {
		$form['formdata']['username']['#default_value'] = $username;
	}
	 
	$form['formdata']['file']= array(
		'#type'         => $filetype, 
		'#title'        => 'Upload TTD', 
		'#default_value'=> $file, 
	);
	$form['formdata']['uploaded']= array(
		'#type'         => 'markup', 
		//'#title'        => 'Nama File', 
		'#value'=> '<image src="'.$ttd.'" width="100" height="80"></image></br>', 
	);
	$form['formdata']['e_file']= array(
		'#type'         => 'value', 
		'#title'        => 'Nama File', 
		'#default_value'=> $file, 
	);
	$form['formdata']['e_username']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $username, 
    );
	
    $form['e_nomor']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $nomor, 
    ); 
	//...................................//.............................................................XXXXXXXX
	/*$form['formdata']['username']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Username', 
		//'#description'  => 'username', 
		'#maxlength'    => 60, 
		'#size'         => 40, 
		'#required'     => !$disabled, 
		'#disabled'     => $disabled, 
		//'#value'=> '',
		//'#default_value' => $username,
	);
	if ($disabled) {
		$form['formdata']['username']['#value'] = $username;
	} else {
		$form['formdata']['username']['#default_value'] = $username;
	}
	$form['formdata']['upwd']= array(
		'#type'         => 'textfield', 
		'#title'        => t('Password'), 
		//'#description'  => 'username', 
		'#maxlength'    => 32, 
		'#size'         => 40, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> '', 
	); 
	$form['formdata']['nama']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama', 
		//'#description'  => 'nama', 
		'#maxlength'    => 100, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $nama, 
	);

	if (isSuperuser()) {
		$privquery = sprintf("select kodeuk, namasingkat from {unitkerja} order by namasingkat");
		$privres = db_query($privquery);
		$options = array();
		while ($privrec = db_fetch_object($privres)) {
			$options[$privrec->kodeuk] = $privrec->namasingkat;
		}
		
		$form['formdata']['kodeuk']= array(
			'#type'         => 'select', 
			'#title'        => 'Unit Kerja',
			'#options'		=> $options,
			//'#description'  => 'kodeuk', 
			//'#maxlength'    => 60, 
			//'#size'         => 20, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $kodeuk, 
		);

		$form['formdata']['kodesuk']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $kodesuk, 
		);		
		$form['formdata']['akses']= array(
			'#type'         => 'select', 
			'#title'        => 'Hak Akses',
			'#options'		=> $lakses,
			//'#description'  => 'kodeuk', 
			//'#maxlength'    => 60, 
			//'#size'         => 20, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $akses, 
		); 
		
		
	} else {

		$kodeuk = apbd_getuseruk();
		$akses = $takses['user kecamatan'];
		
		$form['formdata']['kodeuk']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $kodeuk, 
		);

		//$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
		$pquery = sprintf('select kodesuk, namasuk from {subunitkerja} where kodeuk=\'%s\' order by kodesuk', $kodeuk);
		
		//drupal_set_message($pquery);
		
		$pres = db_query($pquery);
		$subskpd = array();
		$subskpd[''] = '- Pilih Bidang -';
		while ($data = db_fetch_object($pres)) {
			$subskpd[$data->kodesuk] = $data->namasuk;
		}
		
		$form['formdata']['kodesuk']= array(
			'#type'         => 'select', 
			'#title'        => 'Bidang/Bagian',
			'#options'		=> $subskpd,
			//'#description'  => 'kodesuk', 
			//'#maxlength'    => 60, 
			//'#size'         => 20, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $kodesuk, 
		); 
		
		$form['formdata']['akses']= array(
			'#type'         => 'hidden', 
			'#options'		=> $lakses,
			'#default_value'=> $akses, 
		); 
		
		
	}
	$form['file']= array(
		'#type'         => 'file', 
		'#title'        => 'Nama File', 
		//'#default_value'=> $file, 
	);
    $form['formdata']['e_username']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $username, 
    );*/
	//..................................//
	
    $form['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/download' class='btn_blue' style='color: white'>Tutup</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function upload_edit_form_validate($form, &$form_state) {

	if ($form_state['values']['file'] =='') {
					$file = file_save_upload('file', array(
							'file_validate_extensions' => array('pdf zip png gif jpg jpeg')
							));
							
					// If the file passed validation:
					if (isset($file->filename)) {
						// Move the file, into the Drupal file system
						//if (file_move($file, $file->filename)) {
						if (file_move($file, 'download/' . $file->filename)) {
							// Update the new file location in the database.
							drupal_write_record('files', $file, 'fid');
							// Save the file for use in the submit handler.
							$form_state['storage']['file'] = $file;
					}
					else {
						form_set_error('file', t('Failed to write the uploaded file the site\'s file folder.'));
						}
					}
					else {
						form_set_error('file', t('Invalid file, only file with the extension pdf, zip, png, gif, jpg, jpeg are allowed'));
					}
				}
	
	
}
function upload_edit_form_submit($form, &$form_state) {
	 $e_username = $form_state['values']['e_username'];
    $upwd = $form_state['values']['upwd'];
	$username = $form_state['values']['username'];
	$nama = $form_state['values']['nama'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodesuk = $form_state['values']['kodesuk'];
	$user = null;
    $akses = $form_state['values']['akses'];
	$roles = array($akses => 'new akses');
	$e_file = $form_state['values']['e_file'];
	//File upload ......................................................
						if ($form_state['values']['file'] =='') {
							$file = $form_state['storage']['file'];
							// We are done with the file, remove it from storage.
							unset($form_state['storage']['file']);
							// Make the storage of the file permanent
							file_set_status($file, FILE_STATUS_PERMANENT);
							// Set a response to the user.

							$fileurl = file_create_url($file->filepath);
							$filename =  $file->filename;
						}
				//if ($e_file=='')
				//{
					//$nomor = getnomor();
					//$sql = 'insert into {apbdop} (ttd) values(\'%s\') where username=\'%s\'';        
					//$res = db_query(db_rewrite_sql($sql), array('fff', 'bangsri'));
				/*} else {
					$sql = 'update {apbdop} set ttd=\'%s\' where username=\'%s\' ';
					$res = db_query(db_rewrite_sql($sql), array($fileurl, $username));
				}*/
//...............................................................................
	drupal_set_message($filename);
	$iroles = user_roles(TRUE);			
	
    if ($e_username=='')
    {
		
			$sql = 'insert into {apbdop} (username,nama,kodeuk,kodesuk,ttd) values(\'%s\', \'%s\', \'%s\', \'%s\',\'%s\')';        
			$res = db_query(db_rewrite_sql($sql), array($username, strtoupper($nama), strtoupper($kodeuk), strtoupper($kodesuk),$fileurl));
		
    } else {
		
	
        $sql = 'update {apbdop} set ttd=\'%s\' where username=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($fileurl, $e_username));
    }
	if ($user) {
		//$perm = apbd_perm();
		//print_r($form_state['values']);
		//for ($i=0; $i<count($perm); $i++) {
		//	$tperm = rr($perm[$i]);
		//	$v = $form_state['values'][$tperm]];
		//	if ($v)  {
		//		
		//	}
		//	
		//}
	}
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('apbd/manageuser');  
	}
function rr($t) {
	return str_replace(' ', '_', $t);
}

?>
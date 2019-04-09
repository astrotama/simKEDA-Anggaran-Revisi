<?php
    
function download_edit_form(){
    $nomor = arg(3);
    drupal_add_css('files/css/kegiatancam.css');		
	drupal_set_title('Data Donwload');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($nomor))
    {
        if (!user_access('urusan edit'))
            drupal_access_denied();
			
        $sql = 'select nomor,topik,uraian,file,url1 from {download} where nomor=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($nomor));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$nomor = $data->nomor;
				$url1 = $data->url1;
				$topik = $data->topik;
				$uraian = $data->uraian;
				$file = $data->file;
				
				$filetype = 'hidden';
				
                $disabled =TRUE;
			} else {
				$nomor = '';
				$filetype = 'file';
			}
        } else {
			$nomor = '';
			$filetype = 'file';
		}
    } else {
		if (!user_access('urusan tambah'))
			drupal_access_denied();
		$nomor = '';
		$filetype = 'file';
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
	$form['topik']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Topik', 
		//'#description'  => 'topik', 
		'#maxlength'    => 50, 
		'#size'         => 50, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $topik, 
	); 
	$form['uraian']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Uraian', 
		//'#rows' => 5,
		//'#cols' => 5, 
		'#maxlength'    => 100, 
		'#size'         => 100, 
		
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $uraian, 
	);
	$form['url1']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Mirror Link', 
		//'#rows' => 5,
		//'#cols' => 5, 
		'#maxlength'    => 100, 
		'#size'         => 100, 
		
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $url1, 
	);
	$form['file']= array(
		'#type'         => $filetype, 
		'#title'        => 'Nama File', 
		'#default_value'=> $file, 
	);
	
    $form['e_nomor']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $nomor, 
    ); 
	
    $form['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/download' class='btn_blue' style='color: white'>Tutup</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function download_edit_form_validate($form, &$form_state) {

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
function download_edit_form_submit($form, &$form_state) {
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

	//drupal_set_message(t('The form has been submitted and the image has been saved, fileurl: @fileurl. filename: @filename.', array('@filename' => $file->filename, '@fileurl' => $fileurl)));

    $e_nomor = $form_state['values']['e_nomor'];
    
	$nomor = $form_state['values']['nomor'];
	$topik = $form_state['values']['topik'];
	$uraian = $form_state['values']['uraian'];
	$url1 = $form_state['values']['url1'];
    
    if ($e_nomor=='')
    {
		$nomor = getnomor();
        $sql = 'insert into {download} (nomor,topik,uraian,file,url,url1) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
        $res = db_query(db_rewrite_sql($sql), array($nomor, $topik, $uraian, $filename, $fileurl, $url1));
    } else {
        $sql = 'update {download} set topik=\'%s\', uraian=\'%s\', url1=\'%s\'  where nomor=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($topik, $uraian, $url1, $e_nomor));
    }
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('apbd/download');    
}

function getnomor() {
    
	$query = "select max(nomor) maxno from download";
	$pres = db_query($query);
	if ($data=db_fetch_object($pres)) {
		$v = $data->maxno;
		$iv = intval($v);
		$iv++;
	} else {
		return $iv=1;
	}	 
	return sprintf("%03d", $iv);
}

function getnomorBL() {
    
	$query = "select max(blno) maxno from dpanomor";
	$pres = db_query($query);
	if ($data=db_fetch_object($pres)) {
		$v = $data->maxno;
		$iv = intval($v);
		$iv++;
	} else {
		return $iv=1;
	}	 
	return sprintf("%03d", $iv);
}

function getnomorBTL() {
    
	$query = "select max(btlno) maxno from dpanomor";
	$pres = db_query($query);
	if ($data=db_fetch_object($pres)) {
		$v = $data->maxno;
		$iv = intval($v);
		$iv++;
	} else {
		return $iv=1;
	}	 
	return sprintf("%03d", $iv);
}

function getnomorPEN() {
    
	$query = "select max(penno) maxno from dpanomor";
	$pres = db_query($query);
	if ($data=db_fetch_object($pres)) {
		$v = $data->maxno;
		$iv = intval($v);
		$iv++;
	} else {
		return $iv=1;
	}	 
	return sprintf("%03d", $iv);
}

?>
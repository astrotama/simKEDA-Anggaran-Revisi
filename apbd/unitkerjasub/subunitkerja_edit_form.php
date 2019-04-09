<?php
    
function subunitkerja_edit_form(){
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Edit Data',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    drupal_add_css('files/css/kegiatancam.css');		
    $kodesuk = arg(3);
	$kodeuk = '03';
	drupal_set_title('Data Sub SKPD');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($kodesuk))
    {
        if (!user_access('subunitkerja edit'))
            drupal_access_denied();
			
        $sql = 'select kodesuk,kodeuk,namasuk from {subunitkerja} where kodesuk=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($kodesuk));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$kodesuk = $data->kodesuk;
				$kodeuk = $data->kodeuk;
				$namasuk = $data->namasuk;
                $disabled =TRUE;
			} else {
				$kodesuk = '';
			}
        } else {
			$kodesuk = '';
		}
    } else {
		if (!user_access('subunitkerja tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Data';
		$kodesuk = '';
	}
    
	
	$pquery = sprintf("select kodeuk, namasingkat from {unitkerja} order by namasingkat");
	$pres= db_query($pquery);
	$options=array();
	while ($data = db_fetch_object($pres)) {
		$options[$data->kodeuk] = $data->namasingkat;
	}
	$form['formdata']['kodeuk']= array(
		'#type'         => 'select', 
		'#title'        => 'SKPD',
		'#options'		=> $options,
		//'#description'  => 'kodeuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	); 
	$form['formdata']['kodesuk']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kode Sub SKPD', 
		//'#description'  => 'kodesuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodesuk, 
	);
	$form['formdata']['namasuk']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama Sub SKPD', 
		//'#description'  => 'namasuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $namasuk, 
	); 
    $form['formdata']['e_kodesuk']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodesuk, 
    ); 
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/subunitkerja' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function subunitkerja_edit_form_validate($form, &$form_state) {
//$kodesuk = arg(3);
//    if (!isset($kodesuk)) {
//        if (strlen($form_state['values']['kodesuk']) < 8 ) {
//            form_set_error('', 'kodesuk harus terdiri atas 8 karakter');
//        }            
//    }
}
function subunitkerja_edit_form_submit($form, &$form_state) {
    
    $e_kodesuk = $form_state['values']['e_kodesuk'];
    
	$kodesuk = $form_state['values']['kodesuk'];
	$kodeuk = $form_state['values']['kodeuk'];
	$namasuk = $form_state['values']['namasuk'];
    
    if ($e_kodesuk=='')
    {
        $sql = 'insert into {subunitkerja} (kodesuk,kodeuk,namasuk) values(\'%s\', \'%s\', \'%s\')';        
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($kodesuk), strtoupper($kodeuk), strtoupper($namasuk)));
    } else {
        $sql = 'update {subunitkerja} set kodeuk=\'%s\', namasuk=\'%s\' where kodesuk=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($kodeuk), strtoupper($namasuk), $e_kodesuk));
    }
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('apbd/subunitkerja');    
}
?>
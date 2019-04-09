<?php
    
function dinas_edit_form(){
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Edit Data dinas',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $id = arg(3);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($id))
    {
        if (!user_access('dinas edit'))
            drupal_access_denied();
			
        $sql = 'select id,kodeuk,kodeu,nourut,nama from {ukurusan} where id=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($id));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$id = $data->id;
				$kodeuk = $data->kodeuk;
				$kodeu = $data->kodeu;
				$nourut = $data->nourut;
				$nama = $data->nama;
                $disabled =TRUE;
			} else {
				$id = '';
			}
        } else {
			$id = '';
		}
    } else {
		if (!user_access('dinas tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Data dinas';
		$id = '';
	}
    
	
	$form['formdata']['id']= array(
		'#type'         => 'textfield', 
		'#title'        => 'id', 
		'#description'  => 'id', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $id, 
	); 
	$form['formdata']['kodeuk']= array(
		'#type'         => 'textfield', 
		'#title'        => 'kodeuk', 
		'#description'  => 'kodeuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	); 
	$form['formdata']['kodeu']= array(
		'#type'         => 'textfield', 
		'#title'        => 'kodeu', 
		'#description'  => 'kodeu', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeu, 
	); 
	$form['formdata']['nourut']= array(
		'#type'         => 'textfield', 
		'#title'        => 'nourut', 
		'#description'  => 'nourut', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $nourut, 
	); 
	$form['formdata']['nama']= array(
		'#type'         => 'textfield', 
		'#title'        => 'nama', 
		'#description'  => 'nama', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $nama, 
	); 
    $form['formdata']['e_id']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $id, 
    ); 
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
        '#value' => 'Simpan'
    );
    
    return $form;
}
function dinas_edit_form_validate($form, &$form_state) {
//$id = arg(3);
//    if (!isset($id)) {
//        if (strlen($form_state['values']['id']) < 8 ) {
//            form_set_error('', 'id harus terdiri atas 8 karakter');
//        }            
//    }
}
function dinas_edit_form_submit($form, &$form_state) {
    
    $e_id = $form_state['values']['e_id'];
    
	$id = $form_state['values']['id'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodeu = $form_state['values']['kodeu'];
	$nourut = $form_state['values']['nourut'];
	$nama = $form_state['values']['nama'];
    
    if ($e_id=='')
    {
        $sql = 'insert into {ukurusan} (kodeuk,kodeu,nourut,nama) values(\'%s\', \'%s\', \'%s\', \'%s\')';        
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($kodeuk), strtoupper($kodeu), strtoupper($nourut), strtoupper($nama)));
    } else {
        $sql = 'update {ukurusan} set kodeuk=\'%s\', kodeu=\'%s\', nourut=\'%s\', nama=\'%s\' where id=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($kodeuk), strtoupper($kodeu), strtoupper($nourut), strtoupper($nama), $e_id));
    }
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('apbd/dinas');    
}
?>
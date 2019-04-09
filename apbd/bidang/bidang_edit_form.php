<?php
    
function bidang_edit_form(){
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Isian RPJM',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	drupal_set_title('RPJM');
    drupal_add_css('files/css/kegiatancam.css');		
    $kodepro = arg(3);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($kodepro))
    {
        if (!user_access('bidang edit'))
            drupal_access_denied();
		
		//kodepro,s2014,t2014,s2015,t2015,s2016,t2016,s2017,t2017,s2018,t2018
        $sql = 'select * from {program} where kodepro=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($kodepro));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				//drupal_set_message($data->program);
				
				$kodepro = $data->kodepro;
				$program = $data->program;
				$s2014 = $data->s2014;
				$t2014 = $data->t2014;
				$s2015 = $data->s2015;
				$t2015 = $data->t2015;
				$s2016 = $data->s2016;
				$t2016 = $data->t2016;
				$s2017 = $data->s2017;
				$t2017 = $data->t2017;
				$s2018 = $data->s2018;
				$t2018 = $data->t2018;
                $disabled =TRUE;
			} else {
				$kodepro = '';
				//drupal_set_message('err 1');
			}
        } else {
			$kodepro = '';
			//drupal_set_message('err 2');
		}
    } else {
		if (!user_access('bidang tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Data';
		$kodepro = '';
	}
    
	
	$form['formdata']['kodepro']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kode Program', 
		//'#description'  => 'kodepro', 
		'#maxlength'    => 3, 
		'#size'         => 6, 
		'#required'     => !$disabled, 
		'#disabled'     => $disabled, 
		'#default_value'=> $kodepro, 
	);
	$form['formdata']['program']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Program', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 120, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $program, 
	); 
	$form['formdata']['tahun2014'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Tahun 2014',
		'#collapsible' => true,
		'#collapsed' => false,        
	);	
		$form['formdata']['tahun2014']['s2014']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Sasaran', 
			//'#description'  => 'sasaran', 
			'#maxlength'    => 255, 
			'#size'         => 120, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $s2014, 
		); 
		$form['formdata']['tahun2014']['t2014']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Target', 
			//'#description'  => 'target', 
			'#maxlength'    => 255, 
			'#size'         => 120, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $t2014, 
		); 
	$form['formdata']['tahun2015'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Tahun 2015',
		'#collapsible' => true,
		'#collapsed' => false,        
	);	
		$form['formdata']['tahun2015']['s2015']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Sasaran', 
			//'#description'  => 'sasaran', 
			'#maxlength'    => 255, 
			'#size'         => 120, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $s2015, 
		); 
		$form['formdata']['tahun2015']['t2015']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Target', 
			//'#description'  => 'target', 
			'#maxlength'    => 255, 
			'#size'         => 120, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $t2015, 
		); 
	$form['formdata']['tahun2016'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Tahun 2016',
		'#collapsible' => true,
		'#collapsed' => false,        
	);	
		$form['formdata']['tahun2016']['s2016']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Sasaran', 
			//'#description'  => 'sasaran', 
			'#maxlength'    => 255, 
			'#size'         => 120, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $s2016, 
		); 
		$form['formdata']['tahun2016']['t2016']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Target', 
			//'#description'  => 'target', 
			'#maxlength'    => 255, 
			'#size'         => 120, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $t2016, 
		); 
	$form['formdata']['tahun2017'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Tahun 2017',
		'#collapsible' => true,
		'#collapsed' => false,        
	);	
		$form['formdata']['tahun2017']['s2017']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Sasaran', 
			//'#description'  => 'sasaran', 
			'#maxlength'    => 255, 
			'#size'         => 120, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $s2017, 
		); 
		$form['formdata']['tahun2017']['t2017']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Target', 
			//'#description'  => 'target', 
			'#maxlength'    => 255, 
			'#size'         => 120, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $t2017, 
		); 
	$form['formdata']['tahun2018'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Tahun 2018',
		'#collapsible' => true,
		'#collapsed' => false,        
	);	
		$form['formdata']['tahun2018']['s2018']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Sasaran', 
			//'#description'  => 'sasaran', 
			'#maxlength'    => 255, 
			'#size'         => 120, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $s2018, 
		); 
		$form['formdata']['tahun2018']['t2018']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Target', 
			//'#description'  => 'target', 
			'#maxlength'    => 255, 
			'#size'         => 120, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $t2018, 
		); 
	
    $form['formdata']['e_kodepro']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodepro, 
    ); 
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/bidang' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function bidang_edit_form_validate($form, &$form_state) {
//$kodepro = arg(3);
//    if (!isset($kodepro)) {
//        if (strlen($form_state['values']['kodepro']) < 8 ) {
//            form_set_error('', 'kodepro harus terdiri atas 8 karakter');
//        }            
//    }
}
function bidang_edit_form_submit($form, &$form_state) {
    
    $e_kodepro = $form_state['values']['e_kodepro'];
    
	$kodepro = $form_state['values']['kodepro'];
	$s2014 = $form_state['values']['s2014'];
	$t2014 = $form_state['values']['t2014'];
	$s2015 = $form_state['values']['s2015'];
	$t2015 = $form_state['values']['t2015'];
	$s2016 = $form_state['values']['s2016'];
	$t2016 = $form_state['values']['t2016'];
	$s2017 = $form_state['values']['s2017'];
	$t2017 = $form_state['values']['t2017'];
	$s2018 = $form_state['values']['s2018'];
	$t2018 = $form_state['values']['t2018'];
    
    if ($e_kodepro!='')
	{
        $sql = 'update {program} set s2014=\'%s\', t2014=\'%s\', s2015=\'%s\', t2015=\'%s\', s2016=\'%s\', t2016=\'%s\', s2017=\'%s\', t2017=\'%s\', t2018=\'%s\', s2018=\'%s\' where kodepro=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($s2014, $t2014, $s2015, $t2015, $s2016, $t2016, $s2017, $t2017, $s2018, $t2018,$e_kodepro));
    }
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('apbd/bidang/' );    
}
?>
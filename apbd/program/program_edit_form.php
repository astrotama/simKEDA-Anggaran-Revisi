<?php
    
function program_edit_form(){
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Edit Data',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	drupal_set_title('Data Program');
    drupal_add_css('files/css/kegiatancam.css');		
    $kodepro = arg(3);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($kodepro))
    {

			
        $sql = 'select kodepro, kodeu, program, s2015, s2016, s2017, s2018, t2015, t2016, t2017, t2018, np from {program} where kodepro=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($kodepro));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$kodepro = $data->kodepro;
				$kodeu = $data->kodeu;
				$program = $data->program;
				//$sifat = $data->sifat;

				$s2015 = $data->s2015;
				$s2016 = $data->s2016;
				$s2017 = $data->s2017;
				$s2018 = $data->s2018;
				
				$t2015 = $data->t2015;
				$t2016 = $data->t2016;
				$t2017 = $data->t2017;
				$t2018 = $data->t2018;

				$np = $data->np;
                $disabled =TRUE;
			} else {
				$kodepro = '';
			}
        } else {
			$kodepro = '';
		}
    } else {
		if (!user_access('program tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Data';
		$kodepro = '';
	}
    
	
	$form['formdata']['kodepro']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kode Program', 
		'#description'  => 'Kode, dengan format, UUUPP dimana UUU adalah kode urusan, PP adalah no. urut program', 
		'#maxlength'    => 5, 
		'#size'         => 5, 
		'#required'     => !$disabled, 
		'#disabled'     => $disabled, 
		'#default_value'=> $kodepro, 
	);
	$query = sprintf("select kodeu, urusansingkat from {urusan} order by urusan");
	$res = db_query($query);
	$urusanopt = array();
	$urusanopt['000'] = 'URUSAN PADA SEMUA SKPD';
	while ($data = db_fetch_object($res)) {
		$urusanopt[$data->kodeu] = $data->kodeu . ' - ' . $data->urusansingkat;
	}
	$form['formdata']['kodeu']= array(
		'#type'         => 'select', 
		'#title'        => 'Kode Urusan', 
		//'#description'  => 'kodeu',
		'#options'		=> $urusanopt,
		//'#maxlength'    => 3, 
		//'#size'         => 5, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeu, 
	); 
	//$form['formdata']['np']= array(
	//	'#type'         => 'textfield', 
	//	'#title'        => 'No Urut', 
	//	'#description'  => 'Nomor urut, no. urut program sesuai kode program. Yaitu dua digit terakhir pada kode program', 
	//	'#maxlength'    => 2, 
	//	'#size'         => 5, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $np, 
	//); 
	$form['formdata']['program']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Program', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $program, 
	); 
	//$form['formdata']['sifat']= array(
	//	'#type'         => 'textfield', 
	//	'#title'        => 'Sifat', 
	//	//'#description'  => 'sifat', 
	//	'#maxlength'    => 1, 
	//	'#size'         => 3, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $sifat, 
	//); 
	//$form['formdata']['sifat']= array(
	//	'#type' => 'radios', 
	//	'#title' => t('Sifat'), 
	//	'#default_value' => $sifat,
	//	'#options' => array(	
	//		 '0' => t('Rutin'), 	
	//		 '1' => t('Wajib'), 	
	//		 '2' => t('Pilihan'),	
	//	   ),
	//);	

	//2015
	$form['formdata']['tahun2015'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Tahun 2015',
		'#collapsible' => true,
		'#collapsed' => true,        
	);
	$form['formdata']['tahun2015']['s2015']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Sasaran', 
		'#description'  => 'Isian ini otomatis terisi dari RPJMD, isikan lagi bila anda ingin mengubahnya', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		'#default_value'=> $s2015, 
	); 	
	$form['formdata']['tahun2015']['t2015']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target', 
		'#description'  => 'Isian ini otomatis terisi dari RPJMD, isikan lagi bila anda ingin mengubahnya', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		'#default_value'=> $t2015, 
	); 

	//2016
	$form['formdata']['tahun2016'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Tahun 2016',
		'#collapsible' => true,
		'#collapsed' => true,        
	);
	$form['formdata']['tahun2016']['s2016']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Sasaran', 
		'#description'  => 'Isian ini otomatis terisi dari RPJMD, isikan lagi bila anda ingin mengubahnya', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		'#default_value'=> $s2016, 
	); 	
	$form['formdata']['tahun2016']['t2016']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target', 
		'#description'  => 'Isian ini otomatis terisi dari RPJMD, isikan lagi bila anda ingin mengubahnya', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		'#default_value'=> $t2016, 
	); 

	//2017
	$form['formdata']['tahun2017'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Tahun 2017',
		'#collapsible' => true,
		'#collapsed' => true,        
	);
	$form['formdata']['tahun2017']['s2017']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Sasaran', 
		'#description'  => 'Isian ini otomatis terisi dari RPJMD, isikan lagi bila anda ingin mengubahnya', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		'#default_value'=> $s2017, 
	); 	
	$form['formdata']['tahun2017']['t2017']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target', 
		'#description'  => 'Isian ini otomatis terisi dari RPJMD, isikan lagi bila anda ingin mengubahnya', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		'#default_value'=> $t2017, 
	); 

	//2018
	$form['formdata']['tahun2018'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Tahun 2018',
		'#collapsible' => true,
		'#collapsed' => true,        
	);
	$form['formdata']['tahun2018']['s2018']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Sasaran', 
		'#description'  => 'Isian ini otomatis terisi dari RPJMD, isikan lagi bila anda ingin mengubahnya', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		'#default_value'=> $s2018, 
	); 	
	$form['formdata']['tahun2018']['t2018']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target', 
		'#description'  => 'Isian ini otomatis terisi dari RPJMD, isikan lagi bila anda ingin mengubahnya', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		'#default_value'=> $t2018, 
	); 
	
    $form['formdata']['e_kodepro']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodepro, 
    ); 

	if (user_access('program edit')) {
		//drupal_access_denied();	
		$form['formdata']['submit'] = array (
			'#type' => 'submit',
			'#suffix' => "&nbsp;<a href='/apbd/program' class='btn_blue' style='color: white'>Batal</a>",
			'#value' => 'Simpan'
		);
	}
		
    
    return $form;
}
function program_edit_form_validate($form, &$form_state) {
//$kodepro = arg(3);
//    if (!isset($kodepro)) {
//        if (strlen($form_state['values']['kodepro']) < 8 ) {
//            form_set_error('', 'kodepro harus terdiri atas 8 karakter');
//        }            
//    }
}
function program_edit_form_submit($form, &$form_state) {
    
   $e_kodepro = $form_state['values']['e_kodepro'];
    
	$kodepro = $form_state['values']['kodepro'];
	$kodeu = $form_state['values']['kodeu'];
	$sifat = substr($kodeu, 0, 1);	
	
	//$tahun = $form_state['values']['tahun'];
	$tahun = '2015';
	$program = $form_state['values']['program'];
	
	

	$s2015 = $form_state['values']['s2015'];
	$t2015 = $form_state['values']['t2015'];
	$s2016 = $form_state['values']['s2016'];
	$t2016 = $form_state['values']['t2016'];
	$s2017 = $form_state['values']['s2017'];
	$t2017 = $form_state['values']['t2017'];
	$s2018 = $form_state['values']['s2018'];
	$t2018 = $form_state['values']['t2018'];
	
	//$np = $form_state['values']['np'];
	$np = substr($kodepro, -2);
    
    if ($e_kodepro=='')
    {
        $sql = 'insert into {program} (kodepro,kodeu,tahun,program,sifat,s2015,t2015,s2016,t2016,s2017,t2017,s2018,t2018,np) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($kodepro), strtoupper($kodeu), strtoupper($tahun), $program, strtoupper($sifat), $s2015, $t2015, $s2016, $t2016, $s2017, $t2017, $s2018, $t2018, strtoupper($np)));
    } else {
        $sql = 'update {program} set kodeu=\'%s\', program=\'%s\', sifat=\'%s\', s2015=\'%s\', t2015=\'%s\', s2016=\'%s\', t2016=\'%s\', s2017=\'%s\', t2017=\'%s\', s2018=\'%s\', t2018=\'%s\', np=\'%s\' where kodepro=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($kodeu), $program, strtoupper($sifat), $s2015, $t2015, $s2016, $t2016, $s2017, $t2017, $s2018, $t2018, strtoupper($np), $e_kodepro));
    }
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('apbd/program/' );    
}
?>
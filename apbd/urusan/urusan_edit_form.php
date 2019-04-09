<?php
    
function urusan_edit_form(){
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Edit Data',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $kodeu = arg(3);
    drupal_add_css('files/css/kegiatancam.css');		
	drupal_set_title('Data Urusan Pemerintahan');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($kodeu))
    {
        if (!user_access('urusan edit'))
            drupal_access_denied();
			
        $sql = 'select kodeu,sifat,urusan,urusansingkat, kodebid from {urusan} where kodeu=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($kodeu));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$kodeu = $data->kodeu;
				$sifat = $data->sifat;
				$urusan = $data->urusan;
				$urusansingkat = $data->urusansingkat;
				$kodebid = $data->kodebid;
                $disabled =TRUE;
			} else {
				$kodeu = '';
			}
        } else {
			$kodeu = '';
		}
    } else {
		if (!user_access('urusan tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Data';
		$kodeu = '';
	}
    
	
	$form['formdata']['kodeu']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kode', 
		//'#description'  => 'kodeu', 
		'#maxlength'    => 3, 
		'#size'         => 6, 
		'#required'     => !$disabled, 
		'#disabled'     => $disabled, 
		'#default_value'=> $kodeu, 
	); 
	$form['formdata']['sifat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Sifat', 
		//'#description'  => 'sifat', 
		'#maxlength'    => 1, 
		'#size'         => 3, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $sifat, 
	); 
	$form['formdata']['urusan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama', 
		//'#description'  => 'urusan', 
		'#maxlength'    => 100, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $urusan, 
	); 
	$form['formdata']['urusansingkat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nama Singkat', 
		//'#description'  => 'urusansingkat', 
		'#maxlength'    => 50, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $urusansingkat, 
	);
	$squery = "select kodebid, bidang from {bidang} order by bidang";
	$sres = db_query($squery);
	$bidang= array();
	while ($sdata = db_fetch_object($sres)) {
		$bidang[$sdata->kodebid] = $sdata->bidang;
	}
	$form['formdata']['kodebid']= array(
		'#type'         => 'select', 
		'#title'        => 'Bidang',
		'#options'		=> $bidang,
		//'#description'  => 'kodeu', 
		'#default_value'=> $kodebid, 
	); 
	
    $form['formdata']['e_kodeu']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodeu, 
    ); 
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/urusan' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function urusan_edit_form_validate($form, &$form_state) {
	$kodeu = arg(3);
    if (!isset($kodeu)) {
        if (strlen($form_state['values']['kodeu']) != 3 ) {
            form_set_error('', 'Kode Urusan harus terdiri atas 3 karakter');
        }            
    }
}
function urusan_edit_form_submit($form, &$form_state) {
    
    $e_kodeu = $form_state['values']['e_kodeu'];
    
	$kodeu = $form_state['values']['kodeu'];
	$sifat = $form_state['values']['sifat'];
	$urusan = $form_state['values']['urusan'];
	$urusansingkat = $form_state['values']['urusansingkat'];
    
    if ($e_kodeu=='')
    {
        $sql = 'insert into {urusan} (kodeu,sifat,urusan,urusansingkat) values(\'%s\', \'%s\', \'%s\', \'%s\')';        
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($kodeu), strtoupper($sifat), strtoupper($urusan), strtoupper($urusansingkat)));
    } else {
        $sql = 'update {urusan} set sifat=\'%s\', urusan=\'%s\', urusansingkat=\'%s\' where kodeu=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($sifat), strtoupper($urusan), strtoupper($urusansingkat), $e_kodeu));
    }
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('apbd/urusan');    
}
?>
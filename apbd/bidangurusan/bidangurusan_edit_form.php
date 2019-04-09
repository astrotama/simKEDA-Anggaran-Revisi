<?php
    
function bidangurusan_edit_form(){
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Edit Data',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    $kodebid = arg(3);
	$kodeu = arg(4);
	$tahun = variable_get("apbdtahun", 0);
    drupal_add_css('files/css/kegiatancam.css');	
	drupal_set_title('Data Bidang Urusan');
	
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($kodebid))
    {
        if (!user_access('bidangurusan edit'))
            drupal_access_denied();
			
        $sql = 'select kodebid,kodeu,tahun from {bidangurusan} where tahun = %s and kodebid=\'%s\' and kodeu=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($tahun, $kodebid, $kodeu));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$kodebid = $data->kodebid;
				$tahun = $data->tahun;
				$kodeu = $data->kodeu;
                $disabled =TRUE;
			} else {
				$kodebid = '';
				//$tahun = variable_get('apbdtahun', 0);
				$kodeu = "";
			}
        } else {
			$kodebid = '';
			//$tahun = variable_get('apbdtahun', 0);
			$kodeu = "";
		}
    } else {
		if (!user_access('bidangurusan tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Data';
		$kodebid = $_SESSION['kodebid'];
	}
    $query = sprintf("select kodebid, bidang from {bidang} order by bidang ");
	$qres = db_query($query);
	$bidang = array();
	while ($data = db_fetch_object($qres)) {
		$bidang[$data->kodebid] = $data->bidang;
	}
	
    $query = sprintf("select kodeu, urusansingkat from {urusan} order by urusansingkat");
	$qres = db_query($query);
	$urusan = array();
	while ($data = db_fetch_object($qres)) {
		$urusan[$data->kodeu] = $data->urusansingkat;
	}

	$form['formdata']['kodebid']= array(
		'#type'         => 'select', 
		'#title'        => 'Bidang',
		'#options'		=> $bidang,
		//'#description'  => 'kodebid', 
		//'#maxlength'    => 2, 
		//'#size'         => 10, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodebid, 
	); 
	$form['formdata']['kodeu']= array(
		'#type'         => 'select', 
		'#title'        => 'Urusan', 
		'#options'		=> $urusan,
		//'#description'  => 'bidang', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeu, 
	); 
    $form['formdata']['e_kodebid']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodebid, 
    ); 
    $form['formdata']['e_kodeu']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodeu, 
    ); 
    $form['formdata']['e_tahun']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $tahun, 
    ); 
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/bidangurusan' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function bidangurusan_edit_form_validate($form, &$form_state) {
}
function bidangurusan_edit_form_submit($form, &$form_state) {
    
    $e_kodebid = $form_state['values']['e_kodebid'];
    $e_kodeu = $form_state['values']['e_kodeu'];
	$tahun = $form_state['values']['e_tahun'];
    
	$kodebid = $form_state['values']['kodebid'];
	$kodeu = $form_state['values']['kodeu'];
    
    if ($e_kodebid=='')
    {
        $sql = 'insert into {bidangurusan} (tahun,kodebid,kodeu) values(\'%s\', \'%s\', \'%s\')';        
        $res = db_query(db_rewrite_sql($sql), array($tahun, $kodebid, $kodeu));
    } else {
        $sql = 'update {bidangurusan} set tahun=\'%s\', kodebid=\'%s\', kodeu=\'%s\' where tahun=\'%s\' and kodebid=\'%s\' and kodeu=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($tahun, $kodebid, $kodeu, $tahun, $kodebid, $kodeu));
    }
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
	$_SESSION['kodebid'] = $kodebid;
    drupal_goto('apbd/bidangurusan');    
}
?>
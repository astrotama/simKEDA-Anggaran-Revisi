<?php
    
function kegiatanlt_edit_form(){
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Edit Data',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	drupal_add_js('files/js/kegiatanlt.js');
    drupal_add_css('files/css/kegiatancam.css');
	drupal_set_title('Master Data Kegiatan');
    $kegid = arg(3);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
	$u1='0';
	$u2='0';
	$nk="";
    $disabled = FALSE;
	$kodepro = $_SESSION['kodepro'];
    if (isset($kegid))
    {
        if (!user_access('kegiatanlt edit'))
            drupal_access_denied();
			
        $sql = 'select kegid,u1,u2,np,nk,kegiatan,kodepro,kodeu from {kegiatanlt} where kegid=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($kegid));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$kegid = $data->kegid;
				$u1 = $data->u1;
				$u2 = $data->u2;
				$np = $data->np;
				$nk = $data->nk;
				$kegiatan = $data->kegiatan;
				$kodepro = $data->kodepro;
				$kodeu = $data->kodeu;
                $disabled =TRUE;
			} else {
				$kegid = '';
			}
        } else {
			$kegid = '';
		}
    } else {
		if (!user_access('kegiatanlt tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Data';
		$kegid = '';
	}
    
	
	$form['formdata']['kegid']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kegid', 
		'#description'  => 'kegid', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kegid, 
	); 
	$form['formdata']['u1']= array(
		'#type'         => 'hidden', 
		'#title'        => 'u1', 
		'#description'  => 'u1', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $u1, 
	); 
	$form['formdata']['u2']= array(
		'#type'         => 'hidden', 
		'#title'        => 'u2', 
		'#description'  => 'u2', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $u2, 
	); 
	$form['formdata']['np']= array(
		'#type'         => 'hidden', 
		'#title'        => 'np', 
		'#description'  => 'np', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $np, 
	); 
	$query = sprintf("select kodepro, program from program order by kodepro");
	$res = db_query($query);	
	$programop = array();
	while($data = db_fetch_object($res)) {
		$programop [$data->kodepro] = $data->kodepro . " - " . $data->program;
	}
	$form['formdata']['program']= array(
		'#type'         => 'select', 
		'#title'        => 'Program',
		'#options'		=> $programop,
		'#attributes'	=> array('style'=>'width: 400px;'),
		//'#description'  => 'kegiatan', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodepro, 
	); 
	$form['formdata']['nk']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nomor Kegiatan', 
		//'#description'  => 'nk', 
		'#maxlength'    => 3, 
		'#size'         => 10, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $nk, 
	);
	
	$form['formdata']['kegiatan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kegiatan', 
		//'#description'  => 'kegiatan', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kegiatan, 
	); 
	$form['formdata']['kodepro']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodepro', 
		'#description'  => 'kodepro', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodepro, 
	); 
	$form['formdata']['kodeu']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodeu', 
		'#description'  => 'kodeu', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeu, 
	); 
    $form['formdata']['e_nk']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $nk, 
    ); 
    $form['formdata']['e_kegiatan']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kegiatan, 
    ); 
    $form['formdata']['e_kegid']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kegid, 
    ); 
	$url ="";
	if (strlen($_SESSION['kodeu']) > 0) {
		$url = "/filter/" . $_SESSION['kodeu'];
		if (strlen($_SESSION['kodepro'])>0)
			$url .= "/" . $_SESSION['kodepro'];
	}
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanlt" . $url . "' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function kegiatanlt_edit_form_validate($form, &$form_state) {
//$kegid = arg(3);
//    if (!isset($kegid)) {
//        if (strlen($form_state['values']['kegid']) < 8 ) {
//            form_set_error('', 'kegid harus terdiri atas 8 karakter');
//        }            
//    }
}
function kegiatanlt_edit_form_submit($form, &$form_state) {
    
    $e_kegid = $form_state['values']['e_kegid'];
    
	$kegid = $form_state['values']['kegid'];
	$u1 = $form_state['values']['u1'];
	$u2 = $form_state['values']['u2'];
	$nk = $form_state['values']['nk'];
	$kegiatan = $form_state['values']['kegiatan'];
	$kodepro = $form_state['values']['program'];

	$query = sprintf ("select kodeu, np from {program} where kodepro='%s'", $kodepro);
	$result = db_query($query);
	if ($data = db_fetch_object($result)) {
		$np = $data->np;
		$kodeu = $data->kodeu;
		
	}

    
    if ($e_kegid=='')
    {
        $sql = 'insert into {kegiatanlt} (u1,u2,np,nk,kegiatan,kodepro,kodeu) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($u1), strtoupper($u2), strtoupper($np), strtoupper($nk), $kegiatan, strtoupper($kodepro), strtoupper($kodeu)));
    } else {
        $sql = 'update {kegiatanlt} set u1=\'%s\', u2=\'%s\', np=\'%s\', nk=\'%s\', kegiatan=\'%s\', kodepro=\'%s\', kodeu=\'%s\' where kegid=%s ';
        $res = db_query(db_rewrite_sql($sql), array(strtoupper($u1), strtoupper($u2), strtoupper($np), strtoupper($nk), $kegiatan, strtoupper($kodepro), strtoupper($kodeu), $e_kegid));
    }
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    //drupal_goto('apbd/kegiatanlt');
	drupal_goto("apbd/kegiatanlt/filter/" . $kodeu . "/" . $kodepro);
}
?>
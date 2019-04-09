<?php
    
function subkegiatanskpd_edit_form(){
    drupal_add_css('files/css/kegiatancam.css');	
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Edit Data Sub Kegiatan Renja SKPD',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	$kodekeg=arg(4);
    $id = arg(5);
	drupal_set_title('Sub Kegiatan Renja SKPD');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($id))
    {
        if (!user_access('kegiatanskpd edit'))
            drupal_access_denied();
			
        $sql = 'select id,kodekeg,uraian,jumlah,jumlahsebelum,jumlahsesudah from {kegiatanskpdsub} where id=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($id));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$id = $data->id;
				$kodekeg = $data->kodekeg;
				$uraian = $data->uraian;
				$lokasi = $data->lokasi;
				$jumlah = $data->jumlah;
				$jumlahsebelum = $data->jumlahsebelum;
				$jumlahsesudah = $data->jumlahsesudah;
                $disabled =TRUE;
			} else {
				$id = '';
			}
        } else {
			$id = '';
		}
    } else {
		if (!user_access('kegiatanskpd tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Sub Kegiatan Renja SKPD';
		$id = '';
	}
    
	
	$form['formdata']['id']= array(
		'#type'         => 'hidden', 
		'#title'        => 'id', 
		//'#description'  => 'id', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $id, 
	); 
	$form['formdata']['uraian']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Uraian', 
		//'#description'  => 'uraian', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraian',
		'#default_value'=> $uraian, 
	); 
	$form['formdata']['jumlah']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jumlah',
		'#attributes'	=> array('style' => 'text-align: right'),
		//'#description'  => 'jumlah', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jumlah, 
	); 
	$form['formdata']['jumlahsebelum']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tahun Lalu',
		'#attributes'	=> array('style' => 'text-align: right'),
		//'#description'  => 'jumlahsebelum', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jumlahsebelum, 
	); 
	$form['formdata']['jumlahsesudah']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tahun Depan',
		'#attributes'	=> array('style' => 'text-align: right'),
		//'#description'  => 'jumlahsesudah', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jumlahsesudah, 
	); 
	
    $form['formdata']['e_id']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $id, 
    ); 
    $form['formdata']['e_kodekeg']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodekeg, 
    ); 
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd/subkegiatan/" . $kodekeg . "' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function subkegiatanskpd_edit_form_validate($form, &$form_state) {
//$id = arg(3);
//    if (!isset($id)) {
//        if (strlen($form_state['values']['id']) < 8 ) {
//            form_set_error('', 'id harus terdiri atas 8 karakter');
//        }            
//    }
}
function subkegiatanskpd_edit_form_submit($form, &$form_state) {
    
    $e_id = $form_state['values']['e_id'];
    $e_kodekeg = $form_state['values']['e_kodekeg'];
	
	$id = $form_state['values']['id'];
	$kodekeg = $form_state['values']['kodekeg'];
	$uraian = $form_state['values']['uraian'];
	$lokasi = $form_state['values']['lokasi'];
	$jumlah = $form_state['values']['jumlah'];
	$jumlahsebelum = $form_state['values']['jumlahsebelum'];
	$jumlahsesudah = $form_state['values']['jumlahsesudah'];

	$sql = 'select kodero from {rincianobyek} where uraian=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($uraian));
	if ($res) {
		$data = db_fetch_object($res);
		if ($data) {
			
			$kodero = $data->kodero;
		} else {
			$kodero = '';
		}
	} else {
		$kodero = '';
	}
	
    
    if ($e_id=='')
    {
        $sql = 'insert into {kegiatanskpdsub} (kodekeg,kodero,uraian,jumlah,jumlahsebelum,jumlahsesudah) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
        $res = db_query(db_rewrite_sql($sql), array($e_kodekeg, $kodero,$uraian, $jumlah, $jumlahsebelum, $jumlahsesudah));
    } else {
        $sql = 'update {kegiatanskpdsub} set uraian=\'%s\', kodero=\'%s\', jumlah=\'%s\', jumlahsebelum=\'%s\', jumlahsesudah=\'%s\' where id=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($uraian, $kodero, $jumlah, $jumlahsebelum, $jumlahsesudah, $e_id));
    }
	
	//UPDATE jumlah KEGIATAN
	$sql = sprintf("select sum(jumlah) as jumlahsub from {kegiatanskpdsub} where kodekeg='%s'",
		   $e_kodekeg
		   );
	$result = db_query($sql);
	if ($data = db_fetch_object($result)) {		
		$jumlahsub = $data->jumlahsub;
		
		$sql = sprintf("update {kegiatanskpd} set total='%s' where kodekeg='%s'",
				db_escape_string($jumlahsub),
				db_escape_string($jumlahsub),
				$e_kodekeg);		
        $res = db_query($sql);
		
	}
	
	//END UPDATE jumlah KEGIATAN
    
	if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('apbd/kegiatanskpd/subkegiatan/' . $e_kodekeg);    
}
?>
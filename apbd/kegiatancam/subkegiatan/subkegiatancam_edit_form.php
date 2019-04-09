<?php
    
function subkegiatancam_edit_form(){
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Edit Data',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	drupal_add_css('files/css/kegiatancam.css');
	$kodekeg=arg(4);
    $id = arg(5);
	drupal_set_message('Data Sub Kegiatan');
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($id))
    {
        if (!user_access('kegiatancam edit'))
            drupal_access_denied();
			
        $sql = 'select id,kodekeg,uraian,lokasi,total,totalsebelum,totalsesudah from {kegiatankecsub} where id=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($id));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				$id = $data->id;
				$kodekeg = $data->kodekeg;
				$uraian = $data->uraian;
				$lokasi = $data->lokasi;
				$total = $data->total;
				$totalsebelum = $data->totalsebelum;
				$totalsesudah = $data->totalsesudah;
                $disabled =TRUE;
			} else {
				$id = '';
			}
        } else {
			$id = '';
		}
    } else {
		if (!user_access('kegiatancam tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Sub Kegiatan';
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
		'#default_value'=> $uraian, 
	); 
	$form['formdata']['lokasi']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Lokasi', 
		//'#description'  => 'lokasi', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $lokasi, 
	); 
	$form['formdata']['total']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jumlah',
		'#attributes'	=> array('style' => 'text-align: right'),
		//'#description'  => 'total', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $total, 
	); 
	$form['formdata']['totalsebelum']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tahun Lalu',
		'#attributes'	=> array('style' => 'text-align: right'),
		//'#description'  => 'totalsebelum', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $totalsebelum, 
	); 
	$form['formdata']['totalsesudah']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tahun Depan',
		'#attributes'	=> array('style' => 'text-align: right'),
		//'#description'  => 'totalsesudah', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $totalsesudah, 
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
		'#suffix' => "&nbsp;<a href='/apbd/kegiatancam/subkegiatan/" . $kodekeg . "' class='btn_blue' style='color: white'>Batal</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}
function subkegiatancam_edit_form_validate($form, &$form_state) {
//$id = arg(3);
//    if (!isset($id)) {
//        if (strlen($form_state['values']['id']) < 8 ) {
//            form_set_error('', 'id harus terdiri atas 8 karakter');
//        }            
//    }
}
function subkegiatancam_edit_form_submit($form, &$form_state) {
    
    $e_id = $form_state['values']['e_id'];
    $e_kodekeg = $form_state['values']['e_kodekeg'];
	
	$id = $form_state['values']['id'];
	$kodekeg = $form_state['values']['kodekeg'];
	$uraian = $form_state['values']['uraian'];
	$lokasi = $form_state['values']['lokasi'];
	$total = $form_state['values']['total'];
	$totalsebelum = $form_state['values']['totalsebelum'];
	$totalsesudah = $form_state['values']['totalsesudah'];
    
    if ($e_id=='')
    {
        $sql = 'insert into {kegiatankecsub} (kodekeg,uraian,lokasi,total,totalsebelum,totalsesudah) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
        $res = db_query(db_rewrite_sql($sql), array($e_kodekeg, $uraian, $lokasi, $total, $totalsebelum, $totalsesudah));
    } else {
        $sql = 'update {kegiatankecsub} set uraian=\'%s\', lokasi=\'%s\', total=\'%s\', totalsebelum=\'%s\', totalsesudah=\'%s\' where id=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($uraian, $lokasi, $total, $totalsebelum, $totalsesudah, $e_id));
    }

	//UPDATE TOTAL KEGIATAN
	$sql = sprintf("select sum(total) as totalsub from {kegiatankecsub} where kodekeg='%s'",
		   $e_kodekeg
		   );
	$result = db_query($sql);
	if ($data = db_fetch_object($result)) {		
		$totalsub = $data->totalsub;
		
		$sql = sprintf("update {kegiatankec} set totalpenetapan='%s',total='%s' where kodekeg='%s'",
				db_escape_string($totalsub),
				db_escape_string($totalsub),
				$e_kodekeg);		
        $res = db_query($sql);
		
	}
	
	//END UPDATE TOTAL KEGIATAN
	
    if ($res)
        drupal_set_message('Penyimpanan data berhasil dilakukan');
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('apbd/kegiatancam/subkegiatan/' . $e_kodekeg);    
}
?>
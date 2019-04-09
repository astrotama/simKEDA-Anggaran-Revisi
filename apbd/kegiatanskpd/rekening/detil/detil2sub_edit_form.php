<?php
    
function detil2sub_edit_form(){
    drupal_add_css('files/css/kegiatancam.css');	
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Mengisi Sub Detil Rekening',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	$kodekeg = arg(6);
	$kodero = arg(7);
	$iddetil = arg(8);
	
	//drupal_set_message($iddetil);
	//drupal_set_message($kodero);
	
	$title = 'Sub Detil Rincian Rekening ';
	if (isset($iddetil)) {
        $sql = 'select uraian,uraian,unitjumlah,unitsatuan,volumjumlah,
		volumsatuan,harga,total from {anggperkegdetil} where {iddetil}=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($iddetil));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) 
				$title = $data->uraian;
				$unitjumlah = $data->unitjumlah;
				$unitsatuan = $data->unitsatuan;
				$volumjumlah = $data->volumjumlah;
				$volumsatuan = $data->volumsatuan;
				$harga = $data->harga;
		
		}
		
	}
	
	drupal_set_title($title);
	
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
    
	$form['formdata']['kodekeg']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodekeg', 
		'#default_value'=> $kodekeg, 
	); 
	$form['formdata']['kodero']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodero', 
		'#default_value'=> $kodero, 
	); 
	$form['formdata']['iddetil']= array(
		'#type'         => 'hidden', 
		'#title'        => 'iddetil', 
		'#default_value'=> $iddetil, 
	); 
	$form['formdata']['idsub']= array(
		'#type'         => 'hidden', 
		'#title'        => 'idsub', 
		'#default_value'=> $idsub, 
	); 
	$form['formdata']['totalsubdetillama']= array(
		'#type'         => 'hidden', 
		'#title'        => 'totalsubdetillama', 
		'#default_value'=> $totalsubdetillama, 
	); 

	$form['formdata']['uraian']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Uraian', 
		'#size'         => 70, 
		//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraianbelanja',
		'#default_value'=> $uraian, 
	); 
	$form['formdata']['unitjumlah']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Unit Jumlah', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#default_value'=> $unitjumlah, 
	); 
	$form['formdata']['unitsatuan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Unit Satuan', 
		'#size'         => 30, 
		//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
		'#default_value'=> $unitsatuan, 
	); 
	$form['formdata']['volumjumlah']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Volume Jumlah', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#default_value'=> $volumjumlah, 
	); 
	$form['formdata']['volumsatuan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Volume Satuan', 
		'#size'         => 30, 
		//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
		'#default_value'=> $volumsatuan, 
	); 
	
	$form['formdata']['harga']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Harga Satuan', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#default_value'=> $harga, 
	); 	
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd/rekening/detil/subdetil/" . $kodekeg . '/' . $kodero . '/' . $iddetil . "' class='btn_blue' style='color: white'>Tutup</a>",
        '#value' => 'Simpan'
    );
    
    return $form;
}

function detil2sub_edit_form_validate($form, &$form_state) {
	$iddetilmaster = $form_state['values']['iddetilmaster'];	
		 
	if ($iddetilmaster=='') {
		form_set_error('', 'Detil atasnya belum diisi.');
	} 
}

function detil2sub_edit_form_submit($form, &$form_state) {

    $kodekeg = $form_state['values']['kodekeg'];
	$kodero = $form_state['values']['kodero'];
    $iddetil = $form_state['values']['iddetil'];
	$iddetilmaster = $form_state['values']['iddetilmaster'];

	//MASUKKAN KE SUB DETIL
	$uraian = $form_state['values']['uraian'];
	$unitjumlah = $form_state['values']['unitjumlah'];
	$unitsatuan = $form_state['values']['unitsatuan'];
	$volumjumlah = $form_state['values']['volumjumlah'];
	$volumsatuan = $form_state['values']['volumsatuan'];
	$harga = $form_state['values']['harga'];
	
	$total  = $unitjumlah * $volumjumlah * $harga;
	
	$sql = 'insert into {anggperkegdetilsub} (iddetil, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
	$res = db_query(db_rewrite_sql($sql), array($iddetil, $uraian, $unitjumlah, $unitsatuan, $volumjumlah, $volumsatuan, $harga, $unitjumlah * $volumjumlah * $harga));	
			
	if ($res)
		drupal_set_message('Penyimpanan data berhasil dilakukan');
	else
		drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
	drupal_goto('apbd/kegiatanskpd/rekening/detil/subdetil/' . $kodekeg . '/' . $kodero . '/' . $iddetil);    
}
?>
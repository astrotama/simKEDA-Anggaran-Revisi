<?php
    
function detil_editsub_form(){
    drupal_add_css('files/css/kegiatancam.css');	
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Mengisi Sub Detil Rekening',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	$kodekeg=arg(5);
    $kodero = arg(6);
	$iddetil = arg(7);

	//drupal_set_message($iddetil);
	
	$title = 'Sub Detil Rincian Rekening ';
	if (isset($iddetil)) {
        $sql = 'select uraian from {anggperkegdetil} where {iddetil}=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($iddetil));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) 
				$title .= $data->uraian;
		
		}
		
	}
	
	$title =l($title, 'apbd/kegiatanskpd/rekening/detilsub/' . $iddetil, array('html'=>true));	
	drupal_set_title($title);
	
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');

	$i = 0;
    if (isset($iddetil))
    {
        if (!user_access('kegiatanskpd edit'))
            drupal_access_denied();

		$form['formdata']['kodero']= array(
			'#type'         => 'hidden', 
			'#title'        => 'kodero', 
			'#default_value'=> $kodero, 
		); 
		$form['formdata']['kodekeg']= array(
			'#type'         => 'hidden', 
			'#title'        => 'kodekeg', 
			'#default_value'=> $kodekeg, 
		); 
		$form['formdata']['iddetil']= array(
			'#type'         => 'hidden', 
			'#title'        => 'iddetil', 
			'#default_value'=> $iddetil, 
		); 
		
        $sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total 
		   from {anggperkegdetilsub} where iddetil=\'%s\' order by idsub';
        $res = db_query(db_rewrite_sql($sql), array ($iddetil));
        if ($res) {
			//drupal_set_message('x');
            while ($data = db_fetch_object($res)) {
				$i += 1; 
				
				//drupal_set_message($i . ' ' . $data->uraian);
				$form['formdata']['detil' . $i] = array (
					'#type' => 'fieldset',
					'#title'=> 'Sub Detil Rekening #' . $i,
					'#collapsible' => true,
					'#collapsed' => false,        
				);

				$form['formdata']['detil' . $i]['idsub' . $i]= array(
					'#type'         => 'hidden', 
					'#default_value'=> $data->idsub, 
				); 
				$form['formdata']['detil' . $i]['uraian' . $i]= array(
					'#type'         => 'textfield', 
					'#title'        => 'Uraian', 
					'#size'         => 70, 
					//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraianbelanja',
					'#default_value'=> $data->uraian, 
				); 
				$form['formdata']['detil' . $i]['unitjumlah' . $i]= array(
					'#type'         => 'textfield', 
					'#title'        => 'Unit Jumlah', 
					'#attributes'	=> array('style' => 'text-align: right'),
					'#size'         => 30, 
					'#default_value'=> $data->unitjumlah, 
				); 
				$form['formdata']['detil' . $i]['unitsatuan' . $i]= array(
					'#type'         => 'textfield', 
					'#title'        => 'Unit Satuan', 
					'#size'         => 30, 
					//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
					'#default_value'=> $data->unitsatuan, 
				); 
				$form['formdata']['detil' . $i]['volumjumlah' . $i]= array(
					'#type'         => 'textfield', 
					'#title'        => 'Volume Jumlah', 
					'#attributes'	=> array('style' => 'text-align: right'),
					'#size'         => 30, 
					'#default_value'=> $data->volumjumlah, 
				); 
				$form['formdata']['detil' . $i]['volumsatuan' . $i]= array(
					'#type'         => 'textfield', 
					'#title'        => 'Volume Satuan', 
					'#size'         => 30, 
					//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
					'#default_value'=> $data->volumsatuan, 
				); 
				
				$form['formdata']['detil' . $i]['harga' . $i]= array(
					'#type'         => 'textfield', 
					'#title'        => 'Harga Satuan', 
					'#attributes'	=> array('style' => 'text-align: right'),
					'#size'         => 30, 
					'#default_value'=> $data->harga, 
				); 
				
			}
			
        } else 
			drupal_access_denied();
		
    } else {
		if (!user_access('kegiatanskpd tambah'))
			drupal_access_denied();
		//$form['formdata']['#title'] = 'Tambah Detil Rekening Kegiatan';
		$iddetil = '';
	}
    
	if ($i<=10)
		$detilnum = 10;
	else
		$detilnum = $i;
	$form['formdata']['detilnum']= array(
		'#type'         => 'hidden', 
		'#title'        => 'detilnum', 
		'#default_value'=> $detilnum, 
	); 
	
	//sisanya
	for ($x = $i+1; $x <= 10; $x++) {
		$collapsed = ($x>3);
		$form['formdata']['detil' . $x] = array (
			'#type' => 'fieldset',
			'#title'=> 'Sub Detil Rekening #' . $x,
			'#collapsible' => true,
			'#collapsed' => $collapsed,        
		);

		$form['formdata']['detil' . $x]['idsub' . $x]= array(
			'#type'         => 'hidden', 
			'#default_value'=> '', 
		); 
		$form['formdata']['detil' . $x]['uraian' . $x]= array(
			'#type'         => 'textfield', 
			'#title'        => 'Uraian', 
			'#size'         => 70, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraianbelanja',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil' . $x]['unitjumlah' . $x]= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil' . $x]['unitsatuan' . $x]= array(
			'#type'         => 'textfield', 
			'#title'        => 'Unit Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		$form['formdata']['detil' . $x]['volumjumlah' . $x]= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Jumlah', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '1', 
		); 
		$form['formdata']['detil' . $x]['volumsatuan' . $x]= array(
			'#type'         => 'textfield', 
			'#title'        => 'Volume Satuan', 
			'#size'         => 30, 
			//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/satuan',
			'#default_value'=> '', 
		); 
		
		$form['formdata']['detil' . $x]['harga' . $x]= array(
			'#type'         => 'textfield', 
			'#title'        => 'Harga Satuan', 
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> '' 
		); 		
	} 	
	
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd/rekening/detil/" . $kodekeg . '/' . $kodero . "' class='btn_blue' style='color: white'>Tutup</a>",
		'#value' => 'Simpan'
	);
	
    return $form;
}

function detil_editsub_form_validate($form, &$form_state) {
    $kodekeg = $form_state['values']['kodekeg'];
    $kodero = $form_state['values']['kodero'];
	$iddetil = $form_state['values']['iddetil'];
	$detilnum = $form_state['values']['detilnum']; 
	$seribu = 1000;
	
	for ($i = i+1; $i <= $detilnum; $i++) {
		$uraian = $form_state['values']['uraian' . $i];

		if ($uraian != '') {
			$idsub = $form_state['values']['idsub' . $i];
			$unitjumlah = $form_state['values']['unitjumlah' . $i];
			$unitsatuan = $form_state['values']['unitsatuan' . $i];
			$volumjumlah = $form_state['values']['volumjumlah' . $i];
			$volumsatuan = $form_state['values']['volumsatuan' . $i];
			$harga = $form_state['values']['harga' . $i];
			
			$totalsub = $unitjumlah * $volumjumlah * $harga;
			$totaldetil += $totalsub;
			
			//if (($totalsub % $seribu)>0) form_set_error('', 'Isian detil rekening #' . $i . ' tidak bulat per seribu');

		}
	}
	/*
	//Hitung detilnya dulu
	$uraian1 = $form_state['values']['uraian1'];
	if ($uraian1 != '') {
		$unitjumlah1 = $form_state['values']['unitjumlah1'];
		$volumjumlah1 = $form_state['values']['volumjumlah1'];
		$harga1 = $form_state['values']['harga1'];
		
		$total1 = $unitjumlah1 * $volumjumlah1 * $harga1;
		$totaldetil = $total1;
	}
	//CEK PER 1000
	if (($total1 % $seribu)>0) form_set_error('', 'Isian detil rekening #1 tidak bulat per seribu');
	
	$uraian2 = $form_state['values']['uraian2'];
	if ($uraian2 != '') {
		$unitjumlah2 = $form_state['values']['unitjumlah2'];
		$volumjumlah2 = $form_state['values']['volumjumlah2'];
		$harga2 = $form_state['values']['harga2'];
		
		$total2 = $unitjumlah2 * $volumjumlah2 * $harga2;
		$totaldetil += $total2;
	}
	//CEK PER 1000
	if (($total2 % $seribu)>0) form_set_error('', 'Isian detil rekening #2 tidak bulat per seribu');

	$uraian3 = $form_state['values']['uraian3'];
	if ($uraian3 != '') {
		$unitjumlah3 = $form_state['values']['unitjumlah3'];
		$volumjumlah3 = $form_state['values']['volumjumlah3'];
		$harga3 = $form_state['values']['harga3'];
		
		$total3 = $unitjumlah3 * $volumjumlah3 * $harga3;
		$totaldetil += $total3;
	}
	//CEK PER 1000
	if (($total3 % $seribu)>0) form_set_error('', 'Isian detil rekening #3 tidak bulat per seribu');

	$uraian4 = $form_state['values']['uraian4'];
	if ($uraian4 != '') {
		$unitjumlah4 = $form_state['values']['unitjumlah4'];
		$volumjumlah4 = $form_state['values']['volumjumlah4'];
		$harga4 = $form_state['values']['harga4'];
		
		$total4 = $unitjumlah4 * $volumjumlah4 * $harga4;
		$totaldetil += $total4;
	}
	//CEK PER 1000
	if (($total4 % $seribu)>0) form_set_error('', 'Isian detil rekening #4 tidak bulat per seribu');
	
	//5
	$uraian5 = $form_state['values']['uraian5'];
	if ($uraian5 != '') {
		$unitjumlah5 = $form_state['values']['unitjumlah5'];
		$volumjumlah5 = $form_state['values']['volumjumlah5'];
		$harga5 = $form_state['values']['harga5'];
		
		$total5 = $unitjumlah5 * $volumjumlah5 * $harga5;
		$totaldetil += $total5;
	}
	//CEK PER 1000
	if (($total5 % $seribu)>0) form_set_error('', 'Isian detil rekening #5 tidak bulat per seribu');
	
	//plafon
	$sql = sprintf("select plafon from {kegiatanskpd} where kodekeg='%s'", $kodekeg);
	$result = db_query($sql);
	if ($data = db_fetch_object($result)) {		
		$plafon = $data->plafon;
	}

	//6
	$uraian6 = $form_state['values']['uraian6'];
	if ($uraian6 != '') {
		$unitjumlah6 = $form_state['values']['unitjumlah6'];
		$volumjumlah6 = $form_state['values']['volumjumlah6'];
		$harga6 = $form_state['values']['harga6'];
		
		$total6 = $unitjumlah6 * $volumjumlah6 * $harga6;
		$totaldetil += $total6;
	}
	//CEK PER 1000
	if (($total6 % $seribu)>0) form_set_error('', 'Isian detil rekening #6 tidak bulat per seribu');

	//7
	$uraian7 = $form_state['values']['uraian7'];
	if ($uraian7 != '') {
		$unitjumlah7 = $form_state['values']['unitjumlah7'];
		$volumjumlah7 = $form_state['values']['volumjumlah7'];
		$harga7 = $form_state['values']['harga7'];
		
		$total7 = $unitjumlah7 * $volumjumlah7 * $harga7;
		$totaldetil += $total7;
	}
	//CEK PER 1000
	if (($total7 % $seribu)>0) form_set_error('', 'Isian detil rekening #7 tidak bulat per seribu');
	
	//8
	$uraian8 = $form_state['values']['uraian8'];
	if ($uraian8 != '') {
		$unitjumlah8 = $form_state['values']['unitjumlah8'];
		$volumjumlah8 = $form_state['values']['volumjumlah8'];
		$harga8 = $form_state['values']['harga8'];
		
		$total8 = $unitjumlah8 * $volumjumlah8 * $harga8;
		$totaldetil += $total8;
	}
	//CEK PER 1000
	if (($total8 % $seribu)>0) form_set_error('', 'Isian detil rekening #8 tidak bulat per seribu');

	//9
	$uraian9 = $form_state['values']['uraian9'];
	if ($uraian9 != '') {
		$unitjumlah9 = $form_state['values']['unitjumlah9'];
		$volumjumlah9 = $form_state['values']['volumjumlah9'];
		$harga9 = $form_state['values']['harga9'];
		
		$total9 = $unitjumlah9 * $volumjumlah9 * $harga9;
		$totaldetil += $total9;
	}
	//CEK PER 1000
	if (($total9 % $seribu)>0) form_set_error('', 'Isian detil rekening #9 tidak bulat per seribu');

	//10
	$uraian10 = $form_state['values']['uraian10'];
	if ($uraian10 != '') {
		$unitjumlah10 = $form_state['values']['unitjumlah10'];
		$volumjumlah10 = $form_state['values']['volumjumlah10'];
		$harga10 = $form_state['values']['harga10'];
		
		$total10 = $unitjumlah10 * $volumjumlah10 * $harga10;
		$totaldetil += $total10;
	}
	//CEK PER 1000
	if (($total10 % $seribu)>0) form_set_error('', 'Isian detil rekening #10 tidak bulat per seribu');
	*/

	//if (($totaldetil % $seribu)>0) form_set_error('', 'Jumlah isian sub detil rekening, ' . apbd_fn($totaldetil) . ' tidak bulat per seribu');
	
	//plafon
	$sql = sprintf("select plafon from {kegiatanskpd} where kodekeg='%s'", $kodekeg);
	$result = db_query($sql);
	if ($data = db_fetch_object($result)) {		
		$plafon = $data->plafon;
	}	

	//total
	$sql = sprintf("select sum(total) totalx from {anggperkegdetil} where kodekeg='%s' and iddetil<>'%s'", $kodekeg, $iddetil);
	$result = db_query($sql);
	if ($data = db_fetch_object($result)) {		
		$total = $data->totalx;
	}

	//CEK SERIBU
	if ((($total+$totaldetil) % $seribu)>0) form_set_error('', 'Jumlah isian sub detil rekening, ' . apbd_fn($totaldetil) . ', menjadikan jumlah rekening menjadi ' . apbd_fn($totaldetil+$total) .', yang tidak bulat per seribu');
	
	//CEK PLAFON
	/*
	if (($total+$totaldetil)>$plafon) {		
		form_set_error('', 'Isian rekening melebihi plafon, Plafon : ' . apbd_fn($plafon) . 
						   ', Sudah Masuk : ' . apbd_fn($total) . ', Isian Baru : ' . apbd_fn($totaldetil) );
	}
	*/
		

}
function detil_editsub_form_submit($form, &$form_state) {

    $kodekeg = $form_state['values']['kodekeg'];
	$kodero = $form_state['values']['kodero'];
	$iddetil = $form_state['values']['iddetil'];
	$detilnum = $form_state['values']['detilnum'];

	$sqlnew = 'insert into {anggperkegdetilsub} (iddetil, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, 
				harga, total) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';
	$sqlupdate = 'update {anggperkegdetilsub} set  uraian=\'%s\', unitjumlah=\'%s\', unitsatuan=\'%s\', 
				volumjumlah=\'%s\', volumsatuan=\'%s\', harga=\'%s\', total=\'%s\' where idsub=\'%s\' ';
	
	$total =0;
	//#1
	for ($i = i+1; $i <= $detilnum; $i++) {
		$uraian = $form_state['values']['uraian' . $i];
		$idsub = $form_state['values']['idsub' . $i];   

		if ($uraian != '') {
			$idsub = $form_state['values']['idsub' . $i];
			$unitjumlah = $form_state['values']['unitjumlah' . $i];
			$unitsatuan = $form_state['values']['unitsatuan' . $i];
			$volumjumlah = $form_state['values']['volumjumlah' . $i];
			$volumsatuan = $form_state['values']['volumsatuan' . $i];
			$harga = $form_state['values']['harga' . $i];
			
			$totalsub = $unitjumlah * $volumjumlah * $harga;
			$total += $totalsub;
			
			if ($idsub=='')
				$res = db_query(db_rewrite_sql($sqlnew), array($iddetil, $uraian, $unitjumlah, $unitsatuan, 
				$volumjumlah, $volumsatuan, $harga, $totalsub));	
			else
				$res = db_query(db_rewrite_sql($sqlupdate), array($uraian, $unitjumlah, $unitsatuan, $volumjumlah, $volumsatuan, $harga, $totalsub, $idsub));	
			
			if (!$res) drupal_set_message('Penyimpanan sub detil #' . $i . ' gagal dilakukan');
		}
		else {
			if ($idsub!='') {
				$sqldelete = 'delete from {anggperkegdetilsub} where idsub=\'%s\' ';
				$res = db_query(db_rewrite_sql($sqldelete), array($idsub));	
				
			}
		}
	}


	//UPDATE JUMLAH DETIL
	if ($res) {
		//drupal_set_message('d');
		
		$sql = sprintf("update {anggperkegdetil} set total='%s' where iddetil='%s'",
				db_escape_string($total), $iddetil);		
				
		//drupal_set_message($sql);
		$res = db_query($sql);
			
	}		
	
	
	//UPDATE JUMLAH REKENING
	if ($res) {
		
		$sql = sprintf("select sum(total) as jumlahrek from {anggperkegdetil} where kodekeg='%s' and kodero='%s'",
			   $kodekeg, $kodero);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {		
			$jumlahrek = $data->jumlahrek;
			
			$sql = sprintf("update {anggperkeg} set jumlah='%s' where kodekeg='%s' and kodero='%s'",
					db_escape_string($jumlahrek),
					db_escape_string($kodekeg),
					$kodero);		
			$res = db_query($sql);
			
		}
	}
	
	//UPDATE JUMLAH KEGIATAN
	$sql = sprintf("select sum(jumlah) as jumlahsub from {anggperkeg} where kodekeg='%s'", $kodekeg);
	$result = db_query($sql);
	if ($data = db_fetch_object($result)) {		
		$jumlahsub = $data->jumlahsub;
		
		$sql = sprintf("update {kegiatanskpd} set total='%s' where kodekeg='%s'", db_escape_string($jumlahsub), $kodekeg);		
		$res = db_query($sql);
		
	}
	
	if ($res)
		drupal_set_message('Penyimpanan data berhasil dilakukan');
	else
		drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
	drupal_goto('apbd/kegiatanskpd/rekening/detil/' . $kodekeg . '/' . $kodero);    
	
}
?>
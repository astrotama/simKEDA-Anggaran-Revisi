<?php
function psubdetil_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');	
    if ($arg) {
		//$qlike = sprintf(" and id='%s'", db_escape_string($id));    
		$kodeuk=arg(4);
		$kodero = arg(5);
		$iddetil=arg(6);
		
		$qlike = sprintf(" and iddetil='%s'", db_escape_string($iddetil));
		
		//drupal_set_message($kodeuk);
		//drupal_set_message($qlike);
		
	} else
		drupal_access_denied();
    
	$header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        //array('data' => ucwords(strtolower('kodeuk')), 'field'=> 'kodeuk', 'valign'=>'top'),
		array('data' => 'Uraian', 'valign'=>'top'),
		array('data' => 'Unit', 'valign'=>'top', 'width'=>'90px'),
		array('data' => 'Volume', 'valign'=>'top', 'width'=>'90px'),
		array('data' => '@Harga', 'valign'=>'top', 'width'=>'90px'),
		array('data' => 'Jml. Harga', 'valign'=>'top', 'width'=>'90px'),
		array('data' => '', 'width' => '150px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    //if ($tablesort=='') {
        $tablesort=' order by idsub';
    //} 

	$allowedit = (batastgl() || (isSuperuser()));	

	if ($allowedit==false) {
		//dispensasippas
		//$sqluk = sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
        $sql = sprintf('select dispensasippas from {unitkerja} where kodeuk=\'%s\'', apbd_getuseruk());
		$res = db_query($sql);
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {  		
				$allowedit = $data->dispensasippas;
			}
		}
	}
	
	$customwhere = ' ';
    $where = ' where true'  . $qlike ;

    $sql = 'select iddetil,idsub,uraian,unitjumlah,unitsatuan,volumjumlah,
			volumsatuan,harga,total	from {anggperukdetilsub}' . $where . $tablesort;
	
	//drupal_set_message($sql);
    $fsql = sprintf($sql, addslashes($nama));
    $no = 0;
	
	
	$result = db_query($fsql);
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$editlink = '';
			
			if (user_access('kegiatanskpd edit')) {
				$editlink .= l('Edit', 'apbd/pendapatan/detil/subdetil/edit/' . $kodeuk . '/'. $kodero . '/'.  $iddetil . '/' . $data->idsub, array('html'=>TRUE)) . '&nbsp;';
			
				if ($allowedit)
					$editlink .=l('Hapus', 'apbd/pendapatan/detil/subdetil/delete/' . $kodeuk . '/'. $kodero . '/'. $iddetil . '/' . $data->idsub, array('html'=>TRUE)) . '&nbsp;';
				else
					$editlink .= 'Hapus';
				
				$unitjumlah = $data->unitjumlah . ' ' . $data->unitsatuan;
				$volumjumlah = $data->volumjumlah . ' ' . $data->volumsatuan;
				$hargasatuan = apbd_fn($data->harga);
				$hargatotal = apbd_fn($data->total);

			}

            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => $unitjumlah, 'align' => 'left', 'valign'=>'top'),
				array('data' => $volumjumlah, 'align' => 'left', 'valign'=>'top'),
				array('data' => $hargasatuan, 'align' => 'right', 'valign'=>'top'),
				array('data' => $hargatotal, 'align' => 'right', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'5')
        );
    }
	$pquery = sprintf("select uraian,total from {anggperukdetil}  where iddetil='%s'",  db_escape_string($iddetil));
	//drupal_set_message($pquery);
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ptitle = $data->uraian . ', Anggaran: ' . apbd_fn($data->total);


	
	$ptitle =l($ptitle, 'apbd/pendapatan/' . $arg, array('html'=>true));
    $output .= theme_box($ptitle, theme_table($header, $rows));
	
	if (user_access('kegiatanskpd tambah'))
		$output .=  l('Sub Detil Baru', 'apbd/pendapatan/detil/subdetil/edit/'  . $kodeuk . '/'. $kodero . '/'. $iddetil , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')))  . "&nbsp;"  ;

		$output .= l('Daftar Detil Rekening', 'apbd/pendapatan/detil/' . $kodeuk . '/' . $kodero, array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')))  . "&nbsp;"  ;

		$output .= l('Daftar Rekening', 'apbd/pendapatan/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;		
	
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpd/subkegiatan/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

?>
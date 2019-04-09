<?php
function subdetil_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');	
    if ($arg) {
		//$qlike = sprintf(" and id='%s'", db_escape_string($id));    
		$kodekeg=arg(5);
		$kodero = arg(6);
		$iddetil=arg(7);
		
		$qlike = sprintf(" and iddetil='%s'", db_escape_string($iddetil));
		
		//drupal_set_message($iddetil);
		//drupal_set_message($qlike);
		
	} else
		drupal_access_denied();
    
	$header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        //array('data' => ucwords(strtolower('kodekeg')), 'field'=> 'kodekeg', 'valign'=>'top'),
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

	$customwhere = ' ';
    $where = ' where true'  . $qlike ;

    $sql = 'select iddetil,idsub,uraian,unitjumlah,unitsatuan,volumjumlah,
			volumsatuan,harga,total	from {anggperkegdetilsub}' . $where . $tablesort;
	
	//drupal_set_message($sql);
    $fsql = sprintf($sql, addslashes($nama));
    $no = 0;
	
	
	$result = db_query($fsql);
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$editlink = '';
			
			if (user_access('kegiatanskpd edit')) {
				$editlink .= l('Edit', 'apbd/kegiatanskpd/rekening/detil/subdetil/edit/' . $kodekeg . '/'. $kodero . '/'.  $iddetil . '/' . $data->idsub, array('html'=>TRUE)) . '&nbsp;';
			
                $editlink .=l('Hapus', 'apbd/kegiatanskpd/rekening/detil/subdetil/delete/' . $kodekeg . '/'. $kodero . '/'. $iddetil . '/' . $data->idsub, array('html'=>TRUE)) . '&nbsp;';

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
	$pquery = sprintf("select uraian,total from {anggperkegdetil}  where iddetil='%s'",  db_escape_string($iddetil));
	//drupal_set_message($pquery);
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ptitle = $data->uraian . ', Anggaran: ' . apbd_fn($data->total);


	
	$ptitle =l($ptitle, 'apbd/kegiatanskpd/rekening/' . $arg, array('html'=>true));
    $output .= theme_box($ptitle, theme_table($header, $rows));
	
	if (user_access('kegiatanskpd tambah'))
		$output .=  l('Sub Detil Baru', 'apbd/kegiatanskpd/rekening/detil/subdetil/edit/'  . $kodekeg . '/'. $kodero . '/'. $iddetil , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')))  . "&nbsp;"  ;

		$output .= l('Daftar Detil Rekening', 'apbd/kegiatanskpd/rekening/detil/' . $kodekeg . '/' . $kodero, array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')))  . "&nbsp;"  ;

		$output .= l('Daftar Rekening', 'apbd/kegiatanskpd/rekening/' . $kodekeg , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;		
	
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpd/subkegiatan/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

?>
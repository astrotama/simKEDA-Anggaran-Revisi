<?php
function detil_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');	
    if ($arg) {
		//$qlike = sprintf(" and id='%s'", db_escape_string($id));    
		$kodekeg=arg(4);
		$kodero = arg(5);
		
		$qlike = sprintf(" and kodekeg='%s' and kodero='%s'", db_escape_string($kodekeg), db_escape_string($kodero));
		
		//drupal_set_message($kodero);
		//drupal_set_message($qlike);
		
	} else
		drupal_access_denied();
    
	$header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        //array('data' => ucwords(strtolower('kodekeg')), 'field'=> 'kodekeg', 'valign'=>'top'),
		array('data' => '', 'width' => '5px', 'valign'=>'top'),
		array('data' => 'Uraian', 'valign'=>'top'),
		array('data' => 'Unit', 'valign'=>'top', 'width'=>'90px'),
		array('data' => 'Volume', 'valign'=>'top', 'width'=>'90px'),
		array('data' => '@Harga', 'valign'=>'top', 'width'=>'90px'),
		array('data' => 'Jml. Harga', 'valign'=>'top', 'width'=>'90px'),
		array('data' => '', 'width' => '200px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by iddetil';
    }

    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = ' ';
    $where = ' where true'  . $qlike ;

    $sql = 'select iddetil,kodekeg,kodero,uraian,unitjumlah,unitsatuan,volumjumlah,
			volumsatuan,harga,total,pengelompokan 
			from {anggperkegdetil}' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $no = 0;
	
	/*
	$limit = 15;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {anggperkegdetil}" . $where;
    $fcountsql = sprintf($countsql, addslashes($nama));
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
    
    $no=0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    }
	*/
	
	$result = db_query($fsql);
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$editlink = '';

			if ($data->pengelompokan) 
				//$inaktif = 'x';
				$group = "<img src='/files/group.png'>";
			
			else
				$group ='';
				
			$kodero = $data->kodero;
			if (user_access('kegiatanskpd edit')) {
				$editlink .= l('Edit', 'apbd/kegiatanskpd/rekening/detil/edit/' . $kodekeg . '/' . $kodero . '/' . $data->iddetil, array('html'=>TRUE)) . '&nbsp;';
			
                $editlink .=l('Hapus', 'apbd/kegiatanskpd/rekening/detil/delete/' . $kodekeg . '/' . $kodero . '/' . $data->iddetil, array('html'=>TRUE)) . '&nbsp;';

				if ($data->pengelompokan) {
					$editlink .= l('Subdetil', 'apbd/kegiatanskpd/rekening/detil/subdetil/' . $kodekeg . '/' . $kodero . '/' . $data->iddetil, array('html'=>TRUE));

					$editlink .= '&nbsp;' . l('Subsimple', 'apbd/kegiatanskpd/rekening/detil/editsub/' . $kodekeg . '/' . $kodero . '/' . $data->iddetil, array('html'=>TRUE));
					
					$unitjumlah = '';
					$volumjumlah = '';
					$hargasatuan = '';
					$hargatotal = apbd_fn($data->total);
					
					//if (isSuperuser()) {
					//	$editlink .= l('Subdetil', 'apbd/kegiatanskpd/rekening/detil/subdetil/' . $kodekeg . '/' . $kodero . '/' . $data->iddetil, array('html'=>TRUE));
					
					//}
					
				} else {
					$editlink .= 'Subdetil' . '&nbsp;' . 'Subsimple' ;

					$unitjumlah = $data->unitjumlah . ' ' . $data->unitsatuan;
					$volumjumlah = $data->volumjumlah . ' ' . $data->volumsatuan;
					$hargasatuan = apbd_fn($data->harga);
					$hargatotal = apbd_fn($data->total);
				}
			}

            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                array('data' => $group, 'align' => 'right', 'valign'=>'top'),
				array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => $unitjumlah, 'align' => 'left', 'valign'=>'top'),
				array('data' => $volumjumlah, 'align' => 'left', 'valign'=>'top'),
				array('data' => $hargasatuan, 'align' => 'right', 'valign'=>'top'),
				array('data' => $hargatotal, 'align' => 'right', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
		//drupal_set_message('Goes here');
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'5')
        );
    }
	$pquery = sprintf("select uraian,jumlah from {anggperkeg}  where kodekeg='%s' and kodero='%s'", db_escape_string($kodekeg), db_escape_string($kodero));
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ptitle = $data->uraian . ', Anggaran: ' . apbd_fn($data->jumlah);


	
	$ptitle =l($ptitle, 'apbd/kegiatanskpd/rekening/' . $arg, array('html'=>true));
    $output .= theme_box($ptitle, theme_table($header, $rows));
	
	if (user_access('kegiatanskpd tambah'))
		$output .=  l('Detil Baru', 'apbd/kegiatanskpd/rekening/detil/edit/' . $kodekeg . '/' . $kodero , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;"  ;

	$output .= l('Daftar Rekening', 'apbd/kegiatanskpd/rekening/' . $kodekeg , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
	
	
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpd/subkegiatan/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

?>
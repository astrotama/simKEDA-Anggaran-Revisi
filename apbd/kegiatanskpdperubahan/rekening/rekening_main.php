<?php
function rekening_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');	
   if ($arg) {
		
		//$qlike = sprintf(" and kodekeg='%s'", db_escape_string($arg));    
		$kodekeg = arg(3);
		$lama = arg(4);
		$qlike = sprintf(" and kodekeg='%s'", db_escape_string($kodekeg));
		
   } else
		drupal_access_denied();

	//drupal_set_message(arg(3));
	if ($lama)
		$opw = '110px';
	else
		$opw = '40px';
		
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        //array('data' => ucwords(strtolower('kodekeg')), 'field'=> 'kodekeg', 'valign'=>'top'),
		array('data' => 'Kode', 'field'=> 'kodero', 'valign'=>'top'),
		array('data' => 'Uraian', 'field'=> 'uraian', 'valign'=>'top'),
		array('data' => 'Penetapan', 'field'=> 'jumlahsebelum', 'valign'=>'top', 'width'=>'90px'),
		array('data' => 'Perubahan', 'field'=> 'jumlah', 'valign'=>'top', 'width'=>'90px'),
		array('data' => '', 'width' => $opw, 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kodero';
    }

	
	$allowedit = (batastgl() || (isSuperuser()));

	if ($allowedit==false) {
		//dispensasirenja
		//$sqluk = sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
        $sql = sprintf('select dispensasi from {kegiatanperubahan} where kodekeg=\'%s\'', $kodekeg);
		$res = db_query($sql);
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {  		
				$allowedit = $data->dispensasi;
			}
		}
	}	
	
	if ($allowedit==false) {
		//dispensasirenja
		//$sqluk = sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
        $sql = sprintf('select dispensasirenja from {unitkerja} where kodeuk=\'%s\'', apbd_getuseruk());
		$res = db_query($sql);
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {  		
				$allowedit = $data->dispensasirenja;
			}
		}
	}	
	
    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = ' ';
    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select kodekeg,kodero,uraian,jumlahsebelum,jumlah,jumlahp,jumlahsesudah from {anggperkegperubahan}' . $where . $tablesort;
    $fsql = sprintf($sql, addslashes($nama));
    $no=0;
    
	//$limit = 15;
    //$countsql = "select count(*) as cnt from {anggperkeg}" . $where;
    //$fcountsql = sprintf($countsql, addslashes($nama));
    //$result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
    
    
	//$page = $_GET['page'];
    //if (isset($page)) {
    //   $no = $page * $limit;
    //} else {
    //   $no = 0;
    //}
	
	//NO PAGER
	$result = db_query($fsql);
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$editlink = '';
			if (isSuperuser()) {
				//apbdkegrekening/
				
				//$uraian = l($data->uraian, 'apbd/kegiatanskpd/rekening/edit/' . $data->kodekeg . "/" . $data->kodero , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				
				if ($lama) {
					$uraian = l($data->uraian, 'apbd/kegiatanskpd/rekening/edit/' . $data->kodekeg . "/" . $data->kodero , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
					$editlink .= l('Detil', 'apbd/kegiatanskpd/rekening/detil/' . $data->kodekeg . "/" . $data->kodero, array('html'=>TRUE)) . '&nbsp;';
					$editlink .= l('Edit', 'apbd/kegiatanskpd/rekening/edit/' . $data->kodekeg . "/" . $data->kodero, array('html'=>TRUE)) . '&nbsp;';
				} else {
					$uraian = l($data->uraian, 'apbdkegrekeningperubahan/' . $data->kodekeg . "/" . $data->kodero , array('html' =>TRUE));					
				}
				
			} else
				$uraian = $data->uraian;
			
			if (user_access('kegiatanskpd penghapusan') and $allowedit)
                $editlink .=l('Hapus', 'apbd/kegiatanskpdperubahan/rekening/delete/' . $data->kodekeg . "/" . $data->kodero, array('html'=>TRUE));
			else
                $editlink ='';
				
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				//array('data' => $data->id, 'align' => 'left', 'valign'=>'top'),
				//array('data' => $data->kodekeg, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->kodero, 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data->jumlah), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data->jumlahp), 'align' => 'right', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'Akses/data error, hubungi administrator', 'colspan'=>'6')
        );
    }
	
	//Kosong
	if ($no==0) {
		$linknew = l('Rekening Baru', 'apbd/kegiatanskpd/rekening/edit/', array('html' =>TRUE));	
		$rows[] = array (
			array('data' => 'Rekening belum diisikan, klik ' . $linknew . ' untuk menambahkan.', 'colspan'=>'6')
		);
	}

	$pquery = sprintf("select kegiatan, total, plafon, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi  from {kegiatanperubahan} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro)  where kodekeg='%s'", db_escape_string($arg));
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres)) {
		$ptitle = $data->kegiatan;
	
		$ptitle =l($ptitle, 'apbd/kegiatanskpd/edit/' . $arg, array('html'=>true));	
		$output .= theme_box('', theme_table($header, $rows));

		//Top Keterangan
		$rows1[] = array (
			array('data' => 'Plafon: ' . apbd_fn($data->plafon) . ', Anggaran: ' . apbd_fn($data->total), 'colspan'=>'6', 'align' => 'right', 'valign'=>'top')
		);	
		$output1 = theme_box('', theme_table('', $rows1));	
		//$output1 = 'Plafon: ' . apbd_fn($data->plafon) . ', Anggaran: ' . apbd_fn($data->total);
		//$output1 .= theme ('pager', NULL, $limit, 0);
		
		drupal_set_title($ptitle);
		
		if (user_access('kegiatanskpd tambah') and $allowedit) {
			/*if ($lama)
				$output2 = l('Rekening Baru', 'apbd/kegiatanskpd/rekening/edit/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
			else
				$output2 = l('Rekening Baru', 'apbdkegrekening/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

			$output2 .= "&nbsp;" . l('Hapus Semua', 'apbd/kegiatanskpd/rekening/delete/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;*/
			
			
		}
		//$output2 .= "&nbsp;" .  l('Triwulan', 'apbd/kegiatanskpd/triwulan/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

			//Cetak
		//$output2 .="&nbsp;" .  l('Preview RKA', 'apbd/kegiatanskpdperubahan/print/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

		//Tombol ke Kegiatan
		//$output2 .= "&nbsp;" . l('Buka Kegiatan', 'apbd/kegiatanskpd/edit/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));	
		
		if ($allowedit) {
			/*if ($lama) 
				$output2 .= "&nbsp;" . l('Tampilan Baru', 'apbd/kegiatanskpd/rekening/' . $kodekeg  , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;')));	
			else
				$output2 .= "&nbsp;" . l('Tampilan Lama', 'apbd/kegiatanskpd/rekening/' . $kodekeg . '/1'  , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;')));	*/
		}
		
	//	if (user_access('kegiatanskpd pencarian'))		
	//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpd/subkegiatan/find/' , array('html'=>TRUE)) ;
		$output .= theme ('pager', NULL, $limit, 0);

	} else
		drupal_access_denied(); 

    return $output1 . $output2 . $output . $output2;
}

?>
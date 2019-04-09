<?php
function kegiatanskpd_main($arg=NULL, $nama=NULL) {
	/*
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');
	*/
	$qlike='';
	$limit = 15;
	
	$kodesuk = '';
	$tahun = variable_get('apbdtahun', 0);
	$ntitle = 'Belanja';
    if ($arg) {
		switch($arg) {
			case 'show':
				//$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				$qlike = sprintf(" and lower(k.kegiatan) like lower('%%%s%%') ", db_escape_string(arg(3)));	
				//drupal_set_message(arg(4));
				break;
			case 'filter':
				$nntitle ='';
				$kodeuk = arg(3);
				$sumberdana = arg(4);
				$statusisi = arg(5);
				$kodesuk = arg(6);
				$statustw = arg(7);
				$statusinaktif = arg(8);
				$jenis = arg(9);
				$plafon = arg(10);
				
				$kegiatan = arg(11);
				$rekening = arg(12);
				//echo $rekening;
				$rincian = arg(13);
				//echo $rincian . '|';
				$exportpdf = arg(14);
				//echo $exportpdf;
				

				break;

			default:
				drupal_access_denied();
				break;
		}
	} else {
		$tahun = variable_get('apbdtahun', 0);
		$sumberdana = $_SESSION['sumberdana'];
		$statusisi = $_SESSION['statusisi'];
		$statustw = $_SESSION['statustw'];	
		$statusinaktif = $_SESSION['statusinaktif'];
		$jenis = $_SESSION['jenis'];
		$plafon = $_SESSION['plafon'];
		
		
		if (isSuperuser() || isUserview()) {
			$kodeuk = $_SESSION['kodeuk'];
			if ($kodeuk == '') 	$kodeuk = '00';
			
			
		} else {
			$kodeuk = apbd_getuseruk();
			if (isUserKecamatan())
				$kodesuk = apbd_getusersuk();
			else
				$kodesuk = $_SESSION['kodesuk'];
		}
		
	}
	
	
	if(isUserview()){
		$url='apbd/belanjadppa';
		drupal_goto($url);
	}
	if (isSuperuser() || isUserview()) {
		//$kodeuk = $_SESSION['kodeuk'];
		if ($kodeuk == '') 	$kodeuk = '00';
		
		
	} else {
		$kodeuk = apbd_getuseruk();
		if (isUserKecamatan())
			$kodesuk = apbd_getusersuk();
		else
			$kodesuk = $_SESSION['kodesuk'];
	}	
	if (isSuperuser() || isUserview()) {
		if ($kodeuk !='00') {
			$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
			$pquery = sprintf("select kodeuk, namasingkat from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk));
			$presult = db_query($pquery);
			if ($data=db_fetch_object($presult)) {
				$ntitle .= ' ' . $data->namasingkat;
			}
		} 
		$adminok = true;
							
	} else {
		$qlike .= sprintf(' and k.inaktif=0 and k.plafon>0 and k.kodeuk=\'%s\' ', $kodeuk);
		if ($kodesuk != '') {
			$qlike .= sprintf(' and (k.kodesuk=\'%s\' ', $kodesuk);
			$qlike .= " or k.kodesuk='')";
		}
		
		$adminok = false;
	}

	//keg cari
	//if (strlen($kegcari)>0) {
	//	$qlike .= sprintf(" and lower(k.kegiatan) like lower('%%%s%%') ", db_escape_string($kegcari));
	//}
	
	//STATUS PENGISIAN
	if ($statusisi=='sudah') {
		$qlike .= sprintf(' and (k.total=k.plafon) and (k.plafon>0)');
	} elseif ($statusisi=='sebagian') {
		$qlike .= sprintf(' and (k.total>0) and (k.total<k.plafon) and (k.plafon>0) ');
	} elseif ($statusisi=='belum') {
		$qlike .= sprintf(' and (k.total=0 or k.total is null) and (k.plafon>0) ');
	} elseif ($statusisi=='lebih') {
		$qlike .= sprintf(' and (k.total>k.plafon) and (k.plafon>0) ');
	}

	//STATUS TW
	if ($statustw=='sudah') {
		$qlike .= sprintf(' and k.total>0 and (k.total=(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	} elseif ($statustw=='belum') {
		$qlike .= sprintf(' and k.total>0 and (k.total>(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	}
	

	//STATUS INAKTIF
	if ($statusinaktif=='0') {
		$qlike .= sprintf(' and k.inaktif=0 ');
	} elseif ($statusinaktif=='1') {
		$qlike .= sprintf(' and (k.inaktif=1 or k.plafon=0) ');
	} elseif ($statusinaktif=='2') {
		$qlike .= sprintf(' and k.dispensasi=1 ');
	}

	//STATUS INAKTIF
	if ($jenis=='gaji') {
		$qlike .= ' and k.jenis=1 and k.isppkd=0 ';
	} elseif ($jenis=='langsung') {
		$qlike .= ' and k.jenis=2 ';
	} elseif ($jenis=='ppkd') {
		$qlike .= ' and k.jenis=1 and k.isppkd=1 ';
	}
	
	//SUMBER DANA
	if ($sumberdana != '') {
		$qlike .= sprintf(' and (k.sumberdana1=\'%s\'  or k.sumberdana2=\'%s\') ', $sumberdana, $sumberdana);
		$ntitle .= ' ' . $sumberdana;
	}
	
	//PLAFON
	if ($plafon=='new') {
		$qlike .= ' and (k.periode=' . variable_get('apbdrevisi', 0) . ' and k.totalpenetapan=0) ';
	} elseif ($plafon=='up') {
		$qlike .= ' and k.plafon>k.plafonpenetapan and k.totalpenetapan>0 ';
	} elseif ($plafon=='down') {
		$qlike .= ' and k.plafon<k.plafonpenetapan ';
	} elseif ($plafon=='still') {
		$qlike .= ' and k.plafon=k.plafonpenetapan ';
	}	
	
	//NAMA KEGIATAN
	if (strlen($kegiatan)>0) {
		$qlike .= sprintf(" and lower(k.kegiatan) like lower('%%%s%%') ", db_escape_string($kegiatan));
	}
	
	
	//$output .= drupal_get_form('kegiatanskpd_transfer_form');
	$output .= drupal_get_form('kegiatanskpd_main_form');
	
	drupal_set_title($ntitle);	
	
	if ((strlen($rekening) == 0) and (strlen($rincian) ==0)) {
		
		$where = ' where true' . $customwhere . $qlike ;
		
		$sql = "select k.kodekeg, k.periode, k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.jenis, k.lokasi,k.programtarget,k.total, k.plafon, k.totalpenetapan, k.plafonpenetapan, u.namasingkat, k.isppkd,  k.adminok, k.sumberdana1 sumberdana, k.inaktif,k.dispensasi, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi from {kegiatanrevisi} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro) " . $where;
		//$fsql = sprintf($sql, addslashes($nama));
		$fsql = $sql;
		//echo $fsql;
		
		
		$countsql = "select count(*) as cnt from {kegiatanrevisi} k" . $where;
		//$fcountsql = sprintf($countsql, addslashes($nama));
		$fcountsql = $countsql;
		$result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
	
	} else if ((strlen($rekening) > 0) and (strlen($rincian) ==0)) {
		$qlike .= sprintf(" and lower(a.uraian) like lower('%%%s%%') ", db_escape_string($rekening));
		$where = ' where true' . $customwhere . $qlike ;

		$sql = "select k.kodekeg,k.periode, k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.jenis, k.lokasi,k.programtarget,k.total, k.plafon, k.totalpenetapan, k.plafonpenetapan, u.namasingkat, k.isppkd,  k.adminok, k.sumberdana1 sumberdana, k.inaktif,k.dispensasi, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi from {kegiatanrevisi} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro) inner join {anggperkegrevisi} a on k.kodekeg=a.kodekeg " . $where;
		$fsql = $sql;

		//echo $fsql;
		$countsql = "select count(*) as cnt from {kegiatanrevisi} k inner join {anggperkegrevisi} a on k.kodekeg=a.kodekeg " . $where;
		//$fcountsql = sprintf($countsql, addslashes($nama));
		$fcountsql = $countsql;
		//$result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
		
	} else if ((strlen($rekening) == 0) and (strlen($rincian) >0)) {
		
		$qlike .= sprintf(" and lower(d.uraian) like lower('%%%s%%') ", db_escape_string($rincian));
		$where = ' where true' . $customwhere . $qlike ;

		$sql = "select distinct k.kodekeg,k.periode, k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.jenis, k.lokasi,k.programtarget,k.total, k.plafon, k.totalpenetapan, k.plafonpenetapan, u.namasingkat, k.isppkd, k.adminok, k.sumberdana1 sumberdana, k.inaktif,k.dispensasi, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi from {kegiatanrevisi} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro) inner join {anggperkegdetilrevisi} d on k.kodekeg=d.kodekeg " . $where;
		$fsql = $sql;

		//echo $fsql;
		$countsql = "select count(distinct k.kodekeg) as cnt from {kegiatanrevisi} k inner join {anggperkegrevisi} a on k.kodekeg=a.kodekeg inner join {anggperkegdetilrevisi} d on a.kodekeg=d.kodekeg and a.kodero=d.kodero " . $where;
		//$fcountsql = sprintf($countsql, addslashes($nama));
		$fcountsql = $countsql;
		//$result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);	
		
		

		
	} else {
		$qlike .= sprintf(" and lower(a.uraian) like lower('%%%s%%') and lower(d.uraian) like lower('%%%s%%') ", db_escape_string($rekening), db_escape_string($rincian));
		$where = ' where true' . $customwhere . $qlike ;

		$sql = "select distinct k.kodekeg,k.periode, k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.jenis, k.lokasi,k.programtarget,k.total, k.plafon, k.totalpenetapan, k.plafonpenetapan, u.namasingkat, k.isppkd,  k.adminok, k.sumberdana1 sumberdana, k.inaktif,k.dispensasi, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi from {kegiatanrevisi} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro) inner join {anggperkegrevisi} a on k.kodekeg=a.kodekeg inner join {anggperkegdetilrevisi} d on k.kodekeg=d.kodekeg " . $where;
		$fsql = $sql;

		//echo $fsql;
		$countsql = "select count(distinct k.kodekeg) as cnt from {kegiatanrevisi} k inner join {anggperkegrevisi} a on k.kodekeg=a.kodekeg inner join {anggperkegdetilrevisi} d on a.kodekeg=d.kodekeg and a.kodero=d.kodero " . $where;
		//$fcountsql = sprintf($countsql, addslashes($nama));
		$fcountsql = $countsql;
		//$result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);			
	}

	if (isset($exportpdf))   {
		if ($exportpdf=='pdf') {
			$pdfFile = 'Daftar_Kegiatan_Perbahan_Dicari.pdf';
			
			$htmlHeader = GenDataHeader($kodeuk);
			$htmlContent = GenDataPrint($kodeuk, $fsql);
			
			apbd_ExportPDF2('L', 'F4', $htmlHeader, $htmlContent, $pdfFile);
			//return $htmlContent;
			
		} else {
			
			kegiatanskpd_exportexcel($fsql);
		}	
		
	} else {
		$output = GenDataView($kodeuk , $sumberdana , $statusisi , $kodesuk , $statustw , 
				$statusinaktif , $jenis , $kegiatan , $rekening , $rincian, $plafon, $fsql, $fcountsql);
		return $output;
	}
    
}

function GenDataView($kodeuk , $sumberdana , $statusisi , $kodesuk , $statustw , 
				$statusinaktif , $jenis , $kegiatan , $rekening , $rincian, $plafon, $fsql, $fcountsql) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');
	$limit = 15;
	
	
	//$output .= drupal_get_form('kegiatanskpd_transfer_form');
	$output .= drupal_get_form('kegiatanskpd_main_form');
	if (isSuperuser()) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Target', 'field'=> 'target', 'valign'=>'top'),
			array('data' => 'Sumberdana', 'field'=> 'sumberdana', 'valign'=>'top'),
			array('data' => 'Plafon', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Penetapan', 'field'=> 'totalpenetapan','width' => '90px', 'valign'=>'top'),
			array('data' => 'Perubahan', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	}
	
	else {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Target', 'field'=> 'target', 'valign'=>'top'),
			array('data' => 'Sumberdana', 'field'=> 'sumberdana', 'valign'=>'top'),
			array('data' => 'Plafon', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Penetapan', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'Perubahan', 'field'=> 'totalp', 'width' => '90px','valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	}
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by koderesmi';
    }
	
	$result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
	
	//Jam,Menit,Detik,Bulan,Hari,Tahun
	//$batas = mktime(20, 0, 0, 6, 16, 2015) ;
	//$sekarang = time () ;
	//$selisih =($batas-$sekarang) ;
	$allowedit = true;		//(($selisih>0) || (isSuperuser() || isUserview()));
	
	//CEK TAHUN
	//$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
    
    $no=0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    }
    if ($result) {
        while ($data = db_fetch_object($result)) {

			$editlink = '';
			
			if (isSuperuser()) {
				//$kegname = l($data->kegiatan, 'apbd/kegiatanskpdperubahan/edit/' . $data->kodekeg , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				
				//kegiatanrevisi/edit
				//$kegname = l($data->kegiatan, 'apbd/kegiatanskpdperubahan/edit/' . $data->kodekeg , array('html' =>TRUE));
				$kegname = l($data->kegiatan, 'apbd/kegiatanrevisi/edit/' . $data->kodekeg , array('html' =>TRUE));

			} else {
				$kegname = $data->kegiatan;
			}

			if ($data->total==0) {
				//$editlink =l('Rekening', 'apbd/kegiatanskpdperubahan/rekening/edit/' . $data->kodekeg, array('html'=>TRUE));
				$editlink = l('Rekening', 'apbdkegrekeningrevisi/' . $data->kodekeg , array('html'=>TRUE));

			} else {
				//$editlink =l('Rekening', 'apbd/kegiatanskpdperubahan/rekening/' . $data->kodekeg, array('html'=>TRUE));
				$editlink = l('Rekening', 'apbd/kegiatanrevisi/rekening/' . $data->kodekeg  . '/0', array('html'=>TRUE));
				
			}
			
			//TW
			$editlink .= "&nbsp;" . l('TW', 'apbd/kegiatanrevisi/triwulan/' . $data->kodekeg, array('html'=>TRUE));
			
			if (isSuperuser()) {
				$editlink .= "&nbsp;" . l('Edit', 'apbd/kegiatanskpdperubahan/editadmin/' . $data->kodekeg, array('html'=>TRUE));
				if ($penetapan_ada)
					$editlink .= "&nbsp;" . 'Hapus';
				else
					$editlink .= "&nbsp;" . l('Hapus', 'apbd/kegiatanskpdperubahan/delete/' . $data->kodekeg, array('html'=>TRUE));
				//$editlink .= "&nbsp;" . l('Cetak', 'apbd/kegiatanskpd/printperubahan/' . $data->kodekeg . '/10/dpa' , array('html'=>TRUE)) ;
				
			} 
			
			//CETAK
			$editlink .= "&nbsp;" . l('Cetak', 'apbd/kegiatanskpd/printusulan/' . $data->kodekeg . '/10/rpka' , array('html'=>TRUE)) ;
			
            $no++;
			

			if ($data->inaktif) 
				$str_info = "<img src='/files/inaktif.png'>";
			
			else {
				if ($data->total > $data->plafon) 
					$str_info = "<img src='/files/limit.png'>";
				else {

					if ($data->dispensasi)
						$str_info = "<img src='/files/revisi16.jpg'>";
					else
						$str_info = '';
				}
			}
			
			//group1.png
			if ($data->plafonpenetapan==$data->plafon)
				$str_plafon = "<img src='/files/icon-still.png'>";
			else if ($data->plafonpenetapan>$data->plafon)
				$str_plafon = "<img src='/files/icon-down.png'>";
			else
				$str_plafon = "<img src='/files/icon-up.png'>";
			
			if (isSuperuser()) { 
				
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $str_info, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $str_plafon, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->programtarget, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sumberdana, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->plafon), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->totalpenetapan), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $str_info, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $str_plafon, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->programtarget, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sumberdana, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->plafon), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->totalpenetapan), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			}
		}
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$btn = "";
	if ($allowedit)
		//
		if (isSuperuser()) {
			//$btn .= l('Baru', 'apbd/kegiatanskpdperubahan/editadmin/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
		}
 
	$status = 0;
	$record = 0;
	if (isSuperuser())
		//$btn .= l('Baru', 'apbd/kegiatanrevisi/editnew/81/3/2/1', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;" ;
		$btn .= l('Baru', 'apbd/kegiatanskpdperubahan/editadmin', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;" ;
		
	//$btn .= l('Cetak', 'apbd/laporan/rka/rekapaggbelanja/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));

	$btn .= l('Cetak', 'apbd/kegiatanskpdperubahan/filter/' . $kodeuk . '/' . $sumberdana . '/' . $statusisi . '/' . $kodesuk . '/'. $statustw . '/' . $statusinaktif . '/' . $jenis  . '/' . $plafon . '/' . $kegiatan . '/' . $rekening . '/' . $rincian . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	
    if(isSuperuser()){$btn .= "&nbsp;" . l("Cari", 'apbd/kegiatanskpdperubahan/find/' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;}
	
	if (isSuperuser() || isUserview()) {
		$btn .= "&nbsp;" . l('Simpan Excel', 'apbd/kegiatanskpdperubahan/filter/' . $kodeuk . '/' . $sumberdana . '/' . $statusisi . '/' . $kodesuk . '/'. $statustw . '/' . $statusinaktif . '/' . $jenis . '/' . $kegiatan . '/' . $rekening . '/' . $rincian . '/' . $plafon . '/xls' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));	
	}
	
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

	
	//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanskpd tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanskpdperubahan/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpdperubahan/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

function GenDataHeader($kodeuk) {
	
	if ($kodeuk!='00') {
		$sql = "select namauk from {unitkerja} where kodeuk='" . $kodeuk . "'" ;
		$res = db_query($sql);
		if ($data = db_fetch_object($res)) {
			$skpd = ' ' . $data->namauk;
		}
	}
	
	$rowsjudul[] = array (array ('data'=>'DAFTAR KEGIATAN' . $skpd, 'width'=>'875px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'___________________________________________', 'width'=>'875px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}


function GenDataPrint($kodeuk, $fsql) {
	set_time_limit(0);
	ini_set('memory_limit', '640M');
	
	//drupal_set_message($fsql);
	
	$totalF =0;
	$totalP =0;
	$totalR =0;
	$headersrek[] = array (
						 
						 array('data' => 'No.',  'width'=> '25px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Kegiatan',  'width' => '175px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Target',  'width' => '165px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Lokasi',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Sumberdana',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Plafon',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Penetapan',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Perubahan',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Ket',  'width' => '50px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
					);


	$result = db_query($fsql);
	
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$no += 1;
			
			$totalF += $data->plafon;
			$totalP += $data->totalpenetapan;
			$totalR += $data->total;
			
			$str_plafon='';					
			if ($data->plafonpenetapan==$data->plafon)
				$str_plafon = "Tetap; ";
			else if ($data->plafonpenetapan>$data->plafon)
				$str_plafon = "Turun; ";
			
			else
				if ($data->totalpenetapan == 0)
					$str_plafon = "Baru; ";
				else
					$str_plafon = "Naik; ";

			if ($data->inaktif) 
				$str_plafon .= "*)";
			
			else {
				if ($data->total > $data->plafon) 
					$str_plafon .= "L";
				else {

					if ($data->dispensasi) $str_plafon .= "D";
				}
			}
			
			if ($kodeuk=='00')
				$kegnama = $data->kegiatan . ' (' . $data->namasingkat . ')';
			else
				$kegnama = $data->kegiatan;
			
			$rowsrek[] = array (
								 array('data' => $no,  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
								 array('data' => $kegnama,  'width' => '175px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => $data->programtarget, 'width' => '165px', 'align' => 'left', 'valign'=>'top', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => str_replace('||',', ', $data->lokasi), 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => ' border-right: 1px solid black; text-align:left;'),								 
								 array('data' => $data->sumberdana,  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($data->plafon),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 array('data' => apbd_fn($data->totalpenetapan),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 array('data' => apbd_fn($data->total),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 array('data' => $str_plafon,  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 );				

		}
	}										 
								 			
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-top: 2px solid black; border-bottom: 1px solid black; text-align:right;'),
						 array('data' => 'TOTAL',  'width' => '175px', 'style' => ' border-top: 2px solid black; border-bottom: 1px solid black; text-align:left;'),
						 array('data' => '',  'width' => '165px', 'style' => ' border-top: 2px solid black; border-bottom: 1px solid black; text-align:left;'),
						 array('data' => '',  'width' => '100px', 'style' => ' border-top: 2px solid black; border-bottom: 1px solid black; text-align:left;'),
						 array('data' => '',  'width' => '90px', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; border-bottom: 1px solid black; text-align:left;'),
						 array('data' => apbd_fn($totalF),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 2px solid black; border-bottom: 1px solid black; text-align:right;'),
						 array('data' => apbd_fn($totalP),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 2px solid black; border-bottom: 1px solid black; text-align:right;'),
						 array('data' => apbd_fn($totalR),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 2px solid black; border-bottom: 1px solid black; text-align:right;'),
						 array('data' => '',  'width' => '50px', 'style' => ' border-right: 1px solid black; border-top: 2px solid black; border-bottom: 1px solid black; text-align:left;'),
						 );				

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$output = theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	return $output;	
}

					
function kegiatanskpd_main_form() {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Pilihan Data',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);
	$filter = arg(2);
	if (isset($filter) && ($filter=='filter')) {
		$kodeuk = arg(3);
		$sumberdana = arg(4);
		$statusisi = arg(5);
		$kodesuk = arg(6);
		$statustw = arg(7);
		$statusinaktif = arg(8);
		$jenis = arg(9);
		$plafon = arg(10);
		
	} else {
		$sumberdana = $_SESSION['sumberdana'];
		$statusisi = $_SESSION['statusisi'];
		$statustw = $_SESSION['statustw'];	
		$statusinaktif = $_SESSION['statusinaktif'];	
		$jenis = $_SESSION['jenis'];	
		$plafon = $_SESSION['plafon'];

		if (isSuperuser() || isUserview()) 
			$kodeuk = $_SESSION['kodeuk'];
		else
			$kodesuk = $_SESSION['kodesuk'];
	}
	//drupal_set_message($filter);

	//if (isset($kodeuk)) {
	//    $form['formdata']['#collapsed'] = TRUE;
	//    //if (isUserKecamatan())
	//    //    if ($kodeuk != apbd_getuseruk())
	//    //        $form['formdata']['#collapsed'] = FALSE;
	//}
		   
	if (!isSuperuser()) {
		$typeuk = 'hidden';
		$kodeuk = apbd_getuseruk();
		
		$typesuk ='select';

		$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
		$pquery = sprintf('select kodesuk, namasuk from {subunitkerja} where kodeuk=\'%s\' order by kodesuk', $kodeuk);
		
		//drupal_set_message($pquery);
		
		$pres = db_query($pquery);
		$subskpd = array();
		$subskpd[''] = '- Pilih Bidang -';
		while ($data = db_fetch_object($pres)) {
			$subskpd[$data->kodesuk] = $data->namasuk;
		}

		if (isUserKecamatan()) {
			$typesuk='hidden';
			$kodesuk = apbd_getusersuk();
		} else
			$typesuk='select';
		
	} else {
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		$dinas['00'] ='00000 - SEMUA SKPD';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}
		
		$typeuk='select';
		$typesuk='hidden';
	}
	 
	$form['formdata']['kodeuk']= array(
		'#type'         => $typeuk, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
		'#weight' => 2,
	);

	$form['formdata']['kodesuk']= array(
		'#type'         => $typesuk, 
		'#title'        => 'Bidang/Bagian',
		'#options'		=> $subskpd,
		//'#description'  => 'kodesuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodesuk, 
		'#weight' => 3,
	); 
	
	$pquery = "select sumberdana from {sumberdanalt} order by nomor" ;
	$pres = db_query($pquery);
	$sumberdanaotp = array();
	$sumberdanaotp[''] = '- SEMUA -';
	while ($data = db_fetch_object($pres)) {
		$sumberdanaotp[$data->sumberdana] = $data->sumberdana;
	}
	$form['formdata']['sumberdana']= array(
		'#type'         => 'select', 
		'#title'        => 'Sumber Dana', 
		'#options'		=> $sumberdanaotp,
		'#width'         => 30, 
		'#default_value'=> $sumberdana, 
		'#weight' => 4,
	);

	$form['formdata']['jenis']= array(
		'#type' => 'radios', 
		'#title' => t('Jenis'), 
		'#default_value' => $jenis,
		'#options' => array(	
			 '' => t('Semua'), 	
			 'gaji' => t('Gaji'), 	
			 'langsung' => t('Langsung'),
			 'ppkd' => t('PPKD'),	
		   ),
		'#weight' => 5,		
	);	
	
	$form['formdata']['ssj'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 5,
	);		
 	
	$form['formdata']['statusisi']= array(
		'#type' => 'radios', 
		'#title' => t('Pengisian'), 
		'#default_value' => $statusisi,
		'#options' => array(	
			 '' => t('Semua'), 	
			 'sudah' => t('Selesai'), 	
			 'sebagian' => t('Sebagian'),
			 'belum' => t('Belum'),	
			 'lebih' => t('Lebih Plafon'),	
		   ),
		'#weight' => 6,		
	);	

	$form['formdata']['ss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 7,
	);		

	$form['formdata']['plafon']= array(
		'#type' => 'radios', 
		'#title' => t('Alokasi Anggaran'), 
		'#default_value' => $plafon,
		'#options' => array(	
			 '' => t('Semua'), 	
			 'new' => t('Usulan Baru'), 	
			 'up' => t('Naik'),
			 'down' => t('Turun'),	
			 'still' => t('Tetap'),	
		   ),
		'#weight' => 8,		
	);	

	$form['formdata']['ss1p'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 9,
	);		
	$form['formdata']['statustw']= array(
		'#type' => 'radios', 
		'#title' => t('Tri Wulan'), 
		'#default_value' => $statustw,
		'#options' => array(	
			 '' => t('Semua'), 	
			 'sudah' => t('Sudah'), 	
			 'belum' => t('Belum'),	
		   ),
		'#weight' => 10,		
	);		
	$form['formdata']['ss2'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 11,
	);		
	
	if (isSuperuser() || isUserview()) {
		$statusinaktiftype = 'radios';
	} else {
		$statusinaktiftype = 'hidden';
		$statusinaktif = '0';
	}
	
	$form['formdata']['statusinaktif']= array(
		'#type' => $statusinaktiftype, 
		'#title' => t('Status'), 
		'#default_value' => $statusinaktif,
		'#options' => array(	
			 '' => t('Semua'), 	
			 '0' => t('Aktif'),	
			 '1' => t('Inaktif'), 	
			 '2' => t('Perpanjang'),
		   ),
		'#weight' => 12,		
	);		
	
	$form['formdata']['ss'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 13,
	);		
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 14
	);
	
	return $form;
}
function kegiatanskpd_main_form_submit($form, &$form_state) {
	$sumberdana = $form_state['values']['sumberdana'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodesuk = $form_state['values']['kodesuk'];
	$statusisi = $form_state['values']['statusisi'];
	$statustw = $form_state['values']['statustw'];
	$statusinaktif = $form_state['values']['statusinaktif'];
	$jenis = $form_state['values']['jenis'];
	$plafon = $form_state['values']['plafon'];
	
	$tahun= $form_state['values']['tahun'];

	$_SESSION['sumberdana'] = $sumberdana;
	$_SESSION['statusisi'] = $statusisi;
	$_SESSION['statustw'] = $statustw;
	$_SESSION['statusinaktif'] = $statusinaktif;
	$_SESSION['jenis'] = $jenis;
	$_SESSION['plafon'] = $plafon;
	
	if (isSuperuser() || isUserview()) 
		$_SESSION['kodeuk'] = $kodeuk;
	else
		$_SESSION['kodesuk'] = $kodesuk;
	
	$uri = 'apbd/kegiatanskpdperubahan/filter/' . $kodeuk . '/' . $sumberdana . '/' . $statusisi . '/' . $kodesuk . '/'. $statustw . '/' . $statusinaktif . '/' . $jenis . '/' .$plafon;

	
	drupal_goto($uri);
	
}


function kegiatanskpd_exportexcel($fsql) {
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit', '640M');

ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once 'files/PHPExcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("SIPKD Anggaran Online")
							 ->setLastModifiedBy("SIPKD Anggaran Online")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Excel document generated from SIPKD Anggaran Online.")
							 ->setKeywords("office 2007 SIPKD Anggaran Online openxml php")
							 ->setCategory("SIPKD Anggaran Analisa");
// Add Header
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $row ,'No')
			->setCellValue('B' . $row ,'Kegiatan')
            ->setCellValue('C' . $row ,'Target')
			->setCellValue('D' . $row ,'Sumberdana')
			->setCellValue('E' . $row ,'Plafon')
			->setCellValue('F' . $row ,'Penetapan')
			->setCellValue('G' . $row ,'Perubahan');

$result = db_query($fsql);
while ($data = db_fetch_object($result)) {
	$row++;
	$sql_pl = "select total from {kegiatanskpd} where kodekeg='" . $data->kodekeg . "'";
	//$sql_pl = "select plafon,total from {kegiatanperubahan2} where kodekeg='" . $data->kodekeg . "'";
	$res_pl = db_query($sql_pl);
	if ($data_pl=db_fetch_object($res_pl)) {
		$total_lama = $data_pl->total;
	}
	
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row, $row-1)
				->setCellValue('B' . $row, $data->kegiatan)
				->setCellValue('C' . $row, $data->programtarget)
				->setCellValue('D' . $row, $data->sumberdana1)
				->setCellValue('E' . $row, $data->plafon)
				->setCellValue('F' . $row, $total_lama)
				->setCellValue('G' . $row, $data->total)
;
}
						

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('DAFTAR KEGIATAN PERUBAHAN');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = 'daftar_kegiatan_perubahan.xlsx';
header('Content-Disposition: attachment;filename=' . $fname);
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}

?>
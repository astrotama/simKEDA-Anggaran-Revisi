<?php
function kegiatanskpdcari_main($arg=NULL, $nama=NULL) {

    if ($arg) {
		switch($arg) {
			case 'filter':

				// $kodeuk . '/' . $sumberdana . '/' . $statusisi . 
				// $kodesuk . '/'. $statustw . '/' . $statusinaktif 
				// $jenis . '/' . $kegiatan . '/' . $rekening . 
				// $rincian ;
			
				$kodeuk = arg(3);
				$sumberdana = arg(4);
				$statusisi = arg(5);
				$kodesuk = arg(6);
				$statustw = arg(7);
				$statusinaktif = arg(8);
				$jenis = arg(9);
				$kegiatan = arg(10);
				$rekening = arg(11);
				$rincian = arg(12);
				$exportpdf = arg(13);

				break;

			default:
				drupal_access_denied();
				break;
		}
	} else {
		drupal_access_denied();
	}
	
	//drupal_set_message($exportpdf);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		$pdfFile = 'Daftar_Kegiatan_Dicari.pdf';
		
		$htmlHeader = GenDataHeader();
		$htmlContent = GenDataPrint($kodeuk , $sumberdana , $statusisi , $kodesuk , $statustw , 
				$statusinaktif , $jenis , $kegiatan , $rekening , $rincian);
		
		apbd_ExportPDF2P(10, 10, $htmlHeader, $htmlContent, $pdfFile);
		
	} else {
		$output = GenDataView($kodeuk , $sumberdana , $statusisi , $kodesuk , $statustw , 
				$statusinaktif , $jenis , $kegiatan , $rekening , $rincian);
		return $output;
	}
}

function GenDataView($kodeuk , $sumberdana , $statusisi , $kodesuk , $statustw , 
				$statusinaktif , $jenis , $kegiatan , $rekening , $rincian ) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 15;
	
	$kodesuk = '';
	$tahun = variable_get('apbdtahun', 0);



	if (isSuperuser()) {
		if ($kodeuk !='00') {
			$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
		} 
		if ($statusinaktif=='0') {
			$qlike .= sprintf(' and k.inaktif=0 ');
		} elseif ($statusinaktif=='1') {
			$qlike .= sprintf(' and (k.inaktif=1 or k.plafon=0) ');
		} elseif ($statusinaktif=='2') {
			$qlike .= sprintf(' and k.dispensasi=1 ');
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

	//STATUS PENGISIAN
	if ($statusisi=='sudah') {
		$qlike .= sprintf(' and (k.total=k.plafon) and (k.plafon>0)');
	} elseif ($statusisi=='sebagian') {
		$qlike .= sprintf(' and (k.total>0) and (k.total<k.plafon) and (k.plafon>0) ');
	} elseif ($statusisi=='belum') {
		$qlike .= sprintf(' and (k.total=0) and (k.plafon>0) ');
	} elseif ($statusisi=='lebih') {
		$qlike .= sprintf(' and (k.total>k.plafon) and (k.plafon>0) ');
	}

	//STATUS TW
	if ($statustw=='sudah') {
		$qlike .= sprintf(' and k.total>0 and (k.total=(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	} elseif ($statustw=='belum') {
		$qlike .= sprintf(' and k.total>0 and (k.total>(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	}
	

	//STATUS JENIS
	if ($jenis=='gaji') {
		$qlike .= sprintf(' and k.jenis=1 and k.isppkd=0 ');
	} elseif ($jenis=='langsung') {
		$qlike .= sprintf(' and k.jenis=2 ');
	} elseif ($jenis=='ppkd') {
		$qlike .= sprintf(' and k.jenis=1 and k.isppkd=1 ');
	}
	
	//SUMBER DANA
	if ($sumberdana != '') {
		$qlike .= sprintf(' and (k.sumberdana1=\'%s\'  or k.sumberdana2=\'%s\') ', $sumberdana, $sumberdana);
		$ntitle .= ' ' . $sumberdana;
	}
			
	//keg cari
	if (strlen($kegiatan)>0) {
		$qlike .= sprintf(" and lower(k.kegiatan) like lower('%%%s%%') ", db_escape_string($kegiatan));
	}
	
	//$output .= drupal_get_form('kegiatanskpd_transfer_form');
	//$output .= drupal_get_form('kegiatanskpd_main_form');
	if (isSuperuser()) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Target', 'field'=> 'target', 'valign'=>'top'),
			array('data' => 'Lokasi',  'valign'=>'top'),
			array('data' => 'Sumberdana', 'field'=> 'sumberdana', 'valign'=>'top'),
			array('data' => 'Plafon', 'field'=> 'plafon','width' => '90px', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	} else {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Target', 'field'=> 'target', 'valign'=>'top'),
			array('data' => 'Lokasi',  'valign'=>'top'),
			array('data' => 'Sumberdana', 'field'=> 'sumberdana', 'valign'=>'top'),
			array('data' => 'Plafon', 'field'=> 'plafon','width' => '90px', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'total', 'width' => '90px','valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	}
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by k.kegiatan';
    }

	if (($rekening == '') and ($rincian =='')) {
		$where = ' where k.periode=2 and ' . sprintf(' k.tahun=%s ', $tahun) . $qlike ;
		$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.lokasi, 				
				k.programtarget,k.total,k.plafon,u.namasingkat, k.adminok, k.sumberdana1 sumberdana, k.inaktif from {kegiatanperubahan} k inner join {unitkerja} u on (k.kodeuk=u.kodeuk) " . $where;
		$fsql = $sql;
		
		$countsql = "select count(*) as cnt from {kegiatanperubahan} k" . $where;
		$result = pager_query($fsql . $tablesort, $limit, 0, $countsql);
		
	} else if (($rekening != '') and ($rincian =='')) {
		
		$qlike .= sprintf(" and lower(a.uraian) like lower('%%%s%%') ", db_escape_string($rekening));
		$where = ' where ' . sprintf(' k.tahun=%s ', $tahun) . $qlike ;
		$sql = "select distinct k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk, k.kegiatan,k.lokasi, 				
				k.programtarget,k.total,k.plafon,u.namasingkat, k.adminok, k.sumberdana1 sumberdana, k.inaktif from {kegiatanperubahan} k inner join {unitkerja} u on (k.kodeuk=u.kodeuk) inner join {anggperkegperubahan} a on k.kodekeg=a.kodekeg " . $where;
		$fsql = $sql;
		
		//echo $fsql;
		
		$countsql = "select count(*) as cnt from {kegiatanperubahan} k inner join {anggperkeg} a on k.kodekeg=a.kodekeg " . $where;
		$result = pager_query($fsql . $tablesort, $limit, 0, $countsql);
		
	} else if (($rekening == '') and ($rincian !='')) {
		
		$qlike .= sprintf(" and lower(d.uraian) like lower('%%%s%%') ", db_escape_string($rincian));
		$where = ' where ' . sprintf(' k.tahun=%s ', $tahun) . $qlike ;
		$sql = "select distinct  k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.lokasi, 				
				k.programtarget,k.total,k.plafon,u.namasingkat, k.adminok, k.sumberdana1 sumberdana, k.inaktif from {kegiatanperubahan} k inner join {unitkerja} u on (k.kodeuk=u.kodeuk) inner join {anggperkegperubahan} a on k.kodekeg=a.kodekeg inner join {anggperkegdetilperubahan} d on a.kodekeg=d.kodekeg and a.kodero=d.kodero " . $where;
		$fsql = $sql;
		
		$countsql = "select count(*) as cnt from {kegiatanperubahan} k inner join {anggperkegperubahan} a on k.kodekeg=a.kodekeg inner join {anggperkegdetilperubahan} d on a.kodekeg=d.kodekeg and a.kodero=d.kodero " . $where;
		$result = pager_query($fsql . $tablesort, $limit, 0, $countsql);
		
	} else {
		$qlike .= sprintf(" and lower(a.uraian) like lower('%%%s%%') and lower(d.uraian) like lower('%%%s%%') ", db_escape_string($rekening), db_escape_string($rincian));
		$where = ' where ' . sprintf(' k.tahun=%s ', $tahun) . $qlike ;
		$sql = "select distinct  k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.lokasi, 				
				k.programtarget,k.total,k.plafon,u.namasingkat, k.adminok, k.sumberdana1 sumberdana, k.inaktif from {kegiatanperubahan} k inner join {unitkerja} u on (k.kodeuk=u.kodeuk) inner join {anggperkegperubahan} a on k.kodekeg=a.kodekeg inner join {anggperkegdetilperubahan} d on a.kodekeg=d.kodekeg and a.kodero=d.kodero " . $where;
		$fsql = $sql;
		
		$countsql = "select count(*) as cnt from {kegiatanperubahan} k inner join {anggperkegperubahan} a on k.kodekeg=a.kodekeg inner join {anggperkegdetilperubahan} d on a.kodekeg=d.kodekeg and a.kodero=d.kodero " . $where;
		$result = pager_query($fsql . $tablesort, $limit, 0, $countsql);
		
	}
	
	//echo $fsql;
	
	//Jam,Menit,Detik,Bulan,Hari,Tahun
	//$batas = mktime(20, 0, 0, 6, 16, 2015) ;
	//$sekarang = time () ;
	//$selisih =($batas-$sekarang) ;
	$allowedit = true;		//(($selisih>0) || (isSuperuser()));
	
	//CEK TAHUN
	$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
    
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

			
			if (user_access('kegiatanskpd edit')) {
				//$kegname = l($data->kegiatan, 'apbd/kegiatanskpdperubahan/edit/' . $data->kodekeg , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				$kegname = l($data->kegiatan, 'apbd/kegiatanskpdperubahan/edit/' . $data->kodekeg , array('html' =>TRUE));
			} else {
				$kegname = $data->kegiatan ;
			}

			//$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/program/edit/' . $data->kodepro, array('html'=>TRUE));
			//$progname = l($data->program, 'apbd/program/edit/' . $data->kodepro , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
			
			if ($allowedit) 
				//if (user_access('kegiatanskpd penghapusan'))			"&nbsp;" .
				//$editlink =l('Rekening', 'apbd/kegiatanskpdperubahan/subkegiatan/' . $data->kodekeg, array('html'=>TRUE));
				
				//Baru boleh mengisi rekening ketika adminok
				if ($adminok or $data->adminok) {
					if ($data->total==0) {
						$editlink =l('Rekening', 'apbd/kegiatanskpdperubahan/rekening/edit/' . $data->kodekeg, array('html'=>TRUE));
					} else {
						$editlink =l('Rekening', 'apbd/kegiatanskpdperubahan/rekening/' . $data->kodekeg, array('html'=>TRUE));
					}
					$editlink .= "&nbsp;" .  l('Triwulan', 'apbd/kegiatanskpdperubahan/triwulan/' . $data->kodekeg, array('html'=>TRUE));
				
				} else {
					$editlink = 'Rekening';
					$editlink .= "&nbsp;" . 'Triwulan';
				}
				
			if (isSuperuser()) {
				$editlink .= "&nbsp;" . l('Edit', 'apbd/kegiatanskpdperubahan/editadmin/' . $data->kodekeg, array('html'=>TRUE));
				$editlink .= "&nbsp;" . l('Hapus', 'apbd/kegiatanskpdperubahan/delete/' . $data->kodekeg, array('html'=>TRUE));
				
			}
			
            $no++;
			
			if ($data->total > $data->plafon)
				$limit = "<img src='/files/limit.png'>";
			else
				$limit = '';
			
			if (isSuperuser()) { 
				if ($data->inaktif) 
					//$inaktif = 'x';
					$inaktif = "<img src='/files/inaktif.png'>";
				
				else
					$inaktif ='';
				
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $inaktif, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $limit, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->programtarget, 'align' => 'left', 'valign'=>'top'),
					array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sumberdana, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->plafon), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),

					array('data' => $limit, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->programtarget, 'align' => 'left', 'valign'=>'top'),
					array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sumberdana, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->plafon), 'align' => 'right', 'valign'=>'top'),
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


    $btn = "&nbsp;" . l("Daftar Kegiatan", 'apbd/kegiatanskpdperubahan' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));


    $btn .= "&nbsp;" . l("Cari", 'apbd/kegiatanskpdperubahan/find/' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
	
    $btn .= "&nbsp;" . l("Cetak", 'apbd/kegiatanskpdperubahancari/filter/' . $kodeuk . '/' . $sumberdana . '/' . $statusisi . '/' . $kodesuk . '/'. $statustw . '/' . $statusinaktif . '/' . $jenis . '/' . $kegiatan . '/' . $rekening . '/' . $rincian . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
	
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

	
	//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanskpd tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanskpdperubahan/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpdperubahan/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
}

function GenDataPrint($kodeuk , $sumberdana , $statusisi , $kodesuk , $statustw , 
				$statusinaktif , $jenis , $kegiatan , $rekening , $rincian ) {
	
	set_time_limit(0);
	ini_set('memory_limit', '640M');
	
	$totalP =0;
	$totalA =0;
	$headersrek[] = array (
						 
						 array('data' => 'No.',  'width'=> '25px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Kegiatan',  'width' => '240px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Sumberdana',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Plafon',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Anggaran',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
					);


	$kodesuk = '';
	$tahun = variable_get('apbdtahun', 0);



	if (isSuperuser()) {
		if ($kodeuk !='00') {
			$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
		} 
		if ($statusinaktif=='0') {
			$qlike .= sprintf(' and k.inaktif=0 ');
		} elseif ($statusinaktif=='1') {
			$qlike .= sprintf(' and (k.inaktif=1 or k.plafon=0) ');
		} elseif ($statusinaktif=='2') {
			$qlike .= sprintf(' and k.dispensasi=1 ');
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

	//STATUS PENGISIAN
	if ($statusisi=='sudah') {
		$qlike .= sprintf(' and (k.total=k.plafon) and (k.plafon>0)');
	} elseif ($statusisi=='sebagian') {
		$qlike .= sprintf(' and (k.total>0) and (k.total<k.plafon) and (k.plafon>0) ');
	} elseif ($statusisi=='belum') {
		$qlike .= sprintf(' and (k.total=0) and (k.plafon>0) ');
	} elseif ($statusisi=='lebih') {
		$qlike .= sprintf(' and (k.total>k.plafon) and (k.plafon>0) ');
	}

	//STATUS TW
	if ($statustw=='sudah') {
		$qlike .= sprintf(' and k.total>0 and (k.total=(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	} elseif ($statustw=='belum') {
		$qlike .= sprintf(' and k.total>0 and (k.total>(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	}
	

	//STATUS JENIS
	if ($jenis=='gaji') {
		$qlike .= sprintf(' and k.jenis=1 and k.isppkd=0 ');
	} elseif ($jenis=='langsung') {
		$qlike .= sprintf(' and k.jenis=2 ');
	} elseif ($jenis=='ppkd') {
		$qlike .= sprintf(' and k.jenis=1 and k.isppkd=1 ');
	}
	
	//SUMBER DANA
	if ($sumberdana != '') {
		$qlike .= sprintf(' and (k.sumberdana1=\'%s\'  or k.sumberdana2=\'%s\') ', $sumberdana, $sumberdana);
		$ntitle .= ' ' . $sumberdana;
	}
			
	//keg cari
	if (strlen($kegiatan)>0) {
		$qlike .= sprintf(" and lower(k.kegiatan) like lower('%%%s%%') ", db_escape_string($kegiatan));
	}

	if (($rekening == '') and ($rincian =='')) {
		$where = ' where k.periode=2 and ' . sprintf(' k.tahun=%s ', $tahun) . $qlike ;
		$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.lokasi, 				
				k.programtarget,k.total,k.plafon,u.namasingkat, k.adminok, k.sumberdana1 sumberdana, k.inaktif from {kegiatanperubahan} k inner join {unitkerja} u on (k.kodeuk=u.kodeuk) " . $where;
		
		
	} else if (($rekening != '') and ($rincian =='')) {
		
		$qlike .= sprintf(" and lower(a.uraian) like lower('%%%s%%') ", db_escape_string($rekening));
		$where = ' where ' . sprintf(' k.tahun=%s ', $tahun) . $qlike ;
		$sql = "select distinct k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk, k.kegiatan,k.lokasi, 				
				k.programtarget,k.total,k.plafon,u.namasingkat, k.adminok, k.sumberdana1 sumberdana, k.inaktif from {kegiatanperubahan} k inner join {unitkerja} u on (k.kodeuk=u.kodeuk) inner join {anggperkegperubahan} a on k.kodekeg=a.kodekeg " . $where;
		
		
	} else if (($rekening == '') and ($rincian !='')) {
		
		$qlike .= sprintf(" and lower(d.uraian) like lower('%%%s%%') ", db_escape_string($rincian));
		$where = ' where ' . sprintf(' k.tahun=%s ', $tahun) . $qlike ;
		$sql = "select distinct  k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.lokasi, 				
				k.programtarget,k.total,k.plafon,u.namasingkat, k.adminok, k.sumberdana1 sumberdana, k.inaktif from {kegiatanperubahan} k inner join {unitkerja} u on (k.kodeuk=u.kodeuk) inner join {anggperkegperubahan} a on k.kodekeg=a.kodekeg inner join {anggperkegdetilperubahan} d on a.kodekeg=d.kodekeg and a.kodero=d.kodero " . $where;
		
		
	} else {
		$qlike .= sprintf(" and lower(a.uraian) like lower('%%%s%%') and lower(d.uraian) like lower('%%%s%%') ", db_escape_string($rekening), db_escape_string($rincian));
		$where = ' where ' . sprintf(' k.tahun=%s ', $tahun) . $qlike ;
		$sql = "select distinct  k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.lokasi, 				
				k.programtarget,k.total,k.plafon,u.namasingkat, k.adminok, k.sumberdana1 sumberdana, k.inaktif from {kegiatanperubahan} k inner join {unitkerja} u on (k.kodeuk=u.kodeuk) inner join {anggperkegperubahan} a on k.kodekeg=a.kodekeg inner join {anggperkegdetilperubahan} d on a.kodekeg=d.kodekeg and a.kodero=d.kodero " . $where;
		
		
	}
	
	$result = db_query($sql);
	
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$no += 1;

			$totalP += $data->plafon;
			$totalA += $data->total;
			
			$rowsrek[] = array (
								 array('data' => $no,  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
								 array('data' => $data->kegiatan . ' (' . $data->namasingkat . ')' ,  'width' => '240px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => $data->sumberdana,  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($data->plafon),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 array('data' => apbd_fn($data->total),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );				

		}
	}										 
								 			
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-top: 2px solid black; border-bottom: 1px solid black; text-align:right;'),
						 array('data' => 'TOTAL',  'width' => '240px', 'style' => ' border-top: 2px solid black; border-bottom: 1px solid black; text-align:left;'),
						 array('data' => '',  'width' => '90px', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; border-bottom: 1px solid black; text-align:left;'),
						 array('data' => apbd_fn($totalP),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 2px solid black; border-bottom: 1px solid black; text-align:right;'),
						 array('data' => apbd_fn($totalA),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 2px solid black; border-bottom: 1px solid black; text-align:right;'),
						 );				

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$output = theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	return $output;
	
}

function GenDataHeader() {

	
	$rowsjudul[] = array (array ('data'=>'HASIL PENCARIAN KEGIATAN', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}

?>
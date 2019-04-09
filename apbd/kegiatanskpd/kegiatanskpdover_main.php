<?php
function kegiatanskpdover_main($arg=NULL, $nama=NULL) {
	  
	$exportpdf = arg(2);

	//drupal_set_message($exportpdf);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		$pdfFile = 'Daftar_Kegiatan_Melebihi_Plafon.pdf';
		
		$htmlHeader = GenDataHeader();
		$htmlContent = GenDataPrint();
		
		apbd_ExportPDF2P(10, 10, $htmlHeader, $htmlContent, $pdfFile);
		
	} else {
		$output = GenDataView();
		return $output;
	}
}

function GenDataView() {
	drupal_add_css('files/css/kegiatancam.css');
	$limit = 15;
	
	$tahun = variable_get('apbdtahun', 0);

	$customwhere = sprintf(' and k.tahun=%s ', $tahun);
	if (isSuperuser()) {
		$adminok = true;
							
	} else {
		$customwhere .= sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
		$adminok = false;
	}

	$customwhere .= sprintf(' and k.inaktif=0 ');

			
	
	if (isSuperuser()) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
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
	
    $where = ' where k.total>k.plafon ' . $customwhere . $qlike ;

	$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodeuk,k.kegiatan,k.lokasi,
			k.programtarget,k.total, k.plafon,u.namasingkat, k.adminok, k.sumberdana1 sumberdana, k.inaktif from {kegiatanskpd} k inner join {unitkerja} u on ( k.kodeuk=u.kodeuk) " . $where;
	//$fsql = sprintf($sql, addslashes($nama));
	$fsql = $sql;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kegiatanskpd} k " . $where;
    //$fcountsql = sprintf($countsql, addslashes($nama));
	$fcountsql = $countsql;
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);

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
				//$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/edit/' . $data->kodekeg , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/edit/' . $data->kodekeg , array('html' =>TRUE));
			} else {
				$kegname = $data->kegiatan ;
			}

			//$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/program/edit/' . $data->kodepro, array('html'=>TRUE));
			//$progname = l($data->program, 'apbd/program/edit/' . $data->kodepro , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
			
			if ($allowedit) 
				//if (user_access('kegiatanskpd penghapusan'))			"&nbsp;" .
				//$editlink =l('Rekening', 'apbd/kegiatanskpd/subkegiatan/' . $data->kodekeg, array('html'=>TRUE));
				
				//Baru boleh mengisi rekening ketika adminok
				if ($adminok or $data->adminok) {
					if ($data->total==0) {
						$editlink =l('Rekening', 'apbd/kegiatanskpd/rekening/edit/' . $data->kodekeg, array('html'=>TRUE));
					} else {
						$editlink =l('Rekening', 'apbd/kegiatanskpd/rekening/' . $data->kodekeg, array('html'=>TRUE));
					}
					$editlink .= "&nbsp;" .  l('Triwulan', 'apbd/kegiatanskpd/triwulan/' . $data->kodekeg, array('html'=>TRUE));
				
				} else {
					$editlink = 'Rekening';
					$editlink .= "&nbsp;" . 'Triwulan';
				}
				
			if (isSuperuser()) {
				$editlink .= "&nbsp;" . l('Edit', 'apbd/kegiatanskpd/editadmin/' . $data->kodekeg, array('html'=>TRUE));
				$editlink .= "&nbsp;" . l('Hapus', 'apbd/kegiatanskpd/delete/' . $data->kodekeg, array('html'=>TRUE));
				
			}
			
            $no++;
			
			
			if (isSuperuser()) { 
				if ($data->inaktif) 
					//$inaktif = 'x';
					$inaktif = "<img src='/files/inaktif.png'>";
				
				else
					$inaktif ='';
				
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $inaktif, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
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
	if ($allowedit)
		//
		if (isSuperuser()) {
			$btn .= l('Baru', 'apbd/kegiatanskpd/editadmin/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
		}

	$status = 0;
	$record = 0;

    $btn = "&nbsp;" . l("Daftar Kegiatan", 'apbd/kegiatanskpd' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
    $btn .= "&nbsp;" . l("Cetak", 'apbd/kegiatanskpdover/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
	
	
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

	
	//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanskpd tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanskpd/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpd/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
	
    return $output;	
}

function GenDataHeader() {

	
	$rowsjudul[] = array (array ('data'=>'DAFTAR KEGIATAN MELEBIHI PLAFON', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}

function GenDataPrint() {
	
	//set_time_limit(0);
	//ini_set('memory_limit', '640M');
	
	$headersrek[] = array (
						 
						 array('data' => 'No.',  'width'=> '25px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Kegiatan',  'width' => '240px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Sumberdana',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Plafon',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Anggaran',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
					);


    $where = ' where k.total>k.plafon ' . $customwhere . $qlike ;

	$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.lokasi,
			k.programtarget,k.total, k.plafon,u.namasingkat, k.adminok, k.sumberdana1 sumberdana, k.inaktif from {kegiatanskpd} k inner join {unitkerja} u on ( k.kodeuk=u.kodeuk) " . $where . " order by k.kegiatan, k.plafon";
	$result = db_query($sql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$no += 1;
			
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
						 array('data' => '',  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;'),
						 array('data' => '',  'width' => '240px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:left;'),
						 array('data' => '',  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:left;'),
						 array('data' => '',  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;'),
						 array('data' => '',  'width' => '90px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;'),
						 );				

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$output = theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	return $output;
	
}

?>
<?php
function kegiatancam_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	$qlike='';
    $limit = 15;
	$allowedit =true;
	$ntitle = 'RPTK ';
    if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
				
			case 'filter':
				$nntitle ='';
				
				$tahun = arg(3);
				$ntitle .= $tahun;
				
				$kodeuk = arg(4);
				$kodeuktujuan = arg(5);
				$sumberdana= arg(6);				

				if (!is_numeric($limit))
					$limit=13;
				if (isSuperuser()) {
					if ($kodeuk !='00') {
						$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
						$pquery = sprintf("select kodeuk, namasingkat from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk));
						$presult = db_query($pquery);
						if ($data=db_fetch_object($presult)) {
							$nntitle .= $data->namasingkat . ", ";
						}
					} else {
						//$ntitle .= ' Semua Kecamatan - '
					}
				} else {
					$tahun = variable_get('apbdtahun', 0);
				}
				
				if ($kodeuktujuan !='') {
					$qlike .= sprintf(' and k.kodeuktujuan=\'%s\' ', $kodeuktujuan);
				}
				switch($sumberdana) {
					case 'apbd':
						$nntitle .= ' Sumber Dana APBD, ';
						$qlike .= sprintf(' and k.apbdkab>0 ');
						break;
					case 'pnpm':
						$nntitle .= ' Sumber Dana PNPM, ';
						$qlike .= sprintf(' and k.pnpm>0 ');
						break;
					case 'pik':
						$nntitle .= ' Sumber Dana PIK, ';
						$qlike .= sprintf(' and k.pik>0 ');
						break;
				}
				if (strlen($nntitle) > 0)
					$ntitle .= ', ' . substr($nntitle, 0 , strlen($nntitle)-2);
				
				
				break;

			case 'excel':
				$tahun = arg(3);
				$kodeuk = arg(4);
				kegiatancam_exportexcel($tahun, $kodeuk);
				break;
				
			default:
				drupal_access_denied();
				break;
		}
	
	} else {
		
		$tahun = variable_get('apbdtahun', 0);
		$ntitle .= $tahun . ' ';
		
		if (!isSuperuser() and !isUserKecamatan()) {
			$kodeuktujuan = apbd_getuseruk();
			$qlike = sprintf(' and k.kodeuktujuan=\'%s\' ', $kodeuktujuan);
		}
	}
	drupal_set_title($ntitle);

	$output .= drupal_get_form('kegiatancam_main_form');
	if (isSuperuser()) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => ucwords(strtolower('Kecamatan')), 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => ucwords(strtolower('kegiatan')), 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => ucwords(strtolower('lokasi')), 'field'=> 'lokasi', 'valign'=>'top'),
			array('data' => ucwords(strtolower('sasaran')), 'field'=> 'sasaran', 'valign'=>'top'),
			array('data' => ucwords(strtolower('target')), 'field'=> 'target', 'valign'=>'top'),
			array('data' => ucwords(strtolower('Dinas Teknis')), 'field'=> 'uktujuan', 'valign'=>'top'),		
			array('data' => ucwords(strtolower('Jumlah')), 'field'=> 'total', 'valign'=>'top'),
			array('data' => '', 'width' => '100px', 'valign'=>'top'),
		);
	
    } elseif (isUserKecamatan()) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => ucwords(strtolower('kegiatan')), 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => ucwords(strtolower('lokasi')), 'field'=> 'lokasi', 'valign'=>'top'),
			array('data' => ucwords(strtolower('sasaran')), 'field'=> 'sasaran', 'valign'=>'top'),
			array('data' => ucwords(strtolower('target')), 'field'=> 'target', 'valign'=>'top'),
			array('data' => ucwords(strtolower('Dinas Teknis')), 'field'=> 'uktujuan', 'valign'=>'top'),		
			array('data' => ucwords(strtolower('Jumlah')), 'field'=> 'total', 'valign'=>'top'),
			array('data' => '', 'width' => '100px', 'valign'=>'top'),
		); 
	} else {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => ucwords(strtolower('kegiatan')), 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => ucwords(strtolower('lokasi')), 'field'=> 'lokasi', 'valign'=>'top'),
			array('data' => ucwords(strtolower('sasaran')), 'field'=> 'sasaran', 'valign'=>'top'),
			array('data' => ucwords(strtolower('target')), 'field'=> 'target', 'valign'=>'top'),
			array('data' => ucwords(strtolower('Kecamatan')), 'field'=> 'namasingkat', 'valign'=>'top'),		
			array('data' => ucwords(strtolower('Jumlah')), 'field'=> 'total', 'valign'=>'top'),
			array('data' => '', 'width' => '100px', 'valign'=>'top'),
		); 
	}
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by koderesmi';
    }
	//FILTER TAHUN-----
	//$customwhere = sprintf(' and k.tahun=%s ', variable_get('apbdtahun', 0));
	$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
	
	$customwhere = sprintf(' and k.tahun=%s ', $tahun);
	if (isUserKecamatan()) {
		$kodeuk = apbd_getuseruk();
		$customwhere .= sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	 
	}
	
    $where = ' where true' . $customwhere . $qlike ;

    $sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kodeuktujuan,k.sifat,k.kegiatan,k.lokasi,
			k.sasaran,k.target,k.totalsebelum,k.total,k.targetsesudah,k.nilai,k.lolos,k.asal,k.kodekec,
			k.apbdkab,k.apbdprov,k.apbdnas,k.kodebid,k.dekon,k.apbp,k.apbn,k.kodesuk,k.totalsebelum2,k.totalsebelum3,
			k.totalpenetapan,k.sumberdana,k.pnpm,skpd.namasingkat uktujuan,u.namasingkat, 
			concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi from {kegiatankec} k 
			left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro) left join {unitkerjaskpd} skpd on {k.kodeuktujuan=skpd.kodeuk}  " . $where;
    $fsql = sprintf($sql, addslashes($nama));
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kegiatankec} k" . $where;
    $fcountsql = sprintf($countsql, addslashes($nama));
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
    
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

			//if (user_access('kegiatancam edit'))
			//	$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/kegiatancam/edit/' . $data->kodekeg, array('html'=>TRUE)). "&nbsp;";
			//if (user_access('kegiatancam penghapusan'))
            //    $editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/kegiatancam/delete/' . $data->kodekeg, array('html'=>TRUE));
				
			//Semua bisa buka
			//if (user_access('kegiatancam edit')) {
				$kegname = l($data->kegiatan, 'apbd/kegiatancam/edit/' . $data->kodekeg, array('html'=>TRUE)). "&nbsp;";
			//} else {
			//	$kegname = $data->kegiatan;
			//}
			if (user_access('kegiatancam penghapusan') and $allowedit)
                $editlink =l('Hapus', 'apbd/kegiatancam/delete/' . $data->kodekeg, array('html'=>TRUE));


			$no++;
			$desa = split(",", $data->lokasi);
			$desalink = "";
			for ($i=0; $i<count($desa); $i++) {
				if (strlen($desa[$i])>0)
					$desalink .= l($desa[$i], 'apbd/desa/showc/' . $desa[$i], array('html'=>true, 'attributes' => array('id' => $data->kodekeg . "_" . $desa[$i], 'name' => 'view_desa'))) . "&nbsp;";
			}
			if (isSuperuser()) {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),                
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sasaran, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->target, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->uktujuan, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
				);
			} elseif (isUserKecamatan()) {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),                
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sasaran, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->target, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->uktujuan, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
				);
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),                
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sasaran, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->target, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
				);
			}
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$tahun = variable_get('apbdtahun', 0);
	$status = 0;
	$record = 0;
	
	$btn = "";
	if (user_access('kegiatancam tambah')  and $allowedit) {
		$btn .= l('Baru', 'apbd/kegiatancam/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	}
	if (user_access('kegiatancam pencarian'))	{
		$btn .= l('Cari', 'apbd/kegiatancam/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	}
	$btn .= l('Cetak', 'apbd/laporan/musrenbangcam/' .$kodeuk . '/' . $tahun . '/' . $kodeuktujuan . '/' . $record, array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	//if (isSuperuser() and $allowedit) {
	//	$btn .= l('Transfer', 'apbd/kegiatanskpd/transfer/' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	//}

	$btn .= "&nbsp;" . l('Simpan Excel', 'apbd/kegiatancam/excel/' . $tahun . '/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));		
	
	$output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;
	

//	if (user_access('kegiatancam tambah')) {
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatancam/edit/' , array('html'=>TRUE)) ;
//		$btn .= l('Tambah Baru', 'apbd/kegiatancam/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue')));
//	}
//	if (user_access('kegiatancam pencarian'))	{
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatancam/find/' , array('html'=>TRUE)) ;
//		$btn .= l('Cari Data', 'apbd/kegiatancam/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue')));
//	}
		
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}


function kegiatancam_main_form() {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Pilihan Data',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);
	$filter = arg(2);
	if (isset($filter) && ($filter=='filter')) {
		$tahun = arg(3);
		$kodeuk = arg(4);
		$kodeuktujuan = arg(5);
		$sumberdana = arg(6);
		//$baris = arg(7);
		//if (!is_numeric($baris))
		//	$baris= 15;
	} else {
		$tahun = variable_get('apbdtahun', 0);
	}
	//drupal_set_message($filter);

	//if (isset($kodeuk)) {
	//    $form['formdata']['#collapsed'] = TRUE;
	//    //if (isUserKecamatan())
	//    //    if ($kodeuk != apbd_getuseruk())
	//    //        $form['formdata']['#collapsed'] = FALSE;
	//}
	$tahunopt = array();
	$tahunopt['2015'] = '2015';
	$tahunopt['2016'] = '2016';
	$tahunopt['2017'] = '2017';
	$tahunopt['2018'] = '2018';
	$tahunopt['2019'] = '2019';
	$tahunopt['2020'] = '2020';
		
	$pquery = "select kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by namauk" ;
	$pres = db_query($pquery);
	$dinas = array();        
	
	$dinas['00'] ='----SEMUA SKPD----';
	while ($data = db_fetch_object($pres)) {
		$dinas[$data->kodeuk] = $data->namasingkat;
	}
	$type='select';
	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		$tahun = variable_get('apbdtahun', 0);
		//drupal_set_message('user kec');
	}  

	$form['formdata']['tahun']= array(
		'#type'         => $type, 
		'#title'        => 'Tahun',
		'#options'	=> $tahunopt,
		'#width'         => 20, 
		'#default_value'=> $tahun, 
	);
	  
	if (isUserKecamatan()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		//drupal_set_message('user kec');
	} else {
		$type='select';
		$pquery = "select kodedinas,kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 and iskecamatan=1 order by kodedinas" ;
		
		$pres = db_query($pquery);
		$dinas = array();        	
		$dinas['00'] ='00000 - SEMUA KECAMATAN';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}
	}
	
	$form['formdata']['kodeuk']= array(
		'#type'         => $type, 
		'#title'        => 'Kecamatan',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	);

	//uktujuan
	$pquery1 = "select kodeuk, namasingkat from {unitkerjaskpd} order by namasingkat" ;
	$pres1 = db_query($pquery1);
	$dinastujuan = array();        
	
	$dinastujuan[''] ='SEMUA DINAS TEKNIS';
	while ($data1 = db_fetch_object($pres1)) {
		$dinastujuan[$data1->kodeuk] = $data1->namasingkat;
	} 
	$type='select';
	if (!isSuperuser() and !isUserKecamatan()) {
		$type = 'hidden';
		$kodeuktujuan = apbd_getuseruk();
		//drupal_set_message('user kec');
	}	
	$form['formdata']['kodeuktujuan']= array(
		'#type'         => $type, 
		'#title'        => 'Dinas Teknis',
		'#options'	=> $dinastujuan,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuktujuan, 
	);

	$recordopt = array();
	$recordopt['00'] = 'APBD+PIK+PNPM';
	$recordopt['apbd'] = 'APBD';
	$recordopt['pik'] = 'PIK';
	$recordopt['pnpm'] = 'PNPM';
	$form['formdata']['sumberdana']= array(
		'#type'         => 'select', 
		'#title'        => 'Sumber Pendanaan',
		'#options'	=> $recordopt,
		'#width'         => 20, 
		'#default_value'=> $sumberdana, 
	);
	
	
	//$bb = array();
	//$bb['15'] = '15 baris/halaman';
	//$bb['30'] = '30 baris/halaman';
	//$bb['50'] = '50 baris/halaman';
	//$bb['100'] = '100 baris/halaman';

	//$form['formdata']['baris']= array(
	//	'#type'         => 'select', 
	//	'#title'        => 'Baris/Halaman',
	//	'#options'	=> $bb,
	//	//'#description'  => 'kodeuktujuan', 
	//	//'#maxlength'    => 60, 
	//	'#width'         => 20, 
	//	//'#required'     => !$disabled, 
	//	//'#disabled'     => $disabled, 
	//	'#default_value'=> $baris, 
	//);

	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan'
	);
	
	return $form;
}

function kegiatancam_main_form_submit($form, &$form_state) {
	$sumberdana = $form_state['values']['sumberdana'];
	$kodeuk= $form_state['values']['kodeuk'];
	$kodeuktujuan= $form_state['values']['kodeuktujuan'];
	$tahun = $form_state['values']['tahun'];
	//$baris= $form_state['values']['baris'];
	
	$uri = 'apbd/kegiatancam/filter/' . $tahun . '/' . $kodeuk . '/' .$kodeuktujuan . '/' . $sumberdana ;
	drupal_goto($uri);
	
}

function kegiatancam_exportexcel($tahun, $kodeuk) {
error_reporting(E_ALL);
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
$objPHPExcel->getProperties()->setCreator("SiPPD Online")
							 ->setLastModifiedBy("SiPPD Online")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Excel document generated from SiPPD Online.")
							 ->setKeywords("office 2007 SiPPD openxml php")
							 ->setCategory("SiPPD Online RKPD");
// Add Header
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $row ,'Tahun')
			->setCellValue('B' . $row ,'Kecamatan')
			->setCellValue('C' . $row ,'Kegiatan')
			->setCellValue('D' . $row ,'Sasaran')
			->setCellValue('E' . $row ,'Target')
			->setCellValue('F' . $row ,'Lokasi')
			->setCellValue('G' . $row ,'Dinas Teknis')
			->setCellValue('H' . $row ,'Jumlah');

//Open data							 
//$customwhere = sprintf(' and k.tahun=%s ', variable_get('apbdtahun', 0));
$customwhere = sprintf(' and k.tahun=%s ', $tahun);
if ($kodeuk!='00') {
	$customwhere .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);	
}	
$where = ' where true' . $customwhere;
	
$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kodeuktujuan,k.sifat,k.kegiatan,
		k.lokasi,k.sasaran,k.target,k.totalsebelum,k.total,k.targetsesudah,k.nilai,k.lolos,k.asal,
		k.kodekec,k.apbdkab,k.apbdprov,k.apbdnas,k.kodebid,k.dekon,k.apbp,k.apbn,k.kodesuk,
		k.totalsebelum2,k.totalsebelum3,k.totalpenetapan,k.sumberdana,k.pnpm,skpd.namasingkat uktujuan,
		u.namasingkat, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi 
		from {kegiatankec} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p 
		on (k.kodepro = p.kodepro) left join {unitkerjaskpd} skpd on {k.kodeuktujuan=skpd.kodeuk}  " . $where;
$result = db_query($sql);
while ($data = db_fetch_object($result)) {
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row, $data->tahun)
				->setCellValue('B' . $row, $data->namasingkat)
				->setCellValue('C' . $row, $data->kegiatan)
				->setCellValue('D' . $row, $data->sasaran)
				->setCellValue('E' . $row, $data->target)
				->setCellValue('F' . $row, $data->lokasi)
				->setCellValue('G' . $row, $data->uktujuan)
				->setCellValue('H' . $row, $data->total);
}
						

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('RPTK');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = 'RPTK_' . $tahun . '-' . $kodeuk . '.xlsx';
header('Content-Disposition: attachment;filename=' . $fname);
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}

?>

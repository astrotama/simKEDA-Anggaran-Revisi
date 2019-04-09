<?php
function pendapatan_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 15;
	$tahun = variable_get('apbdtahun', 0);
	
	$ntitle = 'Pendapatan';
	$namaskpd = '';
    if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
				$kodeuk = arg(3);
				$kodek = arg(4);
				

				break;

			case 'excel':
				$kodeuk = arg(3);
				break;

			default:
				drupal_access_denied();
				break;
		}
		
	} else {
		$kodek = $_SESSION['kodek'];	

		if (isSuperuser()) {
			$kodeuk = $_SESSION['kodeukpendapatan'];
			if ($kodeuk == '') 	$kodeuk = '00';
			
		} 	
	}
	
	if (isSuperuser()) {
		if ($kodeuk !='00') {
			$qlike .= sprintf(' and p.kodeuk=\'%s\' ', $kodeuk);
			
			
			$pquery = sprintf("select kodeuk, namasingkat from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk));
			$presult = db_query($pquery);
			if ($data=db_fetch_object($presult)) {
				$ntitle .= ' ' . $data->namasingkat;
			}
			
		} 

		if ($kodek !='') {
			$qlike .= sprintf(' and left(p.kodero,2)=\'%s\' ', $kodek);
		} 
		
	} 

	
	$allowedit = (batastgl() || (isSuperuser()));	

	if ($allowedit==false) {
		//dispensasirenja
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
	
	//$output .= drupal_get_form('pendapatan_transfer_form');
	if (isSuperuser()) {
		$output .= drupal_get_form('pendapatan_main_form');
	
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Kode', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Uraian', 'field'=> 'sasaran', 'valign'=>'top'),
			array('data' => 'Dasar Hukum', 'field'=> 'ketrekening', 'valign'=>'top'),
			array('data' => 'Sebelumnya', 'field'=> 'jumlahsebelum','width' => '90px', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'jumlah', 'width' => '90px','valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	} else {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Kode', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Uraian', 'field'=> 'sasaran', 'valign'=>'top'),
			array('data' => 'Dasar Hukum', 'field'=> 'ketrekening', 'valign'=>'top'),
			array('data' => 'Sebelumnya', 'field'=> 'jumlahsebelum','width' => '90px', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'jumlah', 'width' => '90px','valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	}
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by p.kodero';
    }
	
	$customwhere = sprintf(' and p.tahun=%s ', $tahun);
	if (!isSuperuser()) {
		$kodeuk = apbd_getuseruk();
		$customwhere .= sprintf(' and p.kodeuk=\'%s\' ', apbd_getuseruk());	
	}	
    $where = ' where true' . $customwhere . $qlike ;

	//drupal_set_message($where);			
	$pquery = 'select sum(jumlah) jumlahx from {anggperuk} p ' . $where;
	
	//drupal_set_message($pquery);			
	
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ntitle .= ', Jumlah Anggaran : ' . apbd_fn($data->jumlahx);
	
	drupal_set_title($ntitle);
	
	$sql = "select p.kodeuk,p.tahun,p.kodero,p.uraian,p.jumlah,p.jumlahsebelum,p.jumlahsesudah,
			u.namasingkat,p.ketrekening from {anggperuk} p left join {unitkerja} u on (p.kodeuk=u.kodeuk) " . $where;
	//$fsql = sprintf($sql, addslashes($nama));
	$fsql = $sql;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {anggperuk} p" . $where;
    //$fcountsql = sprintf($countsql, addslashes($nama));
	$fcountsql = $countsql;
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
			
			if (user_access('kegiatanskpd edit')) {
				$uraian = l($data->uraian, 'apbd/pendapatan/edit/' . $data->kodeuk . '/' . $data->kodero , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
			} else {
				$uraian = $data->uraian;
			}

			if ($allowedit) {
				//if (user_access('kegiatanskpd penghapusan'))
				//$editlink =l('Rekening', 'apbd/pendapatan/subkegiatan/' . $data->kodekeg, array('html'=>TRUE));
				$editlink =l('Detil', 'apbd/pendapatan/detil/' . $data->kodeuk . '/'  . $data->kodero, array('html'=>TRUE))  . '&nbsp;';
				$editlink .=l('Hapus', 'apbd/pendapatan/delete/' . $data->kodeuk . '/'  . $data->kodero, array('html'=>TRUE));
			}
            $no++;
			
			if (isSuperuser()) { 
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->kodero, 'align' => 'left', 'valign'=>'top'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->ketrekening, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->jumlahsebelum), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->jumlah), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $data->kodero, 'align' => 'left', 'valign'=>'top'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->ketrekening, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->jumlahsebelum), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->jumlah), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			}
		}
    } else {
        $rows[] = array (
					array('data' => 'Akses/Data error, hubungi administrator', 'colspan'=>'8')
        );
    }
	if ($no==0) {
		
		if ($allowedit)
			$linknew = l('Rekening Baru', 'apbd/pendapatan/edit/', array('html' =>TRUE));	
		else
			$linknew = 'Rekening Baru';
		$rows[] = array (
			array('data' => 'Rekening belum diisikan, klik ' . $linknew . ' untuk menambahkan.', 'colspan'=>'8')
		);
	}
	

	$btn = "";
	if ($allowedit)
		if (user_access('kegiatanskpd tambah')) {
			$btn .= l('Rekening Baru', 'apbd/pendapatan/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
		}

	$status = 0;
	$record = 0;
	
	if ($kodeuk!='00')  {
		$btn .= l('Anggaran Kas', 'kaspad/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')))  . "&nbsp;";
		
		if ($kodeuk == '81')  
			$btn .= l('Anggaran Kas PPKD', 'kaspppkd' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')))  . "&nbsp;";
		
	$btn .= l('Cetak', 'apbd/pendapatan/print/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	} else {
		$btn .= l('Cetak', 'apbd/laporan/rka/rekapaggpad/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	}
	//$btn .= "&nbsp;" . l('Simpan Excel', '' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));	

    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

	
	//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanskpd tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/pendapatan/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/pendapatan/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}


function pendapatan_main_form() {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Pilihan Data',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);
	$filter = arg(2);
	if (isset($filter) && ($filter=='filter')) {
		$kodeuk = arg(3);
		$kodek = arg(4);
	} else {
		$kodek = $_SESSION['kodek'];	

		if (isSuperuser()) {
			$kodeuk = $_SESSION['kodeukpendapatan'];
			if ($kodeuk == '') 	$kodeuk = '00';
			
		} 		
	}
	//drupal_set_message($filter);

	//if (isset($kodeuk)) {
	//    $form['formdata']['#collapsed'] = TRUE;
	//    //if (isUserKecamatan())
	//    //    if ($kodeuk != apbd_getuseruk())
	//    //        $form['formdata']['#collapsed'] = FALSE;
	//}
		  
	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		$kodek = '00';
		
	} else {

		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		$dinas['00'] ='00000 - SEMUA SKPD';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}

		$pquery = "select kodek, uraian from {kelompok} where kodea='4' order by kodek" ;
		$pres = db_query($pquery);
		$rekopt = array();        
		
		$rekopt[''] ='00 - SEMUA';
		while ($data = db_fetch_object($pres)) {
			$rekopt[$data->kodek] = $data->kodek . ' - ' . $data->uraian;
		}

		$type='select';
	}

	$form['formdata']['kodeuk']= array(
		'#type'         => $type, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	);

	$form['formdata']['kodek']= array(
		'#type'         => $type, 
		'#title'        => 'Kelompok',
		'#options'	=> $rekopt,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodek, 
	);
	
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan'
	);
	
	return $form;
}
function pendapatan_main_form_submit($form, &$form_state) {
	$kodeuk= $form_state['values']['kodeuk'];
	$kodek= $form_state['values']['kodek'];

	$_SESSION['kodek'] = $kodek;
	if (isSuperuser()) 
		$_SESSION['kodeukpendapatan'] = $kodeuk;
	
	$uri = 'apbd/pendapatan/filter/' . $kodeuk  . '/' . $kodek ;
	drupal_goto($uri);
	
}

function pendapatan_transfer_form() {
	$form['formtransfer'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Transfer Data Dari MUSRENBANGCAM',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);
	$pquery = "select kodeuk, namauk, namasingkat from {unitkerja} where aktif=1 and iskecamatan=1 order by namasingkat" ;
	$pres = db_query($pquery);
	$dinas = array();
	$kodeuk = apbd_getuseruk();
	$typekodeuk = 'select';
	if (!isSuperuser())
		$typekodeuk='hidden';
	//$dinas[''] = '--- pilih dinas teknis---';
	while ($data = db_fetch_object($pres)) {
		$dinas[$data->kodeuk] = $data->namasingkat;
	}
	
	$form['formtransfer']['kodeuk']= array(
		'#type'         => 'select', 
		//'#title'        => 'Kecamatan',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk,
		'#attributes'	=> array('style' => 'margin-left: 20px;'),
	); 
	
	

	$musrenbang = l("<div class='boxp' >MUSRENBANGCAM</div>", 'apbd/kegiatancam', array('html'=> true));
	$renja= l("<div class='boxp'>RENJA SKPD</div>", 'apbd/kegiatanskpd', array('html'=>true));
	$proses = "<div class='boxproses' id='boxproses'><a href='#transfercamskpd' class='btn_blue' style='color: white;'>---Transfer---></a></div>";
	$document = "<div style='height: 50px; text-align:center;'>$musrenbang $proses $renja<div style='clear:both;'></div></div>";
	$form['formtransfer']['keterangan'] = array (
		'#type' => 'markup',
		'#value' => $document,
		'#weight' => 1,
	);
	return $form;
}

function pendapatan_exportexcel($tahun, $kodeuk) {
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
			->setCellValue('B' . $row ,'Kode SKPD')
            ->setCellValue('C' . $row ,'Nama SKPD')
            ->setCellValue('D' . $row ,'Kode Program')
			->setCellValue('E' . $row ,'Nama Program')
			->setCellValue('F' . $row ,'Kode Kegiatan')
			->setCellValue('G' . $row ,'Nama Kegiatan')
			->setCellValue('H' . $row ,'Sasaran')
			->setCellValue('I' . $row ,'Target')
			->setCellValue('J' . $row ,'Lokasi')
			->setCellValue('K' . $row ,'Anggaran')
			->setCellValue('L' . $row ,'Anggaran Sebelum')
			->setCellValue('M' . $row ,'Anggaran Sesudah')
			->setCellValue('N' . $row ,'Sumber Dana')
			->setCellValue('O' . $row ,'DAU')
			->setCellValue('P' . $row ,'Banprov')
			->setCellValue('Q' . $row ,'DAK');

//Open data							 
//$customwhere = sprintf(' and k.tahun=%s ', variable_get('apbdtahun', 0));
$customwhere = sprintf(' and k.tahun=%s ', $tahun);
if ($kodeuk!='00') {
	$customwhere .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);	
}	
$where = ' where true' . $customwhere;
	
$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro, concat(p.kodeu,p.np) as kodeproresmi, p.program, k.kegiatan,k.lokasi,k.sasaran,k.target, k.totalsebelum,k.totalsebelum2,k.total,k.targetsesudah,k.apbdkab,k.apbdprov, k.apbdnas,k.sumberdana, u.kodedinas, u.namasingkat, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi from {kegiatanskpd} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro) " . $where;
$result = db_query($sql);
while ($data = db_fetch_object($result)) {
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row, $data->tahun)
				->setCellValue('B' . $row, $data->kodedinas)
				->setCellValue('C' . $row, $data->namasingkat)
				->setCellValue('D' . $row, $data->kodeproresmi)
				->setCellValue('E' . $row, $data->program)
				->setCellValue('F' . $row, $data->koderesmi)
				->setCellValue('G' . $row, $data->kegiatan)
				->setCellValue('H' . $row, $data->sasaran)
				->setCellValue('I' . $row, $data->target)
				->setCellValue('J' . $row, $data->lokasi)
				->setCellValue('K' . $row, $data->total)
				->setCellValue('L' . $row, $data->totalsebelum)
				->setCellValue('M' . $row, $data->totalsebelum2)
				->setCellValue('N' . $row, $data->sumberdana)
				->setCellValue('O' . $row, $data->apbdkab)
				->setCellValue('P' . $row, $data->apbdprov)
				->setCellValue('Q' . $row, $data->apbdnas);
}
						

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Kegiatan RKPD');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = 'kegiatan_skpd_' . $tahun . '-' . $kodeuk . '.xlsx';
header('Content-Disposition: attachment;filename=' . $fname);
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}
?>
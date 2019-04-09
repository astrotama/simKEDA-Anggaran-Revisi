<?php
function desa_main($arg=NULL, $nama=NULL) {
    //$h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');
	drupal_set_title('Renstra SKPD');
	$qlike='';
	$limit = 25;
    if ($arg) {
		switch($arg) {
			case 'show':
				$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				break;
			case 'filter':
				$ntitle = 'Renstra SKPD';
				$nntitle ='';
				$tahun = arg(3);
				$kodeuk = arg(4);
				$kegiatan = arg(5);
				
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
				}

				if (strlen($kegiatan)>0) {
					$qlike .= sprintf(" and lower(kegiatan) like lower('%%%s%%') ", db_escape_string($kegiatan));
				}
			
				if (strlen($nntitle) > 0)
					$ntitle .= ', Filter: ' . substr($nntitle, 0 , strlen($nntitle)-2);
				drupal_set_title($ntitle);
				break;
			default:
				drupal_access_denied();
				break;
		}
	}
	//$output .= drupal_get_form('desa_transfer_form');
	$output .= drupal_get_form('desa_main_form');
	if (isSuperuser()) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => ucwords(strtolower('unit kerja')), 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => ucwords(strtolower('kegiatan')), 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => ucwords(strtolower('sasaran')), 'field'=> 'sasaran', 'valign'=>'top'),
			array('data' => ucwords(strtolower('target')), 'field'=> 'target', 'valign'=>'top'),
			array('data' => ucwords(strtolower('Jumlah')), 'field'=> 'total', 'valign'=>'top'),
			array('data' => '', 'width' => '100px', 'valign'=>'top'),
		);
	} else {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => ucwords(strtolower('kegiatan')), 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => ucwords(strtolower('sasaran')), 'field'=> 'sasaran', 'valign'=>'top'),
			array('data' => ucwords(strtolower('target')), 'field'=> 'target', 'valign'=>'top'),
			array('data' => ucwords(strtolower('Jumlah')), 'field'=> 'total', 'valign'=>'top'),
			array('data' => '', 'width' => '100px', 'valign'=>'top'),
		);
	}
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by koderesmi';
    }
	//FILTER TAHUN-----
	if (strlen($tahun)<=0) $tahun = '2014'; 
	$customwhere = sprintf(' and k.tahun=%s ', $tahun);
	if (!isSuperuser()) {
		$customwhere .= sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
	}	
    $where = ' where true' . $customwhere . $qlike ;

	$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.sifat,k.kegiatan,k.sasaran,k.target,k.total,u.namasingkat,concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi from {kegiatanrenstra} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro) " . $where;
	//$fsql = sprintf($sql, addslashes($nama));
	$fsql = $sql;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kegiatanrenstra} k" . $where;
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

			
			if (user_access('desa edit')) {
				$kegname = l($data->kegiatan, 'apbd/desa/edit/' . $data->kodekeg, array('html'=>TRUE)). "&nbsp;";
			} else {
				$kegname = $data->kegiatan;
			}	
			if (user_access('desa penghapusan'))
                $editlink =l('Hapus ', 'apbd/desa/delete/' . $data->kodekeg, array('html'=>TRUE));
			
            $no++;
			/*
			$desa = split(",", $data->lokasi);
			$desalink = "";
			for ($i=0; $i<count($desa); $i++) {
				if (strlen($desa[$i])>0)
					$desalink .= l($desa[$i], 'apbd/desa/showc/' . $desa[$i], array('html'=>true, 'attributes' => array('id' => $data->kodekeg . "_" . $desa[$i], 'name' => 'view_desa'))) . "&nbsp;";
			}
			*/
			
			if (isSuperuser()) { 
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sasaran, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->target, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sasaran, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->target, 'align' => 'left', 'valign'=>'top'),
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
	if (user_access('desa tambah')) {
		$btn .= l('Baru', 'apbd/desa/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	}
	//if (user_access('desa pencarian'))	{
	//	$btn .= l('Cari', 'apbd/desa/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	//}
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}


function desa_main_form() {
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');

	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Filter Data Renstra',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	$filter = arg(2);
	if (isset($filter) && ($filter=='filter')) {
		$tahun = arg(3);
		$kodeuk = arg(4);
	}

	$opttahun = array();     
	$opttahun['2014'] = '2014';
	$opttahun['2015'] = '2015';
	$opttahun['2016'] = '2016';
	$opttahun['2017'] = '2017';
	$opttahun['2018'] = '2018';

	$form['formdata']['tahun']= array(
		'#type'         => 'select', 
		'#title'        => 'Tahun', 
		'#options'	=> $opttahun,
		//'#description'  => 'tahun', 
		//'#maxlength'    => 4, 
		//'#size'         => 6, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#width'         => 20, 
		'#default_value'=> $tahun, 
		'#weight' => 1,
	);
	
  
	$pquery = "select kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by namauk" ;
	$pres = db_query($pquery);
	$dinas = array();        
	
	$dinas['00'] ='-SEMUA SKPD-';
	while ($data = db_fetch_object($pres)) {
		$dinas[$data->kodeuk] = $data->namasingkat;
	}
	$type='select';
	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		//drupal_set_message('user kec');
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
		'#weight' => 2,
	);
	
	$form['formdata']['kegiatanx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kegiatan', 
		//'#description'  => 'kegiatan', 
		//'#maxlength'    => 60, 
		'#size'         => 42, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kegiatan, 
		'#weight' => 3,
	);
	
	
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 4,
	);
	
	return $form;
}

function desa_main_form_submit($form, &$form_state) {
	$tahun = $form_state['values']['tahun'];
	$kodeuk= $form_state['values']['kodeuk'];
	$kegiatan= $form_state['values']['kegiatanx'];
	
	$uri = 'apbd/desa/filter/' .$tahun . '/' . $kodeuk . '/' . $kegiatan;
	drupal_goto($uri);
	
}
?>
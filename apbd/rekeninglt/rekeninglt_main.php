<?php

function rekeninglt_main($arg='normal', $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/rekeningbl.js');	
	switch($arg) {
		case 'show':
			$qlike = " and lower(kegiatan) like lower('%%%s%%')";
			$_SESSION['kodeu'] = '';
			$_SESSION['kodepro'] = '';
			break;
		case 'filter':
			$kodeu = arg(3);
			$kodepro = arg(4);
			$_SESSION['kodeu'] = '';
			$_SESSION['kodepro'] = '';
			if (strlen($kodeu)>0) {
				$_SESSION['kodeu'] = $kodeu;
				$qlike .= sprintf(" and kodeu='%s' ", db_escape_string($kodeu));
				if (strlen($kodepro)>0) {
					$qlike .= sprintf(" and kodepro='%s' ", db_escape_string($kodepro));
					$_SESSION['kodepro'] = $kodepro;
				}
			}
			break;
		default:
			$_SESSION['kodeu'] = '';
			$_SESSION['kodepro'] = '';
			break;
	}
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
		array('data' => ucwords(strtolower('kode')), 'field'=> 'kodeu', 'valign'=>'top'),
		array('data' => ucwords(strtolower('kegiatan')), 'field'=> 'kegiatan', 'valign'=>'top'),

		array('data' => '', 'width' => '110px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kegid';
    }

    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = ' ';
    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select kegid,u1,u2,np,nk,kegiatan,kodepro,kodeu from {kegiatanlt}' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 15;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kegiatanlt}" . $where;
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
			$editlink = "";
			//if (user_access('kegiatanlt edit'))
			//	$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/kegiatanlt/edit/' . $data->kegid, array('html'=>TRUE)) .'&nbsp;';
			//if (user_access('kegiatanlt penghapusan'))
         //       $editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/kegiatanlt/delete/' . $data->kegid, array('html'=>TRUE));
                
				if (user_access('kegiatanlt edit'))
					$namakeg = l($data->kegiatan, 'apbd/kegiatanlt/edit/' . $data->kegid, array('html'=>TRUE)). "&nbsp;";
				else 
					$namakeg = $data->kegiatan;
					
				if (user_access('kegiatanlt penghapusan'))
                $editlink =l('Hapus ', 'apbd/kegiatanlt/delete/' . $data->kegid, array('html'=>TRUE));
            
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right'),
                
				array('data' => $data->kodeu . $data->np  . $data->nk , 'align' => 'left', 'valign'=>'top'),
				array('data' => $namakeg, 'align' => 'left', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right'),
            );
        }
    } else { 
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$btn = "";
	if (user_access('kegiatanlt tambah')) {
		$btn .= l('Baru', 'apbd/kegiatanlt/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	}
	if (user_access('kegiatanlt pencarian'))	{
		$btn .= l('Cari', 'apbd/kegiatanlt/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	}
    //$output .=  "<div id='fl_filter'>" . drupal_get_form ('kegiatanlt_filter_form') . "</div>" . $btn . theme_box('', theme_table($header, $rows)) . $btn;

//	if (user_access('kegiatanlt tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanlt/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanlt pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanlt/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;	
}

function rekeninglt_browse($arg='normal', $nama=NULL) {
	switch($arg){
		case 'show':
			$qlike = " and lower(uraian) like lower('%%%s%%')";
			break;
			
		case 'kodeu':
			$qlike = " and left(k.kodeo,3)='%s'";
			break;
			
		case 'kodepro':
			$qlike = " and k.kodeo='%s'";
			break;
	}
	
    $header = array (
		array('data' => 'Kode', 'field'=> 'kodero', 'valign'=>'top', 'width' => '80px'),
		array('data' => 'Uraianxxx', 'field'=> 'uraian', 'valign'=>'top'),
		array('data' => '', 'width' => '110px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by k.kodero';
    }


	$kodek='52';
	$customwhere = sprintf(' and left(k.kodeo,2)=\'%s\' ', $kodek);
    $where = ' where true' . $customwhere . $qlike ;
	
    $sql = 'select k.kodero, k.uraian from {rincianobyek} k ' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 13;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {rincianobyek} k" . $where;
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
			$attrlink = "kegiatan='" . $data->uraian .
						"' nk='" . $data->kodero . "'";
			$editlink = "<a href='#' class='btn_blue' " . $attrlink . " style='color:white;'>Pilih</a>";
            $no++;
            $rows[] = array (
				array('data' => $data->kodero, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right'),
            );
        } 
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    } 
    $output .= theme_box('', theme_table($header, $rows));
    $output .= theme ('pager', NULL, $limit, 0);
	
	$pquery = 'select kodej, uraian from jenis where kodek=\'%s\' order by kodej';
	
	$pres = db_query(db_rewrite_sql($pquery), array($kodek));
	$option = "<option value=''>- Pilih Jenis Rekening -</option>";
	while ($prow = db_fetch_object($pres)) {
		$option .= "<option value='" . $prow->kodej . "'>" .  $prow->kodej . ' - ' . $prow->uraian . "</option>";
	}
	
	$rr[] = array (
		array('data' => 'Jenis', 'width' => '150px'),
		//array('data' => "<select id='ur' style='width: 500px;'>" . $option. "</select>", 'width' => '200px'),
		array('data' => "<select id='ur' style='width: 500px;'>" . $option. "</select>"),
		//array('data' => 'Rekening', 'width' => '150px', 'align'=>'right'),
		//array('data' => "<input type='text' id='i_kg' value='' style='width: 150px;'/>", 'width'=>'150px'),
		array('data' => "<a href='#batal' class='btn_blue' style='color: #ffffff;'>Tutup</a>", 'align' => 'right'),
		//array('data' => "<a href='#sikg' class='btn_blue'>Cari</a>")
	);
	$rr[] = array ( 
		array('data' => 'Obyek', 'width' => '150px'),
		array('data' => "<select id='pr' style='width: 500px;'></select>", 'colspan'=>2),
		//array('data' => "<select id='pr' style='width: 500px;'></select>"),
		//array('data' => 'Rekening', 'width' => '150px', 'align'=>'right'),
		//array('data' => "<input type='text' id='i_kg' value='' style='width: 150px;'/>", 'width'=>'150px'),
		//array('data' => '&nbsp;', 'colspan'=>3)
	);
	
	
	//echo $arg;
	if (!($arg == 'kodepro')) {
	//if (!(($arg == 'kodepro')||($arg=='show'))) {
		$output ="<div id='pvtab'>" . $output . "</tab>";
		echo theme_box('', theme_table(array(), $rr));
	}
	
	echo $output;
}
?>
<?php
function subkegiatanppa_main($arg=NULL, $nama=NULL) {
    if ($arg)
		$qlike = sprintf(" and kodekeg='%s'", db_escape_string($arg));    
	else
		drupal_access_denied();
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        //array('data' => ucwords(strtolower('kodekeg')), 'field'=> 'kodekeg', 'valign'=>'top'),
		array('data' => ucwords(strtolower('uraian')), 'field'=> 'uraian', 'valign'=>'top'),
		array('data' => ucwords(strtolower('lokasi')), 'field'=> 'lokasi', 'valign'=>'top'),
		array('data' => ucwords(strtolower('Jumlah')), 'field'=> 'total', 'valign'=>'top', 'width'=>'90px'),
		array('data' => ucwords(strtolower('Tahun Lalu')), 'field'=> 'totalsebelum', 'valign'=>'top', 'width'=>'90px'),
		array('data' => ucwords(strtolower('Tahun Depan')), 'field'=> 'totalsesudah', 'valign'=>'top', 'width'=>'90px'),

		array('data' => 'op', 'width' => '110px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by id';
    }

    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = ' ';
    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select id,kodekeg,uraian,lokasi,total,totalsebelum,totalsesudah from {kegiatanppasub}' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 13;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kegiatanppasub}" . $where;
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
			if (user_access('kegiatanppa edit'))
				$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/kegiatanppa/subkegiatan/edit/' . $data->kodekeg . "/" . $data->id, array('html'=>TRUE)) . '&nbsp;';
			if (user_access('kegiatanppa penghapusan'))
                $editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/kegiatanppa/subkegiatan/delete/' . $data->kodekeg . "/" . $data->id, array('html'=>TRUE));
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				//array('data' => $data->id, 'align' => 'left', 'valign'=>'top'),
				//array('data' => $data->kodekeg, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->lokasi, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data->totalsebelum), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data->totalsesudah), 'align' => 'right', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$pquery = sprintf("select kegiatan, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi  from {kegiatanppa} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro)  where kodekeg='%s'", db_escape_string($arg));
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ptitle = $data->koderesmi . ' - ' . $data->kegiatan;
	 
	$ptitle =l($ptitle, 'apbd/kegiatanppa/anggaran/' . $arg, array('html'=>true));
    $output .= theme_box($ptitle, theme_table($header, $rows));
	//if (user_access('kegiatanppa tambah'))
	if (user_access('kegiatanppa edit')) 
		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanppa/subkegiatan/edit/' . db_escape_string($arg) , array('html'=>TRUE)) ;
		//$output .= l('Baru', 'apbd/kegiatanppa/subkegiatan/edit/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
//	if (user_access('kegiatanppa pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanppa/subkegiatan/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

?>
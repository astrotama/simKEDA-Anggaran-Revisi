<?php
function subkegiatanskpd_main($arg=NULL, $nama=NULL) {
    if ($arg)
		$qlike = sprintf(" and kodekeg='%s'", db_escape_string($arg));    
	else
		drupal_access_denied();
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        //array('data' => ucwords(strtolower('kodekeg')), 'field'=> 'kodekeg', 'valign'=>'top'),
		array('data' => ucwords(strtolower('Kode')), 'field'=> 'kodero', 'valign'=>'top'),
		array('data' => ucwords(strtolower('Uraian')), 'field'=> 'uraian', 'valign'=>'top'),
		array('data' => ucwords(strtolower('Plafon')), 'field'=> 'plafon', 'valign'=>'top', 'width'=>'90px'),
		array('data' => ucwords(strtolower('Jumlah')), 'field'=> 'jumlah', 'valign'=>'top', 'width'=>'90px'),
		array('data' => 'op', 'width' => '110px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by id';
    }

    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = ' ';
    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select id,kodekeg,kodero,uraian,jumlahsebelum,jumlah,jumlahsesudah from {kegiatanskpdsub}' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 13;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {kegiatanskpdsub}" . $where;
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
			if (user_access('kegiatanskpd edit'))
				$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/kegiatanskpd/subkegiatan/edit/' . $data->kodekeg . "/" . $data->id, array('html'=>TRUE)) . '&nbsp;';
			if (user_access('kegiatanskpd penghapusan'))
                $editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/kegiatanskpd/subkegiatan/delete/' . $data->kodekeg . "/" . $data->id, array('html'=>TRUE));
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				//array('data' => $data->id, 'align' => 'left', 'valign'=>'top'),
				//array('data' => $data->kodekeg, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->kodero, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data->jumlah), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data->jumlahsebelum), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data->jumlahsesudah), 'align' => 'right', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$pquery = sprintf("select kegiatan, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi  from {kegiatanskpd} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro)  where kodekeg='%s'", db_escape_string($arg));
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ptitle = $data->kegiatan;
	
	$ptitle =l($ptitle, 'apbd/kegiatanskpd/edit/' . $arg, array('html'=>true));
    $output .= theme_box($ptitle, theme_table($header, $rows));
	if (user_access('kegiatanskpd tambah'))
		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanskpd/subkegiatan/edit/' . db_escape_string($arg) , array('html'=>TRUE)) ;
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpd/subkegiatan/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

?>
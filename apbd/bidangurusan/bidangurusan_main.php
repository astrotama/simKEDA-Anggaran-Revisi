<?php
function bidangurusan_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        array('data' => ucwords(strtolower('Tahun')), 'field'=> 'tahun', 'valign'=>'top', 'width' => '15px'),
        array('data' => ucwords(strtolower('Bidang')), 'field'=> 'bidang', 'valign'=>'top', 'width' => '150px'),
		array('data' => ucwords(strtolower('Urusan')), 'field'=> 'urusansingkat', 'valign'=>'top'),
		array('data' => 'op', 'width' => '110px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kodebid';
    }
	$tahun = variable_get('apbdtahun', 0);
    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = sprintf(' and b.tahun=\'%s\'', $tahun);
    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select b.tahun, b.kodebid, b.kodeu, bg.bidang, u.urusansingkat from {bidangurusan} b left join {bidang} bg on (b.kodebid=bg.kodebid) left join {urusan} u on (b.kodeu=u.kodeu) ' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 13;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {bidangurusan} b" . $where;
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
			if (user_access('bidangurusan edit'))
				$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/bidangurusan/edit/' . $data->kodebid . "/" . $data->kodeu, array('html'=>TRUE)) . '&nbsp;';
			if (user_access('bidangurusan penghapusan'))
                $editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/bidangurusan/delete/' . $data->kodebid . "/" . $data->kodeu, array('html'=>TRUE));
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
				array('data' => $data->tahun, 'align' => 'left', 'valign'=>'top'),                
				array('data' => $data->bidang, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->urusansingkat, 'align' => 'left', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	if (user_access('bidangurusan tambah')) {
		$btn .= l('Tambah Data Baru', 'apbd/bidangurusan/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	}
	if (user_access('bidangurusan pencarian'))	{
		$btn .= l('Cari Data', 'apbd/bidangurusan/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	}
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

?>
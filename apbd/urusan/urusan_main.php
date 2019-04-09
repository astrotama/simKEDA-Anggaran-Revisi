<?php
function urusan_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
    if ($arg)
        if ($arg=='show') {
           $qlike = " and lower(u.urusansingkat) like lower('%%%s%%')";    
        }
        else
            drupal_access_denied();
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        array('data' => ucwords(strtolower('kode')), 'field'=> 'kodeu', 'valign'=>'top', 'width'=>'8px'),
		//array('data' => ucwords(strtolower('sifat')), 'field'=> 'sifat', 'valign'=>'top'),
		array('data' => ucwords(strtolower('urusan')), 'field'=> 'urusan', 'valign'=>'top'),
		//array('data' => ucwords(strtolower('urusansingkat')), 'field'=> 'urusansingkat', 'valign'=>'top'),
		array('data' => ucwords(strtolower('Bidang')), 'field'=> 'bidang', 'valign'=>'top'),

		array('data' => 'op', 'width' => '110px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by u.urusansingkat';
    }

    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = ' ';
    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select u.kodeu, u.sifat, u.urusan, u.urusansingkat, b.bidang from {urusan} u left join {bidang} b on (u.kodebid=b.kodebid) ' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 13;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {urusan} u" . $where;
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
			if (user_access('urusan edit'))
				$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/urusan/edit/' . $data->kodeu, array('html'=>TRUE)) . '&nbsp;';
			if (user_access('urusan penghapusan'))
                $editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/urusan/delete/' . $data->kodeu, array('html'=>TRUE));
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				array('data' => $data->kodeu, 'align' => 'left', 'valign'=>'top'),
				//array('data' => $data->sifat, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->urusan, 'align' => 'left', 'valign'=>'top'),
				//array('data' => $data->urusansingkat, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->bidang, 'align' => 'left', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$btn = "";
	if (user_access('urusan tambah')) {
		$btn .= l('Tambah Data Baru', 'apbd/urusan/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	}
	if (user_access('urusan pencarian'))	{
		$btn .= l('Cari Data', 'apbd/urusan/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	}
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

?>
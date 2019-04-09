<?php
function download_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
    if ($arg)
        if ($arg=='show') {
           $qlike = " and lower(topik) like lower('%%%s%%')";    
        }
        else
            drupal_access_denied();
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        array('data' => 'Topik', 'field'=> 'topik', 'width' => '200px', 'valign'=>'top'),
		array('data' => 'Uraian', 'field'=> 'uraian', 'width' => '300px', 'valign'=>'top'),
		array('data' => '', 'width' => '100px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by tanggal desc';
    }

	$customwhere = ' ';
    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select nomor,topik,uraian,url,url1 from download ' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 15;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {download} " . $where;
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
			$editlink =l('Download', $data->url, array('html'=>TRUE)) . '&nbsp;';
			if ($data->url1 =='')
				$editlink .= 'Mirror' . '&nbsp;';
			else
				$editlink .=l ('Mirror', $data->url1, array('html'=>TRUE)) . '&nbsp;';
			if (user_access('urusan edit'))
				//$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/urusan/edit/' . $data->kodeu, array('html'=>TRUE)) . '&nbsp;';
				$topik = l($data->topik, 'apbd/download/edit/' . $data->nomor, array('html'=>TRUE)) . '&nbsp;';

			else
				$topik = $data->topik;
				
			if (user_access('urusan penghapusan'))
                //$editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/urusan/delete/' . $data->kodeu, array('html'=>TRUE));
                $editlink .=l('Hapus', 'apbd/download/delete/' . $data->nomor, array('html'=>TRUE));
			
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				array('data' => $topik, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
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
		$btn .= l('Baru', 'apbd/download/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	}
	//if (user_access('urusan pencarian'))	{
	//	$btn .= l('Cari', 'apbd/download/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	//}
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

?>
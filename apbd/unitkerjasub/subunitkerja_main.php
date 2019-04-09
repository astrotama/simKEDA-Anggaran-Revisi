<?php
function subunitkerja_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
    if ($arg)
        if ($arg=='show') {
           $qlike = " and lower(namasuk) like lower('%%%s%%')";    
        }
        else
            drupal_access_denied();
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
		array('data' => t('SKPD'), 'field'=> 'kodeuk', 'valign'=>'top', 'width'=>'8px'),
        array('data' => t('Sub SKPD'), 'field'=> 'kodesuk', 'valign'=>'top', 'width'=>'100px'),
		array('data' => t('Nama Sub SKPD'), 'field'=> 'namasuk', 'valign'=>'top'),

		array('data' => 'op', 'width' => '110px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kodesuk';
    }

    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = ' ';
    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select kodesuk,kodeuk,namasuk from {subunitkerja}' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 13;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {subunitkerja}" . $where;
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
			if (user_access('subunitkerja edit'))
				$editlink .= l("<img src='/files/button-edit.png' title='Edit data'>", 'apbd/subunitkerja/edit/' . $data->kodesuk, array('html'=>TRUE)) . '&nbsp;';
			if (user_access('subunitkerja penghapusan'))
                $editlink .=l("<img src='/files/button-delete.png' title='Hapus data'>", 'apbd/subunitkerja/delete/' . $data->kodesuk, array('html'=>TRUE));
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				array('data' => $data->kodeuk, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->kodesuk, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->namasuk, 'align' => 'left', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$btn = "";
	if (user_access('subunitkerja tambah')) {
		$btn .= l('Tambah Data Baru', 'apbd/subunitkerja/edit/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	}
	if (user_access('subunitkerja pencarian'))	{
		$btn .= l('Cari Data', 'apbd/subunitkerja/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	}
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

?>
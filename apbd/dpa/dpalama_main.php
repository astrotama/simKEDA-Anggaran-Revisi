<?php
function dpa_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
    if ($arg)
        if ($arg=='show') {
           $qlike = " and lower(u.namasingkat) like lower('%%%s%%')";    
        }
        else
            drupal_access_denied();
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        array('data' => 'SKPD', 'field'=> 'namasingkat', 'width' => '200px', 'valign'=>'top'),
        array('data' => 'Keterangan', 'field'=> 'namasingkat', 'width' => '250px', 'valign'=>'top'),
		array('data' => 'Download', 'width' => '200px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by u.namasingkat';
    }

	if (!isSuperuser())
		$customwhere = sprintf(' and u.kodeuk=\'%s\' ', apbd_getuseruk());
    $where = ' where true ' . $customwhere . $qlike ;

    $sql = 'select u.namasingkat, d.uraian, d.b_url, d.b_url1, d.p_url, d.p_url1, d.s_url, d.b_inaktif, d.p_inaktif from {unitkerja} u left join {dpa} d on u.kodeuk=d.kodeuk ' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 15;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {unitkerja} u " . $where;
    $fcountsql = sprintf($countsql, addslashes($nama));
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
    
    $no=0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    } 
	$prefix = 'http://103.229.72.238/pdf/2015/APBD/DPPA/';
	$prefixp = 'http://103.229.72.238/pdf/2015/APBD/DPPA/P/';
	$prefixs = 'http://103.229.72.238/pdf/2015/APBD/DPPA/S/';
    if ($result) {
        while ($data = db_fetch_object($result)) {
			
			
			if ($data->b_inaktif) {
				$ldownload = 'Belanja' . '&nbsp;';
			} else {
				//if (file_check_location($prefix, $prefix . $data->b_url))
				//	$d_belanja = 'Download' . '&nbsp;';
				//else
					$ldownload =l('Belanja', $prefix . $data->b_url, array('html'=>TRUE)) . '&nbsp;';
				
				//if ($data->b_url1 =='')
				//	$d_belanja .= 'Mirror' . '&nbsp;';
				//else
				//	$d_belanja .=l ('Mirror', $data->b_url1, array('html'=>TRUE)) . '&nbsp;';
			}

			if ($data->p_inaktif) {
				$ldownload .= 'Pendapatan'  . '&nbsp;';
			} else {
				$ldownload .=l('Pendapatan', $prefixp . $data->p_url, array('html'=>TRUE)) . '&nbsp;';
				//if ($data->p_url1 =='')
				//	$d_pendapatan .= 'Mirror' . '&nbsp;';
				//else
				//	$d_pendapatan .=l ('Mirror', $data->p_url1, array('html'=>TRUE)) . '&nbsp;';
			}
			$ldownload .=l('Sampul', $prefixs . $data->s_url, array('html'=>TRUE)) . '&nbsp;';
			
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),         
				array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
                array('data' => $ldownload, 'align' => 'left', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$btn = "";
	//if (user_access('urusan pencarian'))	{
	//	$btn .= l('Cari', 'apbd/download/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	//}
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

?>
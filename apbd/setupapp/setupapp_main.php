<?php
function setupapp_main($arg=NULL, $nama=NULL) {
    drupal_add_css('files/css/kegiatancam.css');
	switch ($arg) {
        case 'activate':
            $tahun = $nama;
            $query = sprintf("select tahun, uraian from {setupapp} where tahun=%s", $tahun);
            $result = db_query($query);
            if ($data = db_fetch_object($result)) {
                $wilayah = $data->wilayah;
				$uraian = $data->uraian;
                variable_set('apbdtahun', $tahun);
				variable_set('apbdkegiatan', $uraian);
            }
            break;
    }
	drupal_set_title('Setup Penyusunan APBD');
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        array('data' => 'Tahun', 'field'=> 'tahun', 'valign'=>'top', 'width'=>'17px'),
		array('data' => 'Uraian', 'field'=> 'wilayah', 'width' => '500px','valign'=>'top'),
		array('data' => '', 'width' => '180px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by tahun';
    }

    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = ' ';
    $where = ' where true' . $customwhere ;

    $sql = 'select tahun, wilayah, uraian from {setupapp}' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 13;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {setupapp}" . $where;
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
        $tahunactive = variable_get('apbdtahun', 0);
        $wilayahactive = variable_get('apbdwilayah', '');
		$uraian = variable_get('apbdkegiatan', 0);
        while ($data = db_fetch_object($result)) {
            $activestring ="";
            if ($data->tahun == $tahunactive)
                $activestring= "&nbsp;&nbsp;<b>(Aktif)</b>";
			$editlink = '';
			if (user_access('setupapp activate'))
				//$editlink .= l("<img src='/files/button-default.png' title='Aktifkan'>", 'apbd/setupapp/activate/' . $data->tahun, array('html'=>TRUE)) . '&nbsp;';
                $editlink .= l('|Aktifkan', 'apbd/setupapp/activate/' . $data->tahun, array('html'=>TRUE)) . '&nbsp;';
			if (user_access('setupapp edit'))
				$editlink .= l('|Edit', 'apbd/setupapp/edit/' . $data->tahun, array('html'=>TRUE)) . '&nbsp;';
			if (user_access('setupapp penghapusan')) {
				if ($data->tahun == $tahunactive)
					$editlink .= '|Hapus';
				else
					$editlink .= l('|Hapus', 'apbd/setupapp/delete/' . $data->tahun, array('html'=>TRUE));
			}
                


            $no++; 
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                array('data' => $data->tahun . $activestring, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
    $output .= theme_box('', theme_table($header, $rows));

	if (user_access('setupapp tambah'))
		//$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/setupapp/edit/' , array('html'=>TRUE)) ;
        $output .= l('Baru', 'apbd/setupapp/edit/' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";

//	if (user_access('setupapp pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/setupapp/find/' , array('html'=>TRUE)) ;

    $output .= theme ('pager', NULL, $limit, 0);
    
    $temp = "<br/><br/><div style='border: 1px solid black; float: left;width:600px;'>" .
            "<div style='background-color: white;padding: 3px 8px;font-weight: bold;'>Konfigurasi Active</div>" .
            "<div style='padding: 3px 8px;font-weight: bold;float:left;width: 80px;'>Tahun</div>" .
            "<div style='padding: 3px 8px;font-weight: bold;float:left;width: 10px;'>:</div>" .
            "<div style='padding: 3px 8px;font-weight: bold;float:left;'>" . $tahunactive . "</div>" .
            "<div style='clear:both;'></div>" .
            "<div style='padding: 3px 8px;font-weight: bold;float:left;width: 80px;'>Wilayah</div>" .
            "<div style='padding: 3px 8px;font-weight: bold;float:left;width: 10px;'>:</div>" .
            "<div style='padding: 3px 8px;font-weight: bold;float:left;'>". $wilayahactive . "</div>" .
            "<div style='clear:both;'></div>" .
            "<div style='padding: 3px 8px;font-weight: bold;float:left;width: 80px;'>Prosedur Isi Kegiatan</div>" .
            "<div style='padding: 3px 8px;font-weight: bold;float:left;width: 10px;'>:</div>" .
            "<div style='padding: 3px 8px;font-weight: bold;float:left;'>". $uraian . "</div>" .
            "<div style='clear:both;'></div>" .
            "</div><br/><br/>";
    //$output = 'Tahun :' . $tahunactive . $output;
    //$output = $temp . $output;
    return $output;
}



?>
<?php
function programsasaran_main($arg=NULL, $nama=NULL) {

    drupal_add_css('files/css/kegiatancam.css');

    if ($arg) {
		$kodepro = substr($arg, 0, 5);
		$tahun = substr($arg, -4);
	}
	else {
		
		drupal_access_denied();
	}
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        array('data' => 'Sasaran', 'valign'=>'top'),
		array('data' => 'Target Kinerja', 'valign'=>'top'),
		array('data' => 'Target Dana', 'valign'=>'top'),
		array('data' => '', 'width' => '90px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by nomor';
    }

    $qlike = sprintf(" and kodepro='%s'", db_escape_string($kodepro));    
    $customwhere = sprintf(" and tahun=%s", db_escape_string($tahun));    
    $where = ' where true' . $customwhere . $qlike ;

	
	
    $sql = 'select kodepro,tahun,ktarget,satuan,rtarget,nomor,sasaran from {programsasaran}' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 15;

    $countsql = "select count(*) as cnt from {programsasaran}" . $where;
    $fcountsql = sprintf($countsql, addslashes($nama));
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
    
    $no=0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    }

    $ada = 0;
    if ($result) {
        while ($data = db_fetch_object($result)) {
            $ada = 1;
			$sasaran = l($data->sasaran, 'apbd/programsasaran/edit/' . $data->kodepro . '/' . $data->tahun  . '/' . $data->nomor, array('html'=>TRUE));
			$editlink = l('Hapus', 'apbd/programsasaran/delete/' . $data->kodepro . '/' . $data->tahun  . '/' . $data->nomor, array('html'=>TRUE));

            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                array('data' => $sasaran, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->ktarget  . ' ' . $data->satuan, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data->rtarget), 'align' => 'right', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
        //$rows[] = array ( 
        //    array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        //);

        $rows[] = array (
            array('data' => '', 'align' => 'right', 'valign'=>'top'),
            array('data' => 'Data kosong', 'align' => 'left', 'valign'=>'top'),
            array('data' => 'Klik tombol Baru untuk menambah sasaran/target. Klik tombol Program untuk kembaki ke daftar program. Klik tombol Tahun yang lain untuk mengisi sasaran/target pada tahun tersebut', 'align' => 'left', 'valign'=>'top'),
            array('data' => '', 'align' => 'right', 'valign'=>'top'),
            array('data' => '', 'align' => 'right', 'valign'=>'top'),
        );

    }

    if ($ada==0) {
        $rows[] = array (
            array('data' => '', 'align' => 'right', 'valign'=>'top'),
            array('data' => 'Data kosong', 'align' => 'left', 'valign'=>'top'),
            array('data' => 'Klik tombol Baru untuk menambah sasaran/target. Klik tombol Program untuk kembaki ke daftar program. Klik tombol Tahun yang lain untuk mengisi sasaran/target pada tahun tersebut', 'align' => 'left', 'valign'=>'top'),
            array('data' => '', 'align' => 'right', 'valign'=>'top'),
            array('data' => '', 'align' => 'right', 'valign'=>'top'),
        );

    }

	$pquery = sprintf("select program from {program} where kodepro='%s'", db_escape_string($kodepro));
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ptitle = $data->program . ' Tahun ' . $tahun;
	
    $output .= theme_box($ptitle, theme_table($header, $rows));
	
    //$btn .= l('Cari', 'apbd/program/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));

	$output .= l('Baru', 'apbd/programsasaran/edit/' . $kodepro . '/' . $tahun . '/n' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp" ;
    $output .= l('Program', 'apbd/program', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')))  . "&nbsp" ;

    //$editlink = l('2015|', 'apbd/programsasaran/' . $data->kodepro . '2015'  , array('html'=>TRUE));
    $x = 2015; 
    while($x < $tahun) {
        $output .= l($x, 'apbd/programsasaran/' . $kodepro . $x, array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')))  . "&nbsp" ;
        $x++;
    }     
    $x = $tahun+1; 
    while($x <= 2019) {
        $output .= l($x,  'apbd/programsasaran/' . $kodepro . $x, array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')))  . "&nbsp" ;
        $x++;
    }     

    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

?>

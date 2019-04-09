<?php
function downloadr2_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');

	$strrevisi = '';
	$periode = '';
    if ($arg) {
		$strrevisi = arg(2);
		$periode = $strrevisi+1;
	}
	
	if ($strrevisi=='') {
		$strrevisi = '3';
		$periode = '4';
	}
	//drupal_set_message(arg(2));
	
	drupal_add_js('foo.js');
    $header = array (
        array('data' => 'No','width' => '10px', 'valign'=>'top'),
        array('data' => 'SKPD', 'valign'=>'top'),
		array('data' => 'File', 'width' => '300px', 'valign'=>'top'),
		array('data' => '', 'align'=>'right','valign'=>'top'),
		array('data' => '', 'align'=>'right','valign'=>'top'),
		//array('data' => 'OK ', 'width' => '100px', 'align'=>'center','valign'=>'top'),
    );

	
	drupal_set_title('Download DPPA-SKPD Revisi #' . $strrevisi );
	$customwhere = ' ';
    $where = '';//' where true' . $customwhere . $qlike ;

	//$fsql = "select distinct uk.kodedinas, uk.kodeuk, uk.namasingkat, uk.namauk from {unitkerja} uk inner join {kegiatanperubahan" . $strrevisi . "} kp on (uk.kodeuk=kp.kodeuk) where uk.aktif=1 and kp.periode=" . $periode . " and kp.inaktif=0 order by kodedinas" ;
	
	if ($strrevisi == variable_get('apbdrevisi', 0)) 
		$strtable = '';
	else
		$strtable = $strrevisi;
	
	$fsql = "select uk.kodedinas, uk.kodeuk, uk.namasingkat, uk.namauk from {unitkerja} uk where uk.kodeuk in (select kodeuk from {kegiatanperubahan" . $strtable . "} kp where kp.periode=" . $periode . " and kp.inaktif=0) order by uk.kodedinas" ;
	
	//drupal_set_message($fsql);
    $result = db_query($fsql);
    
    $no=0;
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$no++;
			$editlink =l('Download','files/dppar' . $strrevisi . '/apbd_'. $data->kodeuk . '20162_2.zip', array('html'=>TRUE)) . '&nbsp;';
			
			//$editlink2 ='<input type="checkbox" name="'.$data->file.'" value="ok" checked></input>';
			if (substr($data->namasingkat,0,4)=='PKM.') 
				$editlinkblud =l('Download BLUD','files/dppar3/blud/apbd_blud'. $data->kodeuk . '20162_2.zip', array('html'=>TRUE)) . '&nbsp;';
			else
				$editlinkblud = ''. '&nbsp;';
				
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				array('data' => $data->kodedinas . ' - ' . $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
				array('data' => 'apbd_'. $data->kodeuk . '20162_2.zip', 'align' => 'left', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'center', 'valign'=>'top'),
				array('data' => $editlinkblud, 'align' => 'center', 'valign'=>'top'),
				//array('data' => $editlink2, 'align' => 'center', 'valign'=>'top'),
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
	//$output .= drupal_get_form('downloadr2_form');
    return $output;
	
	
}

/*
function downloadr2_form() {
	$form['formtransfer'] = array (
		'#type' => 'fieldset',
		
		
	);
	$form['formtransfer']['simpan']= array(
		'#type'         => 'submit', 
		'#value'		=> 'Simpan',
		//'#attributes'	=> array('style' => 'margin-left: 20px;'),
	); 
	
	
	return $form;
}
*/

?>
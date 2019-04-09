<?php
    
function setupapp_edit_form(){
	drupal_set_title('Konfigurasi Aplikasi');
    drupal_add_css('files/css/kegiatancam.css');		

    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> '',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,        
    );
    $tahun = arg(3);
	$revisi = 0;
	$uraian = variable_get("apbdkegiatan", 0);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($tahun))
    {
        if (!user_access('setupapp edit'))
            drupal_access_denied();
			
        $sql = 'select tahun, revisi, uraian, tglbatasrka, tglbatasdpa, tglbatasrevisi, perdano, perdatgl, perbupno, perbuptgl,perdanop, perdatglp, perbupnop, perbuptglp, dpatgl, budnama, budnip, budjabatan, setdanama, setdanip, setdajabatan, dpabtlformat, dpablformat, dpapenformat, dpappkdpformat, dpappkdbformat, 
		dpatgl1, dpabtlformat1, dpablformat1, dpapenformat1, dpappkdpformat1, dpappkdbformat1,
		dpatgl2, dpabtlformat2, dpablformat2, dpapenformat2, dpappkdpformat2, dpappkdbformat2,
		dpatgl3, dpabtlformat3, dpablformat3, dpapenformat3, dpappkdpformat3, dpappkdbformat3,
		dpatgl4, dpabtlformat4, dpablformat4, dpapenformat4, dpappkdpformat4, dpappkdbformat4,
		dpatgl5, dpabtlformat5, dpablformat5, dpapenformat5, dpappkdpformat5, dpappkdbformat5
		from {setupapp} where tahun=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($tahun));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                $tahun = $data->tahun;
				$revisi = $data->revisi;
                
                $tglbatasrka = strtotime($data->tglbatasrka);
                $tglbatasdpa = strtotime($data->tglbatasdpa); 
                $tglbatasrevisi = strtotime($data->tglbatasrevisi); 

				$uraian = $data->uraian;
				
				$perdano = $data->perdano;
				$perdatgl = $data->perdatgl;
				
				$perbupno = $data->perbupno;
				$perbuptgl = $data->perbuptgl;
				
				$perdanop = $data->perdanop;
				$perdatglp = $data->perdatglp;
				$perbupnop = $data->perbupnop;
				$perbuptglp = $data->perbuptglp;
				
				$dpatgl = $data->dpatgl;
				
				$budnama = $data->budnama;
				$budnip = $data->budnip; 
				$budjabatan = $data->budjabatan;
				$setdanama = $data->setdanama;
				$setdanip = $data->setdanip;
				$setdajabatan = $data->setdajabatan;
				
				$setdajabatan = $data->setdajabatan;
				
				$dpabtlformat = $data->dpabtlformat;
				$dpablformat = $data->dpablformat;
				$dpapenformat = $data->dpapenformat;
				$dpappkdpformat = $data->dpappkdpformat;
				$dpappkdbformat = $data->dpappkdbformat;
				
				//1
				$dpatgl1 = $data->dpatgl1;
				$dpabtlformat1 = $data->dpabtlformat1;
				$dpablformat1 = $data->dpablformat1;
				$dpapenformat1 = $data->dpapenformat1;
				$dpappkdpformat1 = $data->dpappkdpformat1;
				$dpappkdbformat1 = $data->dpappkdbformat1;
				
				//2
				$dpatgl2 = $data->dpatgl2;
				$dpabtlformat2 = $data->dpabtlformat2;
				$dpablformat2 = $data->dpablformat2;
				$dpapenformat2 = $data->dpapenformat2;
				$dpappkdpformat2 = $data->dpappkdpformat2;
				$dpappkdbformat2 = $data->dpappkdbformat2;

				//3
				$dpatgl3 = $data->dpatgl3;
				$dpabtlformat3 = $data->dpabtlformat3;
				$dpablformat3 = $data->dpablformat3;
				$dpapenformat3 = $data->dpapenformat3;
				$dpappkdpformat3 = $data->dpappkdpformat3;
				$dpappkdbformat3 = $data->dpappkdbformat3;

				//4
				$dpatgl4 = $data->dpatgl4;
				$dpabtlformat4 = $data->dpabtlformat4;
				$dpablformat4 = $data->dpablformat4;
				$dpapenformat4 = $data->dpapenformat4;
				$dpappkdpformat4 = $data->dpappkdpformat4;
				$dpappkdbformat4 = $data->dpappkdbformat4;

				//5
				$dpatgl5 = $data->dpatgl5;
				$dpabtlformat5 = $data->dpabtlformat5;
				$dpablformat5 = $data->dpablformat5;
				$dpapenformat5 = $data->dpapenformat5;
				$dpappkdpformat5 = $data->dpappkdpformat5;
				$dpappkdbformat5 = $data->dpappkdbformat5;
				
                $disabled =TRUE;
			} else {
				$tahun = '';
			}
        } else {
			$tahun = '';

		}
    } else {
		if (!user_access('setupapp tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Tahun Anggaran';
		$tahun = '';

        $tglbatasrka = '1420045200';
        $tglbatasdpa = $tglbatasrka; 
        $tglbatasrevisi = $tglbatasrka; 

	}
    
	//drupal_set_message($tglbatasrka);
	$form['formdata']['tahun']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tahun', 
		//'#description'  => 'tahun', 
		'#maxlength'    => 4, 
		'#size'         => 6, 
		'#attributes'	=> array('style' => 'text-align: right'), 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#weight'     => -1, 
		'#default_value'=> $tahun, 
	); 
	$form['formdata']['revisi']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Revisi', 
		//'#description'  => 'tahun', 
		'#maxlength'    => 4, 
		'#size'         => 6, 
		'#attributes'	=> array('style' => 'text-align: right'), 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#weight'     => 0, 
		'#default_value'=> $revisi, 
	); 
	$form['formdata']['e_revisi']= array(
		'#type'         => 'value', 
		'#value'=> $revisi, 
	); 
	$form['formdata']['uraian']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Uraian', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $uraian, 
	); 

    $form['formdata']['perda'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Peraturan Daerah',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,        
    );
	$form['formdata']['perda']['perdano']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nomor Perda', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perdano, 
	); 
	$form['formdata']['perda']['perdatgl']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tgl. Perda', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perdatgl, 
	); 

	
	

    $form['formdata']['perbub'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Peraturan Daerah',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,        
    );
	$form['formdata']['perbub']['perbupno']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nomor Perbup', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perbupno, 
	); 
	$form['formdata']['perbub']['perbuptgl']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tgl. Perbup', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perbuptgl, 
	); 

	$form['formdata']['perdap'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Peraturan Daerah Perubahan',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,        
    );
	$form['formdata']['perdap']['perdanop']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nomor Perda', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perdanop, 
	); 
	$form['formdata']['perdap']['perdatglp']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tgl. Perda', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perdatglp, 
	); 
	$form['formdata']['perbubp'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Peraturan Daerah Perubahan',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,        
    );
	$form['formdata']['perbubp']['perbupnop']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nomor Perbup', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perbupnop, 
	); 
	$form['formdata']['perbubp']['perbuptglp']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tgl. Perbup', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perbuptglp, 
	);
	//DPA
    $form['formdata']['dpaskpd'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Format Nomor DPA',
        '#collapsible' => TRUE,
        '#collapsed' => ($revisi!=0),        
    );
	$form['formdata']['dpaskpd']['dpatgl']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tgl. DPA', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dpatgl, 
	); 

	$form['formdata']['dpaskpd']['dpapenformat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Pendapatan', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dpapenformat, 
	); 
	$form['formdata']['dpaskpd']['dpabtlformat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'BTL SKPD', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dpabtlformat, 
	); 
	$form['formdata']['dpaskpd']['dpablformat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'BL SKPD', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dpablformat, 
	); 

	$form['formdata']['dpaskpd']['dpappkdpformat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Pendapatan PPKD', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dpappkdpformat, 
	); 
	$form['formdata']['dpaskpd']['dpappkdbformat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Belanja PPKD', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dpappkdbformat, 
	); 	

	if ($revisi>=1) {
		//DPA R1
		$form['formdata']['dpaskpd1'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Format Nomor DPA Revisi #1',
			'#collapsible' => TRUE,
			'#collapsed' => ($revisi!=1),        
		);
		$form['formdata']['dpaskpd1']['dpatgl1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Tgl. DPA', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpatgl1, 
		); 

		$form['formdata']['dpaskpd1']['dpapenformat1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Pendapatan', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpapenformat1, 
		); 
		$form['formdata']['dpaskpd1']['dpabtlformat1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'BTL SKPD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpabtlformat1, 
		); 
		$form['formdata']['dpaskpd1']['dpablformat1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'BL SKPD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpablformat1, 
		); 

		$form['formdata']['dpaskpd1']['dpappkdpformat1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Pendapatan PPKD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpappkdpformat1, 
		); 
		$form['formdata']['dpaskpd1']['dpappkdbformat1']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Belanja PPKD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpappkdbformat1, 
		); 	
	}
	
	if ($revisi>=2) {
		//DPA R2
		$form['formdata']['dpaskpd2'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Format Nomor DPA Revisi #2',
			'#collapsible' => TRUE,
			'#collapsed' => ($revisi!=2),        
		);
		$form['formdata']['dpaskpd2']['dpatgl2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Tgl. DPA', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpatgl2, 
		); 

		$form['formdata']['dpaskpd2']['dpapenformat2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Pendapatan', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpapenformat2, 
		); 
		$form['formdata']['dpaskpd2']['dpabtlformat2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'BTL SKPD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpabtlformat2, 
		); 
		$form['formdata']['dpaskpd2']['dpablformat2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'BL SKPD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpablformat2, 
		); 

		$form['formdata']['dpaskpd2']['dpappkdpformat2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Pendapatan PPKD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpappkdpformat2, 
		); 
		$form['formdata']['dpaskpd2']['dpappkdbformat2']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Belanja PPKD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpappkdbformat2, 
		); 	
	}

	if ($revisi>=3) {
		//DPA R3
		$form['formdata']['dpaskpd3'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Format Nomor DPA Revisi #3',
			'#collapsible' => TRUE,
			'#collapsed' => ($revisi!=3),        
		);
		$form['formdata']['dpaskpd3']['dpatgl3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Tgl. DPA', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpatgl3, 
		); 

		$form['formdata']['dpaskpd3']['dpapenformat3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Pendapatan', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpapenformat3, 
		); 
		$form['formdata']['dpaskpd3']['dpabtlformat3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'BTL SKPD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpabtlformat3, 
		); 
		$form['formdata']['dpaskpd3']['dpablformat3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'BL SKPD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpablformat3, 
		); 

		$form['formdata']['dpaskpd3']['dpappkdpformat3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Pendapatan PPKD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpappkdpformat3, 
		); 
		$form['formdata']['dpaskpd3']['dpappkdbformat3']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Belanja PPKD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpappkdbformat3, 
		); 	
	}
		
	if ($revisi>=4) {
		//DPA R4
		$form['formdata']['dpaskpd4'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Format Nomor DPA Revisi #4',
			'#collapsible' => TRUE,
			'#collapsed' => ($revisi!=4),        
		);
		$form['formdata']['dpaskpd4']['dpatgl4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Tgl. DPA', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpatgl4, 
		); 

		$form['formdata']['dpaskpd4']['dpapenformat4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Pendapatan', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpapenformat4, 
		); 
		$form['formdata']['dpaskpd4']['dpabtlformat4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'BTL SKPD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpabtlformat4, 
		); 
		$form['formdata']['dpaskpd4']['dpablformat4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'BL SKPD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpablformat4, 
		); 

		$form['formdata']['dpaskpd4']['dpappkdpformat4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Pendapatan PPKD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpappkdpformat4, 
		); 
		$form['formdata']['dpaskpd4']['dpappkdbformat4']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Belanja PPKD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpappkdbformat4, 
		); 	
	}
	
	if ($revisi>=5) {
		//DPA R5
		$form['formdata']['dpaskpd5'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Format Nomor DPA Revisi #5',
			'#collapsible' => TRUE,
			'#collapsed' => ($revisi!=5),        
		);
		$form['formdata']['dpaskpd5']['dpatgl5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Tgl. DPA', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpatgl5, 
		); 

		$form['formdata']['dpaskpd5']['dpapenformat5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Pendapatan', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpapenformat5, 
		); 
		$form['formdata']['dpaskpd5']['dpabtlformat5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'BTL SKPD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpabtlformat5, 
		); 
		$form['formdata']['dpaskpd5']['dpablformat5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'BL SKPD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpablformat5, 
		); 

		$form['formdata']['dpaskpd5']['dpappkdpformat5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Pendapatan PPKD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpappkdpformat5, 
		); 
		$form['formdata']['dpaskpd5']['dpappkdbformat5']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Belanja PPKD', 
			//'#description'  => 'program', 
			'#maxlength'    => 255, 
			'#size'         => 60, 
			//'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $dpappkdbformat5, 
		); 	
	}
		
	//RPTK
	$form['formdata']['penyusunan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Batas Tanggal Penyusunan',
		'#collapsible' => true,
		'#collapsed' => false,     
		'#weight'     => 3,    
	);
	$form['formdata']['penyusunan']['tglbatasrka']= array(
		'#type'         => 'date', 
		'#title'        => 'Tgl. Penutupan RKA',
		//'#description'  => 'Tanggal dimulai pengisian usulan kegiatan', 
		'#default_value'=> array(
			'year' => format_date($tglbatasrka, 'custom', 'Y'),
			'month' => format_date($tglbatasrka, 'custom', 'n'), 
			'day' => format_date($tglbatasrka, 'custom', 'j'), 
		  ), 
	); 	
	$form['formdata']['penyusunan']['tglbatasdpa']= array(
		'#type'         => 'date', 
		'#title'        => 'Tgl. Penutupan DPA',
		//'#description'  => 'Batas akhir pengisian kegiatan, setelah tanggal ini sudah tidak bisa menambah/mengubah/menghapus kegiatan', 
		'#default_value'=> array(
			'year' => format_date($tglbatasdpa, 'custom', 'Y'),
			'month' => format_date($tglbatasdpa, 'custom', 'n'), 
			'day' => format_date($tglbatasdpa, 'custom', 'j'), 
		  ), 
	); 	
	$form['formdata']['penyusunan']['tglbatasrevisi']= array(
		'#type'         => 'date', 
		'#title'        => 'Tgl. Penutupan Revisi',
		//'#description'  => 'Batas akhir pengisian plafon anggaran, setelah tanggal ini sudah tidak bisa mengubah plafon anggaran', 
		'#default_value'=> array(
			'year' => format_date($tglbatasrevisi, 'custom', 'Y'),
			'month' => format_date($tglbatasrevisi, 'custom', 'n'), 
			'day' => format_date($tglbatasrevisi, 'custom', 'j'), 
		  ), 
	); 	
	
	

    $form['formdata']['e_tahun']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $tahun, 
    ); 
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#weight'     => 99, 
		'#suffix' => "&nbsp;<a href='/apbd/setupapp' class='btn_blue' style='color: white'>Batal</a>",
		'#value' => 'Simpan'
    );
    
    return $form;
}
function setupapp_edit_form_validate($form, &$form_state) {
	$revisi = $form_state['values']['revisi'];
	if ($revisi > 5 ) {
		form_set_error('', 'Revisi maksimal sampai dengan 5 (lima)');
	}            
}
function setupapp_edit_form_submit($form, &$form_state) {
    
    $e_tahun = $form_state['values']['e_tahun'];
    
	$tahun = $form_state['values']['tahun'];
	$revisi = $form_state['values']['revisi'];
	$e_revisi = $form_state['values']['e_revisi'];
	$wilayah = 'KABUPATEN JEPARA';
	$uraian= $form_state['values']['uraian'];
	
	$perdano = $form_state['values']['perdano'];
	$perdatgl= $form_state['values']['perdatgl'];
	
	$perbupno  = $form_state['values']['perbupno'];
	$perbuptgl = $form_state['values']['perbuptgl'];
	
	$perdanop = $form_state['values']['perdanop'];
	$perdatglp= $form_state['values']['perdatglp'];
	
	$perbupnop  = $form_state['values']['perbupnop'];
	$perbuptglp = $form_state['values']['perbuptglp'];
	
	//DPA
	$dpatgl = $form_state['values']['dpatgl'];
	
	$dpapenformat = $form_state['values']['dpapenformat'];
	$dpabtlformat = $form_state['values']['dpabtlformat'];
	$dpablformat = $form_state['values']['dpablformat'];

	$dpappkdpformat = $form_state['values']['dpappkdpformat'];
	$dpappkdbformat = $form_state['values']['dpappkdbformat'];

	
	//drupal_set_message($e_revisi);
	//drupal_set_message($revisi);
	

	
	//READ VARIABLE
    $tglbatasrka = $form_state['values']['tglbatasrka'];
    $tglbatasdpa = $form_state['values']['tglbatasdpa'];
    $tglbatasrevisi = $form_state['values']['tglbatasrevisi'];

    //FORMAT TANGGAL
    $tglbatasrkasql = $tglbatasrka['year'] . '-' . $tglbatasrka['month'] . '-' . $tglbatasrka['day'];
    $tglbatasdpasql = $tglbatasdpa['year'] . '-' . $tglbatasdpa['month'] . '-' . $tglbatasdpa['day'];
    $tglbatasrevisisql = $tglbatasrevisi['year'] . '-' . $tglbatasrevisi['month'] . '-' . $tglbatasrevisi['day'];

    if ($e_tahun=='') 
    {
        $sql = 'insert into {setupapp} (tahun, wilayah, uraian, tglbatasrka, tglbatasdpa, tglbatasrevisi) 
				values(%s, \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
				$res = db_query(db_rewrite_sql($sql), array($tahun, strtoupper($wilayah), $uraian, $tglbatasrkasql, $tglbatasdpasql, $tglbatasrevisisql));
    } else {
		//dpabtlformat, dpablformat, dpapenformat

		//DPA #1
		if ($e_revisi>=1) {
			$dpatgl1 = $form_state['values']['dpatgl1'];
			
			$dpapenformat1 = $form_state['values']['dpapenformat1'];
			$dpabtlformat1 = $form_state['values']['dpabtlformat1'];
			$dpablformat1 = $form_state['values']['dpablformat1'];

			$dpappkdpformat1 = $form_state['values']['dpappkdpformat1'];
			$dpappkdbformat1 = $form_state['values']['dpappkdbformat1'];
		}
		//DPA #2
		if ($e_revisi>=2) {
			$dpatgl2 = $form_state['values']['dpatgl2'];
			
			$dpapenformat2 = $form_state['values']['dpapenformat2'];
			$dpabtlformat2 = $form_state['values']['dpabtlformat2'];
			$dpablformat2 = $form_state['values']['dpablformat2'];

			$dpappkdpformat2 = $form_state['values']['dpappkdpformat2'];
			$dpappkdbformat2 = $form_state['values']['dpappkdbformat2'];
		}
		//DPA #3
		if ($e_revisi>=3) {
			$dpatgl3 = $form_state['values']['dpatgl3'];
			
			$dpapenformat3 = $form_state['values']['dpapenformat3'];
			$dpabtlformat3 = $form_state['values']['dpabtlformat3'];
			$dpablformat3 = $form_state['values']['dpablformat3'];

			$dpappkdpformat3 = $form_state['values']['dpappkdpformat3'];
			$dpappkdbformat3 = $form_state['values']['dpappkdbformat3'];
		}
		//DPA #4
		if ($e_revisi>=4) {
			$dpatgl4 = $form_state['values']['dpatgl4'];
			
			$dpapenformat4 = $form_state['values']['dpapenformat4'];
			$dpabtlformat4 = $form_state['values']['dpabtlformat4'];
			$dpablformat4 = $form_state['values']['dpablformat4'];

			$dpappkdpformat4 = $form_state['values']['dpappkdpformat4'];
			$dpappkdbformat4 = $form_state['values']['dpappkdbformat4'];
		}
		//DPA #5
		if ($e_revisi>=5) {
			$dpatgl5 = $form_state['values']['dpatgl5'];
			
			$dpapenformat5 = $form_state['values']['dpapenformat5'];
			$dpabtlformat5 = $form_state['values']['dpabtlformat5'];
			$dpablformat5 = $form_state['values']['dpablformat5'];

			$dpappkdpformat5 = $form_state['values']['dpappkdpformat5'];
			$dpappkdbformat5 = $form_state['values']['dpappkdbformat5'];
		}		
		
		if ($e_revisi==0) 
			$res = db_query(db_rewrite_sql('update {setupapp} set tahun=%s, revisi=%s, uraian=\'%s\',tglbatasrka=\'%s\', 
					tglbatasdpa=\'%s\', tglbatasrevisi=\'%s\', perdano=\'%s\', perdatgl=\'%s\', perbupno=\'%s\', perbuptgl=\'%s\', perdanop=\'%s\', perdatglp=\'%s\', perbupnop=\'%s\', perbuptglp=\'%s\', dpatgl=\'%s\', dpabtlformat=\'%s\', dpablformat=\'%s\', dpapenformat=\'%s\', dpappkdpformat=\'%s\', dpappkdbformat=\'%s\' where tahun=\'%s\''), array($tahun, $revisi, $uraian, $tglbatasrkasql, $tglbatasdpasql, $tglbatasrevisisql, $perdano, $perdatgl, $perbupno, $perbuptgl,$perdanop, $perdatglp, $perbupnop, $perbuptglp, $dpatgl, $dpabtlformat, $dpablformat,$dpapenformat, $dpappkdpformat, $dpappkdbformat, $e_tahun));
        
		else if ($e_revisi==1) 
			$res = db_query(db_rewrite_sql('update {setupapp} set tahun=%s, revisi=%s, uraian=\'%s\',tglbatasrka=\'%s\', 
					tglbatasdpa=\'%s\', tglbatasrevisi=\'%s\', perdano=\'%s\', perdatgl=\'%s\', perbupno=\'%s\', perbuptgl=\'%s\', perdanop=\'%s\', perdatglp=\'%s\', perbupnop=\'%s\', perbuptglp=\'%s\', dpatgl=\'%s\', dpabtlformat=\'%s\', dpablformat=\'%s\', dpapenformat=\'%s\', dpappkdpformat=\'%s\', dpappkdbformat=\'%s\', dpatgl1=\'%s\', dpabtlformat1=\'%s\', dpablformat1=\'%s\', dpapenformat1=\'%s\', dpappkdpformat1=\'%s\', dpappkdbformat1=\'%s\' where tahun=\'%s\''), array($tahun, $revisi, $uraian, $tglbatasrkasql, $tglbatasdpasql, 		
					$tglbatasrevisisql, $perdano, $perdatgl, $perbupno, $perbuptgl,$perdanop, $perdatglp, $perbupnop, $perbuptglp, $dpatgl, $dpabtlformat, $dpablformat,$dpapenformat, $dpappkdpformat, $dpappkdbformat, $dpatgl1, $dpabtlformat1, $dpablformat1,$dpapenformat1, $dpappkdpformat1, $dpappkdbformat1, $e_tahun));

		else if ($e_revisi==2) 
			$res = db_query(db_rewrite_sql('update {setupapp} set tahun=%s, revisi=%s, uraian=\'%s\',tglbatasrka=\'%s\', 
					tglbatasdpa=\'%s\', tglbatasrevisi=\'%s\', perdano=\'%s\', perdatgl=\'%s\', perbupno=\'%s\', perbuptgl=\'%s\', perdanop=\'%s\', perdatglp=\'%s\', perbupnop=\'%s\', perbuptglp=\'%s\', dpatgl=\'%s\', dpabtlformat=\'%s\', dpablformat=\'%s\', dpapenformat=\'%s\', dpappkdpformat=\'%s\', dpappkdbformat=\'%s\', 
					dpatgl1=\'%s\', dpabtlformat1=\'%s\', dpablformat1=\'%s\', dpapenformat1=\'%s\', dpappkdpformat1=\'%s\', dpappkdbformat1=\'%s\', 
					dpatgl2=\'%s\', dpabtlformat2=\'%s\', dpablformat2=\'%s\', dpapenformat2=\'%s\', dpappkdpformat2=\'%s\', dpappkdbformat2=\'%s\' 
					where tahun=\'%s\''), 
					array($tahun, $revisi, $uraian, $tglbatasrkasql, $tglbatasdpasql, 		
					$tglbatasrevisisql, $perdano, $perdatgl, $perbupno, $perbuptgl,$perdanop, $perdatglp, $perbupnop, $perbuptglp, $dpatgl, $dpabtlformat, $dpablformat,$dpapenformat, $dpappkdpformat, $dpappkdbformat, 
					$dpatgl1, $dpabtlformat1, $dpablformat1,$dpapenformat1, $dpappkdpformat1, $dpappkdbformat1, 
					$dpatgl2, $dpabtlformat2, $dpablformat2,$dpapenformat2, $dpappkdpformat2, $dpappkdbformat2, 
					$e_tahun));
					
		else if ($e_revisi==3) 
			$res = db_query(db_rewrite_sql('update {setupapp} set tahun=%s, revisi=%s, uraian=\'%s\',tglbatasrka=\'%s\', 
					tglbatasdpa=\'%s\', tglbatasrevisi=\'%s\', perdano=\'%s\', perdatgl=\'%s\', perbupno=\'%s\', perbuptgl=\'%s\', perdanop=\'%s\', perdatglp=\'%s\', perbupnop=\'%s\', perbuptglp=\'%s\', dpatgl=\'%s\', dpabtlformat=\'%s\', dpablformat=\'%s\', dpapenformat=\'%s\', dpappkdpformat=\'%s\', dpappkdbformat=\'%s\', 
					dpatgl1=\'%s\', dpabtlformat1=\'%s\', dpablformat1=\'%s\', dpapenformat1=\'%s\', dpappkdpformat1=\'%s\', dpappkdbformat1=\'%s\', 
					dpatgl2=\'%s\', dpabtlformat2=\'%s\', dpablformat2=\'%s\', dpapenformat2=\'%s\', dpappkdpformat2=\'%s\', dpappkdbformat2=\'%s\', 
					dpatgl3=\'%s\', dpabtlformat3=\'%s\', dpablformat3=\'%s\', dpapenformat3=\'%s\', dpappkdpformat3=\'%s\', dpappkdbformat3=\'%s\' 
					where tahun=\'%s\''), 
					array($tahun, $revisi, $uraian, $tglbatasrkasql, $tglbatasdpasql, 		
					$tglbatasrevisisql, $perdano, $perdatgl, $perbupno, $perbuptgl,$perdanop, $perdatglp, $perbupnop, $perbuptglp, $dpatgl, $dpabtlformat, $dpablformat,$dpapenformat, $dpappkdpformat, $dpappkdbformat, 
					$dpatgl1, $dpabtlformat1, $dpablformat1,$dpapenformat1, $dpappkdpformat1, $dpappkdbformat1, 
					$dpatgl2, $dpabtlformat2, $dpablformat2,$dpapenformat2, $dpappkdpformat2, $dpappkdbformat2, 
					$dpatgl3, $dpabtlformat3, $dpablformat3,$dpapenformat3, $dpappkdpformat3, $dpappkdbformat3, 
					$e_tahun));		

		else if ($e_revisi==4) 
			$res = db_query(db_rewrite_sql('update {setupapp} set tahun=%s, revisi=%s, uraian=\'%s\',tglbatasrka=\'%s\', 
					tglbatasdpa=\'%s\', tglbatasrevisi=\'%s\', perdano=\'%s\', perdatgl=\'%s\', perbupno=\'%s\', perbuptgl=\'%s\', perdanop=\'%s\', perdatglp=\'%s\', perbupnop=\'%s\', perbuptglp=\'%s\', dpatgl=\'%s\', dpabtlformat=\'%s\', dpablformat=\'%s\', dpapenformat=\'%s\', dpappkdpformat=\'%s\', dpappkdbformat=\'%s\', 
					dpatgl1=\'%s\', dpabtlformat1=\'%s\', dpablformat1=\'%s\', dpapenformat1=\'%s\', dpappkdpformat1=\'%s\', dpappkdbformat1=\'%s\', 
					dpatgl2=\'%s\', dpabtlformat2=\'%s\', dpablformat2=\'%s\', dpapenformat2=\'%s\', dpappkdpformat2=\'%s\', dpappkdbformat2=\'%s\', 
					dpatgl3=\'%s\', dpabtlformat3=\'%s\', dpablformat3=\'%s\', dpapenformat3=\'%s\', dpappkdpformat3=\'%s\', dpappkdbformat3=\'%s\',
					dpatgl4=\'%s\', dpabtlformat4=\'%s\', dpablformat4=\'%s\', dpapenformat4=\'%s\', dpappkdpformat4=\'%s\', dpappkdbformat4=\'%s\' 					
					where tahun=\'%s\''), 
					array($tahun, $revisi, $uraian, $tglbatasrkasql, $tglbatasdpasql, 		
					$tglbatasrevisisql, $perdano, $perdatgl, $perbupno, $perbuptgl,$perdanop, $perdatglp, $perbupnop, $perbuptglp, $dpatgl, $dpabtlformat, $dpablformat,$dpapenformat, $dpappkdpformat, $dpappkdbformat, 
					$dpatgl1, $dpabtlformat1, $dpablformat1,$dpapenformat1, $dpappkdpformat1, $dpappkdbformat1, 
					$dpatgl2, $dpabtlformat2, $dpablformat2,$dpapenformat2, $dpappkdpformat2, $dpappkdbformat2, 
					$dpatgl3, $dpabtlformat3, $dpablformat3,$dpapenformat3, $dpappkdpformat3, $dpappkdbformat3, 
					$dpatgl4, $dpabtlformat4, $dpablformat4,$dpapenformat4, $dpappkdpformat4, $dpappkdbformat4, 
					$e_tahun));		

		else 
			$res = db_query(db_rewrite_sql('update {setupapp} set tahun=%s, revisi=%s, uraian=\'%s\',tglbatasrka=\'%s\', 
					tglbatasdpa=\'%s\', tglbatasrevisi=\'%s\', perdano=\'%s\', perdatgl=\'%s\', perbupno=\'%s\', perbuptgl=\'%s\', perdanop=\'%s\', perdatglp=\'%s\', perbupnop=\'%s\', perbuptglp=\'%s\', dpatgl=\'%s\', dpabtlformat=\'%s\', dpablformat=\'%s\', dpapenformat=\'%s\', dpappkdpformat=\'%s\', dpappkdbformat=\'%s\', 
					dpatgl1=\'%s\', dpabtlformat1=\'%s\', dpablformat1=\'%s\', dpapenformat1=\'%s\', dpappkdpformat1=\'%s\', dpappkdbformat1=\'%s\', 
					dpatgl2=\'%s\', dpabtlformat2=\'%s\', dpablformat2=\'%s\', dpapenformat2=\'%s\', dpappkdpformat2=\'%s\', dpappkdbformat2=\'%s\', 
					dpatgl3=\'%s\', dpabtlformat3=\'%s\', dpablformat3=\'%s\', dpapenformat3=\'%s\', dpappkdpformat3=\'%s\', dpappkdbformat3=\'%s\',
					dpatgl4=\'%s\', dpabtlformat4=\'%s\', dpablformat4=\'%s\', dpapenformat4=\'%s\', dpappkdpformat4=\'%s\', dpappkdbformat4=\'%s\', 					
					dpatgl5=\'%s\', dpabtlformat5=\'%s\', dpablformat5=\'%s\', dpapenformat5=\'%s\', dpappkdpformat5=\'%s\', dpappkdbformat5=\'%s\' 					
					where tahun=\'%s\''), 
					array($tahun, $revisi, $uraian, $tglbatasrkasql, $tglbatasdpasql, 		
					$tglbatasrevisisql, $perdano, $perdatgl, $perbupno, $perbuptgl,$perdanop, $perdatglp, $perbupnop, $perbuptglp, $dpatgl, $dpabtlformat, $dpablformat,$dpapenformat, $dpappkdpformat, $dpappkdbformat, 
					$dpatgl1, $dpabtlformat1, $dpablformat1,$dpapenformat1, $dpappkdpformat1, $dpappkdbformat1, 
					$dpatgl2, $dpabtlformat2, $dpablformat2,$dpapenformat2, $dpappkdpformat2, $dpappkdbformat2, 
					$dpatgl3, $dpabtlformat3, $dpablformat3,$dpapenformat3, $dpappkdpformat3, $dpappkdbformat3, 
					$dpatgl4, $dpabtlformat4, $dpablformat4,$dpapenformat4, $dpappkdpformat4, $dpappkdbformat4, 
					$dpatgl5, $dpabtlformat5, $dpablformat5,$dpapenformat5, $dpappkdpformat5, $dpappkdbformat5, 
					$e_tahun));							
    }
    if ($res) {
        drupal_set_message('Penyimpanan data berhasil dilakukan');
		if ($tahun == variable_get('apbdtahun', 0)) {
			variable_set('apbdrevisi', $revisi);
			variable_set('apbdwilayah', $wilayah);
			variable_set('apbdkegiatan', $uraian);
		}
    }
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('apbd/setupapp');    
}
?>

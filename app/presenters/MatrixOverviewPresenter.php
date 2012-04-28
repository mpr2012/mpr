<?php

/**
 * MatrixOverview presenter.
 *
 * @author     Petr Kucera
 * @package    MyApplication
 */
class MatrixOverviewPresenter extends SecuredPresenter
{
    
	public function renderDefault()
	{
		$this->template->matrixs = $this->db->table('matice')->order('nazev DESC');
	}
    
    public function renderCreate()
    {
        
    }
    
    public function createComponentCreateNewMatrixForm()
    {
		$form = new \Nette\Application\UI\Form;
        $form->addGroup('LRM');
		$form->addText('nazev', 'Název projektu:')
            ->setAttribute('size', '30')
			->setRequired('Název nesmí být prázdný řetězec.');

        $form->addGroup('Oprávnění');
        $groups = array();
        foreach ($this->db->table('uzivatel')->select('DISTINCT skupina') as $group)
            $groups[] = $group->skupina;
        $form->addCheckboxList('skupiny', 'Skupiny:', $groups);
        $users = array();
        foreach ($this->db->table('uzivatel') as $user)
            if ($user->id != $this->getUser()->getId())
                $users[$user->id] = $user->jmeno . " " . $user->prijmeni . " (" . $user->username . ")";
        $form->addCheckboxList('users', 'Uživatelé:', $users);
        
        $form->addGroup('Potvrzení');
		$form->addSubmit('create', 'Vytvořit');

		$form->onSuccess[] = callback($this, 'NewMatrixFormSubmitted');
		return $form;
    }
    
    public function newMatrixFormSubmitted(\Nette\Application\UI\Form $form)
    {
        $values = $form->getValues();
        $nazev = $values['nazev'];
        unset($values['nazev']);
        $this->db->table('matice')->insert(array('nazev' => $nazev));
        $maticeId = $this->db->lastInsertId();
        $this->db->table('clen')->insert(array('matice' => $maticeId, 'uzivatel' => $this->getUser()->getId()));
        if ($values['users'])
            foreach ($values['users'] as $userId)
                $this->db->table('clen')->insert(array('matice' => $maticeId, 'uzivatel' => $userId));
        $this->flashMessage("Matice s názvem '$nazev' byla úspěšně vložena. Pokračujte její editací.");
        $this->redirect('MatrixOverview:view', $maticeId);
    }
    
    public function renderDelete($id)
    {
        $nazev = $this->db->table('matice')->get($id)->nazev;
        $this->db->table('matice')->get($id)->delete();
        $this->flashMessage("Matice '$nazev' byla úspěšně smazána.");
        $this->redirect('MatrixOverview:');
    }
    
    /**
     * BEGIN
     * --- EXPORT MATICE - MARCEL ---
     * 
     */
    public function renderExport($id)
    {
        $this->template->matrix = $this->db->table('matice')->get($id);
        $this->template->xml = $this->exportMatrix($id);
    }
    
    public function exportMatrix($matriceID)
    {

        $xmlText  = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
        $xmlText .= "<Project xmlns=\"http://schemas.microsoft.com/project\">\"\n";
        $xmlText .= "<SaveVersion>1</SaveVersion>\n";
        $xmlText .= "<Name>" . $this->db->table('matice')->get($matriceID)->nazev . ".xml</Name>\n";
        $xmlText .= "<Title>Plan projektu_" . $this->db->table('matice')->get($matriceID)->nazev . "</Title>\n";
        $xmlText .= "<Author>Ones</Author>\n";

        $xmlText .= "<LastSaved>2012-04-27T13:06:00</LastSaved>\n";
        $xmlText .= "<ScheduleFromStart>1</ScheduleFromStart>\n";
        $xmlText .= "<CalendarUID>3</CalendarUID>\n";
        $xmlText .= "<MinutesPerDay>1440</MinutesPerDay>\n";
        $xmlText .= "<MinutesPerWeek>10080</MinutesPerWeek>\n";
        $xmlText .= "<DaysPerMonth>100</DaysPerMonth>\n";
        $xmlText .= "<ProjectExternallyEdited>0</ProjectExternallyEdited>\n";
//        $xmlText .= $this->calendars;

        $tasks = "<Tasks>\n";
        $resources = "<Resources>\n";
        $assignments = "<Assignments>\n";

//        $query = "select * from vystup where id=" + $matriceID;
//        $resultSummary = mysql_query($query, $connection) or die("Could not complete database query");
//        $internalID = 1;
//        while ($rowSummary = mysql_fetch_array($resultSummary)) {
//            $tasks .= newSummary($internalID++, $rowSummary["nazev"], $rowSummary["poradi"]);
//            $query .= "select * from aktivita where vystup=" . $rowSummary["id"];
//            $resultTask = mysql_query($query, $connection) or die("Could not complete database query");
//            while($rowTask = mysql_fetch_array($resultTask)) {
//                $taskUID = $internalID++;
//                $tasks  .= newTask($taskUID, $rowTask["nazev"],$rowTask["zacatek"],$rowTask["konec"],$rowTask["poradi"], $rowSummary["poradi"]);
//            /* for each resource */
//                $resUID  = $internalID++;
//                $resources   .= newResource($resUID, $rowSummary["zdroje"], $rowTask["poradi"], $rowSummary["poradi"]);
//                $assignments .= newAssignments($internalID++, $resUID, $taskUID);
//            }
//        }

        $tasks .= "</Tasks>\n";
        $resources .= "</Resources>\n";
        $assignments .= "</Assignments>\n";

        $xmlText .= $tasks;
        $xmlText .= $resources;
        $xmlText .= $assignments;
        $xmlText .= "</Project>\n";

        return $xmlText;
    }
    
    /**
     * END
     * --- EXPORT MATICE - MARCEL ---
     * 
     */
    
    public function renderView($id)
    {
        $this->template->matrixId = $id;
        $this->template->matrixNazev = $this->db->table('matice')->get($id)->nazev;
        
        if (!$this->isAjax())
        {
            $this->template->zamer              = $this->db->table('matice')->get($id)->zamer;
            $this->template->cil                = $this->db->table('matice')->get($id)->cil;
            /*
             * nutno vice rozdrobit, aby se daly lepe pouzit snippety
             */
            $this->template->ukazatele1         = $this->db->table('ukazatel')->where(array('matice' => $id, 'radek' => '1'));
            $this->template->ukazatele2         = $this->db->table('ukazatel')->where(array('matice' => $id, 'radek' => '2'));
            $this->template->ukazatele3         = $this->db->table('ukazatel')->where(array('matice' => $id, 'radek' => '3'));
            $this->template->zdroje_overeni1    = $this->db->table('zdroj_overeni')->where(array('matice' => $id, 'radek' => '1'));
            $this->template->zdroje_overeni2    = $this->db->table('zdroj_overeni')->where(array('matice' => $id, 'radek' => '2'));
            $this->template->zdroje_overeni3    = $this->db->table('zdroj_overeni')->where(array('matice' => $id, 'radek' => '3'));
            $this->template->predpoklady2       = $this->db->table('predpoklad')->where(array('matice' => $id, 'radek' => '2'));
            $this->template->predpoklady3       = $this->db->table('predpoklad')->where(array('matice' => $id, 'radek' => '3'));
            $this->template->predpoklady4       = $this->db->table('predpoklad')->where(array('matice' => $id, 'radek' => '4'));
            $this->template->predpoklady5       = $this->db->table('predpoklad')->where(array('matice' => $id, 'radek' => '5'));
            $this->template->vystupy            = $this->db->table('vystup')->where(array('matice' => $id))->order('poradi');
            $this->template->aktivity           = array();
            foreach ($this->template->vystupy as $vystup)
                $this->template->aktivity[$vystup->id] = $this->db->table('aktivita')->where(array('vystup' => $vystup->id))->order('poradi');
            $this->template->akt_frm_nazev = '';
            $this->template->akt_frm_zdroje = '';
            $this->template->akt_frm_od = '';
            $this->template->akt_frm_do = '';
            $this->template->akt_frm_vystup = '';
            $this->template->akt_frm_id = '';
        }
    }
    
    // zamer
    public function handleEdit_zamer($id, $text)
    {
        $this->db->table('matice')->get($id)->update(array(
            'zamer' => $text
        ));
        $this->template->zamer  = $this->db->table('matice')->get($id)->zamer;
        
        $this->invalidateControl('ul_zamer');
    }
    
    // cil
    public function handleEdit_cil($id, $text)
    {
        $this->payload->message = 'Sucess';
        $this->payload->status = 'ok';
        $this->db->table('matice')->get($id)->update(array(
            'cil' => $text
        ));
        $this->template->cil  = $this->db->table('matice')->get($id)->cil;
        $this->invalidateControl('ul_cil');
    }
    
    // overitelne ukazatele
    public function handleNew_uk($id, $text, $row_id)
    {
        $ukazatele  = 'ukazatele' . $row_id;
        $this->db->table('ukazatel')->where(array('matice' => $id, 'radek' => $row_id))->insert(array(
            'matice'    => $id,
            'radek'     => $row_id,
            'nazev'     => $text
        ));
        $this->template->$ukazatele = $this->db->table('ukazatel')->where(array(
            'matice'    => $id,
            'radek'     => $row_id
        ));
        $this->invalidateControl('ul_uk' . $row_id);
    }
    public function handleEdit_uk($id, $text, $uk_id)
    {
        $row_id     = $this->db->table('ukazatel')->get($uk_id)->radek;
        $ukazatele  = 'ukazatele' . $row_id;
        $this->db->table('ukazatel')->get($uk_id)->update(array(
            'nazev'     => $text
        ));
        $this->template->$ukazatele = $this->db->table('ukazatel')->where(array(
            'matice'    => $id,
            'radek'     => $row_id
        ));
        $this->invalidateControl('ul_uk' . $row_id);
    }
    public function handleDelete_uk($id, $rec_id)
    {
        $row_id     = $this->db->table('ukazatel')->get($rec_id)->radek;
        $ukazatele  = 'ukazatele' . $row_id;
        $this->db->table('ukazatel')->get($rec_id)->delete();
        $this->template->$ukazatele = $this->db->table('ukazatel')->where(array(
            'matice'    => $id,
            'radek'     => $row_id
        ));
        $this->invalidateControl('ul_uk' . $row_id);
    }
    
    // zdroje overeni
    public function handleNew_zdroje($id, $text, $row_id)
    {
        $zdroje  = 'zdroje_overeni' . $row_id;
        $this->db->table('zdroj_overeni')->where(array('matice' => $id, 'radek' => $row_id))->insert(array(
            'matice'    => $id,
            'radek'     => $row_id,
            'nazev'     => $text
        ));
        $this->template->$zdroje = $this->db->table('zdroj_overeni')->where(array(
            'matice'    => $id,
            'radek'     => $row_id
        ));
        $this->invalidateControl('ul_zd' . $row_id);
    }
    public function handleEdit_zdroje($id, $text, $zdr_id)
    {
        $row_id     = $this->db->table('zdroj_overeni')->get($zdr_id)->radek;
        $zdroje  = 'zdroje_overeni' . $row_id;
        $this->db->table('zdroj_overeni')->get($zdr_id)->update(array(
            'nazev'     => $text
        ));
        $this->template->$zdroje = $this->db->table('zdroj_overeni')->where(array(
            'matice'    => $id,
            'radek'     => $row_id
        ));
        $this->invalidateControl('ul_zd' . $row_id);
    }
    public function handleDelete_zdroje($id, $rec_id)
    {
        $row_id     = $this->db->table('zdroj_overeni')->get($rec_id)->radek;
        $zdroje  = 'zdroje_overeni' . $row_id;
        $this->db->table('zdroj_overeni')->get($rec_id)->delete();
        $this->template->$zdroje = $this->db->table('zdroj_overeni')->where(array(
            'matice'    => $id,
            'radek'     => $row_id
        ));
        $this->invalidateControl('ul_zd' . $row_id);
    }
    
    // predpoklady
    public function handleNew_predpoklady($id, $text, $row_id)
    {
        $predpoklady  = 'predpoklady' . $row_id;
        $this->db->table('predpoklad')->where(array('matice' => $id, 'radek' => $row_id))->insert(array(
            'matice'    => $id,
            'radek'     => $row_id,
            'nazev'     => $text
        ));
        $this->template->$predpoklady = $this->db->table('predpoklad')->where(array(
            'matice'    => $id,
            'radek'     => $row_id
        ));
        $this->invalidateControl('ul_pr' . $row_id);
    }
    public function handleEdit_predpoklady($id, $text, $pr_id)
    {
        $row_id     = $this->db->table('predpoklad')->get($pr_id)->radek;
        $predpoklady  = 'predpoklady' . $row_id;
        $this->db->table('predpoklad')->get($pr_id)->update(array(
            'nazev'     => $text
        ));
        $this->template->$predpoklady = $this->db->table('predpoklad')->where(array(
            'matice'    => $id,
            'radek'     => $row_id
        ));
        $this->invalidateControl('ul_pr' . $row_id);
    }
    public function handleDelete_predpoklady($id, $rec_id)
    {
        $row_id     = $this->db->table('predpoklad')->get($rec_id)->radek;
        $predpoklady  = 'predpoklady' . $row_id;
        $this->db->table('predpoklad')->get($rec_id)->delete();
        $this->template->$predpoklady = $this->db->table('predpoklad')->where(array(
            'matice'    => $id,
            'radek'     => $row_id
        ));
        $this->invalidateControl('ul_pr' . $row_id);
    }
    
    // vystupy
    public function handleNew_vystupy($id, $text)
    {
        $max_poradi = $this->db->table('vystup')->where(array('matice' => $id))->max('poradi');
        $this->db->table('vystup')->insert(array(
            'matice'    => $id,
            'nazev'     => $text,
            'poradi'     => ($max_poradi + 10)
        ));
        $this->template->vystupy = $this->db->table('vystup')->where(array(
            'matice'    => $id
        ))->order('poradi');
        $this->template->akt_frm_nazev = '';
        $this->template->akt_frm_zdroje = '';
        $this->template->akt_frm_od = '';
        $this->template->akt_frm_do = '';
        $this->template->akt_frm_vystup = '';
        $this->template->akt_frm_id = '';
        $this->invalidateControl('ul_vys');
        $this->invalidateControl('aktivita_form');

    }
    public function handleEdit_vystupy($id, $text, $vys_id)
    {
        $this->db->table('vystup')->get($vys_id)->update(array(
            'nazev'     => $text
        ));
        $this->template->vystupy = $this->db->table('vystup')->where(array(
            'matice'    => $id
        ))->order('poradi');
        $this->template->akt_frm_nazev = '';
        $this->template->akt_frm_zdroje = '';
        $this->template->akt_frm_od = '';
        $this->template->akt_frm_do = '';
        $this->template->akt_frm_vystup = '';
        $this->template->akt_frm_id = '';
        $this->invalidateControl('ul_vys');
        $this->invalidateControl('aktivita_form');
    }
    public function handleDelete_vystupy($id, $rec_id)
    {
        $this->db->table('vystup')->get($rec_id)->delete();
        $this->template->vystupy = $this->db->table('vystup')->where(array(
            'matice'    => $id
        ))->order('poradi');
        $this->template->aktivity = array();
        foreach ($this->template->vystupy as $vystup)
            $this->template->aktivity[$vystup->id] = $this->db->table('aktivita')->where(array('vystup' => $vystup->id))->order('poradi');
        $this->template->akt_frm_nazev = '';
        $this->template->akt_frm_zdroje = '';
        $this->template->akt_frm_od = '';
        $this->template->akt_frm_do = '';
        $this->template->akt_frm_vystup = '';
        $this->template->akt_frm_id = '';
        $this->invalidateControl('ul_vys');
        $this->invalidateControl('ul_akt');
        $this->invalidateControl('ul_zdroje');
        $this->invalidateControl('ul_casram');
        $this->invalidateControl('aktivita_form');
    }
    public function handleChange_seq_vystupy($id, $poradi)
    {
        $poradi = json_decode($poradi);
        foreach ($poradi as $vys_id => $index)
        {
            $this->db->table('vystup')->get($vys_id)->update(array('poradi' => $index));
        }
        $this->template->vystupy = $this->db->table('vystup')->where(array(
            'matice' => $id
        ))->order('poradi');
        $this->template->aktivity = array();
        foreach ($this->template->vystupy as $vystup)
            $this->template->aktivity[$vystup->id] = $this->db->table('aktivita')->where(array('vystup' => $vystup->id))->order('poradi');
        $this->template->akt_frm_nazev = '';
        $this->template->akt_frm_zdroje = '';
        $this->template->akt_frm_od = '';
        $this->template->akt_frm_do = '';
        $this->template->akt_frm_vystup = '';
        $this->template->akt_frm_id = '';
        $this->invalidateControl('ul_vys');
        $this->invalidateControl('ul_akt');
        $this->invalidateControl('ul_zdroje');
        $this->invalidateControl('ul_casram');
        $this->invalidateControl('aktivita_form');
    }
    
    // aktivity
    public function handleNew_aktivita($id, $cas_do, $cas_od, $nazev, $vys_id, $zdroje)
    {
        $this->db->table('aktivita')->insert(array(
            'zacatek'   => date('Y-m-d', strtotime($cas_od)),
            'konec'     => date('Y-m-d', strtotime($cas_do)),
            'nazev'     => $nazev,
            'vystup'    => $vys_id,
            'zdroje'    => $zdroje
        ));
        $this->template->vystupy = $this->db->table('vystup')->where(array(
            'matice'    => $id
        ))->order('poradi');
        $this->template->aktivity = array();
        foreach ($this->template->vystupy as $vystup)
            $this->template->aktivity[$vystup->id] = $this->db->table('aktivita')->where(array('vystup' => $vystup->id))->order('poradi');
        $this->template->akt_frm_nazev = '';
        $this->template->akt_frm_zdroje = '';
        $this->template->akt_frm_od = '';
        $this->template->akt_frm_do = '';
        $this->template->akt_frm_vystup = '';
        $this->template->akt_frm_id = '';
        $this->invalidateControl('ul_akt');
        $this->invalidateControl('ul_zdroje');
        $this->invalidateControl('ul_casram');
        $this->invalidateControl('aktivita_form');
    }
    public function handleEdit_aktivity($id, $akt_id)
    {
        $this->template->akt_frm_nazev = $this->db->table('aktivita')->get($akt_id)->nazev;
        $this->template->akt_frm_zdroje = $this->db->table('aktivita')->get($akt_id)->zdroje;
        $this->template->akt_frm_od = $this->db->table('aktivita')->get($akt_id)->zacatek;
        $this->template->akt_frm_do = $this->db->table('aktivita')->get($akt_id)->konec;
        $this->template->akt_frm_vystup = $this->db->table('aktivita')->get($akt_id)->vystup;
        $this->template->akt_frm_id = $akt_id;
        $this->template->vystupy = $this->db->table('vystup')->where(array('matice' => $id))->order('poradi');
        $this->invalidateControl('aktivita_form');
    }
    public function handleEdit_aktivita($id, $akt_id, $nazev, $zdroje, $cas_od, $cas_do, $vys_id)
    {
        $this->db->table('aktivita')->get($akt_id)->update(array(
            'nazev'     => $nazev,
            'zdroje'     => $zdroje,
            'zacatek'   => date('Y-m-d', strtotime($cas_od)),
            'konec'     => date('Y-m-d', strtotime($cas_do)),
            'vystup'     => $vys_id
        ));
        $this->template->vystupy = $this->db->table('vystup')->where(array(
            'matice'    => $id
        ))->order('poradi');
        $this->template->aktivity = array();
        foreach ($this->template->vystupy as $vystup)
            $this->template->aktivity[$vystup->id] = $this->db->table('aktivita')->where(array('vystup' => $vystup->id))->order('poradi');
        $this->template->akt_frm_nazev = '';
        $this->template->akt_frm_zdroje = '';
        $this->template->akt_frm_od = '';
        $this->template->akt_frm_do = '';
        $this->template->akt_frm_vystup = '';
        $this->template->akt_frm_id = '';
        $this->invalidateControl('ul_akt');
        $this->invalidateControl('ul_zdroje');
        $this->invalidateControl('ul_casram');
        $this->invalidateControl('aktivita_form');
    }
    public function handleDelete_aktivity($id, $rec_id)
    {
        $this->db->table('aktivita')->get($rec_id)->delete();
        $this->template->vystupy = $this->db->table('vystup')->where(array(
            'matice'    => $id
        ))->order('poradi');
        $this->template->aktivity = array();
        foreach ($this->template->vystupy as $vystup)
            $this->template->aktivity[$vystup->id] = $this->db->table('aktivita')->where(array('vystup' => $vystup->id))->order('poradi');
        $this->invalidateControl('ul_akt');
        $this->invalidateControl('ul_zdroje');
        $this->invalidateControl('ul_casram');
    }
    public function handleChange_seq_aktivity($id, $poradi)
    {
        $poradi = json_decode($poradi);
        foreach ($poradi as $akt_id => $index)
        {
            $this->db->table('aktivita')->get($akt_id)->update(array('poradi' => $index));
        }
        $this->template->vystupy = $this->db->table('vystup')->where(array(
            'matice' => $id
        ))->order('poradi');
        $this->template->aktivity = array();
        foreach ($this->template->vystupy as $vystup)
            $this->template->aktivity[$vystup->id] = $this->db->table('aktivita')->where(array('vystup' => $vystup->id))->order('poradi');
        $this->invalidateControl('ul_akt');
        $this->invalidateControl('ul_zdroje');
        $this->invalidateControl('ul_casram');
    }
    
}

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
		$allMatrixs = $this->db->table('matice')->order('nazev DESC');
        $this->template->matrixs = array();
        foreach ($allMatrixs as $matrix)
        {
            if ($this->db->table('clen')->where(array(
                'uzivatel'  => $this->template->userData['id'],
                'matice'    => $matrix->id
            ))->count())
            {
                $this->template->matrixs[] = $matrix;
            }
        }
//        dump($this->template->userData);
	}
    
    public function renderCreate()
    {
        
    }
    
    public function createComponentCreateNewMatrixForm()
    {
		$form = new \Nette\Application\UI\Form;
        $form->addHidden('majitel', $this->template->userData['id']);
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
        $majitel = $values['majitel'];
        unset($values['nazev']);
        unset($values['majitel']);
        $this->db->table('matice')->insert(array('nazev' => $nazev, 'majitel' => $majitel));
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
        \Nette\Environment::getHttpResponse()->setContentType('<?xml version="1.0" encoding="utf-8"?>');
        \Nette\Environment::getHttpResponse()->setHeader('Pragma', "public");
        \Nette\Environment::getHttpResponse()->setHeader('Expires', 0);
        \Nette\Environment::getHttpResponse()->setHeader('Cache-Control', "must-revalidate, post-check=0, pre-check=0");
        \Nette\Environment::getHttpResponse()->setHeader('Content-Transfer-Encoding', "binary");
        \Nette\Environment::getHttpResponse()->setHeader('Content-Description', "File Transfer");
        \Nette\Environment::getHttpResponse()->setHeader('Content-Length', mb_strlen($this->exportMatrix($id)));
        \Nette\Environment::getHttpResponse()->setHeader('Content-Disposition', 'attachment; filename="' . $this->db->table('matice')->get($id)->nazev . " " . date('d-m-Y') . '"');
        $this->template->matrix = $this->db->table('matice')->get($id);
        echo $this->exportMatrix($id);
        $this->terminate();
    }

    public function newSummary($UID, $nazev, $poradi)
    {
	$task  = "<Task>\n";
	$task .= "<UID>{$UID}</UID>\n";
	$task .= "<Name>{$nazev}</Name>\n";
	$task .= "<IsNull>0</IsNull>\n";
	$task .= "<WBS>{$poradi}</WBS>\n";
	$task .= "<WBSLevel>1</WBSLevel>\n";
	$task .= "<OutlineNumber>{$poradi}</OutlineNumber>\n";
	$task .= "<OutlineLevel>1</OutlineLevel>\n";
	$task .= "<Summary>1</Summary>\n";
	$task .= "</Task>\n";
	return $task;
    }

    public function newTask($UID, $nazev, $zacatek, $konec, $poradi)
    {
	$task  = "<Task>\n";
	$task .= "<UID>{$UID}</UID>\n";
	$task .= "<Name>{$nazev}</Name>\n";
	$task .= "<Manual>1</Manual>\n";
	$task .= "<IsNull>0</IsNull>\n";
	$task .= "<WBS>{$poradi}</WBS>\n";
	$task .= "<WBSLevel>2</WBSLevel>\n";
	$task .= "<OutlineNumber>{$poradi}</OutlineNumber>\n";
	$task .= "<OutlineLevel>2</OutlineLevel>\n";
	$task .= "<CalendarUID>-1</CalendarUID>\n";
	$datetime1 = new DateTime($zacatek);
	$datetime2 = new DateTime($konec);
	$interval = $datetime1->diff($datetime2);
	$diff = ((int)$interval->format('%h'))+24;
	$task .= "<Start>{$zacatek->format('Y-m-d')}T08:00:00</Start>\n";
	$task .= "<ActualDuration>PT{$diff}H0M0S</ActualDuration>\n";
	$task .= "<DurationFormat>7</DurationFormat>\n";
	$task .= "<Milestone>0</Milestone>\n";
	$task .= "<Summary>0</Summary>\n";
	$task .= "</Task>\n";
	return $task;
    }

    public function newResource($UID, $nazev, $poradi)
    {
	$resource  = "<Resource>\n";
	$resource .= "<UID>{$UID}</UID>\n";
	$resource .= "<Name>{$nazev}_{$poradi}</Name>\n";
	$resource .= "<Type>0</Type>\n";
	$resource .= "<IsNull>0</IsNull>\n";
	$resource .= "<WorkGroup>1</WorkGroup>\n";
	$resource .= "<IsCostResource>1</IsCostResource>\n";
	$resource .= "</Resource>\n";
	return $resource;
    }

    public function newAssignments($UID, $resUID, $taskUID, $date)
    {
	$assignment  = "<Assignment>\n";
	$assignment .= "<UID>{$UID}</UID>\n";
	$assignment .= "<TaskUID>{$taskUID}</TaskUID>\n";
	$assignment .= "<ResourceUID>{$resUID}</ResourceUID>\n";
	$assignment .= "<Start>{$date->format('Y-m-d')}T08:00:00</Start>\n";
	$assignment .= "<Finish>{$date->format('Y-m-d')}T08:00:00</Finish>\n";
	$assignment .= "</Assignment>\n";
	return $assignment;
    }
    
    public function exportMatrix($matriceID)
    {
        // base project formating and atributes
        $xmlText  = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
        $xmlText .= "<Project xmlns=\"http://schemas.microsoft.com/project\">\n";
        $xmlText .= "<SaveVersion>1</SaveVersion>\n";
        $xmlText .= "<Name>" . $this->db->table('matice')->get($matriceID)->nazev . ".xml</Name>\n";
        $xmlText .= "<Title>Plan projektu_" . $this->db->table('matice')->get($matriceID)->nazev . "</Title>\n";
        $xmlText .= "<Author>Ones</Author>\n";

        $xmlText .= "<LastSaved>" . date("Y-m-d\TG:i:s") . "</LastSaved>\n";
        $xmlText .= "<ScheduleFromStart>1</ScheduleFromStart>\n";
        $xmlText .= "<CalendarUID>3</CalendarUID>\n";
        $xmlText .= "<MinutesPerDay>1440</MinutesPerDay>\n";
        $xmlText .= "<MinutesPerWeek>10080</MinutesPerWeek>\n";
        $xmlText .= "<DaysPerMonth>100</DaysPerMonth>\n";
        $xmlText .= "<ProjectExternallyEdited>0</ProjectExternallyEdited>\n";
        
        // set calendar
        $xmlText .= "<Calendars>\n";
        $xmlText .= "<Calendar>\n";
        $xmlText .= "<UID>3</UID>\n";
        $xmlText .= "<Name>FullTime</Name>\n";
        $xmlText .= "<IsBaseCalendar>1</IsBaseCalendar>\n";
        $xmlText .= "<IsBaselineCalendar>0</IsBaselineCalendar>\n";
        $xmlText .= "<BaseCalendarUID>-1</BaseCalendarUID>\n";
        $xmlText .= "<WeekDays>\n";
        $xmlText .= "<WeekDay>\n";
        $xmlText .= "<DayType>1</DayType>\n";
        $xmlText .= "<DayWorking>1</DayWorking>\n";
        $xmlText .= "<WorkingTimes>\n";
        $xmlText .= "<WorkingTime>\n";
        $xmlText .= "<FromTime>00:00:00</FromTime>\n";
        $xmlText .= "<ToTime>00:00:00</ToTime>\n";
        $xmlText .= "</WorkingTime>\n";
        $xmlText .= "</WorkingTimes>\n";
        $xmlText .= "</WeekDay>\n";
        $xmlText .= "<WeekDay>\n";
        $xmlText .= "<DayType>2</DayType>\n";
        $xmlText .= "<DayWorking>1</DayWorking>\n";
        $xmlText .= "<WorkingTimes>\n";
        $xmlText .= "<WorkingTime>\n";
        $xmlText .= "<FromTime>00:00:00</FromTime>\n";
        $xmlText .= "<ToTime>00:00:00</ToTime>\n";
        $xmlText .= "</WorkingTime>\n";
        $xmlText .= "</WorkingTimes>\n";
        $xmlText .= "</WeekDay>\n";
        $xmlText .= "<WeekDay>\n";
        $xmlText .= "<DayType>3</DayType>\n";
        $xmlText .= "<DayWorking>1</DayWorking>\n";
        $xmlText .= "<WorkingTimes>\n";
        $xmlText .= "<WorkingTime>\n";
        $xmlText .= "<FromTime>00:00:00</FromTime>\n";
        $xmlText .= "<ToTime>00:00:00</ToTime>\n";
        $xmlText .= "</WorkingTime>\n";
        $xmlText .= "</WorkingTimes>\n";
        $xmlText .= "</WeekDay>\n";
        $xmlText .= "<WeekDay>\n";
        $xmlText .= "<DayType>4</DayType>\n";
        $xmlText .= "<DayWorking>1</DayWorking>\n";
        $xmlText .= "<WorkingTimes>\n";
        $xmlText .= "<WorkingTime>\n";
        $xmlText .= "<FromTime>00:00:00</FromTime>\n";
        $xmlText .= "<ToTime>00:00:00</ToTime>\n";
        $xmlText .= "</WorkingTime>\n";
        $xmlText .= "</WorkingTimes>\n";
        $xmlText .= "</WeekDay>\n";
        $xmlText .= "<WeekDay>\n";
        $xmlText .= "<DayType>5</DayType>\n";
        $xmlText .= "<DayWorking>1</DayWorking>\n";
        $xmlText .= "<WorkingTimes>\n";
        $xmlText .= "<WorkingTime>\n";
        $xmlText .= "<FromTime>00:00:00</FromTime>\n";
        $xmlText .= "<ToTime>00:00:00</ToTime>\n";
        $xmlText .= "</WorkingTime>\n";
        $xmlText .= "</WorkingTimes>\n";
        $xmlText .= "</WeekDay>\n";
        $xmlText .= "<WeekDay>\n";
        $xmlText .= "<DayType>6</DayType>\n";
        $xmlText .= "<DayWorking>1</DayWorking>\n";
        $xmlText .= "<WorkingTimes>\n";
        $xmlText .= "<WorkingTime>\n";
        $xmlText .= "<FromTime>00:00:00</FromTime>\n";
        $xmlText .= "<ToTime>00:00:00</ToTime>\n";
        $xmlText .= "</WorkingTime>\n";
        $xmlText .= "</WorkingTimes>\n";
        $xmlText .= "</WeekDay>\n";
        $xmlText .= "<WeekDay>\n";
        $xmlText .= "<DayType>7</DayType>\n";
        $xmlText .= "<DayWorking>1</DayWorking>\n";
        $xmlText .= "<WorkingTimes>\n";
        $xmlText .= "<WorkingTime>\n";
        $xmlText .= "<FromTime>00:00:00</FromTime>\n";
        $xmlText .= "<ToTime>00:00:00</ToTime>\n";
        $xmlText .= "</WorkingTime>\n";
        $xmlText .= "</WorkingTimes>\n";
        $xmlText .= "</WeekDay>\n";
        $xmlText .= "</WeekDays>\n";
        $xmlText .= "</Calendar>\n";
        $xmlText .= "</Calendars>\n";

        // process tasks
        $tasks = "<Tasks>\n";
        $resources = "<Resources>\n";
        $assignments = "<Assignments>\n";

        $internalID = 0;

        foreach ( $this->db->table('vystup')->where("matice", $matriceID) as $rowVystup )
        {	// select all outputs of choosen matrice
            $summaryUID = $internalID++;
            $tasks .= $this->newSummary($summaryUID, $rowVystup->nazev, $rowVystup->poradi);
            foreach ( $this->db->table('aktivita')->where("vystup", $rowVystup->id) as $rowAktivita)
            {	// select all tasks of choosen output
                $taskUID = $internalID++;
                $order = $rowVystup->poradi . "." . $rowAktivita->poradi; //"1.5"
                $tasks .= $this->newTask($taskUID, $rowAktivita->nazev, $rowAktivita->zacatek, $rowAktivita->konec, $order);
                $resOrder = 1;
                foreach (explode(',',$rowAktivita->zdroje) as $zdroj)
                {	// process all resources of choosen task
                    $resUID  = $internalID++;
                    $resources .= $this->newResource($resUID, $zdroj, $order . "." . $resOrder); // "1.5.2"
                    $resOrder++;
                    $assignments .= $this->newAssignments($internalID++, $resUID, $taskUID, $rowAktivita->zacatek);
                }
            }
        }

        $tasks       .= "</Tasks>\n";
        $resources   .= "</Resources>\n";
        $assignments .= "</Assignments>\n";

        // complete together
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
        $this->template->matrixMajitel = $this->db->table('matice')->get($id)->majitel;
        
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
		
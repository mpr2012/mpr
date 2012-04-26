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
    
    public function renderView($id)
    {
        $this->template->matrixId = $id;
        
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
            'zacatek'      => $cas_od,
            'konec'      => $cas_do,
            'nazev'      => $nazev,
            'vystup'      => $vys_id,
            'zdroje'     => $zdroje
        ));
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
    public function handleEdit_aktivity($id, $text, $akt_id)
    {
        // TODO
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

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
    
    public function handleEditZamer($id, $text)
    {
        $this->db->table('matice')->get($id)->update(array('zamer' => $text));
        $this->template->zamer  = $this->db->table('matice')->get($id)->zamer;
        $this->invalidateControl('zamer');
    }
    
}

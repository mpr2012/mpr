<?php

/**
 * Homepage presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class HomepagePresenter extends SecuredPresenter
{

	public function renderDefault()
	{
		$this->template->news = $this->db->table('novinka')->order('id DESC');
        $this->template->authors = $this->db->table('uzivatel');
	}
    
    public function createComponentInsertNewForm($name)
    {
        $form = new \Nette\Application\UI\Form($this, $name);
        
        $form->addGroup('Vložit novinku');
        $form->addHidden('author', $this->getUser()->getIdentity()->getId());
        $form->addText('header', 'Nadpis')
                ->setRequired('Vložte prosím nadpis.');
        $form->addTextArea('text', 'Text', 40, 5)
                ->setRequired('Vložte prosím text.');
        $form->addSubmit('submit', 'Vložit');
        $form->onSuccess[] = callback($this, 'insertNewFormSubmitted');
        
        return $form;
    }
    
    public function insertNewFormSubmitted(\Nette\Application\UI\Form $form)
    {
        $values = $form->getValues();
        try
        {
            $this->db->table('novinka')->insert($values);
            $this->flashMessage('Novinka úspěšně vložena.');
        }
        catch (\PDOException $e)
        {
            $this->flashMessage($e->getMessage(), 'error');
        }
        $this->redirect('Homepage:');
    }

}

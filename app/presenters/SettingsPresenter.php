<?php

/**
 * Homepage presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class SettingsPresenter extends SecuredPresenter
{
    
	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}
    
    public function createComponentChangeInfoForm($name)
    {
        $form = new \Nette\Application\UI\Form($this, $name);
        
        $form->addGroup('Informace');
        $form->addText('jmeno', 'Jméno:')
                ->setDefaultValue($this->db->table('uzivatel')->get($this->getUser()->getId())->jmeno);
        $form->addText('prijmeni', 'Příjmení:')
                ->setDefaultValue($this->db->table('uzivatel')->get($this->getUser()->getId())->prijmeni);
        $form->addSubmit('submit', 'Uložit');
        $form->onSuccess[] = callback($this, 'changeInfoFormSubmitted');
        
        return $form;
    }
    
    public function changeInfoFormSubmitted(\Nette\Application\UI\Form $form)
    {
        $values = $form->getValues();
        $this->db->table('uzivatel')->get($this->getUser()->getId())->update($values);
        $this->flashMessage('Vaše údaje byly změněny.');
        $this->redirect('Settings:');
    }
    
    public function createComponentChangePasswordForm($name)
    {
        $form = new \Nette\Application\UI\Form($this, $name);
        
        $form->addGroup('Změna hesla');
        $form->addPassword('old_pass', 'Původní heslo:')
                ->setRequired('Zadejte, prosím, své původní heslo.');
        $form->addPassword('new_pass', 'Nové heslo:')
                ->addRule(\Nette\Application\UI\Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaky.', 4)
                ->setRequired('Zadejte, prosím, nové heslo.');
        $form->addPassword('new_pass_2', 'Nové heslo (podruhé):')
                ->addRule(\Nette\Application\UI\Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaky.', 4)
                ->setRequired('Zadejte, prosím, heslo ještě jednou pro kontrolu.')
                ->addRule(\Nette\Application\UI\Form::EQUAL, 'Hesla se neshodují.', $form['new_pass']);
        $form->addSubmit('submit', 'Změnit heslo');
        $form->onSuccess[] = callback($this, 'changePasswordFormSubmitted');
        
        return $form;
    }
    
    public function changePasswordFormSubmitted(\Nette\Application\UI\Form $form)
    {
        $values = $form->getValues();
        $user = $this->db->table('uzivatel')->get($this->getUser()->getIdentity()->id);
        if (md5($values['old_pass']) == $user->password)
        {
            try
            {
                $user->update(array('password' => md5($values['new_pass'])));
                $this->flashMessage('Heslo bylo úspěšně změněno!','success');
            }
            catch(\PDOException $e)
            {
                $this->flashMessage($e->getMessage());
            }
        }
        else
        {
            $this->flashMessage('Nezadali jste správně staré heslo.', 'error');
        }
        $this->redirect('Settings:');
    }

}

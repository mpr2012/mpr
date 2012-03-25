<?php

use Nette\Application\UI,
	Nette\Security as NS;


/**
 * Sign in/out presenters.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class SignPresenter extends BasePresenter
{
    
    public function renderIn()
    {
        if ($this->getUser()->isLoggedIn()) {
            $this->redirect('Homepage:');
        }
    }

    /**
	 * Sign in form component factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		$form = new UI\Form;
        $form->addGroup('Ones - MPR 2012');
		$form->addText('username', 'E-mail:')
            ->setAttribute('size', '30')
            ->addRule(\Nette\Forms\Form::EMAIL, 'Prosím, překontrolujte správnost emailové adresy.')
			->setRequired('Prosím vyplňte uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
            ->setAttribute('size', '30')
			->setRequired('Prosím vyplňte heslo.');

		$form->addCheckbox('remember', 'Zapamatovat si mne na tomto počítači');

		$form->addSubmit('send', 'Přihlásit se');

		$form->onSuccess[] = callback($this, 'signInFormSubmitted');
		return $form;
	}



	public function signInFormSubmitted($form)
	{
		try {
			$values = $form->getValues();
			if ($values->remember) {
				$this->getUser()->setExpiration('+ 14 days', FALSE);
			} else {
				$this->getUser()->setExpiration('+ 20 minutes', TRUE);
			}
			$this->getUser()->login($values->username, $values->password);
            $this->flashMessage('Vítejte v portálu týmu Ones předmětu MPR 2012!', 'info');
			$this->redirect('Homepage:');

		} catch (NS\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}



	public function actionOut()
	{
		$this->getUser()->logout();
		$this->redirect('in');
	}

}

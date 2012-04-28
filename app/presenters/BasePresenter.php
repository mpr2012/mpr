<?php

/**
 * Base class for all application presenters.
 *
 * @author     John Doe
 * @package    MyApplication
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    
    protected $db;
    protected $user;
    
    public function __construct(Nette\DI\IContainer $context) {
        parent::__construct($context);
        
        $this->db = $context->database;
        $this->template->userData = $this->getUser()->getIdentity()->data;
    }
    
    public function handleSignOut()
    {
        $this->getUser()->logout();
        $this->redirect('Sign:in');
    }
    
}

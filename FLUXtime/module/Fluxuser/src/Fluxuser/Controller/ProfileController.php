<?php

namespace Fluxuser\Controller;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Fluxuser\Form\ConfirmResetpasswordForm;
use Fluxuser\Form\ConfirmuserForm;
use Fluxuser\Form\ResetpasswordForm; 
use Fluxuser\Form\ProfileForm;
use Fluxuser\Utils\ActionHelper;
use Fluxuser\Entity\Email;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use DateTime;
use Zend\Validator\Regex; 

class ProfileController extends ActionHelper {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Når man har ønsket at få reset sit password
     * @return type form
     */
    public function resetpasswordAction() {
        $request = $this->getRequest();
        $form = new ResetPasswordForm();
        //Submit form
        if ($request->isPost()) {
            //Validering
            $form->setInputFilter($form->getInputFilterSpecification());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $user = $this->getUsermapper()->findOneBy(array('workEmail' => $data['workEmail']));
                // type to er reset password email
                $emailtype = $this->getEmailtypemapper()->find(2);
                $email = new Email();
                $email->setEmailtypefk($emailtype);
                $senttime = new DateTime(date("Y-m-d H:i:s"));
                $email->setSenttime($senttime);
                $email->setUserfk($user);
                $email->setFkaccountid($user->getFkaccountid());
                $this->getEntityManager()->persist($email);
                // send reset password email
                $this->sendResetmessage($user->getUsername(), $user->getWorkEmail(), $this->encrypt($email->getId(), SECRET_KEY), $data['username']);
                //Gemmer email i databasen
                $this->getEntityManager()->flush();
                // Redirect til login
                return $this->redirect()->toRoute('login');
            }
        }
        return array(
            'form' => $form
        );
    }

    /**
     * Edit profile
     * @return type form
     */
    public function editAction() {

        $request = $this->getRequest();
        $identity = $this->identity();
        $id = $this->params()->fromRoute('id');
        //Submit form
        if ($request->isPost()) {
            $data = $request->getPost();
            $user = $this->getUsermapper()->find($id);
            $form = new ProfileForm($this->getEntityManager());
            $form->setData($data);
            $form->get('username')->setAttributes(array(
                'readonly' => 'readonly',
            ));
            $form->get('firstname')->setAttributes(array(
                'readonly' => 'readonly',
            ));
            $form->get('lastname')->setAttributes(array(
                'readonly' => 'readonly',
            ));
            $validator = new Regex(array('pattern' => '/^[a-zA-Z0-9]+$/'));
            $v = 1;
            if ($form->get('password')->getValue() != null) {
                $v = $validator->isValid($form->get('password')->getValue());
            }
            //Hvis password correct
            if ($this->decryptMessage($data['oldpassword'], $identity->getPassword())) {
                //Validering
                $form->setInputFilter($form->getInputFilterSpecification());
                if ($form->isValid() && $v == 1) {
                    if ($data['password'] !== "") {
                        $encryptedPassword = $this->encryptMessage($data['password']);
                        $user->setPassword($encryptedPassword);
                    }
                    $user->setPhonePrivate($data['phonePrivate']);
                    $user->setPrivateEmail($data['privateEmail']);
                    //Cache  til hukommelsen
                    $this->getEntityManager()->persist($user);
                    //Gemmer user i db
                    $this->getEntityManager()->flush();
                    // Redirect to list of tasks
                    return $this->redirect()->toRoute('mytask');
                } else if ($v != 1) {
                    $form->get('password')->setMessages(array('Only letters and numbers allowed'));
                    $id = $user->getId();
                    return array('id' => $id, 'form' => $form, 'messages' => $form->getMessages());
                }
            } else {
                $form->get('oldpassword')->setMessages(array('Incorrect password'));
                $id = $user->getId();
                return array('id' => $id, 'form' => $form, 'messages' => $form->getMessages());
            }
        }
        $id = $this->decrypt($this->params()->fromRoute('id'), SECRET_KEY);
        //Hvis user id er null
        if (!$id || $identity->getId() != $id) {
            //Redirect til task-liste
            return $this->redirect()->toRoute('mytask');
        }
        try {
            $user = $this->getUsermapper()->find($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('mytask');
        }
        $form = $this->prepareProfileform($user);
        $id = $user->getId();
        return array('id' => $id, 'form' => $form, 'messages' => $form->getMessages());
    }

    /**
     * Prepare profile form - edit profile
     * @param type $user
     * @return ProfileForm
     */
    private function prepareProfileform($user) {
        $form = new ProfileForm($this->getEntityManager());
        // set hydrator to populate form - sætter user's data i form
        $form->setHydrator(new DoctrineHydrator($this->getEntityManager(), 'Fluxuser\Entity\Fluxuser'));
        $form->bind($user);
        $form->get('username')->setAttributes(array(
            'readonly' => 'readonly',
        ));
        $form->get('firstname')->setAttributes(array(
            'readonly' => 'readonly',
        ));
        $form->get('lastname')->setAttributes(array(
            'readonly' => 'readonly',
        ));
        return $form;
    }

    /**
     * Confirm the the welcome email and start using the user
     * @return ViewModel
     */
    public function confirmuserAction() {
        $messages = array();
        $request = $this->getRequest();
        // get the url id and decrypt first urlencode and then the decryption method
        $id = $this->decrypt($this->params()->fromRoute('id'), SECRET_KEY);
        // if the id is not in url, then redirect to login
        if (!$id) {
            return $this->redirect()->toRoute('login');
        }
        $form = new ConfirmuserForm();
        // find emails the type of welcome mail 
        $email = $this->getEmailMapper()->find($id);
        if ($email == null) {
            return $this->redirect()->toRoute('login');
        }
        // email only have a timestamp which have been send no more than 24 hours ago
        $onedayago = new DateTime('now');
        $onedayago->modify("-1 days");
        if ($onedayago->getTimestamp() > $email->getSenttime()->getTimeStamp()) {
            $messages[] = 'This subscription is cancelled. Please require a new one';
            return new ViewModel(array('cancelled' => true, 'messages' => $messages));
        }
        $id = $email->getUserfk();
        //Hent udvalgt user i db hvor state er 1
        $user = $this->getUsermapper()->findOneBy(array('id' => $id, 'state' => 2));
        if (!$user) {
            return $this->redirect()->toRoute('login');
        } else if ($request->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $data = $request->getPost();
            $form->setData($data);
            if ($form->isValid($data)) {
                //Hent udvalgt user i db hvor state er 2
                $user = $this->getUsermapper()->findOneBy(array('workEmail' => $data['workEmail'], 'state' => 2));
                //  Authenticate
                if ($user != null && $this->decryptMessage($data['temppassword'], $user->getPassword())) {
                    // encrypt the new password
                    $encryptedPassword = $this->encryptMessage($data['newpassword']);
                    $user->setPassword($encryptedPassword);
                    // change state to 1 
                    $user->setState(1);
                    //Cache til hukommelsen
                    $this->getEntityManager()->persist($user);
                    //Gemmer user i db
                    $this->getEntityManager()->flush();
                    // Redirect to list of tasks
                    return $this->redirect()->toRoute('login');
                } else {
                    $messages[] = 'Incorrect creadentials';
                }
            } else {
                $messages[] = 'Incorrect creadentials';
            }
        }
        return new ViewModel(array('form' => $form, 'messages' => $messages));
    }

    /**
     * Confirm the the welcome email and start using the user
     * @return ViewModel
     */
    public function confirmresetpasswordAction() {
        $messages = array();
        $request = $this->getRequest();
        // get the url id and decrypt first urlencode and then the decryption method
        $id = $this->decrypt($this->params()->fromRoute('id'), SECRET_KEY);
        // if the id is not in url, then redirect to login
        if (!$id) {
            return $this->redirect()->toRoute('login');
        }
        $form = new ConfirmResetpasswordForm();
        // find emails the type of welcome mail 
        $email = $this->getEmailmapper()->findOneBy(array('id' => $id, 'emailtypefk' => 2));
        // email only have a timestamp wich have been send no more than 24 hours ago
        $onedayago = new DateTime('now');
        $onedayago->modify("-1 days");
        if ($onedayago->getTimestamp() > $email->getSenttime()->getTimeStamp()) {
            $messages[] = 'This subscription is cancelled. Please require a new one';
            return new ViewModel(array('cancelled' => true, 'messages' => $messages));
        }
        $id = $email->getUserfk();
        //Hent udvalgt user i db hvor state er 1
        $user = $this->getUsermapper()->find($id);
        if (!$user) {
            return $this->redirect()->toRoute('login');
        } else if ($request->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $data = $request->getPost();
            $form->setData($data);

            if ($form->isValid($data)) {
                $user = $this->getUsermapper()->findOneBy(array('workEmail' => $data['workEmail']));
                // found a username and authenticate
                if ($user != null) {
                    // encrypt the new password
                    $encryptedPassword = $this->encryptMessage($data['newpassword']);
                    // change credentials on user
                    $user->setPassword($encryptedPassword);
                    // change type
                    $user->setState(1);
                    //Cache til hukommelsen
                    $this->getEntityManager()->persist($user);
                    //Gemmer user i db
                    $this->getEntityManager()->flush();
                    // Redirect to list of tasks
                    return $this->redirect()->toRoute('login');
                } else {
                    $messages[] = 'Incorrect creadentials';
                }
            } else {
                $messages[] = 'Incorrect creadentials';
            }
        }
        return new ViewModel(array('form' => $form, 'messages' => $messages));
    }

    /**
     * Changes language DK/UK
     */
    public function changeLanguageAction() {
        $translator = $this->getServiceLocator()->get('translator');
        // New Container will get he Language Session if the SessionManager already knows the language session.
        $session = new Container('language');
        if ($translator->getLocale() === $session->offsetGet('English')) {
            $session->offsetSet('Current', $session->offsetGet('Danish'));
        } else {
            $session->offsetSet('Current', $session->offsetGet('English'));
        }
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        $this->redirect()->toUrl($url);
    }

    /**
     * PROTECTED METHODS
     */

    /**
     * Sets the EntityManager
     * @param EntityManager $em
     * @access protected
     * @return PostController
     */
    protected function setEntityManager(EntityManager $em) {
        $this->entityManager = $em;
        return $this;
    }

    /** 
     * Returns the EntityManager
     * Fetches the EntityManager from ServiceLocator if it has not been initiated
     * and then returns it
     * @access protected
     * @return EntityManager
     */
    protected function getEntityManager() {
        if (null === $this->entityManager) {
            $this->setEntityManager($this->getServiceLocator()->get('doctrine.entitymanager.orm_default'));
        }
        return $this->entityManager;
    }

}
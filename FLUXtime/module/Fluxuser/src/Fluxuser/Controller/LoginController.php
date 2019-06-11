<?php

namespace Fluxuser\Controller;

use Fluxuser\Form\LoginForm;
use Fluxuser\Utils\GoogleAuth;
use Google_Client;
use Google_Service_Plus;
use Zend\Authentication\AuthenticationService;
use Fluxuser\Utils\ActionHelper;
use Zend\View\Model\ViewModel;
use Fluxuser\Entity\Email;
use Doctrine\ORM\EntityManager;
use DateTime;

class LoginController extends ActionHelper {

    /**
     * Instance of GoogleAuth.
     * @see GoogleAuth
     * @var GoogleAuth 
     */
    protected $googleAuth;

    /**
     * If you are logged in go to my tasks
     */
    private function checkBootURL() {
        if ($this->identity() !== NULL) {
            $role = $this->identity()->getFkuserrole()->getPermissiongroup();
            if ($role != 'guest') {
                $this->redirect()->toRoute('mytask');
            }
        }
    }

    /**
     * User Login with password validation
     * @return ViewModel
     */
    public function loginAction() {
        // redirect if logged in
        $this->checkBootURL();
        // error messages
        $messages = array();
        // form
        $form = new LoginForm($this->getEntityManager());
        $form->get('submit')->setValue('Login');
        $request = $this->getRequest();

        if ($this->getGoogleAuth()->checkRedirectCode()) {
            $messages = $this->googleLogin();
        } else
        // hvis nogen har trykket submit knap i form
        if ($request->isPost()) {
            $form->setInputFilter($form->getInputFilterSpecification());
            $data = $request->getPost();
            $form->setData($data);
            // hvis formen er korrekt udfyldt
            if ($form->isValid($data)) {
                //Hent udvalgt user i db hvor state er 1
                $user = $this->getUsermapper()->findOneBy(array('workEmail' => $data['workEmail'], 'state' => 1));
                // found a username
                // først decrypter man password og tjecker altså med det i database
                // derefter opretter man en user identification object som man kan få fat på alle steder                
                if ($user != null && $this->decryptMessage($data['password'], $user->getPassword()) && $user->getFkaccountid()->getActive() == 1 && $user->getFkaccountid()->getState() == 1) {
                    // Get our authentication adapter and check credentials
                    $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
                    $adapter = $authService->getAdapter();
                    $username = $user->getUsername();
                    $adapter->setIdentity($username);
                    // vi har tjekket password så vi kan godt bruge det fra formen
                    $adapter->setCredential($user->getPassword());
                    $authService = new AuthenticationService();
                    $result = $authService->authenticate($adapter);
                    $messages[] = $result->getMessages();
                    return $this->redirect()->toRoute('mytask'); //?
                } else if ($user === null) {
                    $messages[] = 'User does not exist or is not active';
                } else {
                    $messages[] = 'Incorrect creadentials';
                }
            } else {
                $messages[] = $form->getMessages();
            }
        }
        return new ViewModel(array('form' => $form, 'messages' => $messages, 'googleAuth' => $this->getGoogleAuth()));
    }

    private function googleLogin() {
        $payload = $this->getGoogleAuth()->getPayload();
        $user = $this->getUsermapper()->findOneBy(array('workEmail' => $payload['email'], 'state' => 1));
        
        /** If $user is not set, it's because the email does not exists in our database * */
        if (isset($user) && $user->getFkaccountid()->getActive() == 1 && $user->getFkaccountid()->getState() == 1) {
            $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
            $adapter = $authService->getAdapter();
            $adapter->setIdentityValue($user->getUsername());
            // vi har tjekket password så vi kan godt bruge det fra formen
            $adapter->setCredentialValue($user->getPassword());
            $authService = new AuthenticationService();
            $result = $authService->authenticate($adapter);
            return $this->redirect()->toRoute('mytask');
        } else {
            $form = new LoginForm($this->getEntityManager());
            $form->get('submit')->setValue('Login');
            $messages = array();
            $messages[] = 'No such user with the email: ' . $payload['email'];
            return $messages;
        }
    }

    /**
     * Logout and clear user identity
     * Send email if running task & stop task
     */
    public function logoutAction() {
        $identity = $this->identity();
        $task = $this->getRunningTask();
        if ($task != null) {
            $emailtype = $this->getEmailtypemapper()->find(3);
            $email = new Email();
            $email->setEmailtypefk($emailtype);
            $senttime = new DateTime(date("Y-m-d H:i:s"));
            $email->setSenttime($senttime);
            $email->setUserfk($identity);
            $email->setFkaccountid($identity->getFkaccountid());
            $task->setTimestop($senttime);
            $this->getEntityManager()->persist($email);
            $this->getEntityManager()->persist($task);
            // send reset password email
            $this->sendLogoutmessage($identity->getUsername(), $identity->getWorkEmail());
            //Gemmer email i databasen
            $this->getEntityManager()->flush();
        }
        $auth = new AuthenticationService();
        $auth->clearIdentity();
        return $this->redirect()->toRoute('login');
    }

    /**
     * Gets the active task
     * @return type
     */
    private function getRunningTask() {
        $identity = $this->identity();
        $taskowners = $this->getOwnermapper()->findBy(array('fkuserid' => $identity));
        if ($taskowners == null) { return null; }
        foreach ($taskowners as $taskowner) {
            $timereg = $this->getTimeregmapper()->findOneBy(array('fktaskownerid' => $taskowner, 'timestop' => null, 'state' => 1));
        }
        return $timereg;
    }

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

    /**
     * If GoogleAuth is not initialized, we initialize it with the credentials 
     * provided by Google in the config file, and return the GoogleAuth instance.
     * @return GoogleAuth @see GoogleAuth
     * @access private
     */
    private function getGoogleAuth() {
        if (!isset($this->googleAuth)) {
            $config = $this->getServiceLocator()->get('Config');
            $googleConfig = $config['google'];

            $googleClient = new Google_Client;
            $googleClient->setClientId($googleConfig['id']);
            $googleClient->setClientSecret($googleConfig['secret']);
            $googleClient->setRedirectUri($googleConfig['redirect']);

            $googleClient->setScopes(array(Google_Service_Plus::USERINFO_PROFILE, Google_Service_Plus::USERINFO_EMAIL, Google_Service_Plus::PLUS_LOGIN, Google_Service_Plus::PLUS_ME));

            // Remember to remove this line, so we don't force the user to approve our app every time!
            $googleClient->setApprovalPrompt('force');

            $this->googleAuth = new GoogleAuth($googleClient);
        }
        return $this->googleAuth;
    }

}

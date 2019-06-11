<?php

namespace Fluxuser\Utils;

use Doctrine\ORM\EntityManager;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\Transport;
use Zend\Mail\Transport\Sendmail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DateTime;

define('SECRET_KEY', 'qmQ3x4eE$m3Gxgj7'); // salt   

class ActionHelper extends AbstractActionController {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Encrypt message with Rijndael_256 - password
     * @param type $message
     * @return type
     */
    public function encryptMessage($message) {
        // Create the secure password.
        return password_hash($message, PASSWORD_DEFAULT, ['cost' => 11]);
    }

    /**
     * Decrypt message with buildin password encryption - password 
     * build on blowfish and BCrypt
     * @param type $message
     * @return type
     */
    public function decryptMessage($message, $fromDB) {
        return password_verify($message, $fromDB);
    }

    /**
     * Returns an encrypted & utf8-encoded
     */
    public function encrypt($pure_string, $encryption_key) {
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $encrypted_string = urlencode(mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, $pure_string, MCRYPT_MODE_ECB, $iv));
        return $encrypted_string;
    }

    /**
     * Returns decrypted original string
     */
    public function decrypt($encrypted_string, $encryption_key) {
        $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypted_string = urldecode(mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv));
        return $decrypted_string;
    }

     
    /**
     * Send en welcome email med sikkerhed
     * @param type $name
     * @param type $setTo
     * @param type $emailId
     * @param type $username
     * @param type $password
     * @return type
     */
    public function sendWelcomemessage($name, $setTo, $emailId, $username, $password) {
        $this->renderer = $this->getServiceLocator()->get('ViewRenderer');

        // Get the email from file and correct the dynamic content
        $content = $this->renderer->render($this->getWelcomeMail($name, $emailId, $username, $password, $setTo), null);

        // make a header as html
        $html = new MimePart($content);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($html,));

        $mail = new Mail\Message();
        $mail->setBody($body);
        $mail->setFrom('noreply@supeo.dk', 'Supeo - FluxTime');
        $mail->addTo($setTo, $name);
        $mail->setSubject('Welcome to FluxTime');

        $transport = new Transport\Sendmail();
        $transport->send($mail);
        return array();
    }

    /**
     * Get the correct view to welcome mail
     * @param type $name
     * @param type $emailId
     * @param type $username
     * @param type $password
     * @return ViewModel
     */
    public function getWelcomeMail($name, $emailId, $username, $password, $setTo) {
        $view = new ViewModel(array('name' => $name, 'emailId' => $emailId, 'username' => $username, 'password' => $password, 'setTo' => $setTo));
        $view->setTemplate('fluxuser/email/welcomemail');
        return $view;
    }

    /**
     * Send email reset password
     * @param type $name
     * @param type $setTo
     * @param type $emailId
     * @param type $password
     * @return type
     */
    public function sendResetmessage($name, $setTo, $emailId, $password) {
        $this->renderer = $this->getServiceLocator()->get('ViewRenderer');
        $content = $this->renderer->render($this->getResetEmail($name, $emailId, $password, $setTo), null);

        // make a header as html
        $html = new MimePart($content);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($html,));

        $mail = new Message();
        $mail->setBody($body);
        $mail->setFrom('noreply@supeo.dk', 'Supeo - FluxTime');
        $mail->addTo($setTo, $name);
        $mail->setSubject('FluxTime - reset password');

        $transport = new Sendmail();
        $transport->send($mail);
        return array();
    }

    /**
     * Get the correct View to reset email
     * @param type $name
     * @param type $emailId
     * @param type $username
     * @param type $password
     * @return ViewModel
     */
    public function getResetEMail($name, $emailId, $password, $setTo) {
        $view = new ViewModel(array('name' => $name, 'emailId' => $emailId, 'password' => $password, 'setTo' => $setTo));
        $view->setTemplate('fluxuser/email/resetemail');
        return $view;
    }

    //Usermapper
    public function getUsermapper() {
        $userMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\FluxUser');
        return $userMapper;
    }

    //Projectmapper
    public function getProjectmapper() {
        $projectMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Project');
        return $projectMapper;
    }

    //Labelmapper
    public function getLabelmapper() {
        $labelMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Projectlabel');
        return $labelMapper;
    }

    //Ownermapper
    public function getOwnermapper() {
        $ownerMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Taskowner');
        return $ownerMapper;
    }

    //Taskmapper
    public function getTaskmapper() {
        $taskMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Task');
        return $taskMapper;
    }

    //Projectusermapper
    public function getProjectusermapper() {
        $memberMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Projectuser');
        return $memberMapper;
    }

    //Projectusermapper
    public function getTimeregmapper() {
        $timeregMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Timereg');
        return $timeregMapper;
    }

    //Emailmapper
    public function getEmailmapper() {
        $emailMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Email');
        return $emailMapper;
    }

    //Emailtypemapper
    public function getEmailtypemapper() {
       return $this->getEntityManager()->getRepository('Fluxuser\Entity\Emailtype');
    }

    //Accountmapper
    public function getAccountmapper() {
        $accountMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Systemaccount');
        return $accountMapper;
    }

    //Rolemapper
    public function getRolemapper() {
        $roleMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Usergroup');
        return $roleMapper;
    }
    
     //Clientmapper
    public function getClientmapper() {
        $clientMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Client');
        return $clientMapper;
    }
    
     //Accountclientmapper
    public function getAccountclientmapper() {
        $accountclientMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Accountclient');
        return $accountclientMapper;
    }
    
     //Contactmapper
    public function getContactmapper() {
        $contactMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Contact');
        return $contactMapper;
    }
    
     //projectcontactmapper
    public function getProjectcontactmapper() {
        $projectcontactMapper = $this->getEntityManager()->getRepository('Fluxuser\Entity\Projectcontact');
        return $projectcontactMapper;
    }
    
     /**
     * Send en mail hvis task er stoppet ved logout
     * @param type $name
     * @param type $setTo
     * @return type array
     */
    public function sendLogoutmessage($name, $setTo) {
        $this->renderer = $this->getServiceLocator()->get('ViewRenderer');

        // Get the email from file and correct the dynamic content
        $content = $this->renderer->render($this->getLogoutMail($name), null);

        // make a header as html
        $html = new MimePart($content);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($html,));

        $mail = new Mail\Message();
        $mail->setBody($body);
        $mail->setFrom('noreply@supeo.dk', 'Supeo - FluxTime');
        $mail->addTo($setTo, $name);
        $mail->setSubject('Task stopped by logout');

        $transport = new Transport\Sendmail();
        $transport->send($mail);
        return array();
    }

    /**
     * Get the correct view of logoutmail
     * @param type $name
     * @return ViewModel
     */
    public function getLogoutMail($name) {
        $view = new ViewModel(array('name' => $name));
        $view->setTemplate('fluxuser/email/logoutmail');
        return $view;
    }
    
      /**
     * Send en mail hvis task er stoppet ved logout
     * @param type $name
     * @param type $setTo
     * @return type array
     */
    public function sendMidnightmessage($name, $setTo) {
        $this->renderer = $this->getServiceLocator()->get('ViewRenderer');

        // Get the email from file and correct the dynamic content
        $content = $this->renderer->render($this->getMidnightMail($name), null);

        // make a header as html
        $html = new MimePart($content);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($html,));

        $mail = new Mail\Message();
        $mail->setBody($body);
        $mail->setFrom('noreply@supeo.dk', 'Supeo - FluxTime');
        $mail->addTo($setTo, $name);
        $mail->setSubject('Task stopped by logout');

        $transport = new Transport\Sendmail();
        $transport->send($mail);
        return array();
    }

    /**
     * Get the correct view of logoutmail
     * @param type $name
     * @return ViewModel
     */
    public function getMidnightMail($name) {
        $view = new ViewModel(array('name' => $name));
        $view->setTemplate('fluxuser/email/midnightmail');
        return $view;
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



}

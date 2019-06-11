<?php

namespace Fluxuser\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Fluxuser\Entity\Email;
use Fluxuser\Entity\Client;
use Fluxuser\Entity\Fluxuser;
use Fluxuser\Entity\Systemaccount;
use Fluxuser\Entity\Accountclient;
use Fluxuser\Form\AccountEditForm;
use Fluxuser\Form\AccountForm;
use Fluxuser\Form\SearchAccountForm;
use Fluxuser\Utils\ActionHelper;
use Zend\View\Model\JsonModel;



class AccountController extends ActionHelper {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct() {
        
    }

   /**
     * Index - viser system accounts
     * @return type form, accounts, editform
     */
    public function indexAction() {
        //Henter request 
        $request = $this->getRequest();
        //Ny form laves 
        $form = new SearchAccountForm($this->getEntityManager());
        $editform = new AccountEditForm($this->getEntityManager());
        //Hent alle accounts hvor state 1  
        $accounts = $this->getAccountmapper()->findBy(array('state' => 1), array('accountid' => 'DESC'));
        $accountclients = $this->getAccountclientmapper()->FindAll();
        //Hvis click på button i form
        if ($request->isPost()) {
            $data = $request->getPost();
            $search = $data['search'];
            if ($search !== "") {
                $collection = new ArrayCollection($accounts);
                $account = $this->getAccountmapper()->find(1);
                $client = $this->getClientmapper()->findOneBy(array('clientname' => $search, 'state' => 1, 'fkaccountid' => $account));
                 $field = 'state';
                $accountid = 100;
                if($client != null){
                $accclient = $this->getAccountclientmapper()->FindOneBy(array('fkclientid' => $client));
                if($accclient != null){
                $accountid = $accclient->getFkaccountid()->getAccountid();
                $field = 'accountid';
                }   
                }
                $criteria = Criteria::create()
                        ->where(Criteria::expr()->eq("customer", $search))
                        ->orWhere(Criteria::expr()->eq("customerid", $search))
                         ->orWhere(Criteria::expr()->eq($field, $accountid));
                $accounts = $collection->matching($criteria);
                return array('accounts' => $accounts, 'form' => $form, 'accountclients' => $accountclients,  'editform' => $editform,);
            }
        }
        //Return accounts
        return array('accounts' => $accounts,
            'accountclients' => $accountclients,
            'form' => $form,
            'editform' => $editform,
        );
    }

    public function addAction() {
        //Henter request 
        $request = $this->getRequest();
        //Ny form laves 
       $form = $this->prepareAddForm();
        //Hvis click på button på form
        if ($request->isPost()) {
             $form->setData($request->getPost());
            
            //Inputfilter sættes på formen (validering)
            $form->setInputFilter($form->getInputFilterSpecification());       //Sæt indtastede data på formen
           $exists = $this->recordExistCreate('customer', $form->get('customer')->getValue());
           $emailExists = $this->emailExistCreate('workEmail', $form->get('workEmail')->getValue());
           if(!$exists && !$emailExists){
              $combo = $form->get('client');
            $form->remove('client'); 
           }
            //Hvis form-data er valid
            if ($form->isValid() && !$exists && !$emailExists) {
                $user = new Fluxuser();
                //Henter data ud af formen
                $data = $form->getData();
                //Create and get new account
                $clientid = $combo->getValue();
                if ($clientid != null) {
                    $client = $this->getClientmapper()->find($clientid);
                } else {
                    $client = new Client();
                }
                $newaccount = $this->createAccount($data, $client);
                // encrypt the temporary password
                $encryptedPassword = $this->encryptMessage($data['password']);
                // tilføj encrypted password til data array
                $data['password'] = $encryptedPassword;
                //Sætter user-fields til form-data  
                $userrole = $this->getRolemapper()->find(1);
                $newuser = $this->exchangeArray($data, $user, $userrole, $newaccount);
                //Cache færdig user til hukommelse
                $this->getEntityManager()->persist($newuser);
                //Prepare email
                $email = $this->prepareEmail($newuser, $newaccount);
                $this->getEntityManager()->persist($email);
                //encrypt id så ingen kan let kan ændre på andre information
                $encryptedid = $this->encrypt($email->getId(), SECRET_KEY);
                // send welcome mail
                $this->sendWelcomemessage($data['firstname'], $data['workEmail'], $encryptedid, $data['username'], $form->getData()['password']);
                //Gemmer user i databasen
                $this->getEntityManager()->flush();
                // Redirect til liste med alle users hvor state er 1
                return $this->redirect()->toRoute('account');
            } else{
                if($exists){
                $form->get('customer')->setMessages(array('Already exists'));
                $form->get('client')->setMessages(array(' '));}
                 if($emailExists){
                $form->get('workEmail')->setMessages(array('Already exists'));
                $form->get('client')->setMessages(array(' '));}
            }
        }
        return array('form' => $form,);
    }

    /**
     * Creates and returns account 
     * @return type account
     */
    private function createAccount($data, $client) {
        $account = new Systemaccount();
        $account->setActive($data['active']);
        $account->setDescription($data['description']);
        $account->setState(1);
        $account->setCustomer($data['customer']);
        $account->setCustomerid($data['customerid']);

        $this->getEntityManager()->persist($account);
        $this->getEntityManager()->flush($account);
        $accountid = $account->getAccountid();
        $newaccount = $this->getAccountmapper()->find($accountid);
        if ($client->getClientid() != null) {
            $accountclient = new Accountclient();
            $accountclient->setFkaccountid($newaccount);
            $accountclient->setFkclientid($client);
            $this->getEntityManager()->persist($accountclient);
             $this->getEntityManager()->flush($accountclient);
        }
        return $newaccount;
    }

    /**
     * Prepare welcome email for admin user for new account
     * @param type $newuser
     * @param type $newaccount
     * @return Email
     */
    private function prepareEmail($newuser, $newaccount) {
        // create email in database
        $emailtype = $this->getEmailtypemapper()->findOneBy(array('id' => 1));
        // find emails the type of welcome mail 
        $email = new Email();
        $email->setEmailtypefk($emailtype);
        $senttime = new DateTime(date("Y-m-d H:i:s"));
        $email->setSenttime($senttime);
        // tilføj bruger til email
        $email->setUserfk($newuser);
        $email->setFkaccountid($newaccount);
        return $email;
    }
    
    private function prepareAddForm(){
         $form = new AccountForm($this->getEntityManager());
         $account = $this->getAccountmapper()->find(1);
        $userrole = $this->getRolemapper()->find(1);
        $combo = $form->get('fkuserrole');
        $combo->setValueOptions(array($userrole->getId() => $userrole->getPermissiongroup()));
        $clientcombo = $form->get('client');
        $clientcombo->setEmptyOption('Please select client');
        $clients = $this->getClientmapper()->findBy(array('fkaccountid' => $account, 'state' => 1));
        $list = [];
        if (count($clients) > 0) {
            foreach ($clients as $client) {
                $list[$client->getClientid()] = $client->getClientname();
            }
            $clientcombo->setValueOptions($list);
        } else {
            $clientcombo->setValueOptions(array(0 => ''));
        }
        return $form;
    }

    /**
     * Ajax - Slet account(set state 0)
     * @return JsonModel
     */
    public function ajaxdeleteAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $data = $request->getPost();
        }
        $id = $data['id'];
        $ok = $this->delete($id);
        $result['status'] = $ok;
        //Return result
        return new JsonModel($result);
    }

    /**
     * Slet account - set state 0
     * @return boolean
     */
    private function delete($id) {
         //Hvis id er null
        if (!$id) {
            //Redirect til liste med accounts 
            return $this->redirect()->toRoute('account');
        }
        //Henter i db
        $account = $this->getAccountmapper()->find($id);
        //Sæt state til 0
        $account->setState(0);
        //Cache til hukommelse
        $this->getEntityManager()->persist($account);
        //Gemmer i db
        $this->getEntityManager()->flush();
        //Return status
        return true;
    }

    /**
     * Ajax - edit account
     * @return JsonModel
     */
    public function ajaxeditAction() {
        $form = new AccountEditForm($this->getEntityManager());
        // Henter data og sætter form
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $form->setData($this->getRequest()->getPost());
        }
        //Returnerer result
        $status = $this->editaccount($form);
        $result['status'] = $status;
        return new JsonModel($result);
    }

    /**
     * Edit account
     * @param type $form
     * @return boolean
     */
    private function editaccount($form) {
        //Validere form
        $form->setInputFilter($form->getInputFilterSpecification());
       $value = $form->get('customer')->getValue();
       $id = $form->get('accountid')->getValue();
        $exists = $this->recordExistEdit('customer', $value, $id);
        if ($form->isValid() && !$exists) {
            //Henter object fra db
            $account = $this->getAccountmapper()->find($form->get('accountid')->getValue());
            //Sætter label
            $account->setCustomer($form->get('customer')->getValue());
            $account->setCustomerid($form->get('customerid')->getValue());
            $account->setDescription($form->get('description')->getValue());
            $account->setActive($form->get('active')->getValue());
            //Cache data til hukommelse
            $this->getEntityManager()->persist($account);
            //Gemmer label i db 
            $this->getEntityManager()->flush();
            //Returnerer auto increment
            return true;
        } else if($exists){
            return false;
        }
    }

    /**
     * Check if value exists in Database - create account
     * @param type $field
     * @param type $value
     */
    private function recordExistCreate($field, $value) {
        //Hent udvalgt i db
        $account = $this->getAccountmapper()->findOneBy(array($field => $value));
        $exists = true;
        if ($account === NULL) {
          $exists = false;
        } 
        return $exists;
    }
    
    /**
     * Check if value exists in Database - edit account
     * @param type $field
     * @param type $value
     * @param type $id
     * @return boolean
     */
    private function recordExistEdit($field, $value, $id) {
        //Hent udvalgt i db
        $account = $this->getAccountmapper()->findOneBy(array($field => $value));
        if ($account === NULL) {
          return false;
        } 
        else {
          if($account->getAccountid() === $id){
           return false;   
          }  if($account->getAccountid() != $id){
           return true;   
          }
        } 
    }

    /**
     * Sætter fields til formdata (input fra brugeren)
     * @param type $data
     * @param type $user
     * @param type $userrole
     * @param type $account
     * @return type user
     */
    protected function exchangeArray($data, $user, $userrole, $account) {
        $user->setEmployeeId((isset($data['employeeId'])) ? $data['employeeId'] : null);
        $user->setFirstname((isset($data['firstname'])) ? $data['firstname'] : null);
        $user->setLastname((isset($data['lastname'])) ? $data['lastname'] : null);
        $user->setPhone((isset($data['phone'])) ? $data['phone'] : null);
        $user->setPrivateEmail((isset($data['privateEmail'])) ? $data['privateEmail'] : null);
        $user->setWorkEmail((isset($data['workEmail'])) ? $data['workEmail'] : null);
        $user->setUsername((isset($data['username'])) ? $data['username'] : null);
        $user->setPassword((isset($data['password'])) ? $data['password'] : null);
        $user->setStreet((isset($data['street'])) ? $data['street'] : null);
        $user->setHouseNumber((isset($data['houseNumber'])) ? $data['houseNumber'] : null);
        $user->setCity((isset($data['city'])) ? $data['city'] : null);
        $user->setZipCode((isset($data['zipCode'])) ? $data['zipCode'] : null);
        $user->setCountry((isset($data['country'])) ? $data['country'] : null);
        $user->setPhonePrivate((isset($data['phonePrivate'])) ? $data['phonePrivate'] : null);
        $user->setState(2);
        $user->setFkuserrole($userrole);
        $user->setFkaccountid($account);
        return $user;
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
     * Check if value exists in Database - create admin user - workemail unique
     * @param type $field
     * @param type $value
     */
    private function emailExistCreate($field, $value) {
        //Hent udvalgt i db
        $user = $this->getUsermapper()->findOneBy(array($field => $value));
        $exists = true;
        if ($user === NULL) {
            $exists = false;
        }
        return $exists;
    }

}

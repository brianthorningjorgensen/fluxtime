<?php

namespace Fluxuser\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Fluxuser\Utils\ActionHelper;
use Fluxuser\Form\SearchClientForm;
use Fluxuser\Form\ClientForm;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Fluxuser\Entity\Client;
use Zend\View\Model\JsonModel;

class ClientController extends ActionHelper {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct() {
        
    }

    /**
     * Index og søg clients
     * @return type form og clients 
     */
    public function indexAction() {
        $account = $this->identity()->getFkaccountid();
        $request = $this->getRequest();
        $form = new SearchClientForm($this->getEntityManager());
        //Hent alle accounts hvor state 1  
        $clients = $this->getClientmapper()->findBy(array('state' => 1, 'fkaccountid' => $account), array('clientid' => 'DESC'));
        //Hvis click på button i form
        if ($request->isPost()) {
            $data = $request->getPost();
            $search = $data['search'];
            if ($search !== "") {
                $collection = new ArrayCollection($clients);
                $criteria = Criteria::create()
                        ->where(Criteria::expr()->eq("clientname", $search))
                        ->orWhere(Criteria::expr()->eq("cvrid", $search));
                $clients = $collection->matching($criteria);
            }
            return array('clients' => $clients, 'form' => $form);
        }
        //Return accounts
        return array('clients' => $clients,
            'form' => $form
        );
    }

    /**
     * Edit client
     * @return type form og id
     */
    public function editAction() {
        $request = $this->getRequest();
        $userrole = $this->identity()->getFkuserrole()->getId();
        $permissubmit = true;
        $role = $this->identity()->getFkuserrole()->getId();
        if($role != 1 && $role != 5){
            $permissubmit = false;
        }
        $cid = $this->params()->fromRoute('id');
        if ($request->isPost()) {
            if (!$cid) {
                return $this->redirect()->toRoute('client');
            }
            $form = new ClientForm($this->getEntityManager());
            $client = $this->getClientmapper()->find($cid);
            //Validering
            $form->setData($request->getPost());
            $form->setInputFilter($form->getInputFilterSpecification());
            $value = $form->get('clientname')->getValue();
            $exists = $this->recordExistEdit('clientname', $value, $cid);
            if ($form->isValid() && !$exists) {
                $data = $request->getPost();        
                //Sætter fields til form-data 
                $clientSave = $this->exchangeArrayEdit($data, $cid);
                //Cache færdig client til hukommelse
                $this->getEntityManager()->persist($clientSave);
                //Gemmer i databasen
                $this->getEntityManager()->flush();
                // Redirect til liste med clients
                return $this->redirect()->toRoute('client');
            }
            else{
                $form->get('clientname')->setMessages(array('Already exists'));
                 $client = $this->getClientmapper()->find($cid);
                $id = $client->getClientid();
                return array(
                    'form' => $form, 'id' => $id, 'messages' => $form->getMessages(),'permissubmit' => $permissubmit
                );
            }
        }
        $cid = $this->decrypt($this->params()->fromRoute('id'), SECRET_KEY);
        if (!$cid) {
            return $this->redirect()->toRoute('client');
        }
        $form = new ClientForm($this->getEntityManager());
        try{
        $client = $this->getClientmapper()->find($cid);
         }catch(\Exception $e){
            return $this->redirect()->toRoute('client');
        }
        // set hydrator to populate form
        $form->setHydrator(new DoctrineHydrator($this->getEntityManager(), 'Fluxuser\Entity\Client'));
        //Binder user til form
        $form->bind($client);
          $contactform = new \Fluxuser\Form\ContactForm( $this->getEntityManager());
          $id = $client->getClientid();
          $contactform->get('clientid')->setvalue($id);
        
        $contacts = $this->getContactmapper()->findBy(array('fkclientid' => $id, 'state' => 1));
        
      
        
        return array(
            'form' => $form, 'id' => $id, 'permissubmit' => $permissubmit, 'contacts' => $contacts, 'messages' => $form->getMessages(), 'contactform' => $contactform
       
        );
    }

    /**
     * Sæt data på objekt - edit client
     * @param type $data
     * @param type $id
     * @return type client
     */
    private function exchangeArrayEdit($data, $id) {
        $client = $this->getClientmapper()->find($id);
        $client->setClientname($data['clientname']);
        $client->setCvrid($data['cvrid']);
        $client->setPhone($data['phone']);
        $client->setEmail($data['email']);
        $client->setStreet($data['street']);
        $client->setHouseNumber($data['houseNumber']);
        $client->setCity($data['city']);
        $client->setZipCode($data['zipCode']);
        $client->setCountry($data['country']);
        return $client;
    }

    /**
     * Add client
     * @return type form
     */
    public function addAction() {
        $request = $this->getRequest();
        $form = new ClientForm($this->getEntityManager());
        //Hvis click på button på form
        if ($request->isPost()) {
            //Validering
            $form->setInputFilter($form->getInputFilterSpecification());
            $form->setData($request->getPost());
              $value = $form->get('clientname')->getValue();
            $exists = $this->recordExistCreate('clientname', $value);
            if ($form->isValid() && !$exists) {
                $data = $form->getData();
                $account = $this->identity()->getFkaccountid();
                //Sætter fields til form-data 
                $client = $this->exchangeArrayCreate($data, $account);
                //Cache færdig client til hukommelse
                $this->getEntityManager()->persist($client);
                //Gemmer i databasen
                $this->getEntityManager()->flush();
                // Redirect til liste med clients
                return $this->redirect()->toRoute('client');
            }
            else{
                $form->get('clientname')->setMessages(array('Already exists'));
            }
        }
        return array(
            'form' => $form, 'messages' => $form->getMessages()
        );
    }

    /**
     * Sæt data på objekt - add client
     * @param type $data
     * @param type $account
     * @return Client
     */
    private function exchangeArrayCreate($data, $account) {
        $client = new Client();
        $client->setClientname((isset($data['clientname'])) ? $data['clientname'] : null);
        $client->setCvrid((isset($data['cvrid'])) ? $data['cvrid'] : null);
        $client->setPhone((isset($data['phone'])) ? $data['phone'] : null);
        $client->setEmail((isset($data['email'])) ? $data['email'] : null);
        $client->setStreet((isset($data['street'])) ? $data['street'] : null);
        $client->setHouseNumber((isset($data['houseNumber'])) ? $data['houseNumber'] : null);
        $client->setCity((isset($data['city'])) ? $data['city'] : null);
        $client->setZipCode((isset($data['zipCode'])) ? $data['zipCode'] : null);
        $client->setCountry((isset($data['country'])) ? $data['country'] : null);
        $client->setState(1);
        $client->setFkaccountid($account);
        return $client;
    }

    /**
     * Ajax delete client
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
     * Delete client
     * @param type $id
     * @return boolean
     */
    public function delete($id) {
        //Hvis id er null
        if (!$id) {
            //Redirect til liste clients hvor state er 1
            return $this->redirect()->toRoute('client');
        }
        //Henter i db
        $client = $this->getClientmapper()->find($id);
        $projects = $this->getProjectmapper()->FindBy(array('fkclientid' => $client));
        $accclient = $this->getAccountclientmapper()->FindBy(array('fkclientid' => $client));
        if (count($projects) == 0 && count($accclient) == 0) {
            //Sæt state til 0
            $client->setState(0);
            //Cache til hukommelse
            $this->getEntityManager()->persist($client);
            //Gemmer i db
            $this->getEntityManager()->flush();
            //Return status
            return true;
        } else {
            return false;
        }
    }

     /**
     * Check if value exists in Database - create client
     * @param type $field
     * @param type $value
     */
    private function recordExistCreate($field, $value) {
        $account = $this->identity()->getFkaccountid();
        //Hent udvalgt i db
        $client = $this->getClientmapper()->findOneBy(array($field => $value, 'fkaccountid' => $account ));
        $exists = true;
        if ($client === NULL) {
          $exists = false;
        } 
        return $exists;
    }
    
    /**
     * Check if value exists in Database - edit client
     * @param type $field
     * @param type $value
     * @param type $cid
     * @return boolean
     */
    private function recordExistEdit($field, $value, $cid) {
         $account = $this->identity()->getFkaccountid();
        //Hent udvalgt i db
        $client = $this->getClientmapper()->findOneBy(array($field => $value, 'fkaccountid' => $account ));
        if ($client === NULL) {
          return false;
        } 
        else {
          if($client->getClientid() === $cid){
           return false;   
          }  if($client->getClientid() != $cid){
           return true;   
          }
        } 
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

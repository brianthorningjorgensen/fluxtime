<?php

namespace Fluxuser\Controller;


use Doctrine\ORM\EntityManager;
use Fluxuser\Utils\ActionHelper;

class ContactController extends ActionHelper {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct() {

        }
    
    public function ajaxeditAction(){
        $form = new \Fluxuser\Form\ContactForm($this->getEntityManager());
        // Henter data og sætter form
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $form->setData($this->getRequest()->getPost());
        }
      
        //Returnerer result
        $status = $this->edit($form);
        $result['status'] = $status;
        return new \Zend\View\Model\JsonModel($result);
    }
    
    public function edit($form){
     //Validere form
        $form->setInputFilter($form->getInputFilterSpecification());
        $id = $form->get('contactid')->getValue();
        if ($form->isValid() ) {
           
            //Henter object fra db
            $contact = $this->getContactmapper()->find( $id );                  

            //Sætter contact
           // $contact = new \Fluxuser\Entity\Contact();
            $contact->setFirstname($form->get('firstname')->getValue());
            $contact->setLastname($form->get('lastname')->getValue());
            $contact->setPhone($form->get('phone')->getValue());
            $contact->setEmail($form->get('email')->getValue());
            $contact->setDescription($form->get('description')->getValue());           
            //Cache data til hukommelse
            $this->getEntityManager()->persist($contact);
            //Gemmer contact i db 
            $this->getEntityManager()->flush();
            //Returnerer auto increment
            return true;
        } else {
            return false;
        }
    }
    
    public function ajaxaddAction(){
        $form = new \Fluxuser\Form\ContactForm($this->getEntityManager());
        // Henter data og sætter form
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $form->setData($this->getRequest()->getPost());
        }
        //Returnerer result
        $newId = $this->add($form);
        $result['id'] = 'newid' . $newId;
        return new \Zend\View\Model\JsonModel($result);
    } 
    
     public function add($form){
         //Validere form
        $form->setInputFilter($form->getInputFilterSpecification());
              
       if ($form->isValid() ) {
            //Henter object fra db  
            $contact = new \Fluxuser\Entity\Contact();
            $contact->setFirstname($form->get('firstname')->getValue());
            $contact->setLastname($form->get('lastname')->getValue());
            $contact->setPhone($form->get('phone')->getValue());
            $contact->setEmail($form->get('email')->getValue());
            $contact->setDescription($form->get('description')->getValue());           
            $contact->setFkaccountid($this->identity()->getFkaccountid());     
            
            //$client = $this->getClientmapper()->find($form->get('clientid')->getValue());
            $client = $this->getClientmapper()->find($form->get('clientid')->getValue());
            $contact->setFkclientid( $client );           
            $contact->setState(1);           
            //Cache data til hukommelse
           $this->getEntityManager()->persist($contact);
            //Gemmer contact i db 
            $this->getEntityManager()->flush();
            //Returnerer auto increment
            return $contact->getContactid();
         
        } else {
            return 'false';            
        }
    }
       
    public function ajaxdeleteAction(){
        $ok = true;
        $ok = $this->delete();
        $result['status'] = $ok;
        return new \Zend\View\Model\JsonModel($result);
    }
    
    public function delete(){
          //Henter id fra url
        $id = $this->request->getPost("id");     
        //Henter i db
        if ($id != null) {
            $contact = $this->getContactmapper()->find($id);
            if ($contact) {
                //remove from all project
               // $projectcontacts = $this->getProjectcontactmapper()->find( array("fkcontactid"=>$contact));
                $textquery =    'SELECT pc, con, client '
                                . 'FROM Fluxuser\Entity\Projectcontact pc '
                                . 'JOIN pc.fkcontactid con '
                                . 'JOIN con.fkclientid client '
                                . "where "
                                . "client.clientid = " . $contact->getFkclientid()->getClientid();
                     ;
            $query = $this->getEntityManager()->createQuery( $textquery );
            //  echo '<br><br><br>' . $textquery;
           
            $projectcontacts = $query->getResult();
            
                foreach($projectcontacts as $projectcontact) {
                    $this->getEntityManager()->remove($projectcontact);
                }
                
                $contact->setState(0);
                $this->getEntityManager()->persist($contact);                

                //Gemmer i db
                $this->getEntityManager()->flush();                
                //Return result
                return true;
            }
            return false;
        } else {
            return false;
        }
    }
    
    public function ajaxaddtoprojectAction(){
        $form = new \Fluxuser\Form\ProjectClientContactForm($this->getEntityManager());
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $form->setData($this->getRequest()->getPost());
        }
        //Returnerer auto increment fra db
        $newId = $this->addToProject($form);       
        $result['id'] = 'newid' . $newId;
        return new \Zend\View\Model\JsonModel($result);
    }
    
    public function addToProject($form){
        $account = $this->identity()->getFkaccountid();
        //Valirere form
        $cid = $form->get('fkcontactid')->getValue();       
       
        $form->remove('fkcontactid');
        $form->setInputFilter($form->getInputFilterSpecification());
        if ($form->isValid()) {
            //Laver ny projectcontact
            $projectcontact = new \Fluxuser\Entity\Projectcontact();
            //Henter objects fra db
            $project = $this->getProjectmapper()->find($form->get('fkProjectid')->getValue());
            if ($project==null ) { die("no project"); }
            $contact = $this->getContactmapper()->find($cid);
         
            //Sætter fields
            $projectcontact->setFkProjectid($project);
            $projectcontact->setFkcontactid($contact);
            $projectcontact->setFkaccountid($account);
            //Cache data til hukommelse
            $this->getEntityManager()->persist($projectcontact);
            //Gemmer i db 
            $this->getEntityManager()->flush();
            //Returnerer auto increment
            return $projectcontact->getProjectcontactid();
        }            
    }
    
    public function ajaxremovefromprojectAction(){
         $ok = true;
        $ok = $this->removeFromProject();
        $result['status'] = $ok;
        return new \Zend\View\Model\JsonModel($result);
    }
    
    public function removeFromProject(){
        //Henter id fra url
        $id = (int) $this->params()->fromRoute('id', 0);       
        //Henter i db
        if ($id != null) {
            $projectcontact = $this->getProjectcontactmapper()->find($id);
            $this->getEntityManager()->remove($projectcontact);
            //Gemmer i db
            $this->getEntityManager()->flush();
            //Return result
            return true;
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
<?php

namespace Fluxuser\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Fluxuser\Entity\Email;
use Fluxuser\Entity\FluxUser;
use Fluxuser\Form\EditFluxuserForm;
use Fluxuser\Form\FluxuserForm;
use Fluxuser\Form\SearchUserForm;
use Fluxuser\Utils\ActionHelper;
use Fluxuser\Utils\PivotalTracker;
use Zend\View\Model\JsonModel; 

class FluxuserController extends ActionHelper {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Viser brugere fra account med state=1
     * @return type array[user]
     */
    public function indexAction() {
        $account = $this->identity()->getFkaccountid();
        $request = $this->getRequest();
        $form = new SearchUserForm($this->getEntityManager());
        $list = $this->getUsermapper()->findBy(array('fkaccountid' => $account), array('id' => 'DESC'));
        $collection = new ArrayCollection($list);
        //Søgning
        if ($request->isPost()) {
            $data = $request->getPost();
            $search = $data['search'];
            if ($search !== "") {

                $roles = $this->getRolemapper()->findBy(array('permissiongroup' => $search));
                //Finder userrole
                if (count($roles) > 0) {
                    $role = $roles[0];
                }
                $criteria = Criteria::create()
                        ->where(Criteria::expr()->eq("firstname", $search))
                        ->orWhere(Criteria::expr()->eq("lastname", $search))
                        ->orWhere(Criteria::expr()->eq("username", $search))
                        ->orWhere(Criteria::expr()->eq("fkuserrole", $role))
                        ->andWhere(Criteria::expr()->neq("state", 0));
                $users = $collection->matching($criteria);
            } else {
                $criteria = Criteria::create()->where(Criteria::expr()->neq("state", 0));
                $users = $collection->matching($criteria);
            }
            return array('users' => $users, 'form' => $form,);
        }
        //Return alle users
        $criteria = Criteria::create()->where(Criteria::expr()->neq("state", 0));
        $users = $collection->matching($criteria);
        return array('users' => $users, 'form' => $form,);
    }

    /**
     * Opret ny bruger (user)
     * @return type form
     */
    public function addAction() {
        $request = $this->getRequest();
        $form = $this->prepareAddForm();
        //Hvis click på button på form
        if ($request->isPost()) {
            $newuser = new FluxUser();
            //Validering
            $form->setInputFilter($form->getInputFilterSpecification());
            $form->setData($request->getPost());
            $userExists = $this->recordExistCreate('username', $form->get('username')->getValue());
            $idExists = false;
            if($form->get('employeeId')->getValue() != null){
            $idExists = $this->recordExistCreate('employeeId', $form->get('employeeId')->getValue());
            }
            $emailExists = $this->emailExistCreate('workEmail', $form->get('workEmail')->getValue());
            if ($form->isValid() && !$idExists && !$emailExists && !$userExists) {
                $data = $form->getData();
                // encrypt the temporary password
                $encryptedPassword = $this->encryptMessage($data['password']);
                // tilføj encrypted password til data array
                $data['password'] = $encryptedPassword;
                $roleId = $data['fkuserrole'];
                $userrole = $this->getRolemapper()->find($roleId);
                $account = $this->identity()->getFkaccountid();
                //Sætter user-fields til form-data 
                $user = $this->exchangeArray($data, $newuser, $userrole, $account);
                //Cache færdig user til hukommelse
                $this->getEntityManager()->persist($user);
                // create welcome email
                $email = $this->prepareWelcomeEmail($user, $account);
                // gem email i cashen
                $this->getEntityManager()->persist($email);
                //encrypt id så ingen kan let kan ændre på andre information
                $encryptedid = $this->encrypt($email->getId(), SECRET_KEY);
                // send welcome mail
                $this->sendWelcomemessage($data['firstname'], $data['workEmail'], $encryptedid, $data['username'], $form->getData()['password']);
                //Gemmer i databasen
                $this->getEntityManager()->flush();
                // Redirect til liste med users
                return $this->redirect()->toRoute('fluxuser');
            } else {
                if ($emailExists) {
                    $form->get('workEmail')->setMessages(array('Already exists'));
                }
                if ($userExists) {
                    $form->get('username')->setMessages(array('Already exists'));
                }
                if ($idExists) {
                    $form->get('employeeId')->setMessages(array('Already exists'));
                }
                return array(
                    'form'     => $form,
                    'messages' => $form->getMessages(),
                );
            }
        }
        return array(
            'form'     => $form,
            'messages' => $form->getMessages(),
        );
    }

    /**
     * Prepare add user from
     * @return FluxuserForm
     */
    private function prepareAddForm() {
        $form = new FluxuserForm($this->getEntityManager());
        $usergroups = $this->getRolemapper()->findAll();
        $ugarray = [];
        foreach ($usergroups as $usergroup) {
            if ($usergroup->getId() != 5 && $usergroup->getId() != 3) {
                $ugarray[$usergroup->getId()] = $usergroup->getPermissiongroup();
            }
        }
        $form->get('fkuserrole')->setValueOptions($ugarray);
        $defaultrole = $this->getRolemapper()->find(2);
        $combo = $form->get('fkuserrole');
        $combo->setValue($defaultrole->getId(), $defaultrole->getPermissiongroup());
        return $form;
    }

    /**
     * Prepare welcome email - add user
     * @param type $user
     * @param type $account
     * @return Email
     */
    private function prepareWelcomeEmail($user, $account) {
        $emailtype = $this->getEmailtypemapper()->findOneBy(array('id' => 1));
        // find emails the type of welcome mail 
        $email = new Email();
        $email->setEmailtypefk($emailtype);
        $senttime = new DateTime(date("Y-m-d H:i:s"));
        $email->setSenttime($senttime);
        // tilføj bruger til email
        $email->setUserfk($user);
        $email->setFkaccountid($account);
        return $email;
    }

    /**
     * Edit user
     * @return type form
     */
    public function editAction() {
        $request = $this->getRequest();
        //Hent user id fra url
        $id = $this->params()->fromRoute('id');
        //Hvis click på button som er tilføjet form
        if ($request->isPost()) {
            $form = new EditFluxuserForm($this->getEntityManager());
            $userEdit = $this->getUsermapper()->findOneBy(array('id' => $id));
            //Sæt input-filter på form (validering)
            $form->setInputFilter($form->getInputFilterSpecification());
            $form->setData($request->getPost());
             $exists = false;
            if($form->get('employeeId')->getValue() != null){
            $exists = $this->recordExistEdit('employeeId', $form->get('employeeId')->getValue(), $id);
            }
            if (!$exists) {
                $form->remove('fkuserrole');
            }
            //Hvis form er valid
            if (!$exists && $form->isValid()) {
                //Henter intastede data
                $data = $request->getPost();
                //Sætter user-fields til nye data
                $roleId = $data['fkuserrole'];
                $userrole = $this->getRolemapper()->find($roleId);
                $user = $this->editExchangeArray($data, $userEdit, $userrole);
                //Cache til hukommelsen
                $this->getEntityManager()->persist($user);
                //Gemmer user i db
                $this->getEntityManager()->flush();
                return $this->redirect()->toRoute('fluxuser');
            } else {
           return $this->returnErrorForm($userEdit, $form);   
        }
        }
        $id = $this->decrypt($this->params()->fromRoute('id'), SECRET_KEY);
        if (!$id) {
            return $this->redirect()->toRoute('fluxuser');
        }
        //Henter user fra db
        try{
        $userEdit = $this->getUsermapper()->find($id);
         }catch(\Exception $e){
            return $this->redirect()->toRoute('fluxuser');
        }
        //Opret form
        $form = $this->prepareEditForm($userEdit);
        $id = $userEdit->getId();
        return array('id' => $id, 'form' => $form, 'messages' => $form->getMessages());
    }

    private function prepareEditForm($userEdit) {
        $form = new EditFluxuserForm($this->getEntityManager());
        $userroleid = $userEdit->getFkuserrole()->getId();
        if ($userroleid != 5) {
            $usergroups = $this->getRolemapper()->findAll();
            $ugarray = array();

            foreach ($usergroups as $usergroup) {
                if ($usergroup->getId() != 5 && $usergroup->getId() != 3) {
                    $ugarray[$usergroup->getId()] = $usergroup->getPermissiongroup();
                }
            }
            $form->get('fkuserrole')->setValueOptions($ugarray);
            $combo = $form->get('fkuserrole');
            $userrole = $userEdit->getFkuserrole();
            $combo->setValue(array($userrole->getId(), $userrole->getPermissiongroup()));
        } else {
            $combo = $form->get('fkuserrole');
            $combo->setValueOptions(array(5 => 'system owner'));
        }
        // set hydrator to populate form - sætter user's data i form
        $form->setHydrator(new DoctrineHydrator($this->getEntityManager(), 'Fluxuser\Entity\Fluxuser'));
        //Binder user til form
        $form->bind($userEdit);
        return $form;
    }
   
    /**
     * Sets error message if employee id exists
     * @param type $userEdit
     * @param type $form
     * @return type form
     */
    private function returnErrorForm($userEdit, $form) {
        $userroleid = $userEdit->getFkuserrole()->getId();
        if ($userroleid != 5) {
            $usergroups = $this->getRolemapper()->findAll();
            $ugarray = array();

            foreach ($usergroups as $usergroup) {
                if ($usergroup->getId() != 5 && $usergroup->getId() != 3) {
                    $ugarray[$usergroup->getId()] = $usergroup->getPermissiongroup();
                }
            }
            $form->get('fkuserrole')->setValueOptions($ugarray);
            $combo = $form->get('fkuserrole');
        } else {
            $combo = $form->get('fkuserrole');
            $combo->setValueOptions(array(5 => 'system owner'));
        }
        $form->get('employeeId')->setMessages(array('Already exists'));
        $id = $userEdit->getId();
        return array('id' => $id, 'form' => $form, 'messages' => $form->getMessages());
    }

    /**
     * Slet user (sæt state til 0)
     * @return type redirect
     */
    private function delete($id) {
        //Hvis id er null
        if ($id != null) {
            //Henter user i db
            $user = $this->getUsermapper()->find($id);
            if (!isset($user)) { return false; }
            //Sæt state til 0
            $user->setState(0);
            //Cache til hukommelse
            $this->getEntityManager()->persist($user);
            //Gemmer user i db
            $this->getEntityManager()->flush();
            return true;
        }
    }

    /**
     * Ajax call confirm delete
     * @return JsonModel
     */
    public function ajaxconfirmdeleteAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $data = $request->getPost();
            $id = $data['id'];
            $resultDelete = $this->delete($id);
            $result['status'] = $resultDelete;
        } else {
            $result['error'] = 'This method only respons to XmlHttpRequests!';
        }
        
        return new JsonModel($result);
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
     * Sætter fields til formdata (input fra brugeren)
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
        $user->setPivotaltrackerapi((isset($data['pivotaltrackerapi'])) ? $data['pivotaltrackerapi'] : null);
        return $user;
    }

    /**
     * Sætter fields til formdata (input fra brugeren)
     */
    protected function editExchangeArray($data, $user, $userrole) {
        $user->setEmployeeId((isset($data['employeeId'])) ? $data['employeeId'] : null);
        $user->setFirstname((isset($data['firstname'])) ? $data['firstname'] : null);
        $user->setLastname((isset($data['lastname'])) ? $data['lastname'] : null);
        $user->setPhone((isset($data['phone'])) ? $data['phone'] : null);
        $user->setPrivateEmail((isset($data['privateEmail'])) ? $data['privateEmail'] : null);
        $user->setWorkEmail((isset($data['workEmail'])) ? $data['workEmail'] : null);
        $user->setUsername((isset($data['username'])) ? $data['username'] : null);
        $user->setStreet((isset($data['street'])) ? $data['street'] : null);
        $user->setHouseNumber((isset($data['houseNumber'])) ? $data['houseNumber'] : null);
        $user->setCity((isset($data['city'])) ? $data['city'] : null);
        $user->setZipCode((isset($data['zipCode'])) ? $data['zipCode'] : null);
        $user->setCountry((isset($data['country'])) ? $data['country'] : null);
        $user->setPhonePrivate((isset($data['phonePrivate'])) ? $data['phonePrivate'] : null);
        $user->setFkuserrole($userrole);
        $user->setPivotaltrackerapi((isset($data['pivotaltrackerapi'])) ? $data['pivotaltrackerapi'] : null);

        return $user;
    }

    /**
     * Check if value exists in Database - create user
     * @param type $field
     * @param type $value
     */
    private function recordExistCreate($field, $value) {
        $account = $this->identity()->getFkaccountid();
        //Hent udvalgt i db
        $user = $this->getUsermapper()->findOneBy(array($field => $value, 'fkaccountid' => $account));
        $exists = true;
        if ($user === NULL) {
            $exists = false;
        }
        return $exists;
    }

    /**
     * Check if value exists in Database - create user - workemail unique
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

    /**
     * Check if value exists in Database - edit user
     * @param type $field
     * @param type $value
     * @param type $id
     * @return boolean
     */
    private function recordExistEdit($field, $value, $id) {
        $account = $this->identity()->getFkaccountid();
        //Hent udvalgt i db
        $user = $this->getUsermapper()->findOneBy(array($field => $value, 'fkaccountid' => $account));
        if ($user === NULL) {
            return false;
        } else {
            if ($user->getId() === $id) {
                return false;
            } if ($user->getId() != $id) {
                return true;
            }
        }
    }

    /**
     * Get user api token
     * @return JsonModel
     */
    public function fetchApiTokenAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $data = $request->getPost();
            $user = $data['user'];
            $pass = $data['pass'];
            $ok = $this->fetchApiToken($user, $pass);
            $result['apitoken'] = $ok;
        } else {
            $result['error'] = 'This method only respons to XmlHttpRequests!';
        }
        return new JsonModel($result);
    }

    /**
     *
     * @param type $user
     * @param type $pass
     * @return boolean
     */
    private function fetchApiToken($user, $pass) {
        //Henter pivotal tracker util
        $pivotalTracker = new PivotalTracker();
        $apiToken = $pivotalTracker->getToken($user, $pass);
        return $apiToken;
    }

    /**
     * Delete api token
     * @return JsonModel 
     */
    public function deleteApiTokenAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $data = $request->getPost();
            $userid = $data['userid'];
            $ok = $this->deleteApiToken($userid);
            $result['result'] = $ok;
        } else {
            $result['error'] = 'This method only respons to XmlHttpRequests!';
        }
        return new JsonModel($result);
    }

    /**
     *  Delete PT api token
     * @param type $userid
     * @return boolean
     */
    private function deleteApiToken($userid) {
        $user = $this->getUsermapper()->findOneBy(array('id' => $userid));
        $user->setPivotaltrackerapi("");
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return true;
    }
    
}

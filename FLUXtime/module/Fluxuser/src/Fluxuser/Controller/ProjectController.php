<?php

namespace Fluxuser\Controller;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Exception;
use Fluxuser\Entity\Client;
use Fluxuser\Entity\FluxUser;
use Fluxuser\Entity\Project;
use Fluxuser\Entity\Projectcontact;
use Fluxuser\Entity\Projectuser;
use Fluxuser\Form\LabelForm;
use Fluxuser\Form\ProjectClientContactForm;
use Fluxuser\Form\ProjectForm;
use Fluxuser\Form\ProjectmemberForm;
use Fluxuser\Form\SearchProjectForm;
use Fluxuser\Utils\ActionHelper;
use Zend\View\Model\JsonModel;

class ProjectController extends ActionHelper {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Henter projects efter permissions
     * @return type array[project] 
     */
    public function indexAction() {
        $roleid = $this->identity()->getFkuserrole()->getId();
        $account = $this->identity()->getFkaccountid();
        $request = $this->getRequest();
        $form = new SearchProjectForm($this->getEntityManager());
        //Visible projects
        $list = $this->getVisibleProjects($roleid, $account);
        $collection = new ArrayCollection($list);
        $criteria = Criteria::create()->where(Criteria::expr()->eq('active', 1));
        //Hvis submit form
        if ($request->isPost()) {
            $data = $request->getPost();
            $search = $data['search'];
            $active = $data['active'];
            //Søgeresultat
            $projects = $this->getSearchResults($active, $search, $collection, $account);
            $form->get('active')->setValue($active);
            //Return projects og form
            return array('projects' => $projects, 'form' => $form, 'messages' => $form->getMessages());
        }

        $projects = $collection->matching($criteria);
        $form->get('active')->setValue(1);
        //Return projects og form
        return array('projects' => $projects, 'form' => $form, 'messages' => $form->getMessages());
    }

    /**
     * Admin og system owner må se alle projects fra egen account med state 1
     * User må se projects hvor han er project member
     * Project manager må se projects hvor han er project member eller project manager
     * @param type $roleid
     * @param type $account
     * @return type
     */
    private function getVisibleProjects($roleid, $account) {
        $list = [];
        switch ($roleid) {
            case 1:
                $list = $this->getProjectmapper()->findBy(array('state' => 1, 'fkaccountid' => $account), array('projectid' => 'DESC'));
                break;
            case 2:
                $projectusers = $this->getProjectusermapper()->findBy(array('fkUserid' => $this->identity()->getId()));
                foreach ($projectusers as $projectUser) {
                    array_push($list, $projectUser->getFkProjectid());
                }
                break;
            case 4:
                $projectusers = $this->getProjectusermapper()->findBy(array('fkUserid' => $this->identity()->getId()));
                $projects = $this->getProjectmapper()->findBy(array('state' => 1, 'fkaccountid' => $account), array('projectid' => 'DESC'));

                foreach ($projectusers as $projectUser) {
                    array_push($list, $projectUser->getFkProjectid());
                }
                foreach ($projects as $project) {
                    if ($project->getFkProjectmanager() != null) {
                        if ($project->getFkProjectmanager() === $this->identity()) {
                            array_push($list, $project);
                        }
                    }
                }
                break;
            case 5:
                $list = $this->getProjectmapper()->findBy(array('state' => 1, 'fkaccountid' => $account), array('projectid' => 'DESC'));
                break;
        }
        return $list;
    }

    /**
     * Søgeresultat active/inactive og søgeord (søger bland de projekter brugeren må se)
     * @param type $active
     * @param type $search
     * @param type $collection
     * @return array projects
     */
    private function getSearchResults($active, $search, $collection, $account) {
        $projects = [];
        if ($search == "") {
            if ($active == 1) {
                $criteria = Criteria::create()
                        ->where(Criteria::expr()->eq('active', 1));
                $projects = $collection->matching($criteria);
            }
            if ($active == 0) {
                $criteria = Criteria::create()
                        ->where(Criteria::expr()->eq('active', 0));
                $projects = $collection->matching($criteria);
            }
        } else if ($search != "") {
            //Henter users som matcher søgning project manager
            $user = $this->getUsermapper()->findOneBy(array('username' => $search, 'fkaccountid' => $account));
            $client = $this->getClientmapper()->findOneBy(array('clientname' => $search, 'fkaccountid' => $account));
            $pm = 'fkProjectmanager';
            $clientfield = 'fkclientid';
            if ($user == null) {
                //Hvis ingen project manager matcher, laves en søgning som ikke giver resultat
                $pm = 'state';
                $user = 100;
            }
            if ($client == null) {
                //Hvis ingen client matcher, laves en søgning som ikke giver resultat
                $clientfield = 'state';
                $client = 100;
            }
            // søge criteria
            $criteria = Criteria::create()
                    ->where(Criteria::expr()->eq("projectname", $search))
                    ->orWhere(Criteria::expr()->eq($clientfield, $client))
                    ->orWhere(Criteria::expr()->eq($pm, $user));
            $temp = $collection->matching($criteria);
            //Sorter efter active
            $list = [];
            foreach ($temp as $project) {
                if ($project->getActive() == $active) {
                    array_push($list, $project);
                }
            }
            $projects = $list;
        }
        return $projects;
    }

    /**
     * Opret nyt project
     * @return type form
     */
    public function addAction() {
        $account = $this->identity()->getFkaccountid();
        $identity = $this->identity();
        $request = $this->getRequest();
        $form = $this->prepareAddForm($identity, $account);
        //Submit projekt
        if ($request->isPost()) {
            //Project og oprettes
            $projectAdd = new Project();
            //Inputfilter sættes på formen (validering)
            $form->setInputFilter($form->getInputFilterSpecification());
            //Sæt indtastede data på formen
            $form->setData($request->getPost());
             $exists = $this->recordExistCreate('projectname', $form->get('projectname')->getValue());
            //Remove to validate
            if(!$exists){
                $combo = $form->get('fkProjectmanager');
            $form->remove('fkProjectmanager');
            $comboClient = $form->get('client');
            $form->remove('client');
            }
            //Hvis form-data er valid
            if ($form->isValid() && !$exists) {
                //Henter data ud af formen
                $data = $form->getData();
                //Hent projectmanager-objekt fra db hvis tilføjet på formen og ellers tilføjes tom fluxuser
                $id = $combo->getValue();
                if ($id != null) {
                    $user = $this->getUsermapper()->find($id);
                } else {
                    $user = new FluxUser();
                }
                $clientid = $comboClient->getValue();
                if ($clientid != null && $clientid != 0) {
                    $client = $this->getClientmapper()->find($clientid);
                } else {
                    $client = new Client();
                }
                //Sætter form-data på objekt
                $project = $this->exchangeArrayCreate($data, $user, $projectAdd, $account, $client);
                //Cache data til hukommelse
                $this->getEntityManager()->persist($project);
                //Gemmer project i db 
                $this->getEntityManager()->flush();
                // Redirect til liste projects
                return $this->redirect()->toRoute('project');
            } else{
               $form->get('projectname')->setMessages(array('Already exists'));
               $form->get('fkProjectmanager')->setMessages(array(' '));
                $form->get('client')->setMessages(array(' '));
               return array('form' => $form, 'messages' => $form->getMessages());
            }
        }
        //Returnere form
        return array('form' => $form, 'messages' => $form->getMessages());
    }

    /**
     * Prepare add project form
     * @param type $identity
     * @param type $role
     * @param type $account
     * @return ProjectForm
     */
    private function prepareAddForm($identity, $account) {
        $role = $identity->getFkuserrole()->getId();
        $form = new ProjectForm($this->getEntityManager());
        $form->remove('active');
        $form->remove('secondid');
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
        //Henter combo i form og sætter empty-option
        $pmCombo = $form->get('fkProjectmanager');
        switch ($role) {
            case 1:
                $pmCombo->setEmptyOption('Please select...');
                $managers = $this->getPossibleProjectmanagers($account);
                $pmCombo->setValueOptions($managers);

                break;
            case 2:
                $pmCombo->setValueOptions(array($identity->getId() => $identity->getUsername()));
                break;
            case 4:
                $pmCombo->setValueOptions(array($identity->getId() => $identity->getUsername()));
                break;
            case 5:
                $pmCombo->setEmptyOption('Please select...');
                $managers = $this->getPossibleProjectmanagers($account);
                $pmCombo->setValueOptions($managers);

                break;
        }
        return $form;
    }

    /**
     * Sætter data fra form på objekt - create project
     * @param type $data
     * @param type $user
     * @param type $project
     * @param type $account
     * @return type project
     */
    private function exchangeArrayCreate($data, $user, $project, $account, $client) {
        $dt = new DateTime();
        $project->setProjectname((isset($data['projectname'])) ? $data['projectname'] : null);
        $project->setSecondid((isset($data['secondid'])) ? $data['secondid'] : null);
        $project->setCreatedate($dt);
        //Hvis ikke tomt fluxuser-objekt
        if ($user->getId() != null) {
            $project->setFkProjectmanager($user);
        }
        if ($client->getClientid() != null) {
            $project->setFkclientid($client);
        }
        //Aktiv
        $project->setState(1);
        $project->setActive(1);
        $project->setFkaccountid($account);
        return $project;
    }

     /**
     * Edit project
     * @return type form
     */
    public function editAction() {
        $account = $this->identity()->getFkaccountid();
        $request = $this->getRequest();
        $id = $this->params()->fromRoute('id');
        //Hvis click på save project-button
        if ($request->isPost()) {
            $project = $this->getProjectMapper()->find($id);
           // $form = new ProjectForm($this->getEntityManager());
             $permission = $this->getProjectpermissions($project);
              $secondid = false;
            if ($project->getSecondid() != null) {
                $secondid = true;
            }
            $form = $this->prepareEditForm($project, $account, $permission, $secondid);
            $filter = $form->getInputFilterSpecification();    
            if ($secondid) {
                $filter->remove('projectname');
            }
            //Sæt input-filter på form (validering)
            $form->setInputFilter($filter);
            $form->setData($request->getPost());   
            //Hvis form er valid
            $exists = $this->recordExistEdit('projectname', $form->get('projectname')->getValue(), $id);
            if(!$exists){
                $userId = $form->get('fkProjectmanager')->getValue();
            //Fjerner select box for at kunne validere
            $form->remove('fkProjectmanager');
            $clientId = $form->get('client')->getValue();
            //Fjerner select box for at kunne validere
            $form->remove('client');
            }
            if ($form->isValid() && !$exists) {
                //Henter intastede data
                $data = $request->getPost();
                //Henter fluxuser-objekt i db eller laver tomt objekt
                if ($userId != null) {
                    $user = $this->getUsermapper()->find($userId);
                } else {
                    $user = new FluxUser();
                }
                if ($clientId != null && $clientId != 0) {
                    $client = $this->getClientmapper()->find($clientId);
                } else {
                    $client = new Client();
                }
                //Sætter projectmanager og form-data på objekt
                $projectSave = $this->exchangeArrayEdit($data, $user, $project, $secondid, $client);
                //Cache til hukommelsen
                $this->getEntityManager()->persist($projectSave);
                //Gemmer user i db
                $this->getEntityManager()->flush();
                // Redirect to list of users hvor state er 1
                return $this->redirect()->toRoute('project');
            } else {
                $form->get('projectname')->setMessages(array('Already exists'));
                $form->get('fkProjectmanager')->setMessages(array(' '));
                $form->get('client')->setMessages(array(' '));
                $project = $this->getProjectMapper()->find($id);
                $id = $project->getProjectid();
                $labels = $this->getLabelmapper()->findBy(array('state' => 1, 'fkProjectid' => $id), array('labelid' => 'DESC'));
                $formlabel = new LabelForm($this->getEntityManager());
               
                $pm = $project->getFkProjectmanager();
                $members = $this->getProjectusermapper()->findBy(array('fkProjectid' => $id));
                $formmember = $this->prepareMemberForm($id, $account, $pm);
                return array(
                    'id' => $id, 'form' => $form, 'labels' => $labels, 'formlabel' => $formlabel, 'formmember' => $formmember,
                    'members' => $members, 'permission' => $permission, 'secondid' => $secondid, 'messages' => $form->getMessages());
            }
        }
        $id = $this->decrypt($this->params()->fromRoute('id'), SECRET_KEY);
         
        if (!$id) {
            //Redirect til liste med projects
            return $this->redirect()->toRoute('project');
        }
        try{
       $project = $this->getProjectMapper()->find($id);
        }catch(Exception $e){
            return $this->redirect()->toRoute('project');
        }
        $secondid = false;
        if ($project->getSecondid() != null) {
            $secondid = true;
        }
        $pm = $project->getFkProjectmanager();
        $members = $this->getProjectusermapper()->findBy(array('fkProjectid' => $id));
        $projectcontacts = $this->getProjectcontactmapper()->findBy(array('fkaccountid'=>$account, 'fkprojectid'=>$id) );
        //Henter project-labels hvor state er 1
        $labels = $this->getLabelmapper()->findBy(array('state' => 1, 'fkProjectid' => $id), array('labelid' => 'DESC'));
        $formlabel = new LabelForm($this->getEntityManager());
        $id = $project->getProjectid();
        $formlabel->get('fkProjectid')->setValue($id);
        //Hvem må edit 
        $permission = $this->getProjectpermissions($project);
        $form = $this->prepareEditForm($project, $account, $permission, $secondid);
        $formmember = $this->prepareMemberForm($id, $account, $pm);
        $formcontacts = $this->prepareClientContactForm($id, $account);
        //Binder user til form
        $form->setHydrator(new DoctrineHydrator($this->getEntityManager(), 'Fluxuser\Entity\Project'));
        $form->bind($project);
        return array(
            'id' => $id, 'form' => $form, 'labels' => $labels, 'formlabel' => $formlabel, 'formmember' => $formmember,
            'members' => $members, 'permission' => $permission, 'secondid' => $secondid, 'projectcontacts' => $projectcontacts, 'formcontacts' => $formcontacts);
    }
    
    /**
     * Permission to edit project, add members, labels and contacts
     * Only admin, systemowner and project's project manager
     * @param type $project
     * @return boolean
     */
    private function getProjectpermissions($project) {
        $identity = $this->identity();
        $role = $identity->getFkuserrole()->getId();
        $permission = false;
        $projectmanager = $project->getFkProjectmanager();
        switch ($role) {
            case 1:
                $permission = true;
                break;
            case 2:
                $permission = false;
                break;
            case 4:
                if ($projectmanager != null && $identity == $projectmanager) {
                    $permission = true;
                }
                break;
            case 5:
                $permission = true;
                break;
        }
        return $permission;
    }

    /**
     * Prepare form - edit project 
     * @param type $project
     * @param type $account
     * @param type $permission
     * @return ProjectForm
     */
    private function prepareEditForm($project, $account, $permission, $secondid) {
        $form = new ProjectForm($this->getEntityManager());
        $client = $project->getFkclientid();
        if(!$client){
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
        
       
            
        
        } else {
             $client = $project->getFkclientid();
            $clientcombo = $form->get('client');
             $clientcombo->setValueOptions(array($client->getClientid() => $client->getClientname()));
            $clientcombo->setValue($client->getClientid(), $client->getClientname());
             $clientcombo->setAttribute('readonly', 'readonly');  
        }
        $pmCombo = $form->get('fkProjectmanager');
        $pm = $project->getFkProjectmanager();
        $identity = $this->identity();
        $role = $identity->getFkuserrole()->getId();
        if ($secondid) {
            $form->get('projectname')->setAttributes(array(
                'disabled' => 'disabled',
            ));
        } else {
            $form->remove('secondid');
        }
        switch ($permission) {
            case true:
                $form->get('submit')->setAttributes(array(
                    'disabled' => '',
                ));
                //Project managers må kun vælge sig selv
                if ($role == 4) {
                    $pmCombo->setAttributes(array(
                        'disabled' => 'disabled',
                    ));
                }
                break;

            case false:
                $form->get('submit')->setAttributes(array(
                    'disabled' => 'disabled',
                ));
                break;
        }
        $list = $this->getPossibleProjectmanagers($account);
        // Options   
        $pmCombo->setEmptyOption('Please select...');
        
        if ($pm != null) {
            
            $list[$pm->getId()] = $pm->getUsername();
             $pmCombo->setValueOptions($list);
            $pmCombo->setValue(array($pm->getId(), $pm->getUsername()));
        } else{
            $pmCombo->setValueOptions($list);
        }
        return $form;
    }

    /**
     * Find possible project managers
     * @return type array
     */
    private function getPossibleProjectmanagers($account) {
        $users = $this->getUsermapper()->findBy(array('state' => 1, 'fkaccountid' => $account), array('username' => 'ASC'));
        $collection = new ArrayCollection($users);
        $guest = $this->getRolemapper()->find(3);
        $user = $this->getRolemapper()->find(2);
        $criteria = Criteria::create()
                ->where(Criteria::expr()->neq('fkuserrole', $user))
                ->andWhere(Criteria::expr()->neq('fkuserrole', $guest));
        $managers = $collection->matching($criteria);
        $list = [];
        foreach ($managers as $user) {
            $list[$user->getId()] = $user->getUsername();
        }
        return $list;
    }

    /**
     * Prepare Add project member form
     * @param type $id
     * @param type $account
     * @return ProjectmemberForm
     */
    private function prepareMemberForm($id, $account, $pm) {
        $formmember = new ProjectmemberForm($this->getEntityManager());
        $formmember->get('fkProjectid')->setValue($id);
        //Account users m. state 1
        $users = $this->getUsermapper()->findBy(array('state' => 1, 'fkaccountid' => $account), array('username' => 'ASC'));
        //Project members
        $members = $this->getProjectusermapper()->findBy(array('fkProjectid' => $id));
        //Set options i projectmemberform
        $membercombo = $formmember->get('fkUserid');
        $membercombo->setEmptyOption('Please select, type letter to search');
        $list = [];
        foreach ($users as $user) {
            $addelement = true;
            foreach ($members as $member) {
                // if user allready in selectbox dont add
                if ($addelement && $user->getId() == $member->getFkUserid()->getId()) {
                    $addelement = false;
                }
            }
            if ($addelement && $user != $pm) {
                $list[$user->getId()] = $user->getUsername();
            }
        }
        $membercombo->setValueOptions($list);
        return $formmember;
    }

    /**

     * @param type $id
     * @param type $account
     * @return ProjectmemberForm
     */
    private function prepareClientContactForm($id, $account) {
        $formclientcontact = new ProjectClientContactForm($this->getEntityManager());
        $formclientcontact->get('fkProjectid')->setValue($id);
//        //Account contacts m. state 1
        $project = $this->getProjectmapper()->find($id);
        $client = $project->getFkclientid();
        if($client){
        $allcontacts = $this->getContactmapper()->findBy(array('state' => 1, 'fkaccountid' => $account, 'fkclientid' => $client), array('contactid' => 'ASC'));       
        $projectcontacts = $this->getProjectcontactmapper()->findBy(array( 'fkaccountid' => $account, 'fkprojectid'=>$id), array('projectcontactid' => 'ASC'));       
        //Set options in form
        $combo = $formclientcontact->get('fkcontactid');
        $combo->setEmptyOption('Please select, type letter to search');
        $list = [];
        foreach ($allcontacts as $contact) {
            $addelement = true;
            foreach ($projectcontacts as $projectcontact) {
                // if contact allready in selectbox dont add
                if ($addelement && $contact->getContactid() == $projectcontact->getFkcontactid()->getContactid() ) {
                    $addelement = false;
                }
            }
            if ($addelement) {
                $list[$contact->getContactid()] = $contact->getFirstname() . ' ' . $contact->getLastname();
            }
        }
        $combo->setValueOptions($list);
        }
        return $formclientcontact;
    }

    /**
     * Sætter data på object
     * @param type $data
     * @param type $user
     * @param type $project
     * @return type project
     */
    private function exchangeArrayEdit($data, $user, $project, $secondid, $client) {
        if (!$secondid) {
            $project->setProjectname((isset($data['projectname'])) ? $data['projectname'] : null);
        }
        $project->setActive((isset($data['active'])) ? $data['active'] : null);
        //Hvis ikke tomt fluxuser-objekt
        if ($user->getId() != null) {
            $project->setFkProjectmanager($user);
        }
        if ($user->getId() == null) {
            $project->setFkProjectmanager(null);
        }
        if ($client->getClientid() != null) {
            $project->setFkclientid($client);
        }
        if ($client->getClientid() == null) {
            $project->setFkclientid(null);
        }
        return $project;
    }

    /**
     * Slet project (sæt state til 0)
     * @return type redirect
     */
    private function delete($id) {
        //Kan kun slettes hvis der ikke er tilføjet task, member eller label
        $members = $this->getProjectusermapper()->findBy(array('fkProjectid' => $id));
        $labels = $this->getLabelmapper()->findBy(array('fkProjectid' => $id));
        $tasks = $this->getTaskmapper()->findBy(array('fkprojectid' => $id));
        //Hvis id er null
        if (!$id) {
            //Redirect til liste med alle projects hvor state er 1
            return $this->redirect()->toRoute('project');
        }
        if (count($members) == 0 && count($labels) == 0 && count($tasks) == 0) {
            //Henter projects i db
            $project = $this->getProjectmapper()->find($id);
            //Sæt state til 0
            $project->setState(0);
            //Cache til hukommelse
            $this->getEntityManager()->persist($project);
            //Gemmer projects i db
            $this->getEntityManager()->flush();
            //Return status
            return true;
        } else {
            return false;
        }
    }

    /**
     * Ajax-kald - delete project
     * @return JsonModel
     */
    public function ajaxconfirmdeleteAction() {
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
     * Ajax-kald - add project member
     * @return JsonModel
     */
    public function ajaxaddmemberAction() {
        $form = new ProjectmemberForm($this->getEntityManager());
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $form->setData($this->getRequest()->getPost());
        }
        //Returnerer auto increment fra db
        $newId = $this->addMember($form);
        $result['id'] = 'newid' . $newId;
        return new JsonModel($result);
    }

    /**
     * Add project member
     * @param type $form
     * @return string
     */
    public function addmember($form) {
        $account = $this->identity()->getFkaccountid();
        //Valirere form
        $uid = $form->get('fkUserid')->getValue();
        $form->remove('fkUserid');
        $form->setInputFilter($form->getInputFilterSpecification());
        if ($form->isValid()) {
            //Laver ny projectuser
            $member = new Projectuser();
            //Henter objects fra db
            $project = $this->getProjectmapper()->find($form->get('fkProjectid')->getValue());
            $user = $this->getUserMapper()->find($uid);
            //Sætter fields
            $member->setFkProjectid($project);
            $member->setFkUserid($user);
            $member->setFkaccountid($account);
            //Cache data til hukommelse
            $this->getEntityManager()->persist($member);
            //Gemmer i db 
            $this->getEntityManager()->flush();
            //Returnerer auto increment
            return $member->getProjectuserid();
        }
    }

    /**
     * Remove project member
     * @return string
     */
    public function removemember() {
        //Henter id fra url
        $id = (int) $this->params()->fromRoute('id', 0);
        //Henter i db
        if ($id != null) {
            $member = $this->getProjectusermapper()->find($id);
            $user = $member->getFkUserid();
            $project = $member->getFkProjectid();
            $tasks = $this->getTaskmapper()->findBy(array('fkprojectid' => $project->getProjectid(), 'state' => 1));
            foreach ($tasks as $task) {
                $owner = $this->getOwnermapper()->findOneBy(array('fkuserid' => $user, 'fktaskid' => $task));
                if ($owner) {
                    $this->getEntityManager()->remove($owner);
                }
            }
            //Cache til hukommelse
            $this->getEntityManager()->remove($member);
            //Gemmer i db
            $this->getEntityManager()->flush();
            //Return result
            return true;
        }
    }

    /**
     * Ajax-kald - remove project member
     * @return JsonModel
     */
    public function ajaxremovememberAction() {
        $ok = $this->removemember();
        $result['status'] = $ok;
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
     * Check if value exists in Database - create project
     * @param type $field
     * @param type $value
     */
    private function recordExistCreate($field, $value) {
        $account = $this->identity()->getFkaccountid();
        //Hent udvalgt i db
        $project = $this->getProjectmapper()->findOneBy(array($field => $value, 'fkaccountid' => $account));
        $exists = true;
        if ($project === NULL) {
            $exists = false;
        }
        return $exists;
    }

    /**
     * Check if value exists in Database - edit project
     * @param type $field
     * @param type $value
     * @param type $id
     * @return boolean
     */
    private function recordExistEdit($field, $value, $id) {
        $account = $this->identity()->getFkaccountid();
        //Hent udvalgt i db
        $project = $this->getProjectmapper()->findOneBy(array($field => $value, 'fkaccountid' => $account));
        if ($project === NULL) {
            return false;
        } else {
            if ($project->getProjectid() === $id) {
                return 'false';
            } if ($project->getProjectid() != $id) {
                return 'true';
            }
        }
    }

}

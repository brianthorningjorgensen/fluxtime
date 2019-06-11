<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FluxuserTest;

use Fluxuser\Entity\FluxUser;
use Fluxuser\Entity\Resourcepermission;
use Fluxuser\Entity\Systemaccount;
use Fluxuser\Entity\Urlresource;
use Fluxuser\Entity\Usergroup;

/**
 * Description of FactoryFluxuser
 *
 * @author Anders Bo Rasmussen
 */
class FactoryFluxuser {

    private $entityManager;
    
    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }
    
    public function createSystemAccount(){
        $systemAccount = new Systemaccount();
        $systemAccount->setActive(1);
        $systemAccount->setState(1);
        $systemAccount->setCustomer('systemOwnerAccount');
        $this->entityManager->persist($systemAccount);
        $this->entityManager->flush();
        
        return $systemAccount;
    }
    
    public function createGuestUser($controller, $systemAccount) {
        $userGroup = new Usergroup();
        $userGroup->setPermissiongroup('guest');
        $this->entityManager->persist($userGroup);
        $this->createGuestUserUrlResource($userGroup);

        $userGuest = new FluxUser();

        $userGuest->setFirstname('Guest');
        $userGuest->setLastname('User');
        $userGuest->setWorkEmail('guest@test.biz');
        $userGuest->setUsername('guest');
        $userGuest->setPassword($controller->encryptMessage('password'));
        $userGuest->setState(1);
        $userGuest->setFkuserrole($userGroup);
        $userGuest->setFkaccountid($systemAccount);
        $this->entityManager->persist($userGuest);
        $this->entityManager->flush();
        
        return $userGuest;
    }

    public function createUserUser($controller, $systemAccount) {
        $userGroup = new Usergroup();
        $userGroup->setPermissiongroup('user');
        $this->entityManager->persist($userGroup);
        $this->createUserUserUrlResource($userGroup);

        $userUser = new FluxUser();

        $userUser->setFirstname('User');
        $userUser->setLastname('User');
        $userUser->setWorkEmail('user@test.biz');
        $userUser->setUsername('user');
        $userUser->setPassword($controller->encryptMessage('password'));
        $userUser->setState(1);
        $userUser->setFkuserrole($userGroup);
        $userUser->setFkaccountid($systemAccount);
        $this->entityManager->persist($userUser);
        $this->entityManager->flush();
        
        return $userUser;
    }

    public function createAdminUser($controller, $systemAccount) {
        $userGroup = new Usergroup();
        $userGroup->setPermissiongroup('admin');
        $this->entityManager->persist($userGroup);
        $this->createAdminUserUrlResource($userGroup);

        $userAdmin = new FluxUser();

        $userAdmin->setFirstname('Admin');
        $userAdmin->setLastname('User');
        $userAdmin->setWorkEmail('admin@test.biz');
        $userAdmin->setUsername('admin');
        $userAdmin->setPassword($controller->encryptMessage('password'));
        $userAdmin->setState(1);
        $userAdmin->setFkuserrole($userGroup);
        $userAdmin->setFkaccountid($systemAccount);
        $this->entityManager->persist($userAdmin);
        $this->entityManager->flush();
        
        return $userAdmin;
    }

    public function createOwnerUser($controller, $systemAccount) {
        $userGroup = new Usergroup();
        $userGroup->setPermissiongroup('owner');
        $this->entityManager->persist($userGroup);
        $this->createOwnerUserUrlResource($userGroup);

        $userOwner = new FluxUser();

        $userOwner->setFirstname('Owner');
        $userOwner->setLastname('User');
        $userOwner->setWorkEmail('owner@test.biz');
        $userOwner->setUsername('owner');
        $userOwner->setPassword($controller->encryptMessage('password'));
        $userOwner->setState(1);
        $userOwner->setFkuserrole($userGroup);
        $userOwner->setFkaccountid($systemAccount);
        $this->entityManager->persist($userOwner);
        $this->entityManager->flush();
        
        return $userOwner;
    }

    public function createProjectManagerUser($controller, $systemAccount) {
        $userGroup = new Usergroup();
        $userGroup->setPermissiongroup('projectmanager');
        $this->entityManager->persist($userGroup);
        $this->createProjectmanagerUserUrlResource($userGroup);

        $userProjectmanager = new FluxUser();

        $userProjectmanager->setFirstname('Projectmanager');
        $userProjectmanager->setLastname('User');
        $userProjectmanager->setWorkEmail('projectmanager@test.biz');
        $userProjectmanager->setUsername('projectmanager');
        $userProjectmanager->setPassword($controller->encryptMessage('password'));
        $userProjectmanager->setState(1);
        $userProjectmanager->setFkuserrole($userGroup);
        $userProjectmanager->setFkaccountid($systemAccount);
        $this->entityManager->persist($userProjectmanager);
        $this->entityManager->flush();
        
        return $userProjectmanager;
    }

    private function createGuestUserUrlResource($userGroup) {
        $urls = [
            'home/login',
            'login/login',
            'profile/confirmuser',
            'profile/resetpassword',
            'profile/confirmresetpassword',
            'cronjob/crontasks',
            'logout/logout',
        ];

        $this->createUrlResource($userGroup, $urls);
    }

    private function createUserUserUrlResource($userGroup) {
        $urls = [
            'fluxuser/logout',
            'profile/edit',
            'project/index',
            'project/edit',
            'profile/confirmuser',
            'profile/resetpassword',
            'profile/confirmresetpassword',
            'fluxuser/index',
            'task/mytasks',
            'task/ajaxfinishtask',
            'task/index',
            'task/add',
            'task/edit',
            'task/ajaxcalculatesingletasktime',
            'task/ajaxdelete',
            'task/ajaxaddowner',
            'task/ajaxremoveowner',
            'timereg/ajaxstarttimereg',
            'timereg/ajaxstoptimereg',
            'timereg/mytimereg',
            'client/index',
            'client/edit',
            'fluxuser/recordExist',
            'project/recordExist',
            'account/recordExist',
            'profile/changeLanguage',
            'label/recordExist',
            'fluxuser/emailExist',
            'client/recordExist',
            'logout/logout',
        ];

        $this->createUrlResource($userGroup, $urls);
    }

    private function createAdminUserUrlResource($userGroup) {
        $urls = [
            'fluxuser/logout',
            'fluxuser/add',
            'fluxuser/edit',
            'fluxuser/ajaxconfirmdelete',
            'profile/edit',
            'project/index',
            'project/add',
            'project/edit',
            'project/ajaxconfirmdelete',
            'label/ajaxaddlabel',
            'label/ajaxconfirmdeletelabel',
            'label/ajaxeditlabel',
            'profile/confirmuser',
            'profile/resetpassword',
            'profile/confirmresetpassword',
            'fluxuser/index',
            'project/ajaxaddmember',
            'project/ajaxremovemember',
            'contact/ajaxaddtoproject',
            'contact/ajaxremovefromproject',
            'task/mytasks',
            'task/ajaxfinishtask',
            'task/index',
            'task/add',
            'task/edit',
            'task/ajaxcalculatesingletasktime',
            'task/ajaxdelete',
            'task/ajaxaddowner',
            'task/ajaxremoveowner',
            'timereg/ajaxstarttimereg',
            'timereg/ajaxstoptimereg',
            'timereg/mytimereg',
            'timereg/index',
            'timereg/add',
            'timereg/ajaxdelete',
            'timereg/edit',
            'client/index',
            'client/add',
            'client/edit',
            'client/ajaxdelete',
            'contact/ajaxdelete',
            'contact/ajaxedit',
            'contact/ajaxadd',
            'fluxuser/recordExist',
            'project/recordExist',
            'account/recordExist',
            'profile/changeLanguage',
            'label/recordExist',
            'fluxuser/emailExist',
            'timereg/ajaxfetchlabeltask',
            'timereg/ajaxfetchprojectlabels',
            'timereg/ajaxfetchuserprojects',
            'fluxuser/fetchApiToken',
            'fluxuser/deleteApiToken',
            'report/index',
            'report/client',
            'report/project',
            'report/user',
            'client/recordExist',
        ];

        $this->createUrlResource($userGroup, $urls);
    }

    private function createOwnerUserUrlResource($userGroup) {
        $urls = [
            'fluxuser/logout',
            'fluxuser/add',
            'fluxuser/edit',
            'fluxuser/ajaxconfirmdelete',
            'profile/edit',
            'project/index',
            'project/add',
            'project/edit',
            'project/ajaxconfirmdelete',
            'label/ajaxaddlabel',
            'label/ajaxconfirmdeletelabel',
            'label/ajaxeditlabel',
            'profile/confirmuser',
            'profile/resetpassword',
            'profile/confirmresetpassword',
            'fluxuser/index',
            'project/ajaxaddmember',
            'project/ajaxremovemember',
            'contact/ajaxaddtoproject',
            'contact/ajaxremovefromproject',
            'task/mytasks',
            'task/ajaxfinishtask',
            'task/index',
            'task/add',
            'task/edit',
            'task/ajaxcalculatesingletasktime',
            'task/ajaxdelete',
            'task/ajaxaddowner',
            'task/ajaxremoveowner',
            'timereg/ajaxstarttimereg',
            'timereg/ajaxstoptimereg',
            'timereg/mytimereg',
            'timereg/index',
            'timereg/add',
            'timereg/ajaxdelete',
            'timereg/edit',
            'client/index',
            'client/add',
            'client/edit',
            'client/ajaxdelete',
            'contact/ajaxdelete',
            'contact/ajaxedit',
            'contact/ajaxadd',
            'fluxuser/recordExist',
            'project/recordExist',
            'account/index',
            'account/add',
            'account/ajaxdelete',
            'account/ajaxedit',
            'account/recordExist',
            'profile/changeLanguage',
            'label/recordExist',
            'fluxuser/emailExist',
            'timereg/ajaxfetchlabeltask',
            'timereg/ ajaxfetchprojectlabels',
            'timereg/ajaxfetchuserprojects',
            'fluxuser/fetchApiToken',
            'fluxuser/deleteApiToken',
            'report/index',
            'report/client',
            'report/project',
            'report/user',
            'client/recordExist',
        ];

        $this->createUrlResource($userGroup, $urls);
    }

    private function createProjectmanagerUserUrlResource($userGroup) {
        $urls = [
            'fluxuser/logout',
            'profile/edit',
            'project/index',
            'project/add',
            'project/edit',
            'label/ajaxaddlabel',
            'label/ajaxconfirmdeletelabel',
            'label/ajaxeditlabel',
            'profile/confirmuser',
            'profile/resetpassword',
            'profile/confirmresetpassword',
            'fluxuser/index',
            'project/ajaxaddmember',
            'project/ajaxremovemember',
            'contact/ajaxaddtoproject',
            'contact/ajaxremovefromproject',
            'task/mytasks',
            'task/ajaxfinishtask',
            'task/index',
            'task/add',
            'task/edit',
            'task/ajaxcalculatesingletasktime',
            'task/ajaxdelete',
            'task/ajaxaddowner',
            'task/ajaxremoveowner',
            'timereg/ajaxstarttimereg',
            'timereg/ajaxstoptimereg',
            'timereg/mytimereg',
            'timereg/index',
            'client/index',
            'client/edit',
            'contact/ajaxdelete',
            'contact/ajaxedit',
            'contact/ajaxadd',
            'fluxuser/recordExist',
            'project/recordExist',
            'account/recordExist',
            'profile/changeLanguage',
            'label/recordExist',
            'fluxuser/emailExist',
            'timereg/ajaxfetchlabeltask',
            'timereg/ ajaxfetchprojectlabels',
            'timereg/ajaxfetchuserprojects',
            'report/index',
            'report/project',
            'client/recordExist',
        ];

        $this->createUrlResource($userGroup, $urls);
    }

    private function createUrlResource($userGroup, $urls = array()) {
        foreach ($urls as $url) {
            $resourcePermission = new Resourcepermission();
            $urlResource = $this->entityManager->getRepository('Fluxuser\Entity\Urlresource')->findOneBy(array('urlresource' => $url));
            if (!isset($urlResource)) {
                $urlResource = new Urlresource();
            }

            $urlResource->setUrlresource($url);
            $this->entityManager->persist($urlResource);
            $resourcePermission->setFkurlresource($urlResource);
            $resourcePermission->setFkusergroup($userGroup);
            $this->entityManager->persist($resourcePermission);
        }
    }

}

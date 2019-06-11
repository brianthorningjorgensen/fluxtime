<?php
namespace Fluxuser\Controller\Plugin;

use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Plugin for access controle
 */
class MyAccessPlugin extends AbstractPlugin {

    /**
     * This method checks to see if phpunit is running, if it is, we return the 
     * test db connection, else we return the production database connection.
     * 
     * @return entityManager
     */
    private function getEntityManager($e){
        $servicelocator = $e->getApplication()->getServiceManager();
            return $servicelocator->get('doctrine.entitymanager.orm_default');
    }
    
    /**
     * Get the roles and their urlresource
     * @param type $e
     * @return type
     */
    private function getDbRolesFromDb($e) {
        // get mapper
        $entitymanager = $this->getEntityManager($e);
        // get alle the roles from database
        $resourceMapper = $entitymanager->getRepository('Fluxuser\Entity\Resourcepermission');
        $resources = $resourceMapper->findAll();
        
        // making the roles array
        $roles = array();
        foreach ($resources as $resource) {
            $usergroupname = $resource->getFkusergroup()->getPermissiongroup();
            $resourcename = $resource->getFkurlresource()->getUrlresource();
            $roles[$usergroupname][] = $resourcename;
        }
        return $roles;
    }
    
    /**
     * Get the user from database by userid
     * @param type $e
     * @param type $userid
     * @return type
     */
    private function getUserFromDb($e, $userid) {
        // get mapper
        $entitymanager = $this->getEntityManager($e);
        $userMapper = $entitymanager->getRepository('Fluxuser\Entity\FluxUser');        
        $user = $userMapper->find($userid);
        return $user;
    }
    
    /**
     * Authorization of the request
     * @param type $e
     */
    public function doAuthorization($e) {
        // brugeren authentication (fluxuser entity object)
        $auth = new AuthenticationService();
        $userauth = $auth->getIdentity();
        
        if (isset($userauth) && !empty($auth->getIdentity()->getId()) ) {
            $userid = $auth->getIdentity()->getId();
            $user = $this->getUserFromDb($e, $userid);            
        }

        // prepare acl object to authorization
        $acl = new \Zend\Permissions\Acl\Acl();       
        
        
        // dynamic fetch resources by role from database
        $roles = $this->getDbRolesFromDb($e);

        // feed the acl object with roles and permissions
        $allResources = array();
        foreach ($roles as $role => $resources) {
            // tilføj rolle til acl
            $role = new \Zend\Permissions\Acl\Role\GenericRole($role);
            $acl->addRole($role);

            //tilføj resources
            $allResources = array_merge($resources, $allResources);
            foreach ($resources as $resource) {
                if (!$acl->hasResource($resource)) {
                    $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
                }
            }
            //adding url permissions
            foreach ($resources as $resource) {               
                $acl->allow($role, $resource);
            }
        }

        //getting view
        $e->getViewModel()->acl = $acl;

        // get current route + action
        $route = $e->getRouteMatch()->getMatchedRouteName() . '/' . $e->getRouteMatch()->getParam('action', 'index');
        
        //set custom user role        
        $userrole = 'guest';
        // get role the logged in user
        if (isset($user)) {
            $userrole = $user->getFkuserrole()->getPermissiongroup();
        }
        
        // redirect to error page if page has not defined route
        if ($route == null || !$acl->hasResource($route)) {
            $response = $e->getResponse();
                $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/404');
                $response->setStatusCode(404);
            
        }

        // make the security check based on the users role and the requested route    
        else if (!$acl->isAllowed($userrole, $route)) {
            $response = $e->getResponse();
            $response->getHeaders()->clearHeaders();
            $response->setStatusCode(403);
            // If the user is a guest we redirect them to the login page.
            if ($userrole == 'guest') {
                $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/');
                return $this->getController()->redirect()->toRoute('login');
            } else {
                $response->getHeaders()->addHeaderLine('Location', $e->getRequest()->getBaseUrl() . '/fluxuser');
                return $this->getController()->redirect()->toRoute('fluxuser');
            }
            
        }
        
        // page is allowed
    }

}

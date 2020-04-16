<?php

namespace Pmc\Acl;

use Pmc\Acl\Roles\ {
    Role,
    RoleList,
    RoleTree
};

/**
 * ACL manages a list of resource and role combinations to grant access.
 * 
 * Unlike more complicated ACL's from Zend Framework or Symfony which also evaluate
 * permissions such as read, write, view, etc. This is designed for CQRS where every 
 * action would be ether a command or a query and we only need to determine if access 
 * is granted or not.  
 *
 * By default access is denied unless there is an entry allowing access.
 * 
 * Roles and resources are just strings. I would recommend using the class names 
 * of commands & queries for resources and to assign your users roles or groups 
 * instead of specifying access on a per-user basis.
 * 
 * Each resource can only be allowed access to a single role. This is to promote
 * better structuring of roles and groups.
 * 
 * @author Paul Court <emails@paulcourt.co.uk>
 */
class Acl
{
    private RoleTree $roleTree;

    /**
     * Each entry in the array will consist of the array key = resource name and 
     * the array value is the role that is allowed access.
     */
    private array $accessList;
    
    
    public function __construct(RoleTree $roleTree)
    {
        $this->accessList = [];
        $this->roleTree = $roleTree;
    }
    
    public function roleTree(): RoleTree
    {
        return $this->roleTree;
    }
    
    /**
     * Grant access to a resource.
     */
    public function allow(string $resource, Role $allowedRole): void
    {
        if (array_key_exists((string)$resource, $this->accessList)) {
            throw new Exception\ResourceAlreadyDefinedException(sprintf(
                    "Resource %s has already been defined.",
                    $resource));
        }
        $this->accessList[(string)$resource] = (string)$allowedRole;
    }

    public function isAllowed(string $resource, RoleList $rolesToCheck): bool
    {
        if (!isset($this->accessList[(string)$resource])) {
            return false;
        } else {
            return in_array(
                    $this->accessList[(string)$resource], 
                    $this->roleTree->expand($rolesToCheck)->toArray()
                    );
        }
    }

}

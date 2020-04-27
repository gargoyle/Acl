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
 * By default access is denied unless there is an entry allowing access.
 * 
 * Roles and resources are just strings.
 * 
 * Each resource can only be allowed access to a single role. This is to promote
 * better structuring of roles and groups.
 * 
 * @author Paul Court <emails@paulcourt.co.uk>
 */
class Acl
{
    /**
     * @var RoleTree
     */
    private $roleTree;

    /**
     * Each entry in the array will consist of the array key = resource name and 
     * the array value is the role that is allowed access.
     * 
     * @var array
     */
    private $accessList;
    
    
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
    
    /**
     * Check if access is granted.
     * 
     * @param string $resource
     * @param array $rolesToCheck
     * @return boolean
     */
    public function isAllowed($resource, RoleList $rolesToCheck)
    {
        $expandedRolesToCheck = $this->roleTree->expand($rolesToCheck)->toArray();
        
        foreach ($this->accessList as $key => $value) {
            if ($this->isResourceMatch($resource, $key) && in_array($value, $expandedRolesToCheck)) {
                return true;
            }
        }
        
        return false;
    }
    
    private function isResourceMatch(string $requested, string $check): bool
    {
        if ($requested == $check) {
            return true;
        }
        
        $isWildcard = (substr($check, -1, 1) == '*');
        $prefix = substr($check, 0, -1);
        $isPrefixMatch = ($prefix == substr($requested, 0, strlen($prefix)));
        return ($isWildcard && $isPrefixMatch);
    }

}
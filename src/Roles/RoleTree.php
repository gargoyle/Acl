<?php
namespace Pmc\Acl\Roles;

use Pmc\Acl\Exception\ {
    BadRoleInheritanceException,
    RoleExistsException,
    RoleNotFoundException
};

/**
 * Represents a list of roles.
 * 
 * Roles can inherit from other roles, in which case they would be granted access
 * to anything the roles they inherit are allowed to access.
 * 
 * It's also possible to add "Super" roles. Which will act as if they inherit EVERY other
 * role that is added to the tree.
 *
 * @author Paul Court <emails@paulcourt.co.uk>
 */
class RoleTree
{

    private $roles;
    private $superRoles;

    public function __construct()
    {
        $this->roles = [];
        $this->superRoles = [];
    }

    private function roleExists(Role $roleToCheck): bool
    {
        if ($this->standardRoleExists($roleToCheck) || $this->superRoleExists($roleToCheck)) {
            return true;
        } else {
            return false;
        }
    }

    private function standardRoleExists(Role $roleToCheck): bool
    {
        if (array_key_exists((string) $roleToCheck, $this->roles)) {
            return true;
        } else {
            return false;
        }
    }

    private function superRoleExists(Role $roleToCheck): bool
    {
        if (in_array((string) $roleToCheck, $this->superRoles)) {
            return true;
        } else {
            return false;
        }
    }

    private function checkInheritList(RoleList $inherits): void
    {
        foreach ($inherits as $inheritedRole) {
            if (!$this->roleExists($inheritedRole)) {
                throw new RoleNotFoundException(sprintf("Attempting to inherit unknown role: %s.", ($inheritedRole)));
            }
            if ($this->superRoleExists($inheritedRole)) {
                throw new BadRoleInheritanceException("Attempting to inherit a SuperRole which is not allowed.");
            }
        }
    }

    public function addRole(Role $newRole, RoleList $inherits = null): void
    {
        if ($this->roleExists($newRole)) {
            throw new RoleExistsException(sprintf("Role name %s has already been used.", $newRole));
        }

        if ($inherits instanceof RoleList) {
            $this->checkInheritList($inherits);
            $this->roles[(string) $newRole] = $inherits;
        } else {
            $this->roles[(string) $newRole] = null;
        }
    }

    public function addSuperRole(Role $newRole): void
    {
        if ($this->roleExists($newRole)) {
            throw new RoleExistsException(sprintf("Role name %s has already been used.", $newRole));
        }
        $this->superRoles[] = (string) $newRole;
    }

    public function expand(RoleList $roles): RoleList
    {        
        $expanded = [];
        foreach ($roles as $role) {
            if (in_array($role, $this->superRoles)) {
                $expanded = $this->allRoles()->toArray();
                break;
            }
            
            if (!$this->roleExists($role)) {
                throw new RoleNotFoundException(sprintf("Unable to expand unknown role: %s.", $role));
            }
            
            if ($this->roles[(string)$role] instanceof RoleList) {
                $expanded = array_merge(
                        $expanded, 
                        $this->expand($this->roles[(string)$role])->toArray()
                        );
            }
            $expanded[] = (string)$role;
        }
        
        return new RoleList($expanded);
    }

    public function allRoles(): RoleList
    {
        $allRoles = array_merge(array_keys($this->roles), $this->superRoles);
        return new RoleList($allRoles);
    }

}

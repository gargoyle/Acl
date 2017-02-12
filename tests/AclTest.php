<?php

namespace Pmc\Acl;

use PHPUnit\Framework\TestCase;
use Pmc\Acl\Roles\ {
    Role,
    RoleList,
    RoleTree
};

/**
 * @author Paul Court <emails@paulcourt.co.uk>
 */
class AclTest extends TestCase
{
    private function getExampleTree()
    {
        $roles = new RoleTree();
        $roles->addRole(new Role('Level 0'));
        $roles->addRole(new Role('Level 1'), new RoleList(['Level 0']));
        $roles->addRole(new Role('Level 2'), new RoleList(['Level 1']));
        $roles->addRole(new Role('Group A'));
        $roles->addRole(new Role('Group B'), new RoleList(['Group A', 'Level 1']));
        $roles->addSuperRole(new Role('Admin'));
        return $roles;
    }
    
    public function testRoleAccessIsDeniedByDefault()
    {
        $acl = new Acl($this->getExampleTree());
        $this->assertFalse($acl->isAllowed("Any Resource", new RoleList(['Level 0'])));
    }
    
    public function testSuperRoleAccessIsDeniedByDefault()
    {
        $acl = new Acl($this->getExampleTree());
        $this->assertFalse($acl->isAllowed("Any Resource", new RoleList(['Admin'])));
    }
    
    public function testGrantedRoleAccessIsAllowedDirectly()
    {
        $acl = new Acl($this->getExampleTree());
        $acl->allow("Some Resource", new Role("Level 1"));
        $this->assertTrue($acl->isAllowed("Some Resource", new RoleList(['Level 1'])));
    }
    
    public function testGrantedRoleAccessIsAllowedViaInheritance()
    {
        $acl = new Acl($this->getExampleTree());
        $acl->allow("Some Resource", new Role("Level 1"));
        $this->assertTrue($acl->isAllowed("Some Resource", new RoleList(['Level 2'])));
    }

    public function testNonGrantedRoleIsDeniedAccess()
    {
        $acl = new Acl($this->getExampleTree());
        $acl->allow("Some Resource", new Role("Level 1"));
        $this->assertFalse($acl->isAllowed("Some Resource", new RoleList(['Level 0'])));
    }
    
    public function testSuperRolesAreAllowedAccess()
    {
        $acl = new Acl($this->getExampleTree());
        $acl->allow("Some Resource", new Role("Level 1"));
        $this->assertTrue($acl->isAllowed("Some Resource", new RoleList(['Admin'])));
    }
}

<?php

namespace Pmc\Acl\Tests\Roles;

use PHPUnit_Framework_TestCase;
use Pmc\Acl\ {
    Exception\BadRoleInheritanceException,
    Exception\RoleExistsException,
    Exception\RoleNotFoundException,
    Roles\Role,
    Roles\RoleList,
    Roles\RoleTree
};

/**
 * Define the capabilities for RoleTree
 *
 * @author Paul Court <emails@paulcourt.co.uk>
 */
class RoleTreeTest extends PHPUnit_Framework_TestCase
{
    public function testNewRoleTreesAreEmpty()
    {
        $tree = new RoleTree();
        $this->assertEquals(0, $tree->allRoles()->count());
    }
    
    public function testUniqueRolesAreAddedToTheTree()
    {
        $tree = new RoleTree();
        $tree->addRole(new Role('One'));
        $tree->addRole(new Role('Two'));
        
        $listAsArray = $tree->allRoles()->toArray();
        
        $this->assertEquals(2, $tree->allRoles()->count());
        $this->assertEquals('One', $listAsArray[0]);
        $this->assertEquals('Two', $listAsArray[1]);
    }
    
    public function testNonExistingRolesCannotBeInherited()
    {
        $this->expectException(RoleNotFoundException::class);
        $tree = new RoleTree();
        $tree->addRole(
                new Role('One'), 
                new RoleList(['This Doesnt Exist']));
    }
    
    public function testSuperRolesCannotBeInherited()
    {
        $this->expectException(BadRoleInheritanceException::class);
        $tree = new RoleTree();
        $tree->addSuperRole(new Role('Super Role'));
        $tree->addRole(
                new Role('One'), 
                new RoleList(['Super Role']));
    }
    
    public function testExistingRolesCanBeInherited()
    {
        $tree = new RoleTree();
        $tree->addRole(new Role('One'));
        $tree->addRole(
                new Role('Two'), 
                new RoleList(['One']));
        
        $listAsArray = $tree->allRoles()->toArray();
        
        $this->assertEquals(2, $tree->allRoles()->count());
        $this->assertEquals('One', $listAsArray[0]);
        $this->assertEquals('Two', $listAsArray[1]);
    }
    
    public function testRoleNamesMustBeUnique()
    {
        $this->expectException(RoleExistsException::class);
        $tree = new RoleTree();
        $tree->addRole(new Role('One'));
        $tree->addRole(new Role('One'));
    }
    
    public function testSuperRoleNamesMustBeUnique()
    {
        $this->expectException(RoleExistsException::class);
        $tree = new RoleTree();
        $tree->addSuperRole(new Role('Super'));
        $tree->addSuperRole(new Role('Super'));
    }
    
    public function testAllocatedRolesAreExpandedOnlyToInheritedList()
    {
        $tree = $this->getExampleTree();
        $this->assertArrayHasNotHas(
                ['Level 0'], 
                ['Level 1', 'Level 2', 'Group A', 'Group B'], 
                $tree->expand(new RoleList(['Level 0']))->toArray());
        $this->assertArrayHasNotHas(
                ['Level 0', 'Level 1'],
                ['Level 2', 'Group A', 'Group B'],
                $tree->expand(new RoleList(['Level 1']))->toArray());
        $this->assertArrayHasNotHas(
                ['Level 0', 'Level 1', 'Level 2'],
                ['Group A', 'Group B'],
                $tree->expand(new RoleList(['Level 2']))->toArray());        
        $this->assertArrayHasNotHas(
                ['Level 0', 'Level 1', 'Group A', 'Group B'],
                ['Level 2'],
                $tree->expand(new RoleList(['Level 1', 'Group B']))->toArray());
    }
    
    public function testSuperRolesExpandToAllRoles()
    {
        $tree = $this->getExampleTree();
        $tree->addSuperRole(new Role('Admin'));
        $this->assertArrayHasNotHas(
                ['Level 0', 'Level 1', 'Level 2','Group A','Group B'], 
                [],
                $tree->expand(new RoleList(['Admin']))->toArray()
                );
    }
    
    public function testAllRegisteredRolesCanBeListed()
    {
        $tree = $this->getExampleTree();
        $this->assertArraySubset(
                ['Level 0', 'Level 1', 'Level 2','Group A','Group B'],
                $tree->allRoles()->toArray()
                );
    }
    
    private function getExampleTree()
    {
        $roles = new RoleTree();
        $roles->addRole(new Role('Level 0'));
        $roles->addRole(new Role('Level 1'), new RoleList(['Level 0']));
        $roles->addRole(new Role('Level 2'), new RoleList(['Level 1']));
        $roles->addRole(new Role('Group A'));
        $roles->addRole(new Role('Group B'), new RoleList(['Group A', 'Level 1']));
        return $roles;
    }
    
    private function assertArrayHasNotHas(array $shouldHave, array $shouldNotHave, array $toCheck)
    {
        foreach ($shouldHave as $key) {
            $this->assertTrue(in_array($key, $toCheck));
        }
        
        foreach ($shouldNotHave as $key) {
            $this->assertFalse(in_array($key, $toCheck));
        }
    }
}

<?php

namespace Pmc\Acl\Tests\Roles;

use PHPUnit_Framework_TestCase;
use Pmc\Acl\Roles\ {
    Role,
    RoleList
};

/**
 * Description of RoleTest
 *
 * @author Paul Court <emails@paulcourt.co.uk>
 */
class RoleListTest extends PHPUnit_Framework_TestCase
{
    private function generateTestList(): RoleList
    {
        $testRoleNames = ['one','two','three'];
        $list = new RoleList($testRoleNames);
        return $list;
    }
    
    public function testRoleListsCanBeEmpty()
    {
        $list = new RoleList([]);
        $this->assertInstanceOf(RoleList::class, $list);
        $this->assertEquals(0, $list->count());
    }
    
    public function testListCanBeIterated()
    {
        $list = $this->generateTestList();
        foreach ($list as $key => $role) {
            $this->assertInstanceOf(Role::class, $role);
        }
    }
    
    public function testCanBeConvertedToAnArray()
    {
        $list = $this->generateTestList();
        $listAsArray = $list->toArray();
        $this->assertEquals('one', $listAsArray[0]);
        $this->assertEquals('two', $listAsArray[1]);
        $this->assertEquals('three', $listAsArray[2]);
    }
}

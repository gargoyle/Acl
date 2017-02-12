<?php

namespace Pmc\Acl\Tests\Roles;

use PHPUnit_Framework_TestCase;
use Pmc\Acl\ {
    Exception\BadRoleNameException,
    Roles\Role
};

/**
 * Description of RoleTest
 *
 * @author Paul Court <emails@paulcourt.co.uk>
 */
class RoleTest extends PHPUnit_Framework_TestCase
{
    public function badRoleNameProvider()
    {
        return [
            ["\n\t \t\n "],
            [""],
            [" "]
        ];
    }
    
    public function validRoleNameProvider()
    {
        return [
            ["\n\tTEST\t\n ", "TEST"],
            ["TEST", "TEST"],
            [" TEST ", "TEST"],
            ["TEST MORE ", "TEST MORE"]
        ];
    }
    
    /**
     * @dataProvider badRoleNameProvider
     */
    public function testSanitisedRoleNamesCannotBeEmpty($name)
    {
        $this->expectException(BadRoleNameException::class);
        $role = new Role($name);
    }
    
    public function testSanitisedRoleNameCannotBeLongerThan50Chars()
    {
        $this->expectException(BadRoleNameException::class);
        $role = new Role("A VERY SILLY\t\t AND RALLY QUITE LONG\n \n ROLE NAME WHICH SHOULD THROW AN EXCEPTION.");
    }
    
    /**
     * @dataProvider validRoleNameProvider
     */
    public function testValidNamesAreSanitised($name, $expected)
    {
        $role = new Role($name);
        $this->assertEquals($expected, (string)$role);
    }
}

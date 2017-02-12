<?php

namespace Pmc\Acl\Roles;

use Countable;
use Iterator;
use Pmc\Acl\Roles\Role;
use SplFixedArray;

/**
 * Represents a flat list of roles.
 *
 * @author Paul Court <emails@paulcourt.co.uk>
 */
class RoleList implements Iterator, Countable
{

    /**
     * @var Role[]
     */
    private $roles;

    /**
     *
     * @var int
     */
    private $pointer;
    
    public function __construct(array $roles)
    {
        $roles = array_unique($roles);
        $this->roles = new SplFixedArray(count($roles));
        $this->pointer = 0;
        
        $i = 0;
        foreach ($roles as $roleToAdd) {
            $this->roles[$i] = new Role($roleToAdd);
            $i++;
        }
    }

    public function toArray(): array
    {
        $scalarList = [];
        array_walk($this->roles, function($value, $key) use (&$scalarList){
            $scalarList[] = (string)$value;
        });
        return $scalarList;
    }
    
    public function current(): Role
    {
        return $this->roles[$this->pointer];
    }

    public function key(): int
    {
        return $this->pointer;
    }

    public function next(): void
    {
        $this->pointer++;
    }

    public function rewind(): void
    {
        $this->pointer = 0;
    }

    public function valid(): bool
    {
        return isset($this->roles[$this->pointer]);
    }

    public function count(): int
    {
        return $this->roles->count();
    }

}

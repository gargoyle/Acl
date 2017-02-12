<?php

namespace Pmc\Acl\Exception;

/**
 * Thrown when attempting to add a role name that has already been used.
 *
 * @author Paul Court <emails@paulcourt.co.uk>
 */
class RoleExistsException extends \InvalidArgumentException
{
    
}

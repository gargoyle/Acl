<?php

namespace Pmc\Acl\Exception;

/**
 * Thrown when attempting to use a role name which has not been defined in the tree.
 *
 * @author Paul Court <emails@paulcourt.co.uk>
 */
class RoleNotFoundException extends \InvalidArgumentException
{
    
}

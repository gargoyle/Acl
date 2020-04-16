<?php

namespace Pmc\Acl\Roles;

use Pmc\Acl\Exception\BadRoleNameException;

/**
 * Value object for Role names
 *
 * @author Paul Court <emails@paulcourt.co.uk>
 */
class Role
{
    private string $value;

    public function __construct(string $value)
    {
        $sanitizedValue = trim(filter_var(
                $value, FILTER_SANITIZE_STRING,
                array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_NO_ENCODE_QUOTES)));

        if (empty($sanitizedValue)) {
            throw new BadRoleNameException('Empty value is not permitted for Role names');
        }

        if (mb_strlen($sanitizedValue) > 50) {
            throw new BadRoleNameException('Role names cannot be longer than 50 characters');
        }
        
        $this->value = $sanitizedValue;
    }

    public function __toString() : string
    {
        return $this->value;
    }

}

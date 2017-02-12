# Acl

ACL manages a list of resource and role combinations to grant access.

Unlike more complicated ACL's from Zend Framework or Symfony which also evaluate
permissions such as read, write, view, etc. This is designed for CQRS where every 
action would be ether a command or a query and we only need to determine if access 
is granted or not.  

By default access is denied unless there is an entry allowing access.

Roles and resources are just strings. I would recommend using the class names 
of commands & queries for resources and to assign your users roles or groups 
instead of specifying access on a per-user basis.

Each resource can only be allowed access from a single role. This is to promote
better structuring of roles and groups.

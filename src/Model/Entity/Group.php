<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Group extends Entity
{
    /**
     * Needed for ACLs.
     * Copied from https://github.com/mattmemmesheimer/cakephp-3-acl-example/blob/5ff30a394d9a21dacfbd568b5561ca00b767ed22/README.md
     */
    public function parentNode()
    {
	return null;
    }
}

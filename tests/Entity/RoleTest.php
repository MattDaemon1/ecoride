<?php

namespace App\Tests\Entity;

use App\Entity\Role;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $role = new Role();

        // Test des propriétés simples
        $role->setLibelle('Administrateur');
        $this->assertEquals('Administrateur', $role->getLibelle());

        // Relation avec User
        $user = new User();
        $role->addUser($user);
        $this->assertCount(1, $role->getUser());
        $this->assertTrue($role->getUser()->contains($user));

        $role->removeUser($user);
        $this->assertCount(0, $role->getUser());
    }
}

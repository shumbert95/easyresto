<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Client;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setFirstName('admin');
        $user->setLastName('admin');
        $user->setPlainPassword('admin');
        $user->setEnabled(1);
        $user->setType(1);
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setEmail('admin@email.fr');
        $manager->persist($user);
        $manager->flush();

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername('user-'.$i);
            $user->setFirstName('user-'.$i);
            $user->setLastName('user-'.$i);
            $user->setPlainPassword('password');
            $user->setEnabled(1);
            $user->setEmail('email'.$i.'@email.fr');
            if ($i < 5) {
                $type = 3;
            } else {
                $type = 1;
            }
            $user->setType($type);
            $manager->persist($user);
            $manager->flush();

            $this->addReference('user-'.$i, $user);
        }

        for ($i = 0; $i < 5; $i++) {
            $restaurant = new Restaurant();
            $restaurant->setStatus(1);
            $restaurant->setAddress('10 rue cels');
            $restaurant->setPostalCode('75014');
            $restaurant->setCity('Paris');
            $restaurant->setPhone('0123456789');
            $restaurant->setName('Restaurant '.$i);
            $restaurant->setDescription('Restaurant '.$i);
            $restaurant->setOpen(1);
            $restaurant->addUser($this->getReference('user-'.$i));
            $manager->persist($restaurant);
            $manager->flush();
        }

        for ($i = 5; $i < 10; $i++) {
            $client = new Client();
            $client->setAddress('10 rue cels');
            $client->setPostalCode('75014');
            $client->setCity('Paris');
            $client->setPhone('0123456789');
            $client->setUser($this->getReference('user-'.$i));
            $manager->persist($client);
            $manager->flush();
        }
    }
}
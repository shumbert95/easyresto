<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\CategoryRestaurant;
use AppBundle\Entity\Client;
use AppBundle\Entity\Content;
use AppBundle\Entity\Ingredient;
use AppBundle\Entity\Moment;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\ReservationContent;
use AppBundle\Entity\ReservationSeat;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\TabMeal;
use AppBundle\Entity\Tag;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Unirest\Response;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //SUPER ADMIN
        $user = new User();
        $user->setUsername('admin@email.fr');
        $user->setFirstName('admin');
        $user->setLastName('admin');
        $user->setPlainPassword('admin');
        $user->setEnabled(1);
        $user->setType(1);
        $user->setRoles(array('ROLE_SUPER_ADMIN'));
        $user->setEmail('admin@email.fr');
        $user->setCivility(1);
        $user->setPhoneNumber("010101010101");
        $manager->persist($user);
        $manager->flush();

        //RESTORERS
        $user = new User();
        $user->setUsername('firstrestorer@test.com');
        $user->setFirstName('Firstname');
        $user->setLastName('Lastname');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('firstrestorer@test.com');
        $user->setAddress('Adresse');
        $user->setPostalCode(75000);
        $user->setPhoneNumber("0101010101");
        $user->setType(2);
        $user->setCivility(1);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('firstrestorer@test.com', $user);

        $user = new User();
        $user->setUsername('secondrestorer@test.com');
        $user->setFirstName('Firstname');
        $user->setLastName('Lastname');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('secondrestorer@test.com');
        $user->setAddress('Adresse');
        $user->setPostalCode(75000);
        $user->setPhoneNumber("0101010101");
        $user->setType(2);
        $user->setCivility(1);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('secondrestorer@test.com', $user);

        $user = new User();
        $user->setUsername('thirdrestorer@test.com');
        $user->setFirstName('Firstname');
        $user->setLastName('Lastname');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('thirdrestorer@test.com');
        $user->setAddress('Adresse');
        $user->setPostalCode(75000);
        $user->setPhoneNumber("0101010101");
        $user->setType(2);
        $user->setCivility(1);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('thirdrestorer@test.com', $user);

        $user = new User();
        $user->setUsername('fourthrestorer@test.com');
        $user->setFirstName('Firstname');
        $user->setLastName('Lastname');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('fourthrestorer@test.com');
        $user->setAddress('Adresse');
        $user->setPostalCode(75000);
        $user->setPhoneNumber("0101010101");
        $user->setType(2);
        $user->setCivility(1);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('fourthrestorer@test.com', $user);

        $user = new User();
        $user->setUsername('fifthrestorer@test.com');
        $user->setFirstName('Firstname');
        $user->setLastName('Lastname');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('fifthrestorer@test.com');
        $user->setAddress('Adresse');
        $user->setPostalCode(75000);
        $user->setPhoneNumber("0101010101");
        $user->setType(2);
        $user->setCivility(1);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('fifthrestorer@test.com', $user);

        $user = new User();
        $user->setUsername('sixthrestorer@test.com');
        $user->setFirstName('Firstname');
        $user->setLastName('Lastname');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('sixthrestorer@test.com');
        $user->setAddress('Adresse');
        $user->setPostalCode(75000);
        $user->setPhoneNumber("0101010101");
        $user->setType(2);
        $user->setCivility(2);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('sixthrestorer@test.com', $user);

        $user = new User();
        $user->setUsername('seventhrestorer@test.com');
        $user->setFirstName('Firstname');
        $user->setLastName('Lastname');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('seventhrestorer@test.com');
        $user->setAddress('Adresse');
        $user->setPostalCode(75000);
        $user->setPhoneNumber("0101010101");
        $user->setType(2);
        $user->setCivility(2);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('seventhrestorer@test.com', $user);

        $user = new User();
        $user->setUsername('eighthrestorer@test.com');
        $user->setFirstName('Firstname');
        $user->setLastName('Lastname');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('eighthrestorer@test.com');
        $user->setAddress('Adresse');
        $user->setPostalCode(75000);
        $user->setPhoneNumber("0101010101");
        $user->setType(2);
        $user->setCivility(2);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('eighthrestorer@test.com', $user);

        $user = new User();
        $user->setUsername('ninthrestorer@test.com');
        $user->setFirstName('Firstname');
        $user->setLastName('Lastname');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('ninthrestorer@test.com');
        $user->setAddress('Adresse');
        $user->setPostalCode(75000);
        $user->setPhoneNumber("0101010101");
        $user->setType(2);
        $user->setCivility(2);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('ninthrestorer@test.com', $user);

        $user = new User();
        $user->setUsername('tenthrestorer@test.com');
        $user->setFirstName('Firstname');
        $user->setLastName('Lastname');
        $user->setPlainPassword('password');
        $user->setEnabled(2);
        $user->setEmail('tenthrestorer@test.com');
        $user->setAddress('Adresse');
        $user->setPostalCode(75000);
        $user->setPhoneNumber("0101010101");
        $user->setType(2);
        $user->setCivility(1);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('tenthrestorer@test.com', $user);



        //CLIENTS
        $user = new User();
        $user->setUsername('firstclient@test.com');
        $user->setFirstName('first');
        $user->setLastName('client');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('firstclient@test.com');
        $user->setPostalCode(75012);
        $user->setPhoneNumber(0102030405);
        $user->setType(1);
        $user->setCivility(2);
        $manager->persist($user);
        $manager->flush();
        $this->addReference('firstclient@test.com', $user);

        //RESTAURANT CATEGORIES
        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Indien");
        $categoryRestaurant->setStatus(CategoryRestaurant::STATUS_ONLINE);
        $manager->persist($categoryRestaurant);
        $manager->flush();

        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Japonais");
        $categoryRestaurant->setStatus(CategoryRestaurant::STATUS_ONLINE);
        $manager->persist($categoryRestaurant);
        $manager->flush();

        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Chinois");
        $categoryRestaurant->setStatus(CategoryRestaurant::STATUS_ONLINE);
        $manager->persist($categoryRestaurant);
        $manager->flush();

        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Turc");
        $categoryRestaurant->setStatus(CategoryRestaurant::STATUS_ONLINE);
        $manager->persist($categoryRestaurant);
        $manager->flush();

        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Italien");
        $categoryRestaurant->setStatus(CategoryRestaurant::STATUS_ONLINE);
        $manager->persist($categoryRestaurant);
        $manager->flush();

        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Burger");
        $categoryRestaurant->setStatus(CategoryRestaurant::STATUS_ONLINE);
        $manager->persist($categoryRestaurant);
        $manager->flush();

        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Bar");
        $categoryRestaurant->setStatus(CategoryRestaurant::STATUS_ONLINE);
        $manager->persist($categoryRestaurant);
        $manager->flush();

        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Mexicain");
        $categoryRestaurant->setStatus(CategoryRestaurant::STATUS_ONLINE);
        $manager->persist($categoryRestaurant);
        $manager->flush();

        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Pizza");
        $categoryRestaurant->setStatus(CategoryRestaurant::STATUS_ONLINE);
        $manager->persist($categoryRestaurant);
        $manager->flush();

        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Sandwich");
        $categoryRestaurant->setStatus(CategoryRestaurant::STATUS_ONLINE);
        $manager->persist($categoryRestaurant);
        $manager->flush();


        //Moments
        $moment = new Moment();
        $moment->setName("Petit Déjeuner");
        $moment->setMoment(Moment::TYPE_MORNING);
        $moment->setStatus(1);
        $manager->persist($moment);
        $manager->flush();

        $moment = new Moment();
        $moment->setName("Midi");
        $moment->setMoment(Moment::TYPE_LUNCH);
        $moment->setStatus(1);
        $manager->persist($moment);
        $manager->flush();

        $moment = new Moment();
        $moment->setName("Apéro");
        $moment->setMoment(Moment::TYPE_APERITIF);
        $moment->setStatus(1);
        $manager->persist($moment);
        $manager->flush();

        $moment = new Moment();
        $moment->setName("Dîner");
        $moment->setMoment(Moment::TYPE_DINNER);
        $moment->setStatus(1);
        $manager->persist($moment);
        $manager->flush();

        //RESTAURANTS
        $restaurant = new Restaurant();
        $restaurant->setStatus(Restaurant::STATUS_ONLINE);
        $restaurant->setOpen(0);
        $restaurant->addUser($this->getReference('firstrestorer@test.com'));
        $restaurant->setPicture("http://www.frogpubs.com/fr/pics/data/pubs/illustrations/4-191-1200x650.jpg");
        $restaurant->setAverageNote(4);
        $restaurant->setLongitude(0);
        $restaurant->setLatitude(0);
        $manager->persist($restaurant);
        $manager->flush();

        $restaurant = new Restaurant();
        $restaurant->setStatus(Restaurant::STATUS_ONLINE);
        $restaurant->setOpen(0);
        $restaurant->addUser($this->getReference('secondrestorer@test.com'));
        $restaurant->setPicture("http://www.frogpubs.com/fr/pics/data/pubs/illustrations/4-191-1200x650.jpg");
        $restaurant->setAverageNote(4);
        $restaurant->setLongitude(0);
        $restaurant->setLatitude(0);
        $manager->persist($restaurant);
        $manager->flush();

        $restaurant = new Restaurant();
        $restaurant->setStatus(Restaurant::STATUS_ONLINE);
        $restaurant->setOpen(0);
        $restaurant->addUser($this->getReference('thirdrestorer@test.com'));
        $restaurant->setPicture("http://www.frogpubs.com/fr/pics/data/pubs/illustrations/4-191-1200x650.jpg");
        $restaurant->setAverageNote(4);
        $restaurant->setLongitude(0);
        $restaurant->setLatitude(0);
        $manager->persist($restaurant);
        $manager->flush();

        $restaurant = new Restaurant();
        $restaurant->setStatus(Restaurant::STATUS_ONLINE);
        $restaurant->setOpen(0);
        $restaurant->addUser($this->getReference('fourthrestorer@test.com'));
        $restaurant->setPicture("http://www.frogpubs.com/fr/pics/data/pubs/illustrations/4-191-1200x650.jpg");
        $restaurant->setAverageNote(4);
        $restaurant->setLongitude(0);
        $restaurant->setLatitude(0);
        $manager->persist($restaurant);
        $manager->flush();

        $restaurant = new Restaurant();
        $restaurant->setStatus(Restaurant::STATUS_ONLINE);
        $restaurant->setOpen(0);
        $restaurant->addUser($this->getReference('fifthrestorer@test.com'));
        $restaurant->setPicture("http://www.frogpubs.com/fr/pics/data/pubs/illustrations/4-191-1200x650.jpg");
        $restaurant->setAverageNote(4);
        $restaurant->setLongitude(0);
        $restaurant->setLatitude(0);
        $manager->persist($restaurant);
        $manager->flush();

        $restaurant = new Restaurant();
        $restaurant->setStatus(Restaurant::STATUS_ONLINE);
        $restaurant->setOpen(0);
        $restaurant->addUser($this->getReference('sixthrestorer@test.com'));
        $restaurant->setPicture("http://www.frogpubs.com/fr/pics/data/pubs/illustrations/4-191-1200x650.jpg");
        $restaurant->setAverageNote(4);
        $restaurant->setLongitude(0);
        $restaurant->setLatitude(0);
        $manager->persist($restaurant);
        $manager->flush();

        $restaurant = new Restaurant();
        $restaurant->setStatus(Restaurant::STATUS_ONLINE);
        $restaurant->setOpen(0);
        $restaurant->addUser($this->getReference('seventhrestorer@test.com'));
        $restaurant->setPicture("http://www.frogpubs.com/fr/pics/data/pubs/illustrations/4-191-1200x650.jpg");
        $restaurant->setAverageNote(4);
        $restaurant->setLongitude(0);
        $restaurant->setLatitude(0);
        $manager->persist($restaurant);
        $manager->flush();

        $restaurant = new Restaurant();
        $restaurant->setStatus(Restaurant::STATUS_ONLINE);
        $restaurant->setOpen(0);
        $restaurant->addUser($this->getReference('eighthrestorer@test.com'));
        $restaurant->setPicture("http://www.frogpubs.com/fr/pics/data/pubs/illustrations/4-191-1200x650.jpg");
        $restaurant->setAverageNote(4);
        $restaurant->setLongitude(0);
        $restaurant->setLatitude(0);
        $manager->persist($restaurant);
        $manager->flush();

        $restaurant = new Restaurant();
        $restaurant->setStatus(Restaurant::STATUS_ONLINE);
        $restaurant->setOpen(0);
        $restaurant->addUser($this->getReference('ninthrestorer@test.com'));
        $restaurant->setPicture("http://www.frogpubs.com/fr/pics/data/pubs/illustrations/4-191-1200x650.jpg");
        $restaurant->setAverageNote(4);
        $restaurant->setLongitude(0);
        $restaurant->setLatitude(0);
        $manager->persist($restaurant);
        $manager->flush();

        $restaurant = new Restaurant();
        $restaurant->setStatus(Restaurant::STATUS_ONLINE);
        $restaurant->setOpen(0);
        $restaurant->addUser($this->getReference('tenthrestorer@test.com'));
        $restaurant->setPicture("http://www.frogpubs.com/fr/pics/data/pubs/illustrations/4-191-1200x650.jpg");
        $restaurant->setAverageNote(4);
        $restaurant->setLongitude(0);
        $restaurant->setLatitude(0);
        $manager->persist($restaurant);
        $manager->flush();


    }
}
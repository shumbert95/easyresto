<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\CategoryMeal;
use AppBundle\Entity\CategoryRestaurant;
use AppBundle\Entity\Client;
use AppBundle\Entity\Meal;
use AppBundle\Entity\Restaurant;
use AppBundle\Entity\TabMeal;
use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

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
        $user->setFirstName('first');
        $user->setLastName('restorer');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('firstrestorer@test.com');
        $user->setAddress('Adresse first');
        $user->setAddressComplement('Adresse first complement');
        $user->setPostalCode(75012);
        $user->setPhoneNumber(0102030405);
        $user->setType(2);
        $user->setCivility(1);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('firstrestorer@test.com', $user);

        $user = new User();
        $user->setUsername('secondrestorer@test.com');
        $user->setFirstName('second');
        $user->setLastName('restorer');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('secondrestorer@test.com');
        $user->setAddress('Adresse second');
        $user->setAddressComplement('Adresse second complement');
        $user->setPostalCode(75012);
        $user->setPhoneNumber(0102030405);
        $user->setType(2);
        $user->setCivility(2);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('secondrestorer@test.com', $user);

        $user = new User();
        $user->setUsername('thirdrestorer@test.com');
        $user->setFirstName('third');
        $user->setLastName('restorer');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('thirdrestorer@test.com');
        $user->setAddress('Adresse third');
        $user->setAddressComplement('Adresse third complement');
        $user->setPostalCode(75012);
        $user->setPhoneNumber(0102030405);
        $user->setType(2);
        $user->setCivility(2);
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);
        $manager->flush();
        $this->addReference('thirdrestorer@test.com', $user);

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

        $user = new User();
        $user->setUsername('secondclient@test.com');
        $user->setFirstName('second');
        $user->setLastName('client');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('secondclient@test.com');
        $user->setPostalCode(75012);
        $user->setPhoneNumber(0102030405);
        $user->setType(1);
        $user->setCivility(1);
        $manager->persist($user);
        $manager->flush();
        $this->addReference('secondclient@test.com', $user);

        $user = new User();
        $user->setUsername('thirdclient@test.com');
        $user->setFirstName('third');
        $user->setLastName('client');
        $user->setPlainPassword('password');
        $user->setEnabled(1);
        $user->setEmail('thirdclient@test.com');
        $user->setPostalCode(75012);
        $user->setPhoneNumber(0102030405);
        $user->setType(1);
        $user->setCivility(1);
        $manager->persist($user);
        $manager->flush();
        $this->addReference('thirdclient@test.com', $user);

        //RESTAURANT CATEGORIES
        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Japonais");
        $categoryRestaurant->setStatus(1);
        $this->addReference('categoryJap',$categoryRestaurant);

        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Burgers");
        $categoryRestaurant->setStatus(1);
        $this->addReference('categoryBurgers',$categoryRestaurant);

        $categoryRestaurant = new CategoryRestaurant();
        $categoryRestaurant->setName("Indien");
        $categoryRestaurant->setStatus(1);
        $this->addReference('categoryIndien',$categoryRestaurant);

        //RESTAURANTS
        $restaurant = new Restaurant();
        $restaurant->setStatus(1);
        $restaurant->setAddress('114 Avenue de France');
        $restaurant->setPostalCode('75013');
        $restaurant->setCity('Paris');
        $restaurant->setPhone('0123456789');
        $restaurant->setName('The Frog & British Library');
        $restaurant->setDescription('The Frog & British Library, pub et restaurant');
        $restaurant->setOpen(1);
        $restaurant->addCategory($this->getReference('categoryBurgers'));
        $restaurant->addUser($this->getReference('firstrestorer@test.com'));
        $restaurant->setSeats(10);
        $restaurant->setPicture("http://www.frogpubs.com/fr/pics/data/pubs/illustrations/4-191-1200x650.jpg");
        $restaurant->setAverageNote(8.3);
        $manager->persist($restaurant);
        $manager->flush();
        $this->addReference('firstRestaurant', $restaurant);

        $restaurant = new Restaurant();
        $restaurant->setStatus(1);
        $restaurant->setAddress('29 Rue Mazarine');
        $restaurant->setPostalCode('75006');
        $restaurant->setCity('Paris');
        $restaurant->setPhone('0123456789');
        $restaurant->setName('Kodawari Ramen');
        $restaurant->setDescription('Kodawari Ramen, les meilleurs de Paris');
        $restaurant->setOpen(1);
        $restaurant->addCategory($this->getReference('categoryJap'));
        $restaurant->addUser($this->getReference('secondrestorer@test.com'));
        $restaurant->setSeats(10);
        $restaurant->setPicture("http://www.hemaposesesvalises.fr/wp-content/uploads/2017/11/Kodawari_ramen_restaurant_paris_japon_decor-1080x675.jpg");
        $restaurant->setAverageNote(9.1);
        $manager->persist($restaurant);
        $manager->flush();
        $this->addReference('secondRestaurant', $restaurant);


        $restaurant = new Restaurant();
        $restaurant->setStatus(1);
        $restaurant->setAddress('19 Rue du Télégraphe');
        $restaurant->setPostalCode('75020');
        $restaurant->setCity('Paris');
        $restaurant->setPhone('0123456789');
        $restaurant->setName('Aarchna');
        $restaurant->setDescription('Derrière une jolie façade en bois sculpté, ce restaurant sert des spécialités indiennes traditionnelles.
');
        $restaurant->addCategory($this->getReference('categoryIndien'));
        $restaurant->setOpen(1);
        $restaurant->addUser($this->getReference('thirdrestorer@test.com'));
        $restaurant->setSeats(10);
        $restaurant->setPicture("https://u.tfstatic.com/restaurant_photos/964/15964/169/612/aarchna-vue-de-la-salle-9c000.jpg");
        $restaurant->setAverageNote(7.9);
        $manager->persist($restaurant);
        $manager->flush();
        $this->addReference('thirdRestaurant', $restaurant);




        //TABS
        for($i=1; $i<=3; $i++){
            $tab = new TabMeal();
            $tab->setName("Tab number ".$i);
            $tab->setPosition($i);
            $tab->setStatus(1);
            $tab->setRestaurant($this->getReference("firstRestaurant"));
            $manager->persist($tab);
            $manager->flush();
            $this->addReference('firstRestaurant tab-'.$i,$tab);
        }
        for($i=1; $i<=3; $i++){
            $tab = new TabMeal();
            $tab->setName("Tab number ".$i);
            $tab->setPosition($i+1);
            $tab->setStatus(1);
            $tab->setRestaurant($this->getReference("secondRestaurant"));
            $manager->persist($tab);
            $manager->flush();
            $this->addReference('secondRestaurant tab-'.$i,$tab);
        }
        for($i=1; $i<=3; $i++){
            $tab = new TabMeal();
            $tab->setName("Tab number ".$i);
            $tab->setPosition($i+1);
            $tab->setStatus(1);
            $tab->setRestaurant($this->getReference("thirdRestaurant"));
            $manager->persist($tab);
            $manager->flush();
            $this->addReference('thirdRestaurant tab-'.$i,$tab);


        }

        //CATEGORIES
        for($i=1;$i<=3;$i++){
            for($j=1;$j<=2;$j++) {
                $category = new CategoryMeal();
                $category->setName("Category number " . $i . " tab" . $j);
                $category->setStatus(1);
                $category->setPosition($j);
                $category->setRestaurant($this->getReference('firstRestaurant'));
                $category->setTabMeal($this->getReference('firstRestaurant tab-' . $i));
                $manager->persist($category);
                $manager->flush();
                $this->addReference('firstRestaurant category-'.$j.'-tab'.$i,$category);
            }
        }

        for($i=1;$i<=3;$i++){
            for($j=1;$j<=2;$j++) {
                $category = new CategoryMeal();
                $category->setName("Category number " . $i . " tab" . $j);
                $category->setStatus(1);
                $category->setPosition($j);
                $category->setRestaurant($this->getReference('secondRestaurant'));
                $category->setTabMeal($this->getReference('secondRestaurant tab-' . $i));
                $manager->persist($category);
                $manager->flush();
                $this->addReference('secondRestaurant category-'.$j.'-tab'.$i,$category);
            }
        }

        for($i=1;$i<=3;$i++){
            for($j=1;$j<=2;$j++) {
                $category = new CategoryMeal();
                $category->setName("Category number " . $i . " tab" . $j);
                $category->setStatus(1);
                $category->setPosition($j);
                $category->setRestaurant($this->getReference('thirdRestaurant'));
                $category->setTabMeal($this->getReference('thirdRestaurant tab-' . $i));
                $manager->persist($category);
                $manager->flush();
                $this->addReference('thirdRestaurant category-'.$j.'-tab'.$i,$category);
            }
        }

        //MEALS
        for($i=1;$i<=2;$i++){
            for($j=1;$j<=2;$j++) {
                $meal = new Meal();
                $meal->setName("Meal number " . $i . " Category" . $j);
                $meal->setStatus(1);
                $meal->setPosition($j);
                $meal->setPrice($i+$j+0.99);
                $meal->setInitialStock(30);
                $meal->setCurrentStock(30);
                $meal->setAvailability(1);
                $meal->setRestaurant($this->getReference('firstRestaurant'));
                $meal->setDescription("Good meal");
                $meal->setCategory($this->getReference('firstRestaurant category-'.$j.'-tab'.$i));
                $manager->persist($meal);
                $manager->flush();
            }
        }

        for($i=1;$i<=2;$i++){
            for($j=1;$j<=2;$j++) {
                $meal = new Meal();
                $meal->setName("Meal number " . $i . " Category" . $j);
                $meal->setStatus(1);
                $meal->setPosition($j);
                $meal->setPrice($i+$j+0.99);
                $meal->setInitialStock(30);
                $meal->setCurrentStock(30);
                $meal->setAvailability(1);
                $meal->setRestaurant($this->getReference('secondRestaurant'));
                $meal->setDescription("Good meal");
                $meal->setCategory($this->getReference('secondRestaurant category-'.$j.'-tab'.$i));
                $manager->persist($meal);
                $manager->flush();
            }
        }
        for($i=1;$i<=2;$i++){
            for($j=1;$j<=2;$j++) {
                $meal = new Meal();
                $meal->setName("Meal number " . $i . " Category" . $j);
                $meal->setStatus(1);
                $meal->setPosition($j);
                $meal->setPrice($i+$j+0.99);
                $meal->setInitialStock(30);
                $meal->setCurrentStock(30);
                $meal->setAvailability(1);
                $meal->setRestaurant($this->getReference('thirdRestaurant'));
                $meal->setDescription("Good meal");
                $meal->setCategory($this->getReference('thirdRestaurant category-'.$j.'-tab'.$i));
                $manager->persist($meal);
                $manager->flush();
            }
        }

    }
}
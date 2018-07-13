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

        //Moments
        $moment = new Moment();
        $moment->setName("Petit Déjeuner");
        $moment->setMoment(Moment::TYPE_MORNING);
        $moment->setStatus(1);
        $manager->persist($moment);
        $manager->flush();
        $this->addReference('morning',$moment);

        $moment = new Moment();
        $moment->setName("Midi");
        $moment->setMoment(Moment::TYPE_LUNCH);
        $moment->setStatus(1);
        $manager->persist($moment);
        $manager->flush();
        $this->addReference('lunch',$moment);

        $moment = new Moment();
        $moment->setName("Apéro");
        $moment->setMoment(Moment::TYPE_APERITIF);
        $moment->setStatus(1);
        $manager->persist($moment);
        $manager->flush();
        $this->addReference('aperitif',$moment);

        $moment = new Moment();
        $moment->setName("Dîner");
        $moment->setMoment(Moment::TYPE_DINNER);
        $moment->setStatus(1);
        $manager->persist($moment);
        $manager->flush();
        $this->addReference('dinner',$moment);

        //RESTAURANTS
        $restaurant = new Restaurant();
        $restaurant->setStatus(1);
        $restaurant->setAddress('114 Avenue de France');
        $restaurant->setRegion('Ile de France');
        $restaurant->setPostalCode('75013');
        $restaurant->setCity('Paris');
        $restaurant->setLatitude('48.8311081');
        $restaurant->setLongitude('2.374514');
        $restaurant->setPhone('0123456789');
        $restaurant->setName('The Frog & British Library');
        $restaurant->setDescription('The Frog & British Library, pub et restaurant');
        $restaurant->setOpen(1);
        $restaurant->addCategory($this->getReference('categoryBurgers'));
        $restaurant->addUser($this->getReference('firstrestorer@test.com'));
        $restaurant->addMoment($this->getReference("aperitif"));
        $restaurant->addMoment($this->getReference("dinner"));
        $restaurant->setSeats(20);
        $restaurant->setPicture("http://www.frogpubs.com/fr/pics/data/pubs/illustrations/4-191-1200x650.jpg");
        $restaurant->setWebsite("https://www.frogpubs.com/fr/");
        $restaurant->setAverageNote(4.7);
        $schedule="[{\"name\":\"Dim\",\"timeSteps\":[\"01:30\",\"02:00\",\"02:30\"]},{\"name\":\"Lun\",\"timeSteps\":[\"00:30\",\"01:00\",\"01:30\",\"02:00\"]},{\"name\":\"Mar\",\"timeSteps\":[\"05:30\"]},{\"name\":\"Mer\",\"timeSteps\":[\"00:30\",\"01:00\",\"01:30\",\"02:00\",\"02:30\"]},{\"name\":\"Jeu\",\"timeSteps\":[\"06:30\"]},{\"name\":\"Ven\",\"timeSteps\":[\"01:00\",\"01:30\",\"02:00\",\"02:30\",\"03:00\"]},{\"name\":\"Sam\",\"timeSteps\":[\"02:00\",\"02:30\",\"03:00\"]}]";
        $restaurant->setSchedule($schedule);
        $manager->persist($restaurant);
        $manager->flush();
        $this->addReference('firstRestaurant', $restaurant);

        $restaurant = new Restaurant();
        $restaurant->setStatus(1);
        $restaurant->setAddress('29 Rue Mazarine');
        $restaurant->setRegion('Ile de France');
        $restaurant->setPostalCode('75006');
        $restaurant->setCity('Paris');
        $restaurant->setLatitude('48.8546542');
        $restaurant->setLongitude('2.3359354');
        $restaurant->setPhone('0123456789');
        $restaurant->setName('Kodawari Ramen');
        $restaurant->setDescription('Kodawari Ramen, les meilleurs de Paris');
        $restaurant->setOpen(1);
        $restaurant->addCategory($this->getReference('categoryJap'));
        $restaurant->addUser($this->getReference('secondrestorer@test.com'));
        $restaurant->addMoment($this->getReference("lunch"));
        $restaurant->addMoment($this->getReference("dinner"));
        $restaurant->setSeats(20);
        $restaurant->setPicture("http://www.hemaposesesvalises.fr/wp-content/uploads/2017/11/Kodawari_ramen_restaurant_paris_japon_decor-1080x675.jpg");
        $restaurant->setWebsite("https://www.kodawari-ramen.com/");
        $restaurant->setAverageNote(4.9);
        $schedule="[{\"name\":\"Dim\",\"timeSteps\":[\"01:30\",\"02:00\",\"02:30\"]},{\"name\":\"Lun\",\"timeSteps\":[\"00:30\",\"01:00\",\"01:30\",\"02:00\"]},{\"name\":\"Mar\",\"timeSteps\":[\"05:30\"]},{\"name\":\"Mer\",\"timeSteps\":[\"00:30\",\"01:00\",\"01:30\",\"02:00\",\"02:30\"]},{\"name\":\"Jeu\",\"timeSteps\":[\"06:30\"]},{\"name\":\"Ven\",\"timeSteps\":[\"01:00\",\"01:30\",\"02:00\",\"02:30\",\"03:00\"]},{\"name\":\"Sam\",\"timeSteps\":[\"02:00\",\"02:30\",\"03:00\"]}]";
        $restaurant->setSchedule($schedule);
        $manager->persist($restaurant);
        $manager->flush();
        $this->addReference('secondRestaurant', $restaurant);


        $restaurant = new Restaurant();
        $restaurant->setStatus(1);
        $restaurant->setAddress('19 Rue du Télégraphe');
        $restaurant->setRegion('Ile de France');
        $restaurant->setPostalCode('75020');
        $restaurant->setCity('Paris');
        $restaurant->setLatitude('48.8724229');
        $restaurant->setLongitude('2.3971672');
        $restaurant->setPhone('0123456789');
        $restaurant->setName('Aarchna');
        $restaurant->setDescription('Derrière une jolie façade en bois sculpté, ce restaurant sert des spécialités indiennes traditionnelles.');
        $restaurant->addCategory($this->getReference('categoryIndien'));
        $restaurant->setOpen(1);
        $restaurant->addUser($this->getReference('thirdrestorer@test.com'));
        $restaurant->addMoment($this->getReference("morning"));
        $restaurant->addMoment($this->getReference("lunch"));
        $restaurant->setSeats(20);
        $restaurant->setPicture("https://u.tfstatic.com/restaurant_photos/964/15964/169/612/aarchna-vue-de-la-salle-9c000.jpg");
        $restaurant->setWebsite("http://www.aarchna.com/");
        $restaurant->setAverageNote(4.3);
        $schedule="[{\"name\":\"Dim\",\"timeSteps\":[\"01:30\",\"02:00\",\"02:30\"]},{\"name\":\"Lun\",\"timeSteps\":[\"00:30\",\"01:00\",\"01:30\",\"02:00\"]},{\"name\":\"Mar\",\"timeSteps\":[\"05:30\"]},{\"name\":\"Mer\",\"timeSteps\":[\"00:30\",\"01:00\",\"01:30\",\"02:00\",\"02:30\"]},{\"name\":\"Jeu\",\"timeSteps\":[\"06:30\"]},{\"name\":\"Ven\",\"timeSteps\":[\"01:00\",\"01:30\",\"02:00\",\"02:30\",\"03:00\"]},{\"name\":\"Sam\",\"timeSteps\":[\"02:00\",\"02:30\",\"03:00\"]}]";
        $restaurant->setSchedule($schedule);
        $manager->persist($restaurant);
        $manager->flush();
        $this->addReference('thirdRestaurant', $restaurant);


        //INGREDIENTS
        for($i=1; $i<=3; $i++){
            $ingredient = new Ingredient();
            $ingredient->setName("Ingredient number ".$i);
            $ingredient->setStock(15);
            $ingredient->setStatus(1);
            $ingredient->setRestaurant($this->getReference("firstRestaurant"));
            $manager->persist($ingredient);
            $manager->flush();
            $this->addReference('firstRestaurant ingredient-'.$i,$ingredient);
        }

        for($i=1; $i<=3; $i++){
            $ingredient = new Ingredient();
            $ingredient->setName("Ingredient number ".$i);
            $ingredient->setStock(15);
            $ingredient->setStatus(1);
            $ingredient->setRestaurant($this->getReference("secondRestaurant"));
            $manager->persist($ingredient);
            $manager->flush();
            $this->addReference('secondRestaurant ingredient-'.$i,$ingredient);
        }

        for($i=1; $i<=3; $i++){
            $ingredient = new Ingredient();
            $ingredient->setName("Ingredient number ".$i);
            $ingredient->setStock(15);
            $ingredient->setStatus(1);
            $ingredient->setRestaurant($this->getReference("thirdRestaurant"));
            $manager->persist($ingredient);
            $manager->flush();
            $this->addReference('thirdRestaurant ingredient-'.$i,$ingredient);
        }

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

        //CATEGORIES & MEALS
        for($i=1;$i<=3;$i++){
            $countPos = 1;
            for($j=1;$j<=2;$j++) {
                $category = new Content();
                $category->setName("Category number " . $j . " tab" . $i);
                $category->setStatus(1);
                $category->setType(Content::TYPE_CATEGORY);
                $category->setPosition($countPos);
                $countPos++;
                $category->setRestaurant($this->getReference('firstRestaurant'));
                $category->setTab($this->getReference('firstRestaurant tab-' . $i));
                $manager->persist($category);
                $manager->flush();
                for ($k = 1; $k <= 2; $k++) {
                    $meal = new Content();
                    $meal->setName("Burger");
                    $meal->setStatus(1);
                    $meal->setType(Content::TYPE_MEAL);
                    $meal->setPosition($countPos);
                    $countPos++;
                    $meal->setDescription("Lorem ipsum toussa toussa vive la recette");
                    $meal->setPrice($i + $j + 0.99);
                    $meal->setAvailability(1);
                    $meal->setRestaurant($this->getReference('firstRestaurant'));
                    $meal->setDescription("Good meal");
                    $meal->setTab($this->getReference('firstRestaurant tab-' . $i));
                    $meal->addIngredient($this->getReference('firstRestaurant ingredient-'.$i));
                    $manager->persist($meal);
                    $manager->flush();
                    $this->setReference('firstRestaurant tab-'.$i.'-meal-'.$k,$meal);
                }
            }
        }

        for($i=1;$i<=3;$i++){
            $countPos = 1;
            for($j=1;$j<=2;$j++) {
                $category = new Content();
                $category->setName("Category number " . $j . " tab" . $i);
                $category->setStatus(1);
                $category->setType(Content::TYPE_CATEGORY);
                $category->setPosition($countPos);
                $countPos++;
                $category->setRestaurant($this->getReference('secondRestaurant'));
                $category->setTab($this->getReference('secondRestaurant tab-' . $i));
                $manager->persist($category);
                $manager->flush();
                for ($k = 1; $k <= 2; $k++) {
                    $meal = new Content();
                    $meal->setName("Ramen");
                    $meal->setStatus(1);
                    $meal->setType(Content::TYPE_MEAL);
                    $meal->setPosition($countPos);
                    $countPos++;
                    $meal->setDescription("Lorem ipsum toussa toussa vive la recette");
                    $meal->setPrice($i + $j + 0.99);
                    $meal->setAvailability(1);
                    $meal->setRestaurant($this->getReference('secondRestaurant'));
                    $meal->setDescription("Good meal");
                    $meal->setTab($this->getReference('secondRestaurant tab-' . $i));
                    $meal->addIngredient($this->getReference('secondRestaurant ingredient-'.$i));
                    $manager->persist($meal);
                    $manager->flush();
                    $this->setReference('secondRestaurant tab-'.$i.'-meal-'.$k,$meal);

                }
            }

        }

        for($i=1;$i<=3;$i++){
            $countPos = 1;
            for($j=1;$j<=2;$j++) {
                $category = new Content();
                $category->setName("Category number " . $j . " tab" . $i);
                $category->setStatus(1);
                $category->setType(Content::TYPE_CATEGORY);
                $category->setPosition($countPos);
                $countPos++;
                $category->setRestaurant($this->getReference('thirdRestaurant'));
                $category->setTab($this->getReference('thirdRestaurant tab-' . $i));
                $manager->persist($category);
                $manager->flush();
                for ($k = 1; $k <= 2; $k++) {
                    $meal = new Content();
                    $meal->setName("Curry");
                    $meal->setStatus(1);
                    $meal->setType(Content::TYPE_MEAL);
                    $meal->setPosition($countPos);
                    $countPos++;
                    $meal->setDescription("Lorem ipsum toussa toussa vive la recette");
                    $meal->setPrice($i + $j + 0.99);
                    $meal->setAvailability(1);
                    $meal->setRestaurant($this->getReference('thirdRestaurant'));
                    $meal->setDescription("Good meal");
                    $meal->setTab($this->getReference('thirdRestaurant tab-' . $i));
                    $meal->addIngredient($this->getReference('thirdRestaurant ingredient-'.$i));
                    $manager->persist($meal);
                    $manager->flush();
                    $this->setReference('thirdRestaurant tab-'.$i.'-meal-'.$k,$meal);

                }
            }

        }

        //Reservations
        $reservationUser = $this->getReference('firstclient@test.com');
        $reservationRestaurant = $this->getReference('firstRestaurant');
        $reservation = new Reservation($reservationUser,$reservationRestaurant);
        $date = new \DateTime('2018-10-07T17:30:00Z');
        $reservation->setDate($date);
        $reservation->setNbParticipants(4);
        $reservation->setState(1);
        $reservation->setTimeStep("17h30");
        $firstMeal=$this->getReference('firstRestaurant tab-1-meal-1');
        $secondMeal=$this->getReference('firstRestaurant tab-2-meal-2');
        $thirdMeal=$this->getReference('firstRestaurant tab-2-meal-1');
        $total = $firstMeal->getPrice() + $secondMeal->getPrice() + $thirdMeal->getPrice();
        $reservation->setTotal($total);
        $manager->persist($reservation);
        $manager->flush();

        $reservationSeat = new ReservationSeat();
        $reservationSeat->setName("Simon");
        $manager->persist($reservationSeat);
        $manager->flush();
        $reservationContent = new ReservationContent();
        $reservationContent->setReservation($reservation);
        $reservationContent->setContent($firstMeal);
        $reservationContent->setTotalPrice($firstMeal->getPrice());
        $reservationContent->setQuantity(1);
        $reservationContent->setSeat($reservationSeat);
        $manager->persist($reservationContent);
        $manager->flush();
        $reservationContent = new ReservationContent();
        $reservationContent->setReservation($reservation);
        $reservationContent->setContent($secondMeal);
        $reservationContent->setTotalPrice($secondMeal->getPrice());
        $reservationContent->setQuantity(1);
        $reservationContent->setSeat($reservationSeat);
        $manager->persist($reservationContent);
        $manager->flush();
        $reservationSeat = new ReservationSeat();
        $reservationSeat->setName("Yves");
        $manager->persist($reservationSeat);
        $manager->flush();
        $reservationContent = new ReservationContent();
        $reservationContent->setReservation($reservation);
        $reservationContent->setContent($thirdMeal);
        $reservationContent->setQuantity(2);
        $reservationContent->setTotalPrice($thirdMeal->getPrice()*2);
        $reservationContent->setSeat($reservationSeat);
        $manager->persist($reservationContent);
        $manager->flush();

        $reservationUser = $this->getReference('firstclient@test.com');
        $reservationRestaurant = $this->getReference('secondRestaurant');
        $reservation = new Reservation($reservationUser,$reservationRestaurant);
        $date = new \DateTime('2018-10-07T11:30:00Z');
        $reservation->setDate($date);
        $reservation->setNbParticipants(4);
        $reservation->setTimeStep("11h30");
        $reservation->setState(1);
        $firstMeal=$this->getReference('secondRestaurant tab-1-meal-2');
        $secondMeal=$this->getReference('secondRestaurant tab-1-meal-1');
        $thirdMeal=$this->getReference('secondRestaurant tab-2-meal-1');
        $total = $firstMeal->getPrice() + $secondMeal->getPrice() + $thirdMeal->getPrice();
        $reservation->setTotal($total);
        $manager->persist($reservation);
        $manager->flush();

        $reservationSeat = new ReservationSeat();
        $reservationSeat->setName("Antoine");
        $manager->persist($reservationSeat);
        $manager->flush();
        $reservationContent = new ReservationContent();
        $reservationContent->setReservation($reservation);
        $reservationContent->setContent($firstMeal);
        $reservationContent->setTotalPrice($firstMeal->getPrice());
        $reservationContent->setQuantity(1);
        $reservationContent->setSeat($reservationSeat);
        $manager->persist($reservationContent);
        $manager->flush();
        $reservationContent = new ReservationContent();
        $reservationContent->setReservation($reservation);
        $reservationContent->setContent($secondMeal);
        $reservationContent->setTotalPrice($secondMeal->getPrice());
        $reservationContent->setQuantity(1);
        $reservationContent->setSeat($reservationSeat);
        $manager->persist($reservationContent);
        $manager->flush();
        $reservationContent = new ReservationContent();
        $reservationContent->setReservation($reservation);
        $reservationContent->setContent($thirdMeal);
        $reservationContent->setTotalPrice($thirdMeal->getPrice());
        $reservationContent->setQuantity(1);
        $reservationContent->setSeat($reservationSeat);
        $manager->persist($reservationContent);
        $manager->flush();

        $reservationUser = $this->getReference('firstclient@test.com');
        $reservationRestaurant = $this->getReference('firstRestaurant');
        $reservation = new Reservation($reservationUser,$reservationRestaurant);
        $date = new \DateTime('2018-10-07T18:30:00Z');
        $reservation->setTimeStep("18h30");
        $reservation->setDate($date);
        $reservation->setNbParticipants(4);
        $reservation->setState(1);
        $firstMeal=$this->getReference('firstRestaurant tab-1-meal-1');
        $secondMeal=$this->getReference('firstRestaurant tab-1-meal-2');
        $thirdMeal=$this->getReference('firstRestaurant tab-2-meal-2');
        $total = $firstMeal->getPrice() + $secondMeal->getPrice() + $thirdMeal->getPrice();
        $reservation->setTotal($total);
        $manager->persist($reservation);
        $manager->flush();

        $reservationSeat = new ReservationSeat();
        $reservationSeat->setName("Simon");
        $manager->persist($reservationSeat);
        $manager->flush();
        $reservationContent = new ReservationContent();
        $reservationContent->setReservation($reservation);
        $reservationContent->setContent($firstMeal);
        $reservationContent->setTotalPrice($firstMeal->getPrice());
        $reservationContent->setQuantity(1);
        $reservationContent->setSeat($reservationSeat);
        $manager->persist($reservationContent);
        $manager->flush();
        $reservationContent = new ReservationContent();
        $reservationContent->setReservation($reservation);
        $reservationContent->setContent($secondMeal);
        $reservationContent->setTotalPrice($secondMeal->getPrice());
        $reservationContent->setQuantity(1);
        $reservationContent->setSeat($reservationSeat);
        $manager->persist($reservationContent);
        $manager->flush();
        $reservationSeat = new ReservationSeat();
        $reservationSeat->setName("Antoine");
        $manager->persist($reservationSeat);
        $manager->flush();
        $reservationContent = new ReservationContent();
        $reservationContent->setReservation($reservation);
        $reservationContent->setContent($thirdMeal);
        $reservationContent->setTotalPrice($thirdMeal->getPrice());
        $reservationContent->setQuantity(1);
        $reservationContent->setSeat($reservationSeat);
        $manager->persist($reservationContent);
        $manager->flush();

        /*$reservationUser = $this->getReference('firstclient@test.com');
        $reservationRestaurant = $this->getReference('firstRestaurant');
        $reservation = new Reservation($reservationUser,$reservationRestaurant);
        $date = new \DateTime('2018-10-07T18:00:00Z');
        $reservation->setDate($date);
        $reservation->setNbParticipants(4);
        $reservation->setState(1);
        $firstMeal=$this->getReference('firstRestaurant tab-1-meal-2');
        $secondMeal=$this->getReference('firstRestaurant tab-2-meal-2');
        $total = $firstMeal->getPrice() + $secondMeal->getPrice();
        $reservation->setTotal($total);
        $manager->persist($reservation);
        $manager->flush();

        $reservationContent = new ReservationContent();
        $reservationContent->setReservation($reservation);
        $reservationContent->setContent($firstMeal);
        $reservationContent->setTotalPrice($firstMeal->getPrice());
        $reservationContent->setQuantity(1);
        $manager->persist($reservationContent);
        $manager->flush();
        $reservationContent = new ReservationContent();
        $reservationContent->setReservation($reservation);
        $reservationContent->setContent($secondMeal);
        $reservationContent->setTotalPrice($secondMeal->getPrice());
        $reservationContent->setQuantity(1);
        $manager->persist($reservationContent);
        $manager->flush();*/


    }
}
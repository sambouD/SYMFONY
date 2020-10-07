<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use Faker\Factory;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        // Roles 
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Lior')
                  ->setLastName('Chamla')
                  ->setEmail('tg@gmail.com')
                  ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                  ->setPicture('https://randomuser.me/api/portraits/women/75.jpg')
                  ->setIntroduction($faker->sentence())
                  ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                  ->addUserRole($adminRole);
                
        $manager->persist($adminUser);

        //nous gérons les utilisateur   
        $users = [];
        $genres = ['male', 'female'];


        for ($i=1; $i <=10; $i++) { 
            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1,99) . '.jpg';
         
                $picture .=  ($genre =='man' ? 'men/' :  'women/') .$pictureId;

            // encoder
            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstname($genre))
            ->setLastName($faker->lastname)
            ->setEmail($faker->email)
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
            ->setHash($hash)
            ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;

        }

        //Nous gérons les annonces
        for($i = 1; $i<=30; $i++){
           $ad = new Ad();

           $title = $faker->sentence();
        
           #faker
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            $user = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
               
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40,200))
                ->setRooms(mt_rand(1,5))
                ->setAuthor($user);
               
                for ($j=1; $j <= mt_rand(2, 5) ; $j++) { 
                    # code...
                    $image = new Image();

                    $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);

                    $manager->persist($image);
                }

                //Gestion des réservation
                for ($j=1; $j <= mt_rand(0,10) ; $j++) { 
                    $booking = new Booking();
                    
                    $createdAt= $faker->dateTimeBetween('-6 months');
                    $startDate = $faker->dateTimeBetween('-3 months');

                    //Gestion de date de fin
                    $duration = mt_rand(3,10);

                    $endDate = (clone $startDate)->modify("+$duration days");
                    $amount = $ad->getPrice() * $duration;

                    $booker = $users[mt_rand(0, count($users) -1)];

                    $comment = $faker->paragraph();

                    $booking->setBooker($booker)
                            ->setAd($ad)
                            ->setStartDate($startDate)
                            ->setEndDate($endDate)
                            ->setCreatedAt($createdAt)
                            ->setAmount($amount)
                            ->setComment($comment);

                    $manager->persist($booking);

                    // Gestion des commentaires
                    // tirer pile ou face
                    if (mt_rand(0,1)) {
                        $comment = new Comment();
                        $comment->setContent($faker->paragraph())
                                ->setRating(mt_rand(1,5))
                                ->setAuthor($booker)
                                ->setAd($ad);

                        $manager->persist($comment);
                    }

                }

            $manager->persist($ad); 
        }
        

        $manager->flush();
    }
}

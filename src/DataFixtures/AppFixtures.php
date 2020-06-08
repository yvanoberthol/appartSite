<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Reservation;
use App\Entity\Role;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */



    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('FR-fr');

        //gestion des roles
        $role1 = new Role();
        $role1->setTitle('ROLE_USER');

        $role2 = new Role();
        $role2->setTitle('ROLE_ADMIN');

        $manager->persist($role1);
        $manager->persist($role2);

        //gestion des utilisateurs
        $admin = new Utilisateur();
        $admin->setNom('yvano');
        $admin->setPrenom('berthol');
        $admin->setEmail('yvanoberthol@gmail.com');
        $hash_password2 = $this->passwordEncoder->encodePassword($admin,'yvano1105');
        $admin->setPassword($hash_password2);
        $admin->setPhoto($faker->imageUrl(150,150));
        $admin->addRole($role2);
        $manager->persist($admin);
        $users[] = $admin;

        for ($i=0;$i <= 12; $i++) {

            $utilisateur = new Utilisateur();
            $utilisateur->setNom($faker->firstName);
            $utilisateur->setPrenom($faker->lastName);
            $utilisateur->setEmail($faker->email);
            $hash_password = $this->passwordEncoder->encodePassword($utilisateur, 'yvano1105');
            $utilisateur->setPassword($hash_password);
            $utilisateur->setPhoto($faker->imageUrl(150, 150));
            $utilisateur->addRole($role1);
            $manager->persist($utilisateur);
            $users[] = $utilisateur;
        }

        //gestion des annonces
        for ($i=0;$i <= 30; $i++){
            $annonce = new Annonce();
            $annonce->setTitle($faker->sentence());
            $annonce->setIntroduction($faker->paragraph(2));
            $annonce->setCoverImage($faker->imageUrl(1000,350));
            $annonce->setContent('<p>'.join('</p><p>',$faker->paragraphs(5)).'</p>');
            $annonce->setPrice($faker->numberBetween(15000,32500));
            $annonce->setRooms($faker->numberBetween(2,8));
            $annonce->setAuthor($users[mt_rand(0,count($users)-1)]);

            //gestion des images
            for ($j=0, $jMax = mt_rand(2, 5); $j <= $jMax; $j++){
                $image = new Image();
                $image->setUrl($faker->imageUrl());
                $image->setCaption($faker->sentence());
                $image->setAnnonce($annonce);

                $manager->persist($image);
            }

            //gestion des reservations
            for ($j=0, $jMax = 10; $j <= $jMax; $j++){
                $reservation = new Reservation();
                $reservation->setCreatedAt($faker->dateTimeBetween('-6 months'));
                $startDate = $faker->dateTimeBetween('-3 months');
                $reservation->setStartDate($startDate);
                $duration = random_int(2,8);
                $reservation->setEndDate((clone $startDate)->modify("+ $duration days"));
                $reservation->setMontant($annonce->getPrice() * $duration);
                $reservation->setAnnonce($annonce);
                $reservation->setCommentaire($faker->paragraph());

                $booker = $users[random_int(0,count($users)-1)];

                $reservation->setReserveur($booker);

                $manager->persist($reservation);
            }

            if (random_int(0,1)){
                for ($j=0, $jMax = random_int(1,7); $j <= $jMax; $j++){
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph(2));
                    $comment->setNote(random_int(0,5));
                    $comment->setAnnonce($annonce);
                    $author = $users[random_int(0,count($users)-1)];
                    $comment->setAuthor($author);
                    $manager->persist($comment);
                }
            }



            $manager->persist($annonce);
        }
        $manager->flush();
    }
}

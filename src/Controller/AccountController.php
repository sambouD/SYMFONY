<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
       
        return $this->render('account/login.html.twig',[
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }
    /**
     * Permet de se déconnecter
     *
     * @Route("/logout", name="account_logout")
     * 
     * @return void
     */
    public function logout(){
        // ..rien
    }

    /**
     * Permet d'afficher le formulaire d'inscription
     *
     * @Route("/register", name="account_register")
     * 
     * @return Reponse
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
                $user = new User();
                $form = $this->createForm(RegistrationType::class, $user);

                $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $hash = $encoder->encodePassword($user, $user->getHash());
                $user->setHash($hash);


                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre compte a bien été creé ! vous pouvez maintenant vous connecter !"
                );
                return $this->redirectToRoute("account_login");
            }
    
    return $this->render('account/registration.html.twig',[
        'form' => $form->createView()
    ]);
    }


    /**
     * Permet d'afficher et de traiter le formulaire de modification de profil
     *@Route("/account/profile", name="account_profile")
     *@IsGranted("ROLE_USER")
     * 

     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager){
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

            if ($form->isSubmitted()&& $form->isValid()) {
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success', 
                    "Les données du profil on été enregistrée avec sucès !"
                );
            } 
            return $this->render('account/profile.html.twig',[
                'form' => $form->createView()
            ]);       

        }
        /**
         * Permet de modifier le mot de passe
         *
         * @Route("/account/password-update", name="account_password")
         * @IsGranted("ROLE_USER")
         * 
         * @return Response
         */
        public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager){
            $passwordUpdate = new PasswordUpdate();

            $user = $this->getUser();

            $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
               // 1 Verifier que le oldPassword soit le même que le password de l'user
               if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                   //gérer l'erreur
                    $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé 
                    n'est pas votre de passe actuel !"));
               }
               else{
                   $newPassword = $passwordUpdate->getNewPassword();
                   $hash = $encoder->encodePassword($user, $newPassword);

                   $user->setHash($hash);

                   $manager->persist($user);
                   $manager->flush();

                   $this->addFlash(
                       'success',
                       "Votre mot de passe a bien été modifié !"
                   );
                   return $this->redirectToRoute('homepage');
               }
            }

                return $this->render("account/password.html.twig",[
                    'form' => $form->createView()
                ]);
        }

        /**
         * Permet d'afficher le profil de l'utilisateur connecté
         *
         * @Route("/account", name="account_index")
         * @IsGranted("ROLE_USER")
         * 
         * @return Reponse
         */
         public function myAccount()
        {
                return $this->render('user/index.html.twig',[
                    'user' => $this->getUser()
                ]);
        }

       /**
        * Permet de supprimer une annonce
        *
        *@Route("/ads/{slug}/delete", name="ads_delete")
        *@Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message= "Vous n'avez pas le droit")
        *
        * @param Ad $ad
        * @param EntityManagerInterface $manager
        * @return Response
        */ 
      public function delete(Ad $ad, EntityManagerInterface $manager){
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
              'success',
                "L'annonce<strong> {$ad->getTitle()}</strong> a bien été supprimée !"
            );

            return $this->redirectToRoute("ads_index");
      }

      /**
       * Permet d'afficher la liste des réservations faite par l'utilisateurs
       *@Route("/account/bookings", name="account_bookings")
       * 
       * @return Reponse
       */
      public function booking(){
          return $this->render('account\bookings.html.twig');
      }
}

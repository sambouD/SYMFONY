<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        
        
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }
    
    /**
     * Permet de créer une annonce
     * 
     * @IsGranted("ROLE_USER")
     * 
     *@Route("/ads/new", name="ads_create")
     * 
     * @return Response
     */
    public function create(Request $resquest, EntityManagerInterface $manager){

        $ad = new Ad();
     
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($resquest);
    

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());
            
            $manager->persist($ad);
            $manager->flush();

               // Gestion de flash
            $this->addFlash(
                'success',
                "l'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );


            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/new.html.twig',[
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="cette annonce est pas vous ! vous ne pouvez pas la modifier")
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager){
        

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);
        //

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }


            $manager->persist($ad);
            $manager->flush();

               // Gestion de flash
            $this->addFlash(
                'success',
                "les modifications de l'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
            );


            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }
    
            return $this->render('ad/edit.html.twig',[
                'form' => $form->createView(),
                'ad' => $ad
            ]);
    }
    
    /**
     * Permet d'afficher une seule annonce
     *
     *@Route("/ads/{slug}", name="ads_show")
     *
     *
     *   
     * @return Response
     */
    public function show( Ad $ad){
            return $this->render('ad/show.html.twig',[
                'ad' => $ad
            ]);
    }
}

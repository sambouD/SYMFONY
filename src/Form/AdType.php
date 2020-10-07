<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 
            TextType::class, 
            $this->getConfiguration("Titre", "Tapez un super titre pour votre annonce")
            )
            ->add('slug', 
            TextType::class, 
            $this->getConfiguration("Adresse Web", "Tapez votre adresse web(auto)",
            [
                'required' => false
            ])
            )
            ->add('coverImage', 
            UrlType::class,
             $this->getConfiguration("Url de l'image","Donner l'adresse de l'image")
             )
            ->add('introduction', 
            TextType::class, 
            $this->getConfiguration("Introduction", "Donnez une description")
            )
            ->add('content', 
            TextareaType::class, 
            $this->getConfiguration("Description détaillé", "Donner une description détaillé")
            )
            ->add('rooms',
            IntegerType::class, 
            $this->getConfiguration("Nombre de chambres", "Le nombre de chambres disponibles")
            )
            ->add('price', 
            MoneyType::class, 
            $this->getConfiguration("Prix par nuit", "Indiquez le prix que vous voulez pour une nuit")
            )
            ->add(
                'images',
                CollectionType::class,
                [
                    // c'est quoi le type de champ à répéter 
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ]
            )
    
    ;
          
}

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}

<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller {
        /**
         * @Route("/hello/{prenom}/{age}", name="hello")
         *Montre la page qui dit bonjour
         *
         * @return void
         */
public function hello($prenom = "anonyme", $age = 0){
        return $this->render(
                'hello.html.twig',
                [
                        'prenom' => $prenom,
                        'age' => $age
                ]
                );

}

        /**
         * @Route("/", name="homepage")
         *
         * 
         */

        public  function Home(){
                $prenoms = ["sasu" => 31, "lion" => 15, "cool" => 20]; // tableau "for"

            return $this->render(
                    "home.html.twig",
                    [ 
                        'title' => "Bonjour à tous",
                        'age' => 13,
                        'tableau' => $prenoms
                    ]
            );
        }
}
?>
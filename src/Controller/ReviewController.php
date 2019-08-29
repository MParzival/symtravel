<?php
namespace App\Controller;
use App\Entity\Destination;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
class ReviewController extends AbstractController
{
    /**
     * @Route("/user/{user}/destinations/{destination}", name="review_visited")
     */
    public function hasVisited(ReviewRepository $reviewRepository, ?Destination $destination, ?User $user)
    {
        //$this fait réferrence à l'AbstractController
        //On verifie qu'il s'agit bien du user connecté
        if ( $this->getUser() === $user )
        {
            //Je vais chercher si on a un Review qui possède le user et la destination indiqués dans l'URL
            // pour éviter de dupliquer la review
            // On cherche un objet Review et pas un tableau de Review
            $reviewUserDestination = $reviewRepository->findOneBy([
                "user" => $user,
                "destination" => $destination,
            ]);
            //Si la review n'existe pas en base :
            if (!$reviewUserDestination)
            {
                $review = new Review();
                $review->setUser($user);
                $review->setDestination($destination);
                $em = $this->getDoctrine()->getManager();
                $em->persist($review);
                $em->flush();
            }
        }
        //Dans tous les cas je redirige vers la page index (liste des destination)
        return $this->redirectToRoute('destination_index');
    }

    /**
     * @Route("/user/{user}/destinations/{destination}/unvisited", name="review_unvisited")
     */
    public function removeVisited(ReviewRepository $reviewRepository, ?Destination $destination, ?User $user)
    {
        if ( $this->getUser() === $user )
        {
            //Je vais chercher si on a un Review qui possède le user et la destination indiqués dans l'URL
            // pour éviter de dupliquer la review
            // On cherche un objet Review et pas un tableau de Review
            $reviewUserDestination = $reviewRepository->findOneBy([
                "user" => $user,
                "destination" => $destination,
            ]);
            //Si la review existe pas en base :
            if ($reviewUserDestination)
            {
                $em = $this->getDoctrine()->getManager();
                $em->remove($reviewUserDestination);
                $em->flush();
            }
        }
        return $this->redirectToRoute('destination_index');

    }
}
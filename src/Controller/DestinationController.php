<?php

namespace App\Controller;

use App\Entity\Destination;
use App\Form\DestinationType;
use App\Form\TypeType;
use App\Repository\DestinationRepository;
use App\Repository\TypeRepository;
use App\Services\FileUploadService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DestinationController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var TypeRepository
     */
    private $repository;

    /**
     * TypeController constructor.
     * @param ObjectManager $manager
     * @param TypeRepository $repository
     */
    public function __construct(ObjectManager $manager, TypeRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    /**
     * @Route("/destination", name="destination_index")
     * @param DestinationRepository $repository
     * @return Response
     */
    public function index(DestinationRepository $repository)
    {
        $destinations = $repository->findAll();
        return $this->render('destination/index.html.twig', [
            'controller_name' => 'TypeController',
            'destinations' => $destinations
        ]);
    }

    /**
     * @Route("/new", name="destination_create",methods={"GET","POST"})
     * @Route("/new/{id}/edit", name="destination_edit",methods={"GET","POST"})
     * @param Request $request
     * @param Destination|null $destination
     * @param FileUploadService $fileUploadService
     * @return RedirectResponse|Response
     */
    public function create(Request $request,Destination $destination=null, FileUploadService $fileUploadService){
        if(!$destination){
            $destination=new Destination();
        }
        $form=$this->createForm(DestinationType::class,$destination);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $photoFile = $form['photo']->getData();

            if ($photoFile) {
                $photoName = $fileUploadService->uploadFile( $photoFile );
                $destination->setPhoto( $photoName );
            }
            $this->manager->persist($destination);
            $this->manager->flush();
            $this->addFlash('success', 'Votre destination a bien été modifié avec succés !');
            return $this->redirectToRoute('destination_index');
        }
        return $this->render('destination/create.html.twig',[
            'destination' => $destination,
            'form'=>$form->createView(),
            'editMode'=>$destination->getId()//gerer le bouton update ou save selon le cas
        ]);
    }

    /**
     * @Route("/destination/{id}/delete", name="destination_delete", methods={"DELETE"})
     * @param Request $request
     * @param Destination $destination
     * @return RedirectResponse
     */
    public function delete(Request $request, Destination $destination){
        if ($this->isCsrfTokenValid('delete'.$destination->getId(), $request->request->get('_token'))) {
            $this->manager->remove($destination);
            $this->manager->flush();
            $this->addFlash('success', 'Votre type a bien été supprimé avec succés !');
        }
        return $this->redirectToRoute('destination_index');
    }

    /**
     * @Route("/destination/{id}", name="destination_show", methods={"GET"})
     * @param Destination $destination
     * @return Response
     */
    public function show(Destination $destination): Response
    {
        return $this->render('destination/show.html.twig', [
            'destination' => $destination,
        ]);
    }
}

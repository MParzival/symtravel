<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeType;
use App\Repository\TypeRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TypeController
 * @package App\Controller
 * @Route("/type")
 */
class TypeController extends AbstractController {
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
     * @Route("/", name="type_index")
     */
    public function index() : Response
    {
        $types = $this->repository->findAll();
        return $this->render('type/index.html.twig', [
            'controller_name' => 'TypeController',
            'types' => $types
        ]);
    }

    /**
     * @Route("/new", name="type_create", methods={"POST", "GET"})
     */
    /*public function create(Request $request) : Response
    {
        $type = new Type();
        $form = $this->createForm(TypeType::class, $type);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($type);
            $this->manager->flush();
            $this->addFlash('success', 'Votre Type a été créé avec succès!');

            return $this->redirectToRoute('type_index');
        }
        return $this->render('type/create.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
        ]);
    }*/

    /**
     * @Route("/show/{id}", name="type_show", methods={"GET"})
     * @param Type $type
     * @return Response
     */
    public function show(Type $type){
        return $this->render('type/show.html.twig', [
            'type' => $type,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="type_edit", methods={"GET","POST"})
     * @param Type $type
     * @param Request $request
     * @return RedirectResponse|Response
     */

    /*public function edit(Type $type, Request $request) : Response{
        $form = $this->createForm(TypeType::class, $type);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->manager->flush();
            $this->addFlash('success', 'Votre type a bien été modifié avec succés !');
            return $this->redirectToRoute('type_index');
        }
       return $this->render('type/edit.html.twig', [
           'type' => $type,
           'form' => $form->createView()
       ]);
    }*/

    /**
     * @Route("/new", name="type_create",methods={"GET","POST"})
     * @Route("/new/{id}/edit", name="type_edit",methods={"GET","POST"})
     * @param Request $request
     * @param Type|null $type
     * @return RedirectResponse|Response
     */
    public function create(Request $request,Type $type=null){
        if(!$type){
            $type=new Type();
        }
        $form=$this->createForm(TypeType::class,$type);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->manager->persist($type);
            $this->manager->flush();
            $this->addFlash('success', 'Votre type a bien été modifié avec succés !');
            return $this->redirectToRoute('type_index');
        }
        return $this->render('type/create.html.twig',[
            'type' => $type,
            'form'=>$form->createView(),
            'editMode'=>$type->getId()//gerer le bouton update ou save selon le cas
        ]);
    }

    /**
     * @Route("/{id}/delete", name="type_delete", methods={"DELETE"})
     * @param Request $request
     * @param Type $type
     * @return RedirectResponse
     */
    public function delete(Request $request, Type $type){
        if ($this->isCsrfTokenValid('delete'.$type->getId(), $request->request->get('_token'))) {
            $this->manager->remove($type);
            $this->manager->flush();
            $this->addFlash('success', 'Votre type a bien été supprimé avec succés !');
        }
        return $this->redirectToRoute('type_index');
    }
}

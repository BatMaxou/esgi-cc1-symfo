<?php

namespace App\Controller\Admin;

use App\Entity\Discipline;
use App\Form\DisciplineType;
use App\Repository\DisciplineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/disciplines')]
final class DisciplineController extends AbstractController
{
    #[Route(name: 'admin_discipline', methods: ['GET'])]
    public function index(DisciplineRepository $disciplineRepository): Response
    {
        return $this->render('admin/discipline/index.html.twig', [
            'disciplines' => $disciplineRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_discipline_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $discipline = new Discipline();
        $form = $this->createForm(DisciplineType::class, $discipline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($discipline);
            $entityManager->flush();

            return $this->redirectToRoute('admin_discipline', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/discipline/new.html.twig', [
            'discipline' => $discipline,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_discipline_show', methods: ['GET'])]
    public function show(Discipline $discipline): Response
    {
        return $this->render('admin/discipline/show.html.twig', [
            'discipline' => $discipline,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_discipline_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Discipline $discipline, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DisciplineType::class, $discipline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_discipline', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/discipline/edit.html.twig', [
            'discipline' => $discipline,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_discipline_delete', methods: ['POST'])]
    public function delete(Request $request, Discipline $discipline, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $discipline->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($discipline);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_discipline', [], Response::HTTP_SEE_OTHER);
    }
}

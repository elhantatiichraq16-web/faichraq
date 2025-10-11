<?php

namespace App\Controller;

use App\Entity\Tiers;
use App\Form\TiersType;
use App\Repository\TiersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tiers')]
class TiersController extends AbstractController
{
    public function __construct(
        private TiersRepository $tiersRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/', name: 'tiers_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('search');
        $tiers = $search ? $this->tiersRepository->search($search) : $this->tiersRepository->findBy([], ['nom' => 'ASC']);

        if ($request->isXmlHttpRequest()) {
            return $this->render('tiers/_list.html.twig', [
                'tiers' => $tiers,
            ]);
        }

        return $this->render('tiers/index.html.twig', [
            'tiers' => $tiers,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'tiers_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tiers = new Tiers();
        $form = $this->createForm(TiersType::class, $tiers);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($tiers);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le client a été créé avec succès.');

            return $this->redirectToRoute('tiers_index');
        }

        return $this->render('tiers/new.html.twig', [
            'tiers' => $tiers,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'tiers_show', methods: ['GET'])]
    public function show(Tiers $tiers): Response
    {
        return $this->render('tiers/show.html.twig', [
            'tiers' => $tiers,
        ]);
    }

    #[Route('/{id}/edit', name: 'tiers_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tiers $tiers): Response
    {
        $form = $this->createForm(TiersType::class, $tiers);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Le client a été modifié avec succès.');

            return $this->redirectToRoute('tiers_index');
        }

        return $this->render('tiers/edit.html.twig', [
            'tiers' => $tiers,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'tiers_delete', methods: ['DELETE'])]
    public function delete(Request $request, Tiers $tiers): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tiers->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($tiers);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le client a été supprimé avec succès.');
        }

        return $this->redirectToRoute('tiers_index');
    }

    #[Route('/search', name: 'tiers_search', methods: ['GET'])]
    public function search(Request $request): JsonResponse
    {
        $term = $request->query->get('q', '');
        $tiers = $this->tiersRepository->search($term);

        $results = [];
        foreach ($tiers as $tier) {
            $results[] = [
                'id' => $tier->getId(),
                'text' => $tier->getNom() . ' (' . $tier->getEmail() . ')',
                'nom' => $tier->getNom(),
                'email' => $tier->getEmail(),
            ];
        }

        return new JsonResponse($results);
    }
}


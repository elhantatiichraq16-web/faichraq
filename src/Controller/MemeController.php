<?php

namespace App\Controller;

use App\Entity\Meme;
use App\Form\MemeType;
use App\Repository\MemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/memes')]
class MemeController extends AbstractController
{
    public function __construct(
        private MemeRepository $memeRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'meme_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('search');
        $memes = $this->memeRepository->search($search);

        return $this->render('meme/index.html.twig', [
            'memes' => $memes,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'meme_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $meme = new Meme();
        $form = $this->createForm(MemeType::class, $meme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($meme);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le meme a été créé avec succès.');

            return $this->redirectToRoute('meme_index');
        }

        return $this->render('meme/new.html.twig', [
            'form' => $form,
        ]);
    }
}

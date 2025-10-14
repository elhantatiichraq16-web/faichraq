<?php



namespace App\Controller;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


use App\Entity\Facture;
use App\Entity\LigneFacture;
use App\Form\FactureType;
use App\Repository\FactureRepository;
use App\Repository\TiersRepository;
use App\Service\ReferenceGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/facture')]
class FactureController extends AbstractController
{
    // ...existing code...

    #[Route('/{id}/send-email', name: 'facture_send_email', methods: ['GET'])]
    public function sendEmail(Facture $facture, MailerInterface $mailer): Response
    {
        // Générer le PDF en mémoire
        $html = $this->renderView('facture/pdf.html.twig', [
            'facture' => $facture,
        ]);
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdfOutput = $dompdf->output();

        // Préparer l'email
        $client = $facture->getClient();
        $email = (new Email())
            ->from('no-reply@exemple.ma')
            ->to($client->getEmail())
            ->subject('Votre facture ' . $facture->getReference())
            ->text('Veuillez trouver votre facture en pièce jointe.')
            ->attach($pdfOutput, 'facture-' . $facture->getReference() . '.pdf', 'application/pdf');

        $mailer->send($email);

        $this->addFlash('success', 'La facture a été envoyée par email à ' . $client->getEmail() . '.');
        return $this->redirectToRoute('facture_show', ['id' => $facture->getId()]);
    }
    public function __construct(
        private FactureRepository $factureRepository,
        private TiersRepository $tiersRepository,
        private EntityManagerInterface $entityManager,
        private ReferenceGenerator $referenceGenerator
    ) {
    }

    #[Route('/', name: 'facture_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('search');
        $clientId = $request->query->get('client');
        $etat = $request->query->get('etat');
        $dateDebut = $request->query->get('date_debut') ? new \DateTime($request->query->get('date_debut')) : null;
        $dateFin = $request->query->get('date_fin') ? new \DateTime($request->query->get('date_fin')) : null;

        $client = $clientId ? $this->tiersRepository->find($clientId) : null;

        $factures = $this->factureRepository->search($search, $client, $etat, $dateDebut, $dateFin);
        $clients = $this->tiersRepository->findBy([], ['nom' => 'ASC']);

        if ($request->isXmlHttpRequest()) {
            return $this->render('facture/_list.html.twig', [
                'factures' => $factures,
            ]);
        }

        return $this->render('facture/index.html.twig', [
            'factures' => $factures,
            'clients' => $clients,
            'search' => $search,
            'selected_client' => $client,
            'selected_etat' => $etat,
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
        ]);
    }

    #[Route('/new', name: 'facture_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $facture = new Facture();
        $facture->setReference($this->referenceGenerator->generateReference());
        
        // Ajouter une ligne vide par défaut
        $ligne = new LigneFacture();
        $ligne->setQuantite(1);
        $ligne->setPrixUnitaire('0.00');
        $ligne->setTva('20.00');
        $facture->addLigne($ligne);

        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // attribuer la position selon l'ordre dans le formulaire
            $position = 1;
            foreach ($facture->getLignes() as $ligne) {
                if (method_exists($ligne, 'setPosition')) {
                    $ligne->setPosition($position++);
                }
            }
            // garantir l'unicité de la référence au moment de l'insertion
            while ($this->factureRepository->referenceExists($facture->getReference())) {
                $facture->setReference($this->referenceGenerator->generateReference());
            }
            $facture->calculerTotaux();
            $this->entityManager->persist($facture);
            $this->entityManager->flush();

            $this->addFlash('success', 'La facture a été créée avec succès.');

            return $this->redirectToRoute('facture_show', ['id' => $facture->getId()]);
        }

        return $this->render('facture/new.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'facture_show', methods: ['GET'])]
    public function show(Facture $facture): Response
    {
        return $this->render('facture/show.html.twig', [
            'facture' => $facture,
        ]);
    }

    #[Route('/{id}/edit', name: 'facture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Facture $facture): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $position = 1;
            foreach ($facture->getLignes() as $ligne) {
                if (method_exists($ligne, 'setPosition')) {
                    $ligne->setPosition($position++);
                }
            }
            $facture->calculerTotaux();
            $this->entityManager->flush();

            $this->addFlash('success', 'La facture a été modifiée avec succès.');

            return $this->redirectToRoute('facture_show', ['id' => $facture->getId()]);
        }

        return $this->render('facture/edit.html.twig', [
            'facture' => $facture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'facture_delete', methods: ['DELETE'])]
    public function delete(Request $request, Facture $facture): Response
    {
        if ($this->isCsrfTokenValid('delete' . $facture->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($facture);
            $this->entityManager->flush();

            $this->addFlash('success', 'La facture a été supprimée avec succès.');
        }

        return $this->redirectToRoute('facture_index');
    }

    #[Route('/{id}/pdf', name: 'facture_pdf', methods: ['GET'])]
    public function pdf(Facture $facture): Response
    {
        $html = $this->renderView('facture/pdf.html.twig', [
            'facture' => $facture,
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="facture-' . $facture->getReference() . '.pdf"');

        return $response;
    }

    #[Route('/{id}/duplicate', name: 'facture_duplicate', methods: ['POST'])]
    public function duplicate(Facture $facture): Response
    {
        $nouvelleFacture = new Facture();
        $nouvelleFacture->setReference($this->referenceGenerator->generateReference());
        $nouvelleFacture->setClient($facture->getClient());
        $nouvelleFacture->setDateFacture(new \DateTime());
        $nouvelleFacture->setEtat(Facture::ETAT_BROUILLON);
        $nouvelleFacture->setDevise($facture->getDevise());
        $nouvelleFacture->setNotes($facture->getNotes());

        // Dupliquer les lignes
        foreach ($facture->getLignes() as $ligne) {
            $nouvelleLigne = new LigneFacture();
            $nouvelleLigne->setDesignation($ligne->getDesignation());
            $nouvelleLigne->setQuantite($ligne->getQuantite());
            $nouvelleLigne->setPrixUnitaire($ligne->getPrixUnitaire());
            $nouvelleLigne->setTva($ligne->getTva());
            $nouvelleFacture->addLigne($nouvelleLigne);
        }

        $nouvelleFacture->calculerTotaux();
        $this->entityManager->persist($nouvelleFacture);
        $this->entityManager->flush();

        $this->addFlash('success', 'La facture a été dupliquée avec succès.');

        return $this->redirectToRoute('facture_show', ['id' => $nouvelleFacture->getId()]);
    }
}


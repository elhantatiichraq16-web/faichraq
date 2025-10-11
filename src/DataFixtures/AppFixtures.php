<?php

namespace App\DataFixtures;

use App\Entity\Facture;
use App\Entity\LigneFacture;
use App\Entity\Tiers;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un utilisateur admin
        $user = new User();
        $user->setEmail('admin@example.com');
        $user->setNom('Administrateur');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'admin123'));
        $manager->persist($user);

        // Créer des clients de test
        $clients = [
            [
                'nom' => 'Entreprise ABC SARL',
                'email' => 'contact@abc.ma',
                'adresse' => "123 Avenue Mohammed V\n20000 Casablanca\nMaroc",
                'telephone' => '+212 5XX XXX XXX',
                'numTva' => 'MA123456789'
            ],
            [
                'nom' => 'Société XYZ',
                'email' => 'info@xyz.ma',
                'adresse' => "456 Boulevard Zerktouni\n40000 Marrakech\nMaroc",
                'telephone' => '+212 5XX XXX XXX',
                'numTva' => 'MA987654321'
            ],
            [
                'nom' => 'Client Particulier',
                'email' => 'client@email.com',
                'adresse' => "789 Rue Hassan II\n10000 Rabat\nMaroc",
                'telephone' => '+212 6XX XXX XXX',
                'numTva' => null
            ],
            [
                'nom' => 'Startup Innovante',
                'email' => 'hello@startup.ma',
                'adresse' => "321 Technopark\n80000 Agadir\nMaroc",
                'telephone' => '+212 5XX XXX XXX',
                'numTva' => 'MA456789123'
            ],
            [
                'nom' => 'Commerce Local',
                'email' => 'ventes@commerce.ma',
                'adresse' => "654 Place des Nations\n30000 Fès\nMaroc",
                'telephone' => '+212 5XX XXX XXX',
                'numTva' => 'MA789123456'
            ]
        ];

        $tiersEntities = [];
        foreach ($clients as $clientData) {
            $tiers = new Tiers();
            $tiers->setNom($clientData['nom']);
            $tiers->setEmail($clientData['email']);
            $tiers->setAdresse($clientData['adresse']);
            $tiers->setTelephone($clientData['telephone']);
            $tiers->setNumTva($clientData['numTva']);
            $manager->persist($tiers);
            $tiersEntities[] = $tiers;
        }

        $manager->flush();

        // Créer des factures de test
        $factures = [
            [
                'reference' => 'FAC-2024-0001',
                'client' => $tiersEntities[0],
                'dateFacture' => new \DateTime('-30 days'),
                'dateEcheance' => new \DateTime('-15 days'),
                'etat' => Facture::ETAT_PAYEE,
                'devise' => 'MAD',
                'notes' => 'Facture pour services de développement web',
                'lignes' => [
                    ['designation' => 'Développement site web', 'quantite' => 40, 'prixUnitaire' => 500.00, 'tva' => 20.00],
                    ['designation' => 'Formation utilisateur', 'quantite' => 8, 'prixUnitaire' => 200.00, 'tva' => 20.00]
                ]
            ],
            [
                'reference' => 'FAC-2024-0002',
                'client' => $tiersEntities[1],
                'dateFacture' => new \DateTime('-20 days'),
                'dateEcheance' => new \DateTime('-5 days'),
                'etat' => Facture::ETAT_VALIDEE,
                'devise' => 'MAD',
                'notes' => 'Maintenance et support technique',
                'lignes' => [
                    ['designation' => 'Maintenance mensuelle', 'quantite' => 1, 'prixUnitaire' => 1500.00, 'tva' => 20.00],
                    ['designation' => 'Support technique', 'quantite' => 20, 'prixUnitaire' => 100.00, 'tva' => 20.00]
                ]
            ],
            [
                'reference' => 'FAC-2024-0003',
                'client' => $tiersEntities[2],
                'dateFacture' => new \DateTime('-10 days'),
                'dateEcheance' => new \DateTime('+5 days'),
                'etat' => Facture::ETAT_BROUILLON,
                'devise' => 'MAD',
                'notes' => 'Consultation et conseil',
                'lignes' => [
                    ['designation' => 'Consultation stratégique', 'quantite' => 10, 'prixUnitaire' => 300.00, 'tva' => 20.00]
                ]
            ],
            [
                'reference' => 'FAC-2024-0004',
                'client' => $tiersEntities[3],
                'dateFacture' => new \DateTime('-5 days'),
                'dateEcheance' => new \DateTime('+10 days'),
                'etat' => Facture::ETAT_VALIDEE,
                'devise' => 'MAD',
                'notes' => 'Développement application mobile',
                'lignes' => [
                    ['designation' => 'Développement app iOS', 'quantite' => 1, 'prixUnitaire' => 8000.00, 'tva' => 20.00],
                    ['designation' => 'Développement app Android', 'quantite' => 1, 'prixUnitaire' => 8000.00, 'tva' => 20.00],
                    ['designation' => 'Tests et déploiement', 'quantite' => 1, 'prixUnitaire' => 2000.00, 'tva' => 20.00]
                ]
            ],
            [
                'reference' => 'FAC-2024-0005',
                'client' => $tiersEntities[4],
                'dateFacture' => new \DateTime('-2 days'),
                'dateEcheance' => new \DateTime('+13 days'),
                'etat' => Facture::ETAT_BROUILLON,
                'devise' => 'MAD',
                'notes' => 'Refonte du site e-commerce',
                'lignes' => [
                    ['designation' => 'Design et maquettes', 'quantite' => 1, 'prixUnitaire' => 2500.00, 'tva' => 20.00],
                    ['designation' => 'Développement frontend', 'quantite' => 30, 'prixUnitaire' => 400.00, 'tva' => 20.00],
                    ['designation' => 'Développement backend', 'quantite' => 25, 'prixUnitaire' => 450.00, 'tva' => 20.00]
                ]
            ]
        ];

        foreach ($factures as $factureData) {
            $facture = new Facture();
            $facture->setReference($factureData['reference']);
            $facture->setClient($factureData['client']);
            $facture->setDateFacture($factureData['dateFacture']);
            $facture->setDateEcheance($factureData['dateEcheance']);
            $facture->setEtat($factureData['etat']);
            $facture->setDevise($factureData['devise']);
            $facture->setNotes($factureData['notes']);

            // Ajouter les lignes
            foreach ($factureData['lignes'] as $ligneData) {
                $ligne = new LigneFacture();
                $ligne->setDesignation($ligneData['designation']);
                $ligne->setQuantite($ligneData['quantite']);
                $ligne->setPrixUnitaire((string)$ligneData['prixUnitaire']);
                $ligne->setTva((string)$ligneData['tva']);
                $facture->addLigne($ligne);
            }

            // Calculer les totaux
            $facture->calculerTotaux();

            $manager->persist($facture);
        }

        $manager->flush();
    }
}


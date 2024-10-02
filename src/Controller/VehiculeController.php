<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Enum\EtatVehicule; 
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;


class VehiculeController extends AbstractController
{
    private $vehiculeRepository;
    private $entityManager;
    private $httpClient;

    public function __construct(VehiculeRepository $vehiculeRepository, EntityManagerInterface $entityManager, HttpClientInterface $httpClient)
    {
        $this->vehiculeRepository = $vehiculeRepository;
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient; // Injecter le client HTTP
    }

    #[Route('/vehicule/new', name: 'vehicule_new')]
    public function new(Request $request): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si la plaque existe déjà
            $plaque = $form->get('plaque')->getData();
            $isCreate = $this->vehiculeRepository->findOneByplaque($plaque);
            if ($isCreate) {   
                // Redirige vers le formulaire mais rien n'est envoyé
                return $this->redirectToRoute('vehicule_new');    
            }

            $this->entityManager->persist($vehicule);
            $this->entityManager->flush();

            return $this->redirectToRoute('vehicule_list');
        }

        return $this->render('vehicule/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/vehicule/edit/{id}', name: 'vehicule_edit')]
    public function edit(Request $request, Vehicule $vehicule): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si la plaque existe déjà et éviter les doublons
            $plaque = $form->get('plaque')->getData();
            $isCreate = $this->vehiculeRepository->findOneByplaque($plaque);
            
            if ($isCreate && $isCreate !== $vehicule) {
                // Plaque déjà existante, redirige vers la liste sans sauvegarde les changements 
                //Possibilité d'implémenter un feedback pour plus de lissibilité
                return $this->redirectToRoute('vehicule_list');    
            }

            $this->entityManager->flush();
            return $this->redirectToRoute('vehicule_list');
        }

        return $this->render('vehicule/edit.html.twig', [
            'form' => $form->createView(),
            'vehicule' => $vehicule,
        ]);
    }

    #[Route('/vehicule/delete/{id}', name: 'vehicule_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicule $vehicule): Response
    {
        if ($this->isCsrfTokenValid('delete' . $vehicule->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($vehicule);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('vehicule_list'); 
    }

    #[Route('/vehicule', name: 'vehicule_list')]
    public function index(): Response
    {
        $vehicules = $this->vehiculeRepository->findAll();
        return $this->render('vehicule/index.html.twig', [
            'vehicules' => $vehicules,
        ]);
    }

    #[Route('/vehicule/update-etat-ajax', name: 'vehicule_update_etat_ajax', methods: ['POST'])]
    public function updateEtatAjax(Request $request): JsonResponse
        {
            // Vérifiez que la requête est valide et que les paramètres sont bien présents
            $data = json_decode($request->getContent(), true);
            $id = $data['id'] ?? null;
            $etat = $data['etat'] ?? null;

            if (!$id || !$etat) {
                return $this->json(['error' => 'Donnée invalide.'], Response::HTTP_BAD_REQUEST);
            }

            $vehicule = $this->vehiculeRepository->find($id);

            if (!$vehicule) {
                return $this->json(['error' => 'Véhicule inconnu.'], Response::HTTP_NOT_FOUND);
            }
            if (!in_array($etat, ['neuf', 'endommagé', 'cassé'])) {
                return $this->json(['error' => 'Etat invalide.'], Response::HTTP_BAD_REQUEST);
            }
            $vehicule->setEtat(EtatVehicule::from($etat)); // Mise à jour de l'état du véhicule
            try {
                $this->entityManager->flush();
                return $this->json(['success' => 'État du véhicule mis à jour avec succès.']);
            } catch (\Exception $e) {
                return $this->json(['error' => 'Erreur lors de la mise à jour.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }


    /////////////////////////////////// NE FONCTIONNE PAS //////////////////////////////////////
    //Ne provoque pas d'erreur mais ne renvoie jamias de chemin/NULL
    // Erreur probable mauvaise communication avec L'API ou NUMEROPLAQUE peut ne pas être au bon format ne fonctionne pas au format: AA-000-AA

    #[Route('/vehicule/{id}', name: 'vehicule_detail', methods: ['GET'])]
    public function detail(Vehicule $vehicule): Response
    {
        $httpClient = HttpClient::create(); 
        $url = '';
        
        try {
            //Obtenir le carID avec le numéro de plaque de la voiture
            $response = $httpClient->request('GET', 'https://www.piecesauto.com/homepage/numberplate', [
                'query' => ['value' => $vehicule->getPlaque()]
            ]);

            $data = $response->toArray();
            $carId = $data['carID'] ?? null;
            //renvoie null

    
            if ($carId) {
                //Utiliser le carID pour obtenir le chemin de l'URL
                $response2 = $httpClient->request('GET', 'https://www.piecesauto.com/common/seekCar', [
                    'query' => [
                        'carid' => $carId,
                        'language' => 'fr'
                    ]
                ]);
    
                $data2 = $response2->toArray();
                $path = $data2['path'] ?? null;
    
                if ($path) {
                    $url = 'https://www.piecesauto.com/'.$path;
                }
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible de récupérer les informations sur les pièces détachées.');
        }
    
        return $this->render('vehicule/detail/index.html.twig', [
            'vehicule' => $vehicule,
            'pieces_url' => $url, 
        ]);
    }



}
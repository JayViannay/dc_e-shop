<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Form\ProfileFormType;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/profil', name: 'app_user_profile', methods: ['GET', 'POST'])]
    public function userProfile(Request $request, UserRepository $userRepository, AddressRepository $addressRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $formUser = $this->createForm(ProfileFormType::class, $user);
        $formUser->handleRequest($request);
        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $userRepository->add($user, true);

            $this->addFlash('success', 'Profil mis à jour avec succès');
        }

        $address = new Address();

        $formAddress = $this->createForm(AddressType::class, $address);
        $formAddress->handleRequest($request);

        $formNewAddress = $this->createForm(AddressType::class, $address);
        $formNewAddress->handleRequest($request);

        if ($formAddress->isSubmitted() && $formAddress->isValid()) {
            if ($request->get('address_id')) {
                $addr = $addressRepository->find($request->get('address_id'));
                $addr
                    ->setUser($user)
                    ->setWay($address->getWay())
                    ->setName($address->getName())
                    ->setCity($address->getCity())
                    ->setZipcode($address->getZipcode())
                    ->setCountry($address->getZipcode());

                $addressRepository->add($addr, true);

                $this->addFlash('success', 'Modifications prises en compte !');
                return $this->redirectToRoute('app_user_profile');
            }
        }

        if ($formNewAddress->isSubmitted() && $formNewAddress->isValid()) {
            $address->setUser($user);
            $addressRepository->add($address, true);

            $this->addFlash('success', 'Adresse correctement ajoutée !');
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user/index.html.twig', [
            'formUser' => $formUser->createView(),
            'formAddress' => $formAddress->createView(),
            'formNewAddress' => $formNewAddress->createView(),
        ]);
    }

    #[Route('/api/profile/addresse/{id}', name: 'json_user_addresses')]
    public function addressesUser(Address $address, Request $request, AddressRepository $addressRepository)
    {
        $response = new Response(json_encode(array('address' => [
            "id" => $address->getId(),
            "name" => $address->getName(),
            "way" => $address->getWay(),
            "zipcode" => $address->getZipcode(),
            "city" => $address->getCity(),
            "country" => $address->getCountry()
        ])));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
        //return $this->render('default/about.html.twig');
    }

    #[Route('/profile/address/{id}', name: 'app_user_delete_address', methods: ['POST'])]
    public function delete(Request $request, Address $address, AddressRepository $addressRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $addressRepository->remove($address, true);
        }

        return $this->redirectToRoute('app_user_profile', [], Response::HTTP_SEE_OTHER);
    }
}

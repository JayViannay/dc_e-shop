<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\UserOrder;
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

        $formNewAddress = $this->createForm(AddressType::class, $address);
        $formNewAddress->handleRequest($request);

        $formAddress = $this->createForm(AddressType::class, $address);
        $formAddress->handleRequest($request);

        if ($formAddress->isSubmitted() && $formAddress->isValid()) {
            if ($request->get('address_id')) {
                $addr = $addressRepository->find($request->get('address_id'));
                $addr
                    ->setUser($user)
                    ->setWay($address->getWay())
                    ->setName($address->getName())
                    ->setCity($address->getCity())
                    ->setZipcode($address->getZipcode())
                    ->setCountry($address->getCountry());

                $addressRepository->add($addr, true);

                $this->addFlash('success', 'Modifications prises en compte !');
                return $this->redirectToRoute('app_user_profile');
            }
        }

        if ($formNewAddress->isSubmitted() && $formNewAddress->isValid()) {
            $address->setUser($user);
            $addressRepository->add($address, true);
            $user->setMainAddress($address);
            $userRepository->add($user, true);

            $this->addFlash('success', 'Adresse correctement ajoutée !');
            return $this->redirectToRoute('app_user_addresses');
        }

        return $this->render('user/index.html.twig', [
            'formUser' => $formUser->createView(),
            'formAddress' => $formAddress->createView(),
            'formNewAddress' => $formNewAddress->createView(),
        ]);
    }

    #[Route('/profil/adresses', name: 'app_user_addresses', methods: ['GET','POST'])]
    public function listUserAddress(Request $request, AddressRepository $addressRepository, UserRepository $userRepository)
    {
        $user = $this->getUser();
        $address = new Address();
        $formNewAddress = $this->createForm(AddressType::class, $address);
        $formNewAddress->handleRequest($request);

        if ($formNewAddress->isSubmitted() && $formNewAddress->isValid()) {
            $address->setUser($user);
            $addressRepository->add($address, true);
            $user->setMainAddress($address);
            $userRepository->add($user, true);

            $this->addFlash('success', 'Adresse correctement ajoutée !');
            return $this->redirectToRoute('app_user_addresses');
        }

        return $this->render('user/addresses.html.twig', [
            'formNewAddress' => $formNewAddress->createView(),
        ]);
    }

    #[Route('/profil/commandes', name: 'app_user_orders', methods: ['GET','POST'])]
    public function listUserOrders()
    {
        return $this->render('user/orders.html.twig');
    }

    #[Route('/profil/commandes/{id}', name: 'app_user_order_show', methods: ['GET'])]
    public function userOrder(UserOrder $order)
    {
        return $this->render('user/order_show.html.twig',[
            'order' => $order
        ]);
    }

    #[Route('/profile/address/{id}', name: 'app_user_delete_address', methods: ['POST'])]
    public function deleteAddress(Request $request, Address $address, AddressRepository $addressRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $addressRepository->remove($address, true);
        }

        return $this->redirectToRoute('app_user_addresses', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/profile/address/{id}/choose', name: 'app_user_main_address', methods: ['POST'])]
    public function chooseAddress(Request $request, Address $address, AddressRepository $addressRepository, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('choose'.$address->getId(), $request->request->get('_token'))) {
            $user = $this->getUser();
            $user->setMainAddress($address);
            $userRepository->add($user, true);
            $this->addFlash('success', 'Adresse principale prise en compte');
        }

        return $this->redirectToRoute('app_user_addresses', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/profile/addresse/{id}', name: 'json_user_addresses')]
    public function addressesUser(Address $address)
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
    }
}

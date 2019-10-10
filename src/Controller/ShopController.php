<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Shop;
use App\Form\ShopType;

/**
 * Shop controller.
 *
 */
class ShopController extends AbstractController
{
    /**
     * this method shows all shops
     * 
     * @Route("/admin/shops/", name="admin_shops_list")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $shops = $em->getRepository(Shop::class)->findAll();

        return $this->render('admin/shop/index.html.twig', array(
            'shops' => $shops,
        ));
    }

    /**
     * this method shows the form for adding new shop and on submit register the shop
     * 
     * @Route("/admin/shops/new", name="admin_shops_new", methods={"GET","POST"})
     */

    public function new(Request $request)
    {
        $shop = new Shop();
        $form = $this->createForm(ShopType::class, $shop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            // store in the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($shop);
            $entityManager->flush();

            // back to shops list page
            return $this->redirectToRoute('admin_shops_list');
        }

        return $this->render('admin/shop/new.html.twig', array(
            'shop' => $shop,
            'form' => $form->createView(),
        ));
    }

    /**
     * update the shop information
     * 
     * @Route("/admin/shops/new/{id}", name="admin_shops_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Shop $shop)
    {
        $editForm = $this->createForm(ShopType::class, $shop);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($shop);
            $em->flush();
            
            return $this->redirectToRoute('admin_shops_list');
        }

        return $this->render('admin/shop/edit.html.twig', array(
            'shop' => $shop,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Delete a shop from the database
     * 
     * @Route("/admin/shops/delete/{id}", name="admin_shops_delete", methods={"GET","POST","DELETE"})
     */
    public function delete(Request $request, Shop $shop)
    {
        if ($shop !=NULL) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($shop);
            $em->flush();
        }

        return $this->redirectToRoute('admin_shops_list');
    }
}
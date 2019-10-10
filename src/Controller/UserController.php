<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use App\Form\UserType;

/**
 * Utilisateur controller.
 *
 */
class UserController extends AbstractController
{
    /**
     * this method shows all users
     * 
     * @Route("/admin/users/", name="admin_user_list")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * this method shows the form for adding new user and on submit register the user
     * 
     * @Route("/admin/users/new", name="admin_user_new", methods={"GET","POST"})
     */

    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encrypt the password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // set the role for the current user
            $user->setRoles($user->getRoles());

            // store in the database
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // back to users list page
            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * update the user information
     * 
     * @Route("/admin/users/new/{id}", name="admin_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user)
    {
        $editForm = $this->createForm(UserType::class, $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/edit.html.twig', array(
            'user' => $user,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * Delete a user from the database
     * 
     * @Route("/admin/users/delete/{id}", name="admin_user_delete", methods={"GET","POST","DELETE"})
     */
    public function delete(Request $request, User $user)
    {
        if ($user !=NULL) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('admin_user_list');
    }



}
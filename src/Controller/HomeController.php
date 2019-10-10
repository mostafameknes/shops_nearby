<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Shop;

class HomeController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session){
        $this->session = $session;
    }
    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        // if the user is connected we will show only shops which are not included in the user preferences
        // else we will show all shops
        if($user!=null){
            $shops = $em->getRepository(Shop::class)->notMyPreferred($user->getId());
        }
        else{
            $shops = $em->getRepository(Shop::class)->findAll();
        }
        
        // we need to order shops by distance that's why we need to convert the ArrayCollection to iterator 
        // and apply the order using the private method Distance
        $collection=new \Doctrine\Common\Collections\ArrayCollection($shops);
        $iterator = $collection->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($this->Distance($a->getLatitude(),$a->getLongitude()) > $this->Distance($b->getLatitude(),$b->getLongitude())) ? -1 : 1;
        });

        // convert back the iterator to ArrayCollection
        $shopssorted = new \Doctrine\Common\Collections\ArrayCollection(iterator_to_array($iterator));


        return $this->render('home/index.html.twig',array(
            'shops' => $shopssorted,
            'temps' => time(),
        ));
    }

    /**
     * @Route("/mypreferred", name="app_preferred")
     */
    public function myPreferred(Request $request): Response
    {
        $user = $this->getUser();
        // if the user is not connect redirect the query to home
        if($user==null){
            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/preference.html.twig',array(
            'shops' => $user->getPreference(),
        ));
    }

    /**
     * @Route("/like-shop", name="app_like")
     */
    public function like(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $id=$request->get('id');
        $shop = $em->getRepository(Shop::class)->findOneById($id);
        $user = $this->getUser();
        
        if($shop!=null){
            // add the shop to the user preferences
            $user->addPreference($shop);
            $em->flush();
            // return the json response
            $response = new Response(json_encode(array('alert' => "success",'message' => "The shop ".$shop->getName()." was added successfully to your preferences")));
        }
        else{
            // return error
            $response = new Response(json_encode(array('alert' => "error",'message' => "We cannot found the shop you are asking for")));
        }
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/dislike-shop", name="app_dislike")
     */
    public function dislike(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $id=$request->get('id');
        $shop = $em->getRepository(Shop::class)->findOneById($id);
        
        if($shop!=null){
            // store the disliked shop in the session (the value is the time)
            $this->session->set('shop-'.$id, time());
            $response = new Response(json_encode(array('alert' => "success",'message' => "The shop ".$shop->getName()." will be hided for the next 2 hours")));
        }
        else{
            $response = new Response(json_encode(array('alert' => "error",'message' => "We cannot found the shop you are asking for")));
        }
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/remove-shop", name="app_delete")
     */
    public function remove(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $id=$request->get('id');

        $shop = $em->getRepository(Shop::class)->findOneById($id);
        $user = $this->getUser();
        
        if($shop!=null){
            // remove the shop from preferences
            $user->removePreference($shop);
            $em->flush();
            $response = new Response(json_encode(array('alert' => "success",'message' => "The shop ".$shop->getName()." is removed successfuly from your preference")));
        }
        else{
            $response = new Response(json_encode(array('alert' => "error",'message' => "We cannot found the shop you are asking for")));
        }
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * store the user position 
     * 
     * @Route("/store-position", name="app_position")
     */
    public function storePosition(Request $request):Response
    {
        $this->session->set('latitude',$request->get('latitude'));
        $this->session->set('longitude',$request->get('longitude'));
        $response = new Response(json_encode(array('alert' => "success",'message' => "We will show you nearby shops")));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * this is a private function which calculate the distance between the user and the shop
     * $latitudeTo, $longitudeTo are the shop position
     */
    private function Distance($latitudeTo, $longitudeTo)
    {
        $earthRadius = 6371000;

        // user current position
        $lat=0.0;
        $long=0.0; 
        if( $this->session->get('latitude')>0){
            $lat=$this->session->get('latitude');
        }
        if( $this->session->get('longitude')>0){
            $long=$this->session->get('longitude');
        }
        
        // convert from degrees to radians
        $latFrom = deg2rad($lat);
        $lonFrom = deg2rad($long);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
      
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
      
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
          cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
      }
}
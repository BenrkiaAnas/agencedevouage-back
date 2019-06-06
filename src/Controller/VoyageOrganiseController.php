<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Photos;
use App\Entity\PlanningVoyage;
use App\Entity\VoyageOrganise;
use App\Form\VoyageOrganiseType;
use App\Repository\VoyageOrganiseRepository;
use App\Service\Validate;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Serializer;
use FOS\RestBundle\Controller\Annotations as Rest;
/**
 * @Route("/voyage")
 * Class VoyageOrganiseController
 * @package App\Controller
 */
class VoyageOrganiseController extends AbstractController
{

    /**
     * @Route("/add", name="voyage_add")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function add(Request $request)
    {
        $voyage = new VoyageOrganise();
        $voyage->setReference(uniqid("REF-"));
        $voyage->setTitle($request->get('title'));
        $voyage->setDescription($request->get('description'));
        $voyage->setDestination($request->get('destination'));
            $em= $this->getDoctrine()->getManager();
            $em-> persist($voyage);
            $em-> flush();

            //Affectation des categories
            $arrayCategorie=$request->get('Categories');
            foreach ($arrayCategorie as $categorie)
                   {
                       $repository= $this->getDoctrine()->getRepository(Category::class)
                           ->find($categorie['id']);
                       $voyage->addCategory($repository);
                       $em-> persist($voyage);
                   }
        $reference= $voyage->getReference();
        $id= $voyage->getId();
        $em= $this->getDoctrine()->getManager();
        $em-> flush();

        $response=array(
            'code'=>0,
            'id' => $id,
            'reference'=> $reference,
            'message'=>'Voyage created!',
            'errors'=>'null',
            'result'=>'null'
        );
        return $this->json($response);
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/photos/{id}",name="affectation_photos")
     * @Method({"POST"})
     * @return JsonResponse
     */
    public function affectationPhotos(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $uploadImage = $request->files->get('image');
        /**
         * @var UploadedFile $image
         */

        $image = $uploadImage;
        $imageName = md5(uniqid()) . '.' . $image->guessExtension();
        $image->move($this->getParameter('image_directory'), $imageName);

        $photo = new Photos();
        $photo->setImage($imageName);
        $em->persist($photo);

        $voyage= $this->getDoctrine()->getRepository(VoyageOrganise::class)
            ->find($id);
        $voyage->addPhoto($photo);

        $em->flush();
        $id= $photo->getId();
        $response=array(
            'code'=>0,
            'id'=>$id,
            'message'=>'Image uploaded!',
            'errors'=>'null',
            'result'=>'null'
        );
        return new JsonResponse($response,Response::HTTP_CREATED);

    }


    /**
     * @Route("/visible", name="listVoyageVisible")
     * @Method({"GET"})
     */
    public function listVisibleTrue()
    {
        $repository= $this->getDoctrine()->getRepository(VoyageOrganise::class);
        $items= $repository->findByVisible(true);
        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Voyage Trouver!',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $response=array(
            'data' => $items
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/categorie/{id}", name="CategorieVoyage")
     * @Method({"GET"})
     */
    public function CategorieOfVoyage($id)
    {
        $repository= $this->getDoctrine()->getRepository(VoyageOrganise::class);
        $items= $repository->find($id);
        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Voyage Trouver!',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $response=array(
            'data' => $items->getCategories()
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/photo/{id}", name="PhotoOfVoyage")
     * @Method({"GET"})
     */
    public function photoOfVoyage($id)
    {
        $repository= $this->getDoctrine()->getRepository(VoyageOrganise::class);
        $items= $repository->find($id);
        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Voyage Trouver!',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $response=array(
            'data' => $items->getPhotos()
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/removeCategories/{id}", name="Categoriedelete")
     * @Method({"DELETE"})
     */
    public function removeCategorieOfVoyage($id)
    {
        $voyage= $this->getDoctrine()->getRepository(VoyageOrganise::class);
        $items= $voyage->find($id);
        $categories= $this->getDoctrine()->getRepository(Category::class)
            ->findAll();
        $em=$this->getDoctrine()->getManager();
        foreach ($categories as $categorie)
        {
            $items->removeCategory($categorie);
        }

        $em->flush();
        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Voyage Trouver!',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $response=array(
            'message' => 'success'
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }


    /**
     * @Route("/archive", name="listVoyagearchiver")
     * @Method({"GET"})
     */
    public function listVoyageArchive()
    {
        $repository= $this->getDoctrine()->getRepository(VoyageOrganise::class);
        $items= $repository->findByVisible(false);
        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Voyage Trouver!',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $response=array(
            'data' => $items
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }


    /**
     * @Route("/", name="listVoyage")
     * @Method({"GET"})
     */
    public function list()
    {
        $repository= $this->getDoctrine()->getRepository(VoyageOrganise::class);
        $items= $repository->findAll();

        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Voyage Trouver!',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $response=array(
            'data' => $items
        );
       // dump($response);
        return $this->json($response,JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="listVoyage_id")
     * @Method({"GET"})
     */
    public function listOne($id)
    {
        $repository= $this->getDoctrine()->getRepository(VoyageOrganise::class);
        $items= $repository->find($id);
        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Voyage',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $response=array(
            'data' => $items
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/picture/{id}", name="Photosvoyage")
     * @Method({"GET"})
     */
    public function GetPhoto($id)
    {
        $voyage= $this->getDoctrine()->getRepository(VoyageOrganise::class);
        $items= $voyage->find($id);
        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucune image selectionner pour ce voyage',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $response=array(
            'data' => $items->getPhotos()
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/update/{id}",name="update_voyage")
     * @Method({"PUT"})
     * @return JsonResponse
     */
    public function updateVoyage(Request $request,$id)
    {

        $voyage= $this->getDoctrine()->getRepository(VoyageOrganise::class)->find($id);

        if (empty($voyage))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Voyage',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $voyage->setTitle($request->get('title'));
        $voyage->setDescription($request->get('description'));
        $voyage->setDestination($request->get('destination'));

        $em= $this->getDoctrine()->getManager();
        $em-> persist($voyage);
        $em-> flush();

        //Affectation des categories
        $arrayCategorie=$request->get('Categories');
        foreach ($arrayCategorie as $categorie)
        {
            $repository= $this->getDoctrine()->getRepository(Category::class)
                ->find($categorie['id']);
            $voyage->addCategory($repository);
            $em-> persist($voyage);
        }
        $reference= $voyage->getReference();
        $em->flush();

        $response=array(
            'code'=>0,
            'reference' => $reference,
            'message'=>'Voyage updated!',
            'errors'=>null,
            'result'=>null

        );
        return $this->json($response);
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/archive/{id}",name="archive_voyage")
     * @Method({"PUT"})
     * @return JsonResponse
     */
    public function archiveVoyage(Request $request,$id)
    {
        $voyage= $this->getDoctrine()->getRepository(VoyageOrganise::class)->find($id);
        $planning = $this->getDoctrine()->getRepository(PlanningVoyage::class)->findOneBy(['voyageOrganise'=>$id]);
        if ($planning == null){
            $body=$request->getContent();
            /**
             * @var Serializer $serializer
             */
            $serializer = $this->get('serializer');
            $data = $serializer->deserialize($request->getContent(),VoyageOrganise::class,'json');
            $voyage->setVisible(false);
            $voyage->setActiver(false);
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            $reference= $voyage->getReference();
            $response=array(
                'code'=>0,
                'reference'=> $reference,
                'message'=>'Voyage Archiver!',
                'errors'=>null,
                'result'=>null

            );
            return $this->json($response,200);
        }
        $visible =$planning->getVisible();
        if ($planning != null && $visible == true) {

            $response=array(
                'code'=>2,
                'message'=>'Vous ne Pouvez pas archiver ce voyage, car il est affecter a un planning non Archiver',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response,200);
        }
        $voyage->setVisible(false);
        $voyage->setActiver(false);
        $em=$this->getDoctrine()->getManager();
        $em->flush();
        $response=array(
            'code'=>0,
            'message'=>'Voyage Archiver!',
            'errors'=>null,
            'result'=>null

        );
        return $this->json($response);
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/invisiblesite/{id}",name="invisiblesite_voyage")
     * @Method({"PUT"})
     * @return JsonResponse
     */
    public function invisibleVoyage(Request $request,$id)
    {
        $voyage= $this->getDoctrine()->getRepository(VoyageOrganise::class)->find($id);
        if (empty($voyage))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Voyage',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }

        $body=$request->getContent();
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $data = $serializer->deserialize($request->getContent(),VoyageOrganise::class,'json');
        $voyage->setActiver(false);
        $em=$this->getDoctrine()->getManager();
        $em->flush();
        $reference= $voyage->getReference();
        $response=array(
            'code'=>0,
            'reference'=> $reference,
            'message'=>'Voyage Invisible!',
            'errors'=>null,
            'result'=>null

        );
        return $this->json($response);
    }


    /**
     * @param Request $request
     * @param $id
     * @Route("/visiblesite/{id}",name="visiblesite_voyage")
     * @Method({"PUT"})
     * @return JsonResponse
     */
    public function visibleVoyage(Request $request,$id)
    {
        $voyage= $this->getDoctrine()->getRepository(VoyageOrganise::class)->find($id);
        if (empty($voyage))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Voyage',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }

        $body=$request->getContent();
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $data = $serializer->deserialize($request->getContent(),VoyageOrganise::class,'json');
        $voyage->setActiver(true);
        $em=$this->getDoctrine()->getManager();
        $em->flush();
        $reference= $voyage->getReference();
        $response=array(
            'code'=>0,
            'reference'=> $reference,
            'message'=>'Voyage Visible!',
            'errors'=>null,
            'result'=>null

        );
        return $this->json($response);
    }

    /**
     * @Route("/delete/{id}",name="delete_voyage")
     * @Method({"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deleteVoyageArchive(Request $request,$id)
    {
        $voyage=$this->getDoctrine()->getRepository(VoyageOrganise::class)->find($id);
        $planning = $this->getDoctrine()->getRepository(PlanningVoyage::class)->findOneBy(['voyageOrganise'=>$id]);
        if ($planning == null){
            $em=$this->getDoctrine()->getManager();
            $em->remove($voyage);
            $em->flush();
            $response=array(
                'code'=>0,
                'message'=>'Voyage deleted success !',
                'errors'=>null,
                'result'=>null

            );
            return $this->json($response,200);
        }
        $visible =$planning->getVisible();
        if ($planning != null ) {

            $response=array(
                'code'=>2,
                'message'=>'Vous ne pouvez pas supprimer définitivement ce voyage, car il est affecter a un planning non supprimer',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response,200);
        }

        $em=$this->getDoctrine()->getManager();
        $em->remove($voyage);
        $em->flush();
        $response=array(
            'code'=>0,
            'message'=>'Voyage deleted success !',
            'errors'=>null,
            'result'=>null

        );
        return $this->json($response,200);
    }


}

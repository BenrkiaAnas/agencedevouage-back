<?php

namespace App\Controller;

use App\Entity\Photos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Serializer;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

/**
 * @Route("/photo")
 * Class PhotoController
 * @package App\Controller
 */
class PhotoController extends AbstractController
{
    /**
     * @Route("/add", name="photo_add")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function add(Request $request)
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
     * @Route("/", name="listPhotos")
     * @Method({"GET"})
     */
    public function list()
    {
        $repository= $this->getDoctrine()->getRepository(Photos::class);
        $items= $repository->findAll();

        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucune Photo Trouver!',
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
     * @Route("/{id}", requirements={"id"="\d+"},defaults={"id": 0}, name="listphoto_id")
     * @Method({"GET"})
     */
    public function listOne($id)
    {
        $repository= $this->getDoctrine()->getRepository(Photos::class);
        $items= $repository->find($id);

        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucune Photo Trouver!',
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
     * @Route("/path", name="pathphoto")
     * @Method({"GET"})
     */
    public function Path()
    {
//        $response->headers->set('Access-Control-Allow-Origin', '*');
//        $response->headers->set('Access-Control-Allow-Headers', 'Authorization');
        $path = $this->getParameter('image_directory');

        $response=array(
            'data' => $path
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }


    /**
     * @Route("/delete/{id}",name="delete_photo")
     * @Method({"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deletephoto($id)
    {
        $photo=$this->getDoctrine()->getRepository(Photos::class)->find($id);

        if (empty($photo)) {

            $response=array(
                'code'=>1,
                'message'=>'Photos Not found !',
                'errors'=>null,
                'result'=>null
            );
            return $this->json($response);
        }

        $em=$this->getDoctrine()->getManager();
        $em->remove($photo);
        $em->flush();
        $response=array(
            'code'=>0,
            'message'=>'Photos deleted success !',
            'errors'=>null,
            'result'=>null

        );
        return $this->json($response,200);
    }


}

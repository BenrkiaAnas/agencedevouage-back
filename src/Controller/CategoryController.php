<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\VoyageOrganise;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/category")
 * Class CategoryController
 * @package App\Controller
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/add", name="category_add")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function add(Request $request)
    {
        /**
         * @var Serializer $serializer
         */
//        $serializer = $this->get('serializer');
//        $category = $serializer->deserialize($request->getContent(),Category::class,'json');
        $category = json_decode($request->getContent(), Inclusion::class) ?: [];

        $newCategory =  new Category();
        $newCategory->setLabel($category['label']);

        $em= $this->getDoctrine()->getManager();
        $em-> persist($newCategory);
        $em-> flush();

        $response=array(
            'code'=>0,
            'message'=>'Category created!',
            'errors'=>'null',
            'result'=>'null'
        );
        return $this->json($response);
    }

    /**
     * @Route("/update/{id}", requirements={"id"="\d+"}, defaults={"id": 0} , name="category_update")
     * @Method({"PUT"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update(Request $request, $id)
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $data = $serializer->deserialize($request->getContent(),Category::class,'json');
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $category->setLabel($data->getLabel());

        $em= $this->getDoctrine()->getManager();
        $em-> flush();
        $label = $category->getLabel();
        $response=array(
            'code'=>0,
            'label'=> $label,
            'message'=>'Category Updated!',
            'errors'=>'null',
            'result'=>'null'
        );
        return $this->json($response);
    }


    /**
     * @Route("/", name="listCategory")
     * @Method({"GET"})
     */
    public function list()
    {
        $repository= $this->getDoctrine()->getRepository(Category::class);
        $items= $repository->findAll();

        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucune Categorie Trouver!',
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
     * @Route("/{id}", name="listCategorie_id")
     * @Method({"GET"})
     */
    public function listOne($id)
    {
        $repository= $this->getDoctrine()->getRepository(Category::class);
        $items= $repository->find($id);
        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucune Categorie ',
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
     * @Route("/delete/{id}",name="delete_categorie")
     * @Method({"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deletecategorie($id)
    {
        $categorys=$this->getDoctrine()->getRepository(Category::class)->find($id);
        $myselection= $categorys->getId();

        $voyages=$this->getDoctrine()->getRepository(VoyageOrganise::class)->findAll();
        foreach ($voyages as $voyage)
        {
            $categories =$voyage->getCategories();

            foreach ($categories as $categorie)
            {  $allcategories=$categorie->getId();

                    if ($allcategories == $myselection)
                    {
                        $response=array(
                            'code'=>1,
                            'message'=>'Categorie utiliser Vous ne  pouvez pas la supprimer !',
                            'errors'=>null,
                            'result'=>null

                        );
                        return $this->json($response,200);
                    }

            }

        }

        $em=$this->getDoctrine()->getManager();
        $em->remove($categorys);
        $em->flush();

        $response=array(
            'code'=>0,
            'message'=>'Categorie deleted success !',
            'errors'=>null,
            'result'=>null

        );
        return $this->json($response,200);


    }



}

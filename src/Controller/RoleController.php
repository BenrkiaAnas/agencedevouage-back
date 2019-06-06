<?php

namespace App\Controller;

use App\Entity\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/role")
 * Class RoleController
 * @package App\Controller
 */
class RoleController extends AbstractController
{
    /**
     * @Route("/add", name="role_add")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function add(Request $request)
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $category = $serializer->deserialize($request->getContent(),Role::class,'json');

        $em= $this->getDoctrine()->getManager();
        $em-> persist($category);
        $em-> flush();

        $response=array(
            'code'=>0,
            'message'=>'Role created!',
            'errors'=>'null',
            'result'=>'null'
        );
        return $this->json($response);
    }

}

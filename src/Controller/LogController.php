<?php

namespace App\Controller;

use App\Entity\Log;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/log")
 * Class LogController
 * @package App\Controller
 */
class LogController extends AbstractController
{
    /**
     * @Route("/add", name="log_add")
     * @Method({"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function add(Request $request)
    {
        $log = new Log();
        $log->setAction($request->get('action'));
        $log->setAdmin($request->get('admin'));
        $log->setDate(new \DateTime());

        $em= $this->getDoctrine()->getManager();
        $em-> persist($log);
        $em-> flush();

        $response=array(
            'code'=>0,
            'message'=>'Log Enregistrer!',
            'errors'=>'null',
            'result'=>'null'
        );
        return $this->json($response);
    }
    /**
     * @Route("/", name="listlogadmin")
     * @Method({"GET"})
     */
    public function listLog()
    {
        $repository= $this->getDoctrine()->getRepository(Log::class);
        $items= $repository->findAll();
        if (empty($items))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Historique!',
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

}

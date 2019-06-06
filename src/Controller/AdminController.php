<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Role;
use App\Entity\User;
use App\Form\AdminType;
use App\Security\TokenAuthenticator;
use App\Repository\AdminRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\FOSRestController;
use phpDocumentor\Reflection\Types\String_;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;

class AdminController extends FOSRestController
{
    /**
     * @Rest\Get(
     * path = "/admin",
     * name = "liste_users")
     */
    public function cgetUserAction(AdminRepository $adminRepository)
    {
        $users = $adminRepository->findAll();
        if (empty($users))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun admin profil Trouver!',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $response=array(
            'data' => $users
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }
    /**
     * @Rest\Get(
     * path = "/admin/role/{id}",
     * name = "liste_usersrole")
     */
    public function getRole(AdminRepository $adminRepository,$id)
    {
        $users = $adminRepository->find($id);
        if (empty($users))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun admin profil Trouver!',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        $response=array(
            'data' => $users->getRole()
        );
        return $this->json($response,JsonResponse::HTTP_OK);
    }
    /**
     * @Rest\Post(
     * path = "login2",
     * name = "log_user")
     */
    public function postLoginUserAction(Request $request, AdminRepository $adminRepository,UserPasswordEncoderInterface $Encoder)
    {
        $data = json_decode($request->getContent(), true) ?: [];
        $user = $adminRepository->findOneByUsername($data['username']);
        $token = $this->get('lexik_jwt_authentication.encoder')
            ->encode(['username' => $data['username']]);
        if (!$user) {
            $response=array(
                'message'=>'Nom utilisateur ou Mot de passe Incorrect !'
            );
            return $this->json($response,200);
        }
      if ( $user->getIsvalid() != true)
        {
            $response=array(
                'message'=>'Utilisateur Bloqué.'
            );
            return $this->json($response,200);
        }
        $isPass = $user->getPassword(); //pass in database
        $dataEncoder = $Encoder->encodePassword($user, $data['password']);
        if ($isPass != $dataEncoder){
            $response=array(
                'message'=>'Nom utilisateur ou Mot de passe Incorrect !'
            );
            return $this->json($response,200);
        }

        return $this->json(['data' => ['token' => $token, 'user' => $user]], 200);
    }

    /**
     * @Rest\Post(
     * path = "checkPassword/{id}",
     * name = "checkPassword")
     */
    public function checkPassword(Request $request,$id,UserPasswordEncoderInterface $Encoder)
    {
        $admin=$this->getDoctrine()->getRepository(Admin::class)->find($id);
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $adminp = $serializer->deserialize($request->getContent(),Admin::class,'json');
        $pass= $adminp->getPassword();
        $passwordInMyData= $admin->getPassword();
        $encodedPassword = $Encoder->encodePassword($admin, $pass);
        if ($passwordInMyData == $encodedPassword) {
            $response=array(
                'code'=>1,
                'message'=>'Same Password!',
                'errors'=>'null',
                'result'=>'null'
            );
        }
        else
            $response=array(
                'code'=>0,
                'message'=>'Not Same Password!',
                'errors'=>'null',
                'result'=>'null'
            );

        return $this->json($response);
    }


    /**
     * @Rest\Post(
     * path = "/admin",
     * name = "add_administrateur")
     */
    public function postUserAction(Request $request, AdminRepository $adminRepository,UserPasswordEncoderInterface $passwordEncoder)
    {

        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $admin = $serializer->deserialize($request->getContent(),Admin::class,'json');
        $data = json_decode($request->getContent(), true) ?: [];
        $user = $adminRepository->findOneByUsername($data['username']);
        $email = $adminRepository->findOneByEmail($data['email']);
        if ($user) {
            $response=array(
              'code'=>1,
              'message'=>'Username exist!'
          );
            return $this->json($response,200);
        }
        if ($email) {
            $response=array(
                'code'=>2,
                'message'=>'Email exist!'
            );
            return $this->json($response,200);
        }
        $pass= $admin->getPassword();
        $encodedPassword = $passwordEncoder->encodePassword($admin, $pass);
        $admin->setPassword($encodedPassword);
        $em = $this->getDoctrine()->getManager();
        $em->persist($admin);
        $em->flush();
        $response=array(
            'code'=>0,
            'message'=>'Profil created!',
            'errors'=>'null',
            'result'=>'null'
        );
        return $this->json($response);
    }
    /**
     * @Rest\Get(
     * path = "/admin/{id}",
     * name = "show_user")
     */
    public function getUserAction(Admin $user)
    {
        return $this->json(['data' => $user], 200);
    }

    /**
     * @Rest\Get(
     * path = "/admin/show/{id}",
     * name = "showOne_user")
     */
    public function showOneUser( $id)
    {
        $response=array(
            'code'=>-1,
            'message'=>'Aucun Admin Trouver!',
            'error'=>'null',
            'result'=>'null'
        );

        $repository= $this->getDoctrine()->getRepository(Admin::class);
        $user= $repository->find($id);
        if (!empty($user))
        {
            $response=array(
                'code'=>1,
                'data' => $user
//            'data' => '{ "id": '+$user->getId()+' }'
            );
        }

        return $this->json($response,JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Put(
     * path = "/admin/{id}",
     * name = "modifie_user")
     */
    public function putUserAction(Request $request,$id, AdminRepository $adminRepository)
    {
        $admin= $this->getDoctrine()->getRepository(Admin::class)->find($id);
        $username = $admin->getUsername(); //username in data
        $email = $admin->getEmail(); //Email in data
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $adminData = $serializer->deserialize($request->getContent(),Admin::class,'json');
        $user= $adminData->getUsername(); //user in form
        $mail= $adminData->getEmail(); //user in form
        $data = json_decode($request->getContent(), true) ?: [];
        $users = $adminRepository->findOneByUsername($data['username']);
        $emails = $adminRepository->findOneByEmail($data['email']);
        if ($username != $user)
        {
            if ($users){
                $response=array(
                    'code'=>1,
                    'message'=>'Username exist Déja!',
                    'errors'=>'null',
                    'result'=>'null'
                );
                return $this->json($response);
            }
        }
        if ($email != $mail){
            if ($emails && !$email){
                $response=array(
                    'code'=>2,
                    'message'=>'Email exist Déja!',
                    'errors'=>'null',
                    'result'=>'null'
                );
                return $this->json($response);
            }
        }

        $username = $admin->setUsername($adminData->getUsername());
        $email = $admin->setEmail($adminData->getEmail());
        $nom = $admin->setNom($adminData->getNom());
        $prenom = $admin->setPrenom($adminData->getPrenom());
        $role = $admin->setRole($adminData->getRole());
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $response=array(
            'code'=>0,
            'message'=>'Administrateur Modifier!',
            'errors'=>'null',
            'result'=>'null'
        );
        return $this->json($response);
//        return $this->json(['data' => $user], 200);
    }

    /**
     * @Rest\Put(
     * path = "/admin/block/{id}",
     * name = "get_user")
     */
    public function putUserIsActive(Request $request,$id)
    {
        $admin= $this->getDoctrine()->getRepository(Admin::class)->find($id);
        if (empty($admin))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Administrateur trouver',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $data = $serializer->deserialize($request->getContent(),Admin::class,'json');
        $admin->setIsvalid(false);
        $em=$this->getDoctrine()->getManager();
        $em->flush();

        $response=array(
            'code'=>0,
            'message'=>'Admin Bloqué!',
            'errors'=>null,
            'result'=>null

        );
        return $this->json($response);
    }

    /**
     * @Rest\Put(
     * path = "/admin/deblock/{id}",
     * name = "deblock_user")
     */
    public function putUserIsNotActive(Request $request,$id)
    {
        $admin= $this->getDoctrine()->getRepository(Admin::class)->find($id);
        if (empty($admin))
        {
            $response=array(
                'code'=>1,
                'message'=>'Aucun Administrateur trouver',
                'error'=>'null',
                'result'=>'null'
            );
            return $this->json($response);
        }
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $data = $serializer->deserialize($request->getContent(),Admin::class,'json');
        $admin->setIsvalid(true);
        $em=$this->getDoctrine()->getManager();
        $em->flush();

        $response=array(
            'code'=>0,
            'message'=>'Admin Débloqué!',
            'errors'=>null,
            'result'=>null

        );
        return $this->json($response);
    }


    /**
     * @Rest\Delete(
     * path = "/admin/remove/{id}",
     * name = "remove_admin")
     */
    public function deleteVoyageArchive(Request $request,$id)
    {
        $admin=$this->getDoctrine()->getRepository(Admin::class)->find($id);

        if (empty($admin)) {

            $response=array(
                'code'=>1,
                'message'=>'Admin Not found !',
                'errors'=>null,
                'result'=>null
            );
            return $this->json($response);
        }

//        /**
//         * @var Serializer $serializer
//         */
//        $serializer = $this->get('serializer');
//        $data = $serializer->deserialize($request->getContent(),Admin::class,'json');
//       $verification = $admin->getIsvalid();
//        if ($verification != false){
//            $response=array(
//                'message'=>'Administrateur Non Bloquée ! Bloqué Administrateur et puis Supprimer de nouveau',
//            );
//            return $this->json($response);
//        }
        $em=$this->getDoctrine()->getManager();
        $em->remove($admin);
        $em->flush();
        $response=array(
            'code'=>0,
            'message'=>'Administrateur deleted success !',
            'errors'=>null,
            'result'=>null

        );
        return $this->json($response,200);
    }
    /**
     * @Rest\Put(
     * path = "/admin/password/{id}",
     * name = "password_user")
     */
    public function password(Request $request,$id,UserPasswordEncoderInterface $passwordEncoder)
    {

        $admin=$this->getDoctrine()->getRepository(Admin::class)->find($id);
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $adminp = $serializer->deserialize($request->getContent(),Admin::class,'json');
        $pass= $adminp->getPassword();
        $passwordInMyData= $admin->getPassword();
        $encodedPassword = $passwordEncoder->encodePassword($admin, $pass);

        $admin->setPassword($encodedPassword);
        $em = $this->getDoctrine()->getManager();
        $em->persist($admin);
        $em->flush();
        $response=array(
            'code'=>0,
            'message'=>'Mot de passe changer!',
            'errors'=>'null',
            'result'=>'null'
        );
        return $this->json($response);
    }

    /**
     * @Rest\Put(
     * path = "/admin/update/{id}",
     * name = "updateUser")
     */
    public function profilupdate(Request $request,$id,UserPasswordEncoderInterface $passwordEncoder)
    {
        $admin=$this->getDoctrine()->getRepository(Admin::class)->find($id);
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $adminf = $serializer->deserialize($request->getContent(),Admin::class,'json');

        $admin->setUsername($adminf->getUsername());
        $admin->setEmail($adminf->getEmail());
        $admin->setNom($adminf->getNom());
        $admin->setPrenom($adminf->getPrenom());
        $admin->setRole($adminf->getRole());

        $em = $this->getDoctrine()->getManager();
        $em->flush();
        $response=array(
            'code'=>0,
            'message'=>'Profil updated!',
            'errors'=>'null',
            'result'=>'null'
        );
        return $this->json($response);
    }
}

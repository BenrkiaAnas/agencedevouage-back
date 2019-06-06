<?php

namespace App\Controller;

use App\Entity\User;
use App\Mail\Mailer;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/register", name="registerUser")
     * @Method({"POST"})
     */
    public function register(ObjectManager $om, UserPasswordEncoderInterface $passwordEncoder, Request $request,Mailer $mailer)
    {


        $email = $request->request->get("email");
        $password = $request->request->get("password");
        $passwordConfirmation = $request->request->get("password_confirmation");
        $tele = $request->request->get("tele");
        $username = $request->request->get("username");
        $firstName = $request->request->get("firstName");
        $lastName = $request->request->get("lastName");

        $gendre = $request->request->get("gendre");
        $profession = $request->request->get("profession");
        $country = $request->request->get("country");
        $city = $request->request->get("city");

        $roles = "ROLE_USER";
        $userExist=$om->getRepository(User::class)->findOneBy(['email' => $email]);

        $jsonResponse = Response::HTTP_NOT_FOUND;
        $message = $email." Already used";
        $code= 404;
        if (empty($userExist)) {
            $user = new User();

            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $message = "Incorrect Data Entered";
            if ($password == $passwordConfirmation) {
                $user->setUsername($username);
                $user->setEmail($email);
                $user->setPassword($password);
                //$user->setPassword($encodedPassword);
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setTele($tele);
                $user->setRoles($roles);
                $user->setGendre($gendre);
                $user->setProfession($profession);
                $user->setCity($city);
                $user->setCountry($country);

                $om->persist($user);
                $om->flush();

                $message = " Creat User Success !";
                $code= 200;
                $jsonResponse = Response::HTTP_OK;

                $mailer->sendConfirmationEmail($user);
            }
        }

        $response = array(
            'code' => $code,
            'message' => $message,
            'error' => 'null',
            'result' => 'null',
        );





        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/login", name="loginUser")
     * @Method({"POST"})
     */
    public function login(ObjectManager $om, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {
        $jsonResponse = Response::HTTP_NOT_FOUND;
        $response = array(
            'code' => 404,
            'message' => 'No User Found !',
            'error' => 'true',
            'result' => 'null',
        );

        $email = $request->request->get("email");
        $password = $request->request->get("password");

         $user=$om->getRepository(User::class)->findOneBy(['email' => $email,'status' => 1]);

        if (!empty($user))
        {
            $response = array(
                'code' => 404,
                'message' => 'Incorrect Data Entered',
                'error' => 'true',
                'result' => 'null',
            );

           // $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            if ($user->getPassword() == $password)
            {
                $jsonResponse = Response::HTTP_OK;
                $response = array(
                    'code' => 200,
                    'message' => $user->getFirstName()." : Successfully Connect !",
                    'error' => 'null',
                    'result' => 'null',
                );
            }
        }
        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/", name="listUser")
     * @Method({"GET"})
     */
    public function list(Request $request)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findBy(['status' => 1]);

        $jsonResponse = Response::HTTP_NOT_FOUND;
        $response = array(
            'code' => 404,
            'message' => 'No USE Found !',
            'error' => 'true',
            'result' => 'null',
        );

        if (!empty($user)) {
            $jsonResponse = Response::HTTP_OK;
            $response = array(
                'data' => $user,
            );
        }

        return $this->json($response, $jsonResponse);
    }

    /**
     * @Route("/validation/{id}", name="validationUser")
     * @Method({"GET"})
     */
    public function validationMail($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        //$user = new User();
        $user->setStatus(1);
        $em =$this->getDoctrine()->getManager();
        $em->flush();

        $jsonResponse = Response::HTTP_NOT_FOUND;
        $response = array(
            'code' => 404,
            'message' => 'No USE Found !',
            'error' => 'true',
            'result' => 'null',
        );

        if (!empty($user)) {
            $jsonResponse = Response::HTTP_OK;
            $response = array(
                'data' => $user,
            );
        }

        return $this->json($response, $jsonResponse);
    }
}

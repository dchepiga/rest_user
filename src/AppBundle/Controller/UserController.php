<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;
use AppBundle\Entity\Visit;



class UserController extends FOSRestController
{
    
    /**
     * @Rest\Get("/user/{id}")
     * @ApiDoc(
     *     description="Returns a user by its id",
     *     statusCodes={
     *         404="Returned when the user is not found"
     *     },
     *     requirements={
     *      {"name"="id", "dataType"="integer", "description"="user id"}
     *     },
     * )
     *
     */
    public function getUserAction($id)
    {
        $result = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if (empty($result)) {
            return new View("User not found", Response::HTTP_NOT_FOUND);
        }
        return $result;

    }

    /**
     * @Rest\Get("/users")
     *
     * @ApiDoc(
     *     description="Returns all users",
     *     statusCodes={
     *         404="Returned when the users are not exist"
     *     },
     * )
     */
    public function getUsersAction()
    {
        $restResult = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        if (empty($restResult)) {
            return new View("There are no users exist", Response::HTTP_NOT_FOUND);
        }
        return $restResult;
    }

    /**
     * @Rest\Post("/user_create/")
     *
     * @ApiDoc(
     *     description="Creates a user with login and name",
     *     statusCodes={
     *         200="Returned when user was added successfully",
     *         406="Returned when params are empty"
     *     },
     *     requirements={
     *      {"name"="login", "dataType"="string", "description"="user login"},
     *      {"name"="name", "dataType"="string", "description"="user name"}
     *     },
     * )
     */
    public function postUserAction(Request $request)
    {
        $user = new User;
        $login = $request->get('login');
        $name = $request->get('name');

        if (empty($name) || empty($login)) {
            return new View("Empty params are not allowed", Response::HTTP_NOT_ACCEPTABLE);
        }
        $user->setName($name);
        $user->setLogin($login);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return new View("User was added successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/user_update/{id}")
     *
     *
     *  @ApiDoc(
     *     description="Updates a user data by id",
     *     statusCodes={
     *         200={
     *              "Returned when the user was updated successfully",
     *              "Returned when the login was updated successfully",
     *              "Returned when the username was updated successfully",
     *             },
     *         404="Returned when the user is not found",
     *         406="Returned when username or login are empty",
     *
     *     },
     *     requirements={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="user id"}
     *   },
     *    parameters={
     *      {"name"="login", "dataType"="string", "required"=false, "description"="user login"},
     *      {"name"="name", "dataType"="integer", "required"=false, "description"="user name"}
     *     }
     * )
     */
    public function putUserAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $name = $request->get('name');
        $login = $request->get('login');

        if (empty($user)) {
            return new View("User not found", Response::HTTP_NOT_FOUND);
        } elseif (!empty($name) && !empty($login)) {
            $user->setName($name);
            $user->setLogin($login);
            $em->flush();
            return new View("User was updated successfully", Response::HTTP_OK);
        } elseif (empty($name) && !empty($login)) {
            $user->setLogin($login);
            $em->flush();
            return new View("Login was updated successfully", Response::HTTP_OK);
        } elseif (!empty($name) && empty($login)) {
            $user->setName($name);
            $em->flush();
            return new View("Username was updated successfully", Response::HTTP_OK);
        } else return new View("Username or login cannot be empty", Response::HTTP_NOT_ACCEPTABLE);

    }

    /**
     * @Rest\Delete("/user_remove/{id}")
     *
     * @ApiDoc(
     *     description="Removes a user by id",
     *     statusCodes={
     *         200="Returned when user was removed successfully",
     *         404="Returned when user not found",
     *     },
     *     requirements={
     *      {"name"="id", "dataType"="integer", "description"="user id"}
     *     },
     * )
     */
    public function removeUserAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        if (empty($user)) {
            return new View("User not found", Response::HTTP_NOT_FOUND);
        } else {
            $em->remove($user);
            $em->flush();
        }
        return new View("User was removed successfully", Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/visit")
     *
     * @ApiDoc(
     *     description="Registers a user with timestamp(datetime type)",
     *     statusCodes={
     *         200="Returned when visit was registered successfully",
     *         406="Returned when params are empty",
     *         404="Returned when user not found",
     *     },
     *     requirements={
     *      {"name"="user_id", "dataType"="integer", "description"="user id"}
     *     },
     * )
     */
    public function visitRegistrationAction(Request $request)
    {
        $visit = new Visit;
        $user_id = $request->get('user_id');

        if (empty($user_id)) {
            return new View("Empty params are not allowed", Response::HTTP_NOT_ACCEPTABLE);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($user_id);

        if (empty($user)) {
            return new View("User not found", Response::HTTP_NOT_FOUND);
        } else {
            $visit->setVisitedOn(new \DateTime('now'));
            $visit->setUser($user);
            $em->persist($visit);
            $em->flush();
            return new View("Visit was registered successfully", Response::HTTP_OK);

        }

    }


    /**
     * @Rest\Post("/get_dau")
     *
     * @ApiDoc(
     *     description="Gets a dau by dateFrom and dateTo",
     *     statusCodes={
     *         406={
     *              "Returned when params are empty",
     *              "Returned when date format does not match to 'Y-m-d H:i:s'",
     *              "Returned when Date_from should be earlier than date_to",
     *              }
     *     },
     *     requirements={
     *      {"name"="date_from", "dataType"="datetime", "description"="start date"},
     *      {"name"="date_to", "dataType"="datetime", "description"="end date"}
     *     },
     * )
     */
    public function getDAUAction(Request $request)
    {
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if (empty($dateFrom) || empty($dateTo)) {
            return new View("Empty params are not allowed", Response::HTTP_NOT_ACCEPTABLE);
        } else {
            if (!\DateTime::createFromFormat('Y-m-d H:i:s', $dateFrom) || !\DateTime::createFromFormat('Y-m-d H:i:s', $dateTo)) {
                return new View("Date format does not match to 'Y-m-d H:i:s'", Response::HTTP_NOT_ACCEPTABLE);

            } else {

                if (new \DateTime($dateFrom) > new \DateTime($dateTo)) {
                    return new View("Date_from should be earlier than date_to", Response::HTTP_NOT_ACCEPTABLE);
                } else {
                    return $this->getDoctrine()->getRepository('AppBundle:Visit')->getDAU($dateFrom, $dateTo);
                }
            }

        }


    }

}

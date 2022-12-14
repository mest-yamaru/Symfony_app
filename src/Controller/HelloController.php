<?php

namespace App\Controller;

use App\Entity\Person;
use App\Service\MyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use App\Form\PersonType;
use Symfony\Component\Security\Core\Encoder\UsePasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class HelloController extends AbstractController
{
    /**
     * @Route("/hello/{id}", name="hello")
     */
    public function index(Request $request, int $id=1, MyService $service)
    {
        $person = $service->getPerson($id);
        $msg = $person == null ? 'no person.' : 'name: ' . $person;
        return $this->render('hello/index.html.twig', [
            'title' => 'Hello',
            'message' => $msg,
            'user' => $this->getUser(),
        ]);
    }

    // public function index(Request $request)
    // {
    //     if (!$this->getUser()->isActive()) {
    //         throw new AccessDeniedException('Unable to access!');
    //     }

    //     return $this->render('hello/index.html.twig', [
    //         'title' => 'Hello',
    //         'message' => 'User Information',
    //         'user' => $this->getUser(),
    //     ]);
    // }

    /**
     * @Route("/find", name="find")
     */
    public function find(Request $request)
    {
        $formobj = new FindForm();
        $form = $this->createFormBuilder($formobj)
            ->add('find', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Click'))
            ->getForm();

        if ($request->getMethod() == 'POST'){
            $form->handleRequest($request);
            $findstr = $form->getData()->getFind();
            $repository = $this->getDoctrine()->getRepository(Person::class);
            $result = $repository->findByNameOrMail($findstr); // ???
        } else {
            $result = null;
        }
        return $this->render('hello/find.html.twig', [
            'title' => 'HELLO!!',
            'form' => $form->createView(),
            'data' => $result,
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, ValidatorInterface $validator)
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($request->getMethod() == 'POST') {
            $person = $form->getData();

            $errors = $validator->validate($person);

            if (count($errors) == 0) {
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($person);
                $manager->flush();
                return $this->redirect('/hello');
            } else {
                return $this->render('hello/create.html.twig', [
                    'title' => 'Hello',
                    'message' => 'Error!',
                    'form' => $form->createView(),
                ]);
            }
        } else {
            return $this->render('hello/create.html.twig', [
                'title' => 'Hello',
                'message' => 'Create Entity',
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/update/{id}", name="update")
     */
    public function update(Request $request, Person $person)
    {
        $form = $this->createFormBuilder($person)
            ->add('name', TextType::class)
            ->add('mail', TextType::class)
            ->add('age', IntegerType::class)
            ->add('save', SubmitType::class, array('label' => 'Click'))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            $person = $form->getData();
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();
            return $this->redirect('/hello');
        } else {
            return $this->render('hello/create.html.twig', [
                'title' => 'Hello',
                'message' => 'Update Entity id=' . $person->getId(),
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Request $request, Person $person)
    {
        $form = $this->createFormBuilder($person)
            ->add('name', TextType::class)
            ->add('mail', TextType::class)
            ->add('age', IntegerType::class)
            ->add('save', SubmitType::class, array('label' => 'Click'))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request); //?????????????????????????????????Form?????????????????????
            $person = $form->getData(); //Person???????????????????????????
            $manager = $this->getDoctrine()->getManager(); //???????????????????????????
            $manager->remove($person); //Person?????????????????????????????????
            $manager->flush(); //??????
            return $this->redirect('/hello');
        } else {
            return $this->render('hello/create.html.twig', [
                'title' => 'Hello',
                'message' => 'Delete Entity id=' . $person->getId(),
                'form' => $form->createView(),
            ]);
        }
    }
}

class FindForm
{
    private $find;

    public function getFind()
    {
        return $this->find;
    }
    public function setFind($find)
    {
        $this->find = $find;
    }
}

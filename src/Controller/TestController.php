<?php

namespace App\Controller;

use App\Service\ICNDbClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test", methods={"GET"})
     */
    public function index(ICNDbClient $client): Response
    {
        $categories = $client->getCategories();

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/test", methods={"POST"})
     */
    public function create(
        Request $request,
        ValidatorInterface $validator,
        ICNDbClient $client,
        MailerInterface $mailer
    ): Response {
        $email = $request->request->get('email');
        $category = $request->request->get('category');

        $constraints = array(
            new \Symfony\Component\Validator\Constraints\Email(),
            new \Symfony\Component\Validator\Constraints\NotBlank()
        );

        $errors = $validator->validate($email, $constraints);

        if (count($errors)) return $this->json(['status' => 'email not valid']);

        $joke = $client->getRandomJoke($category);

        $email = (new Email())
            ->from('')
            ->to($email)
            ->subject('Случайная шутка из ' . $category)
            ->text($joke);

        $mailer->send($email);

        $filesystem = new Filesystem();

        $filesystem->appendToFile('jokes.txt', $joke . "\n");

        return $this->redirect('/test');
    }
}

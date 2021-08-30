<?php
/*
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
*/

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *@param BookRepository $book_rep
     *@param Request $request
     *
     * @return Response
     */
    public function index(BookRepository $book_rep,Request $request): Response
    {
        $books = $book_rep->findBy(
            [],
            ['date_read' => 'DESC']
        );
        $user = '';
        if ($this->getUser() !== null) {
            $user = $this->getUser()->getUsername();
        }
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'books' => $books,
            'user' => $user,
        ]);
    }
}
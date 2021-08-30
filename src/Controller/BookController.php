<?php
/*
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
}
*/

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\BookUploader;
use App\Service\ImageUploader;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        if($this->getUser() == null)
        {
            return new RedirectResponse($this->generateUrl('index'));
        }
        $user = $this->getUser()->getUsername();
        return $this->render('book/index.html.twig', [ 
            'books' => $bookRepository->findAll(),
            'user'=>$user,
        ]);
    }

    /**
     * @Route("/new", name="book_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger,
                        BookUploader $bookUploader,ImageUploader $imageUploader): Response
    {
        if($this->getUser() == null)
        {
            return new RedirectResponse($this->generateUrl('index'));
        }
        $user = $this->getUser()->getUsername();
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile=$form->get('image')->getData();
            $bookFile=$form->get('bookFile')->getData();

            if($imageFile){
                $imageName=$imageUploader->upload($imageFile);
                $book->setImage($imageName);
            }

            if($bookFile){
                $bookName=$bookUploader->upload($bookFile);
                $book->setBookFile($bookName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/new.html.twig', [ 
            'book' => $book,
            'form' => $form->createView(),
            'user'=>$user,
        ]);
    }

    /**
     * @Route("/{id}", name="book_show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        if($this->getUser() == null)
        {
            return new RedirectResponse($this->generateUrl('index'));
        }
        $user = $this->getUser()->getUsername();
        return $this->render('book/show.html.twig', [ 
            'book' => $book,
            'user'=>$user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="book_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Book $book,
                         BookUploader $bookUploader,ImageUploader $imageUploader): Response
    {
        if($this->getUser() == null)
        {
            return new RedirectResponse($this->generateUrl('index'));
        }
        $user = $this->getUser()->getUsername();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image=$form->get('image')->getData();
            $bookFile=$form->get('bookFile')->getData();
            //$this->getDoctrine()->getManager()->flush();


            if($image){
                $imageUploader->removeImage($book->getImage());//Если не будет работать, то добавим else где мы присваиваем к новому старое
                $imageName=$imageUploader->upload($image);
                $book->setImage($imageName);
            }

            if($bookFile){
                $bookUploader->removeBook($book->getBookFile());
                $bookName=$bookUploader->upload($bookFile);
                $book->setBookFile($bookName);
            }

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/edit.html.twig', [ //создать
            'book' => $book,
            'form' => $form->createView(),
            'user'=>$user,
        ]);
    }

    /**
     * @Route("/{id}", name="book_delete", methods={"POST"})
     */
    public function delete(Request $request, Book $book,
                           BookUploader $bookUploader, ImageUploader $imageUploader): Response
    {
        if($this->getUser() == null)
        {
            return new RedirectResponse($this->generateUrl('index'));
        }
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {

            $imageUploader->removeImage($book->getImage());
            $bookUploader->removeBook($book->getBookFile());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('book_index');
    }
}
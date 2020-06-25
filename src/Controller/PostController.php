<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param PostRepository $postRepository
     * @return Response
     */
    public function index(PostRepository $postRepository)
    {
        $posts = $postRepository->findAll();

        return $this->render("post/index.html.twig", [
            "posts" => $posts
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     */
    public function create(Request $request) {
        /* create a new post with title */
        $post = new Post();

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);
        //$form->getErrors();

        if ($form->isSubmitted() /*&& $form->isValid()*/) {
            /* entity manager */
            $em = $this->getDoctrine()->getManager();

            /** @var UploadedFile $file */
            $file = $request->files->get("post_form")["attachment"];

            if ($file) {
                $filename = md5(uniqid()) . "." . $file->guessClientExtension();
                $file->move($this->getParameter("uploads_dir"), $filename);
                $post->setImage($filename);

                $em->persist($post);
                $em->flush();
            }

            return $this->redirect($this->generateUrl("post.index"));
        };


        /* redirect to index of post */
        return $this->render("post/create.html.twig", [
            "form" => $form->createView()
        ]);
    }

//    /**
//     * @Route("/show/{id}", name="show")
//     * @return Response
//     */
//    public function show($id, PostRepository $postRepository) {
//        $post = $postRepository->find($id);
//        dump($post);
//        die;
//
//        return $this->render("post/show.html.twig", [
//            "post" => $post
//        ]);
//    }

    /**
     * @Route("/show/{id}", name="show")
     * @param Post $post
     * @return Response
     */
    public function show(Post $post) {
        return $this->render("post/show.html.twig", [
            "post" => $post
        ]);
    }

//    /**
//     * @Route("/show/{id}", name="show")
//     * @param $id
//     * @param PostRepository $postRepository
//     * @return Response
//     */
//    public function show($id, PostRepository $postRepository) {
//        $post = $postRepository->findByCategory($id);
//
//        dump($post);
//
//        return $this->render("post/show.html.twig", [
//            "post" => $post
//        ]);
//    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param Post $post
     * @return Response
     */
    public function remove(Post $post) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        $this->addFlash("succes", "Post was removed!");

        return $this->redirect($this->generateUrl("post.index"));
    }
}

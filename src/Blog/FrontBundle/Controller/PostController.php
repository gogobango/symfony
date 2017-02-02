<?php
/**
 * Created by PhpStorm.
 * User: arthu
 * Date: 30/01/2017
 * Time: 15:38
 */

namespace Blog\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Blog\FrontBundle\Entity\Comment;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostController extends Controller
{
    public function viewAction($id, Request $request) {
        if (!is_numeric($id)) {
            //throw $this->createNotFoundException('Page introuvable');
            throw new HttpException(404, 'Page introuvable');
        }
        else {
            $em = $this->getDoctrine()->getManager();


            $post = $em->getRepository('BlogFrontBundle:Post')
                        ->findOneBy(array('id'=>$id));
            $comments = $em->getRepository('BlogFrontBundle:Comment')
                            ->findBy(array("posts"=>$id));

            $viewForm = $this->addComment($request);

            return $this->render('BlogFrontBundle:Post:post.html.twig',
                                array("content" => "Affichage de l'article n°" . $id,
                                    "article" => $post,
                                    "comments" => $comments,
                                    "form" => $viewForm)
                                );
        }
    }

    public function showLastPostAction($nbPosts){
        $repository = $this->getDoctrine()->getManager()
                        ->getRepository('BlogFrontBundle:Post');
        $posts = $repository->getLastPost($nbPosts);
        return $this->render('BlogFrontBundle:Default:index.html.twig',
                            array('content' => 'Affichage des Posts','posts' => $posts)
                            );
    }

    public function addComment(Request $request){
        $comment = new Comment();

        $form = $this->createFormBuilder($comment)
                    ->add('title', 'text', array(
                        'constraints' => array(
                            new NotBlank
                        )
                    ))
                    ->add('description', 'textarea', array(
                        'constraints' => array(
                            new NotBlank
                        )
                    ))
                    ->add('valider', 'submit')
                    ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            $validForm = 'Formulaire Valide : <br>'
                            .$form->get('title')->getData()
                            .'<br>'
                            .$comment->getDescription();

            return new Response($validForm);
        }

        return $form->createView();
    }
}
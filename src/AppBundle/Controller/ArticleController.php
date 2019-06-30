<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleController extends FOSRestController
{

    /**
     * @Rest\Get(
     *     path="/articles",
     *     name="app_article_list"
     * )
     */
    public function listArticlesAction()
    {
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findAll();
        $articles_array = [];
        $response_array = [];
        foreach ($articles as $article) {
            $articles_array['title'] = $article->getTitle();
            $articles_array['content'] = $article->getContent();
            $articles_array['author'] = $article->getAuthor();
            $response_array[] = $articles_array;
        }
        return $this->view($response_array, Response::HTTP_OK);
    }

    /**
     * @Rest\Get(
     *     path="/articles/{id}",
     *     name="app_article_show",
     *     requirements={"id"="\d+"}
     * )
     *
     * @Rest\View
     *
     */
    public function showArticleAction(Article $article)
    {
        return $article;
    }

    /**
     * @Rest\Post(
     *     path="/articles",
     *     name="app_article_create"
     * )
     * @Rest\View(
     *     statusCode=201
     * )
     * 
     * @ParamConverter("article", converter="fos_rest.request_body")
     *
     */
    public function createArticleAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return $this->view(
            $article,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl(
                    'app_article_show',
                    ['id' => $article->getId(), UrlGeneratorInterface::ABSOLUTE_URL]
                )
            ]
        );
    }
}

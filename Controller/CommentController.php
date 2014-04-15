<?php

namespace Crearock\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Crearock\ProjectBundle\Entity\Comment;

use Crearock\ProjectBundle\Form\CommentType;

class CommentController extends Controller{
    
    /*public function indexAction($project_id){
        $em = $this->getDoctrine()->getEntityManager();
        
        $comments = $em->getRepository('ProjectBundle:Comment')->findByProject($project_id);
        
        return $this->render('ProjectBundle:Comment:comment.html.twig', array('projects' => $comments));
    }*/
    
    public function newAction($user_url, $project_url)
    {   
        $em = $this->getDoctrine()->getEntityManager();
        
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);
        
        $form->bindRequest($this->getRequest());
        if ($form->isValid()) {

            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();
            
            $em = $this->getDoctrine()->getEntityManager();
            $user = $em->getRepository('UserBundle:User')->find($id_user);
            
            
            $project = $em->getRepository('ProjectBundle:Project')->findOneByUrl($project_url);
            $new_id = $em->getRepository('ProjectBundle:Comment')->getNewId($project->getId());
            
//            $comment->setComment(nl2br($comment->getComment()));
            $comment->setId($new_id);
            $comment->setUser($user);
            $comment->setProject($project);
            
            $em->persist($comment);
            $em->flush();
        }
        
        return $this->redirect($this->generateUrl('project_show', array ('user_url'=>$user_url, 'project_url'=>$project_url)));
    }
    
}

?>

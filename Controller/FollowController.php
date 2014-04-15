<?php
namespace Crearock\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Crearock\UserBundle\Entity\User;
use Crearock\ProjectBundle\Entity\Project;
use Crearock\ProjectBundle\Entity\Follow;

class followController extends Controller 
{
    public function followAction($user_url, $project_url){
        $em = $this->getDoctrine()->getEntityManager();
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        $id_user = $user->getId();
        
        $status_allowed = array(    Project::BEING_APPLAUDED,
                                    Project::BEING_SUPPORTED,
                                    Project::DECIDING,
                                    Project::EXTENDED,
                                    Project::ENDED_SUCCESSFULLY,
                                    Project::ENDED_UNSUCCESSFULLY);
        if (    !$em->getRepository('ProjectBundle:Project')->isFollowing($user_url, $project_url, $id_user) &&
                ($project = $em->getRepository('ProjectBundle:Project')->findOneByUrl($project_url))){
            if (in_array($project->getStatus(), $status_allowed)) 
            {
                $follow = new Follow();
                $follow->setUser($user);
                $follow->setProject($project);
                
                $em->persist($follow);
                $em->flush();
            }
        }
        return $this->redirect($this->generateUrl('project_show', array ('user_url'=>$user_url, 'project_url'=>$project_url)));
    }
    
    public function unfollowAction($user_url, $project_url){
        $em = $this->getDoctrine()->getEntityManager();

        $user_id = $this->container->get('security.context')->getToken()->getUser()->getId();
        
        if (($project = $em->getRepository('ProjectBundle:Project')->findOneByUrl($project_url)) &&
                ($follow = $em->getRepository('ProjectBundle:Follow')->findOneBy(array('user' => $user_id, 'project' => $project->getId())))){
                $em->remove($follow);
                $em->flush();
        }
        return $this->redirect($this->generateUrl('project_show', array ('user_url'=>$user_url, 'project_url'=>$project_url)));
    }
}

?>

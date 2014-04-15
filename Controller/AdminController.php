<?php
namespace Crearock\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Crearock\ProjectBundle\Entity\Project;
use Crearock\ProjectBundle\Form\ProjectType;
use Crearock\ProjectBundle\Form\Admin\ProjectType as AdminProjectType;


class AdminController extends Controller
{
    /**
     * Displays a list of Project entities.
     *
     */
    public function listAction()
    {   
        $em = $this->getDoctrine()->getEntityManager();
        $projects = $em->getRepository('ProjectBundle:Project')->findAll(array('created_at' => 'desc'));

        return $this->render('ProjectBundle:Admin:list.html.twig', array(
            'projects'      => $projects
        ));
    }
    
    public function manageAction($user_url, $project_url, $action)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $project_repository = $em->getRepository('ProjectBundle:Project');
        if (!$project_repository->exists($user_url, $project_url)) {
            throw $this->createNotFoundException('El proyecto no ha sido encontrado.');
        }
        
        switch ($action) {
            case 'activar':
                $current_status = Project::UNDER_REVIEW;
                break;
            case 'prorrogar':
                $new_status = Project::EXTENDED;
            case 'finalizar':
                $current_status = Project::DECIDING;
                break;
            default: 
                throw $this->createNotFoundException('La accion %s no existe.', $action);
        }
        
        if (($project = $project_repository->findOneBy(array('url' => $project_url))) && ($current_status == $project->getStatus())) {
            switch ($action) {
                case 'activar':
                    $new_status = Project::BEING_APPLAUDED;
                    $project->setStartFundAt(new \DateTime());
                    break;
                case 'prorrogar':
                    $new_status = Project::EXTENDED;
                    $project->setExtendedAt(new \DateTime());
                    break;
                case 'finalizar':
                    $new_status = Project::ENDED_UNSUCCESSFULLY;
                    break;
            }

            $project->setStatus($new_status);

            $em->persist($project);
            $em->flush();
        }
//        $message = \Swift_Message::newInstance()
//                    ->setSubject('')
//                    ->setFrom(array('noreply@crearock.es' => 'Crea Rock'))
//                    ->setTo($email_list)
//                    ->setBody($this->renderView('EmailNotifierBundle:Project:newProject.html.twig', array(
//                            'userName' => 'Administrador de Crea Rock',
//                            'bandName' => $user->getUsername(),
//                            'projectTitle' => $project->getTitle())), 'text/html');
//        $this->get('mailer')->send($message);

        return $this->redirect($this->generateUrl('project_list'/*, array ('user_url' => $user_url, 'project_url' => $project_url)*/));
    }
}

?>

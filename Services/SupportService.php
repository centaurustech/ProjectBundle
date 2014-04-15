<?php
namespace Crearock\ProjectBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Crearock\PaymentBundle\Services\ResponseService;
use Crearock\ProjectBundle\Entity\Support;
use Crearock\ProjectBundle\Entity\TempSupport;
use Symfony\Component\HttpFoundation\Response;
use Crearock\ProjectBundle\Entity\Project;

class SupportService {
    
    private $response;
    private $request;
    private $em;
    private $mailer;
    private $router;
    private $templateEngine;
    
    function __construct(Request $request, ResponseService $response, EntityManager $entityManager, 
            \Swift_Mailer $mailer, \Symfony\Bundle\FrameworkBundle\Routing\Router $router, \Symfony\Bundle\TwigBundle\TwigEngine $templateEngine) {
                
        $this->request = $request;
        $this->response = $response;
        $this->em = $entityManager;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templateEngine = $templateEngine;
    }
    
    public function confirmSupport($user_url, $project_url){
                                          
        if ($this->request->getMethod() == 'POST'){  
                        
            $httpResponse = new Response();
            
            if(($confirmedTransaction = $this->response->cookResponse()))
            {            
                $tempSupport = $this->em->getRepository('ProjectBundle:TempSupport')->findOneByTransaction($confirmedTransaction->getId());
                if($tempSupport instanceof TempSupport){
                    $support = new Support();
                    $support->setUser($tempSupport->getUser());
                    $reward = $tempSupport->getReward();
                    $reward->addUnit();
                    $support->setReward($reward);
                    $support->setTransaction($tempSupport->getTransaction());

                    //Una vez confirmada la respuesta, guardo el apoyo y el elimino el temporal
                    $this->em->persist($support);
                    //$this->em->remove($tempSupport);
                    $this->em->flush();
                    
                    $status_allowed = array(    Project::BEING_SUPPORTED,
                                    Project::EXTENDED);
                    
                    $project = $this->em->getRepository('ProjectBundle:Project')->findByUserProject($user_url, $project_url, $status_allowed);                    
                    $project['link'] = $this->router->generate('project_show', array ('user_url'=>$user_url, 'project_url'=>$project_url), true);
                    
                    $message = \Swift_Message::newInstance()
                        ->setSubject('¡Has apoyado a un proyecto!')
                        ->setFrom(array('noreply@crearock.es' => 'Crea Rock'))
                        ->setTo($tempSupport->getUser()->getEmail())
                        ->setReplyTo('info@crearock.es', 'Info Crea Rock')
                        ->setBody($this->templateEngine->render('EmailNotifierBundle:Project:supportConfirmation.html.twig', array(
                            'userName' => $tempSupport->getUser()->getUsername(),
                            'project' => $project,
                            'transactionID' => $support->getTransaction()->getId(),
                            'supportAmount' => $support->getReward()->getAmount())), 'text/html');
                    $this->mailer->send($message);
                    
                }
            }
            return $httpResponse;
        }       
    }
}

?>

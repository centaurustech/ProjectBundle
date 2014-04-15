<?php
namespace Crearock\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Crearock\UserBundle\Entity\User;
use Crearock\ProjectBundle\Entity\Project;
use Crearock\ProjectBundle\Entity\Reward;
use Crearock\ProjectBundle\Entity\Support;
use Crearock\ProjectBundle\Entity\TempSupport;
use Crearock\ProjectBundle\Form\SupportType;

class SupportController extends Controller 
{
    public function showSupportAction($user_url, $project_url, $reward_id)
    {   
        $em = $this->getDoctrine()->getEntityManager();
        $status_allowed = array(    Project::BEING_SUPPORTED,
                                        Project::EXTENDED);
        
        //Comprobamos si el proyecto está en un estado en el que se puede apoyar, si no se muestra el proyecto
        if (!$project = $em->getRepository('ProjectBundle:Project')->findByUserProject($user_url, $project_url, $status_allowed))
        {
            return $this->redirect($this->generateUrl('project_show', array ('user_url'=>$user_url, 'project_url'=>$project_url)));
        }   
        
        $request = $this->getRequest();
        if ($request->getMethod() == 'GET') {
            $project['support_selected'] = $reward_id;
            $project['rewards'] = $em->getRepository('ProjectBundle:Reward')->findBy(array('project' => $project['id']), array('amount' => 'asc'));
            
            return $this->render('ProjectBundle:Project:support.html.twig', array(
                'project' => $project,
                'confirmed' => false
            ));
            
        } else if ($request->getMethod() == 'POST') {
            $error = false;
            $support = $request->request->get('support');
            $reward_id = $request->request->get('reward_id');
            if (!$reward_id){
                $request->getSession()->setFlash('error_rewards', 'Selecciona una cantidad con la que apoyar el proyecto.');
                $error = true;
            }
            if (!$support['terms']) {
                $request->getSession()->setFlash('error_terms', 'Debes leer y aceptar los Términos de uso y la Política de privacidad.');
                $error = true;
            }
            if ($error) {
                return $this->redirect($this->generateUrl('project_showSupport', array( 'user_url' => $user_url,
                    'project_url' => $project_url, 'reward_id' => $reward_id )));
            }
            
            //Comprobamos si está logueado, si no pa la home
            if (!$this->get('access_control')->isLogged()){
                return $this->redirect($this->generateUrl('home_page'));
            }
            
            //Comprobamos si el usuario existe
            $id_user = $this->container->get('security.context')->getToken()->getUser()->getId();        
            if (!$user = $em->getRepository('UserBundle:User')->find($id_user))
            {
                return $this->redirect($this->generateUrl('project_show', array ('user_url'=>$user_url, 'project_url'=>$project_url)));
            }
            
            $reward = $em->getRepository('ProjectBundle:Reward')->find($reward_id);
            //El reward existe y pertenece al proyecto ?
            if ( !$reward || $reward->getProject()->getId() != $project['id'] || $reward->isFinished() ) {
                return $this->redirect($this->generateUrl('project_showSupport', array ('user_url'=>$user_url, 'project_url'=>$project_url)));
            }

            //Instancio el apoyo temporal y los guardo sin transacción
            $tempsupport = new TempSupport();
            $tempsupport->setUser($user);
            $tempsupport->setReward($reward);
            $em->persist($tempsupport);
            $em->flush();

            //Instancio el servicio de pago
            $payment = $this->container->get('payment.transaction');
            $urlRespuesta = $this->generateUrl('project_confirmSupport', array ('user_url'=>$user_url, 'project_url'=>$project_url), true);
            $urlOk = $this->generateUrl('project_support_ok', array ('user_url'=>$user_url, 'project_url'=>$project_url), true);
            $urlKo = $this->generateUrl('project_support_ko', array ('user_url'=>$user_url, 'project_url'=>$project_url), true);
            $transaction = $payment->cookTransaction($tempsupport->getId(), $reward->getAmount(), 'Apoyo Crea Rock', $urlRespuesta, $urlOk, $urlKo);        

            //Una vez con la transacción, la relaciono con el apoyo temporal y lo guardo
            $tempsupport->setTransaction($transaction);
            $em->persist($tempsupport);
            $em->flush();

            return $this->render('ProjectBundle:Project:support.html.twig', array(
                'reward' => $reward,
                'project' => $project,
                'transaction' => $transaction,
                'confirmed' => true
            ));
        }
    } 
    
    public function okSupportAction($user_url, $project_url)
    {
        $backlink = array(
            'href' => $this->generateUrl('project_show', array ('user_url'=>$user_url, 'project_url'=>$project_url)),
            'title' => $this->get('translator')->trans('project_back'),
            'text' => $this->get('translator')->trans('project_back')
        );
        
        $this->get('session')->setFlash('message', $this->get('translator')->trans('support_ok'));
        return $this->render('UserBundle:User:showflash.html.twig', array(
            'backlink' => $backlink
        ));
    }
    
    public function koSupportAction($user_url, $project_url)
    {
        $backlink = array(
            'href' => $this->generateUrl('project_show', array ('user_url'=>$user_url, 'project_url'=>$project_url)),
            'title' => $this->get('translator')->trans('project_back'),
            'text' => $this->get('translator')->trans('project_back')
        );
        
        $this->get('session')->setFlash('message', $this->get('translator')->trans('support_ko'));
        return $this->render('UserBundle:User:showflash.html.twig', array(
            'backlink' => $backlink
        ));
    }
}

?>

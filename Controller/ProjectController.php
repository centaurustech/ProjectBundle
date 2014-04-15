<?php

namespace Crearock\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\Container;

use Crearock\UserBundle\Entity\User;
use Crearock\ProjectBundle\Entity\Project;
use Crearock\ProjectBundle\Entity\Comment;
use Crearock\ProjectBundle\Entity\Applause;
use Crearock\ProjectBundle\Entity\Reward;

use Crearock\ProjectBundle\Form\ProjectType;
use Crearock\ProjectBundle\Form\AdminProjectType;
use Crearock\ProjectBundle\Form\CommentType;

/**
 * Project controller.
 *
 */
class ProjectController extends Controller
{
    /**
     * Lists all Project entities.
     *
     */
    public function indexAction()
    {
        $projectRepository = $this->getDoctrine()->getEntityManager()->getRepository('ProjectBundle:Project');
        
        $slide = $projectRepository->findTopRatedSlide();

        $topRated = $projectRepository->findTopRated(3, false);
        $ending = $projectRepository->findEnding(3, false);
        $mostSupported = $projectRepository->findMostSupported(3, false);
        
        return $this->render('ProjectBundle:Project:home.html.twig', array(
            'topRateds' => $topRated,
            'endings' => $ending,
            'mostSupporteds' => $mostSupported,
            'slide' => $slide,
            'navButton' => 'home_page'
        ));
    }
    
    /**
     * Finds and displays a Project entity.
     *
     */
    public function showAction($user_url, $project_url)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $status_allowed = array (   Project::BEING_APPLAUDED,
                                    Project::BEING_SUPPORTED,
                                    Project::DECIDING,
                                    Project::EXTENDED,
                                    Project::ENDED_SUCCESSFULLY,
                                    Project::ENDED_UNSUCCESSFULLY);
        $security = $this->container->get('security.context');
        if ($security->isGranted('ROLE_ADMIN')){
            $status_allowed[] = Project::UNDER_REVIEW;
        }   
        if (!$project = $em->getRepository('ProjectBundle:Project')->findByUserProject($user_url, $project_url, $status_allowed))
        {
// @CHECKME
// Diseñar pagina a mostrar si la url 'username/title' no se encuentra
            throw $this->createNotFoundException('El proyecto que estas buscando no ha sido encontrado.');
        }
        
        $status = $project['status'];
        $project['status_message'] = $this->getStatusMessage($status);
        
        if ($security->isGranted('IS_AUTHENTICATED_FULLY')){         
            $id_user = $security->getToken()->getUser()->getId();
            $project_repository = $em->getRepository('ProjectBundle:Project');
            $project['applauded'] = $project_repository->isApplauded($user_url, $project_url, $id_user);
            $project['following'] = $project_repository->isFollowing($user_url, $project_url, $id_user);
        } else {
            $project['applauded'] = false;
            $project['following'] = false;
        }
        
        if (in_array($status, array (   Project::UNDER_REVIEW,
                                        Project::BEING_APPLAUDED,
                                        Project::ENDED_SUCCESSFULLY,
                                        Project::DECIDING,
                                        Project::ENDED_UNSUCCESSFULLY))
                || $project['days_left'] < 0)
        {
            $project['disabled'] = true;
            $project['days_left'] = 0;
        } else {
            $project['disabled'] = false;
        }
        
//        if (in_array($status, array (   Project::BEING_APPLAUDED,
//                                        Project::BEING_SUPPORTED,
//                                        Project::EXTENDED,
//                                        Project::ENDED_SUCCESSFULLY,
//                                        Project::ENDED_UNSUCCESSFULLY)))
//        {
//        Muestra las recompensas en cualquier estado en el que se encuentre el proyecto
            $project['rewards'] = $em->getRepository('ProjectBundle:Reward')->findBy(array('project' => $project['id']), array('amount' => 'asc'));
//        }
        
        $project['new_comment_form'] = $this->createForm(new CommentType(), new Comment())->createView();
        $project['comments'] = $em->getRepository('ProjectBundle:Comment')->findByProject($project['id']);
        
        return $this->render('ProjectBundle:Project:show.html.twig', array(
            'project'   => $project
        ));
    }

    /**
     * Edit a Project entity.
     *
     */
    public function editAction($user_url, $project_url)
    {
        $security = $this->container->get('security.context');
// Si es administrador
        if ($security->isGranted('ROLE_ADMIN')){
            $em = $this->getDoctrine()->getEntityManager();
// Comprueba si existe un usuario-proyecto
            if ($em->getRepository('ProjectBundle:Project')->exists($user_url, $project_url)) {
                return $this->forward('ProjectBundle:Project:new', 
                    array(
                        'project_url' => $project_url
                    )
                );
            }
// Sino existe, redirecciona a la lista de proyectos
            return $this->redirect($this->generateUrl('project_list'));
        }      
// Sino, redirecciona a la página del proyecto 
        return $this->redirect($this->generateUrl('project_show', array ('user_url' => $user_url, 'project_url' => $project_url)));
        
    }
    
    
    /**
     * Creates a new Project entity.
     *
     */
    public function newAction($project_url)
    {
        $new = ($project_url == null) ? true : false;
        
        $em = $this->getDoctrine()->getEntityManager();
        if ($new) {
            $project = $this->addDefaultRewards(new Project());
        } else {
            $project = $em->getRepository('ProjectBundle:Project')->findOneByUrl( $project_url );
        }
        $form = $this->createForm(new ProjectType(), $project);        
        
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST'){
            $old_title = $project->getTitle();
            $old_image = $project->getImage();

// Si estamos editando un proyecto
            if (!$new){
// Guardamos la lista de recompensas que tenia antes de hacer POST
                $init_col = Array();
                foreach ($project->getRewards() as $reward) $init_col[] = $reward;
            }
            
            $form->bindRequest($request);
            if ($form->isValid()) {
                if ($new ||
                        (!$new && $old_title != $project->getTitle())) {
                    $project->setUrl($this->get('friendly_url')->generate_seo_link($form['title']->getData()));
                }
//                $tempFileName = $form['image']->getData();
// Si es un nuevo proyecto o si esta siendo editado y la imagen es distinta
                $temp_image = $project->getImage();
                if ($new || (!$new && $old_image != $temp_image)) {
                    //FileUploader
// Instanciamos fileUploader service
                    $fileUploader = $this->get('file_uploader');
                    $newFileName = $project->getUrl() .'.'. substr(strrchr($temp_image, '.'), 1);
// Obtengo las variables globales de twig definidas en config.yml
                    $twig_global = $this->container->get('twig')->getGlobals();
                    $fileUploader->setOption(
                        array(
                            'temp_dir' => $twig_global['g_project']['temp_dir'],
                            'upload_dir' => $twig_global['g_project']['upload_dir']
                            )
                        )->confirmImage($temp_image, $newFileName);
                    $project->setImage($newFileName);
                } 
//Si es un nuevo proyecto                 
                if ($new) {
// Se asigna el usuario
                    $user = $this->container->get('security.context')->getToken()->getUser();
                    $project->setUser($user);
                } else {
// Sino, se comprueba si se ha eliminado algun elemento de la coleccion de rewards                    
                    $final_col = $project->getRewards();
                    foreach ($final_col as $final_elem) {
                        foreach ($init_col as $key => $toDel){
                            if ($toDel->getId() == $final_elem->getId()) {
                                unset($init_col[$key]);
                            }
                        }
                    }
                    foreach ($init_col as $reward) {
                        $em->remove($reward);                        
                    }                    
                }
                
                $em->persist($project);
                $em->flush();
                
// Si estamos editando el proyecto y se ha modificado la imagen                
                if (!$new && $old_image != $project->getImage()) {
// Borramos la imagen antigua
                    $fileUploader->deleteOldFile($old_image);
                }
                
                if ($new) {
// Obtengo el nombre y email de los administradores
                    $admins = $em->getRepository('UserBundle:User')->findByRole('ROLE_ADMIN');
                    $email_list = array();
                    foreach($admins as $admin){
                        $email_list[$admin->getEmail()] = $admin->getUsername();
                    }
// Genero el mensaje                    
                    $message = \Swift_Message::newInstance()
                        ->setSubject('¡Nuevo proyecto creado!')
                        ->setFrom(array('noreply@crearock.es' => 'Crea Rock'))
                        ->setTo($email_list)
                        ->setReplyTo('info@crearock.es', 'Info Crea Rock')
                        ->setBody($this->renderView('EmailNotifierBundle:Project:newProject.html.twig', array(
                                'userName' => 'Administrador de Crea Rock',
                                'bandName' => $user->getUsername(),
                                'projectTitle' => $project->getTitle())), 'text/html');
// Y lo envio.
                    $this->get('mailer')->send($message);

// @CHECKME
// Redireccionar a pagina mostrando informacion sobre el proyecto enviado y avisando que esta esperando a ser aceptada.
                    $this->get('session')->setFlash('message', $this->get('translator')->trans('project_created'));
                    return $this->render('UserBundle:User:showflash.html.twig');
                }
// Si se esta editando un proyecto
                return $this->redirect($this->generateUrl('project_show',
                    array('user_url' => $project->getUser()->getUrl(),
                        'project_url' => $project->getUrl())
                ));
            }
        }
        
        return $this->render('ProjectBundle:Project:new.html.twig', array(
            'form'   => $form->createView(),
            'project' => $project,
            'action' => ($new) ? 'new' : 'edit'
        ));
    }
        
    public function uploadImageAction(){
        if ($this->get('access_control')->isLogged()){
            $fileUploader = $this->get('file_uploader');
            
            $twig_global = $this->container->get('twig')->getGlobals();
            $answer = $fileUploader->setOption(
                    array(
                        'temp_dir' => $twig_global['g_project']['temp_dir'],
                        'upload_dir' => $twig_global['g_project']['upload_dir']
                        )
                    )->post();
            
            $response = new Response($answer);

            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
            $response->headers->set('Content-Disposition', 'inline; filename="files.json"');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'OPTIONS, HEAD, GET, POST, PUT, DELETE');
            $response->headers->set('Access-Control-Allow-Headers', 'X-File-Name, X-File-Type, X-File-Size');
            $response->headers->set('Content-Type', 'text/plain');
            
            return $response;
        }            
    }   
    
    public function categoryAction($category, $sortBy, $sortDirection, $page) {
        $path = array();
        $em = $this->getDoctrine()->getEntityManager();
        
        $paginator = $this->get('paginator');
        $paginator->setCurrentPage($page);
        
        $path['project_category'] = array(   'category' => $category,
                                            'sortBy' => $sortBy,
                                            'sortDirection' => $sortDirection);
        $paginator->setPath($path);        
        $result = $em->getRepository('ProjectBundle:Project')->
                findByCategory(
                        $category,
                        $sortBy,
                        $sortDirection,
                        $paginator->getPerPage(),
                        $paginator->getOffset());
        
        $paginator->getPaginator($result['num_rows']);
        
        $categories = $em->getRepository('ProjectBundle:ProjectCategory')->findByEnabled(true);
                
        return $this->render('ProjectBundle:Project:category.html.twig', array(
            'projects'   => $result['projects'],
            'categories_list' => $categories,
            'category' => $category,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
            'paginator' => $paginator,
            'navButton' => 'project_category'
        ));
    }   
    
    public function giveAppaluseAction ($user_url, $project_url){
        $em = $this->getDoctrine()->getEntityManager();
        
        $user = $this->container->get('security.context')->getToken()->getUser();
                
        $status_allowed = array(    Project::BEING_APPLAUDED,
                                    Project::BEING_SUPPORTED,
                                    Project::EXTENDED,
                                    Project::ENDED_SUCCESSFULLY,
                                    Project::ENDED_UNSUCCESSFULLY);
        if (    !$em->getRepository('ProjectBundle:Project')->isApplauded($user_url, $project_url, $user->getId()) &&
                ($project = $em->getRepository('ProjectBundle:Project')->findOneByUrl($project_url))){
            if (in_array($project->getStatus(), $status_allowed)) 
            {
                $num_applause = $project->getApplause() + 1;
                if (($num_applause == Project::MIN_APPLAUSE))
                {
                    $project->setStatus(Project::BEING_SUPPORTED);
                    $project->setStartFundAt(new \DateTime());   
                }
                $project->setApplause($num_applause);
                $applause = new Applause();
                $applause->setProject($project);
                $applause->setUser($user);

                $em->persist($applause);
                $em->flush();
            }
        }
        return $this->redirect($this->generateUrl('project_show', array ('user_url'=>$user_url, 'project_url'=>$project_url)));
    }
    
    public function applauseBoxAction ($page){
        $em = $this->getDoctrine()->getEntityManager();
        
        $paginator = $this->get('paginator');
        $paginator->setCurrentPage($page);
        $paginator->setPathName('project_applauseBox');
        
        
        $result = $em->getRepository('ProjectBundle:Project')->findApplauseBoxProjects($paginator->getPerPage(), $paginator->getOffset());
        $paginator->getPaginator($result['num_rows']);
                
        return $this->render('ProjectBundle:Project:applauseBox.html.twig', array(
            'projects'   => $result['projects'],
            'paginator' => $paginator,
            'navButton' => 'project_applauseBox'
        ));   
    }
    
    public function searchAction($query, $page){
        $paginator = $this->get('paginator');
        $paginator->setCurrentPage($page);
        $result = null;
        $message = null;
        
        $query = trim($query);
        
        if (empty($query)){
            $message = 'Introduce el nombre del grupo a buscar.';
        } else if (strlen($query) < 3) {
            $message = 'Debes introducir al menos 3 carácteres para realizar la busqueda.';
        } else {
            $em = $this->getDoctrine()->getEntityManager();
            
            $result = $em->getRepository('ProjectBundle:Project')->findByQuery($query, $paginator->getPerPage(), $paginator->getOffset());
            $paginator->getPaginator($result['num_rows']);
            $paginator->setPath(array('project_search' => array('query' => $query)));
        }
        
        return $this->render('ProjectBundle:Project:search.html.twig', array(
                'projects' => $result['projects'],
                'paginator' => $paginator,
                'last_query' => $query,
                'message' => $message,
            ));
    }
    
    private function getStatusMessage($status){
        switch ($status){
            case Project::UNDER_REVIEW:
                return 'Status: Esperando la aprobación de un administrador';
                break;                
            case Project::BEING_APPLAUDED:
                return 'Status: Fase de activación (50 aplausos activarán el proyecto e iniciarán la recaudación)';
                break;
            case Project::BEING_SUPPORTED:
                return 'Status: Primera fase de recaudación';
                break;
            case Project::EXTENDED:
                return 'Status: Segunda fase de recaudación (prórroga)';
                break;
            case Project::DECIDING:
                return 'Status: Los autores de este proyecto están decidiendo si continúan';
                break;
            case Project::ENDED_SUCCESSFULLY:
                return 'Status: Finalizado correctamente';
                break;
            case Project::ENDED_UNSUCCESSFULLY:
                return 'Status: Finalizado sin éxito';
                break;
            default: 
                return '';
        }
    }
    
    private function addDefaultRewards($project) {
// @CHECKME
// Creacion de recompensas por defecto. Cargarlas desde una tabla?
        $reward = new Reward();
        $reward->setAmount(10);
        $reward->setTitle('Disco');
        $reward->setDescription('Nuestro disco dedicado');
        $reward->setUnits(0);
        $project->addReward($reward);
        unset($reward);

        $reward = new Reward();
        $reward->setAmount(20);
        $reward->setTitle('Disco + camiseta');
        $reward->setDescription('Nuestro disco dedicado y una camiseta');
        $reward->setUnits(0);
        $project->addReward($reward);
        unset($reward);

        $reward = new Reward();
        $reward->setAmount(30);
        $reward->setTitle('Disco + camiseta + fotos dedicadas');
        $reward->setDescription('Nuestro disco dedicado, una camiseta y fotografías dedicadas del grupo');
        $reward->setUnits(0);
        $project->addReward($reward);
        unset($reward);
        
        return $project;
    }
}

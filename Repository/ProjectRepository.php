<?php
// src/Crearock/ProjectBundle/Repository/ProjectRepository.php
namespace Crearock\ProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Crearock\ProjectBundle\Entity\Project;

class ProjectRepository extends EntityRepository{
    
    private $exclude = array();
    
    public function addExclusion($exclude){
        $this->exclude[] = $exclude;
    }
    
    public function findTopRatedSlide(){
        $status_allowed = implode(',',array(Project::BEING_APPLAUDED,
                                            Project::BEING_SUPPORTED,
                                            Project::EXTENDED,
                                            Project::ENDED_SUCCESSFULLY,
                                            Project::ENDED_UNSUCCESSFULLY));
        
        $connection = $this->getEntityManager()
                    ->getConnection();
                
        $filter = '';
        $result = array();
        $select = ' SELECT  user.username,
                            user.url as user_url,
                            project.id as id,
                            project.applause as applause,
                            project.title as title,
                            project.image as image,
                            project.status as status,
                            project.url as url,
                            project.amount as required_amount,
                            support.amount as supported_amount,
                            FLOOR(support.amount * IF (project.status <> ' . Project::BEING_SUPPORTED . ', 100, 152 ) / project.amount) as supported_percent,
                            IF (project.status <> ' . Project::EXTENDED . ', 0, ' . Project::EXTENDED_DAYS . ') + DATEDIFF(ADDDATE(project.start_fund_at, project.days), now()) as days_left
                    FROM project 
                    LEFT JOIN user
                        ON user.id = project.user_id
                    LEFT JOIN applause 
                        ON project.id = applause.project_id
                    LEFT JOIN (SELECT reward.project_id, SUM( reward.amount ) as amount
                                FROM support
                                LEFT JOIN reward ON support.reward_id = reward.id
                                GROUP BY reward.project_id) AS support
                        ON project.id = support.project_id
                    WHERE project.status IN (' . $status_allowed . ')';
        $sort = '   GROUP BY project.id
                    ORDER BY project.applause DESC
                    LIMIT 0,1';
        $project = $connection->executeQuery($select . $filter . $sort)
                    ->fetch(\PDO::FETCH_ASSOC);
        if (isset($project) && $project != false){
            $result['always'] = $project;
            $result['always']['ranking_message'] = 'PROYECTO MÁS VOTADO CON ' . 
                    $result['always']['applause'] . ' APLAUSOS.';
        }
        if (isset($project['id'])) {
            $this->addExclusion($project['id']);
        }
        
        $filter = ' AND DATE(applause.created_at) >= CURDATE() - INTERVAL DAYOFWEEK(CURDATE()) +7 DAY
                    AND DATE(applause.created_at) < CURDATE() - INTERVAL DAYOFWEEK(CURDATE()) +1 DAY';
        if (count($this->exclude) > 0) {
            $filter .= ' AND project.id NOT IN (' . implode(',', $this->exclude) . ')';
        }
        unset($project);
        $project = $connection->executeQuery($select . $filter . $sort)
                    ->fetch(\PDO::FETCH_ASSOC);
        if (isset($project) && $project != false){
            $result['week'] = $project;
            $result['week']['ranking_message'] = 'GANADOR DE LA SEMANA CON ' . 
                    $result['week']['applause'] . ' APLAUSOS.';
        }
        if (isset($project['id'])) {
            $this->addExclusion($project['id']);
        }
        $filter = ' AND DATE(applause.created_at) = CURDATE() - INTERVAL 1 DAY';
        if (count($this->exclude) > 0) {
            $filter .= ' AND project.id NOT IN (' . implode(',', $this->exclude) . ')';
        }
        unset($project);
        $project = $connection->executeQuery($select . $filter . $sort)
                    ->fetch(\PDO::FETCH_ASSOC);
        if (isset($project) && $project != false){
            $result['day'] = $project;
            $result['day']['ranking_message'] = 'GANADOR DEL DÍA CON ' . 
                    $result['day']['applause'] . ' APLAUSOS.';
        }
        if (isset($result['day']['id'])) {
            $this->addExclusion($result['day']['id']);
        }
        
        return $result;
    }
    
    public function findTopRated($maxResults = 3, $exclude = false) {   
        $status_allowed = implode(',',array( Project::BEING_APPLAUDED,
                                Project::BEING_SUPPORTED,
                                Project::EXTENDED));
        
        $query = '  SELECT user.username,
                            user.url as user_url,
                            project.id as id,
                            project.applause as applause,
                            ' . Project::MIN_APPLAUSE . ' - project.applause as applause_left,
                            FLOOR(project.applause * 100 / ' . Project::MIN_APPLAUSE . ') as applauded_percent,
                            project.title as title,
                            project.image as image,
                            project.resume as resume,
                            project.status as status,
                            project.url as url,
                            project.amount as required_amount,
                            support.amount as supported_amount,
                            FLOOR(support.amount * IF (project.status <> ' . Project::BEING_SUPPORTED . ', 100, 152 ) / project.amount) as supported_percent,
                            IF (project.status <> ' . Project::EXTENDED . ', 0, ' . Project::EXTENDED_DAYS . ') + DATEDIFF(ADDDATE(project.start_fund_at, project.days), now()) as days_left
                        FROM project 
                        LEFT JOIN user 
                            ON user.id = project.user_id
                        LEFT JOIN applause 
                            ON project.id = applause.project_id
                        LEFT JOIN (SELECT reward.project_id, SUM( reward.amount ) as amount
                                    FROM support
                                    LEFT JOIN reward ON support.reward_id = reward.id
                                    GROUP BY reward.project_id) AS support
                            ON project.id = support.project_id    
                        WHERE project.status IN (' . $status_allowed . ')';
        if ($exclude && count($this->exclude) > 0) {
                    $query .= ' AND project.id NOT IN (' . implode(',', $this->exclude) . ')';
        }
                    $query .=  ' GROUP BY project.id
                                ORDER BY project.applause DESC
                                LIMIT 0,' . $maxResults;
                               
        $result = $this->getEntityManager()
                    ->getConnection()
                    ->executeQuery($query)
                    ->fetchAll(\PDO::FETCH_ASSOC);
        if ($exclude) {
            foreach ($result as $project) {
                $this->addExclusion($project['id']);
            }
        }
        return $result;
    }
    
    public function findEnding($maxResults = 3, $exclude = false) {
        $status_allowed = implode(',',array(Project::BEING_SUPPORTED,
                                Project::EXTENDED));
        
        $query = 'SELECT user.username,
                                    user.url as user_url,
                                    project.id as id,
                                    project.title as title,
                                    project.image as image,
                                    project.resume as resume,
                                    project.status as status,
                                    project.url as url,
                                    project.amount as required_amount,
                                    support.amount as supported_amount,
                                    FLOOR(support.amount * IF (project.status <> ' . Project::BEING_SUPPORTED . ', 100, 152 ) / project.amount) as supported_percent,
                                    IF (project.status <> ' . Project::EXTENDED . ', 0, ' . Project::EXTENDED_DAYS . ') + DATEDIFF(ADDDATE(project.start_fund_at, project.days), now()) as days_left
                                FROM project
                                LEFT JOIN user 
                                    ON user.id = project.user_id
                                LEFT JOIN (SELECT reward.project_id, SUM( reward.amount ) as amount
                                            FROM support
                                            LEFT JOIN reward ON support.reward_id = reward.id
                                            GROUP BY reward.project_id) AS support
                                    ON project.id = support.project_id    
                                WHERE project.status IN ('. $status_allowed . ')';
        if ($exclude && count($this->exclude) > 0) {
                    $query .= ' AND project.id NOT IN (' . implode(',', $this->exclude) . ')';
        }
                    $query .= ' ORDER BY days_left ASC
                                LIMIT 0,' . $maxResults;
        $result = $this->getEntityManager()
                    ->getConnection()
                    ->executeQuery($query)
                    ->fetchAll(\PDO::FETCH_ASSOC);
        if ($exclude) {
            foreach ($result as $project) {
                $this->addExclusion($project['id']);
            }
        }
        return $result;
    }
    
    public function findMostSupported($maxResults = 3,  $exclude = false) {
        $status_allowed = implode(',',array(Project::BEING_SUPPORTED,
                                Project::EXTENDED));
        
        $query = 'SELECT user.username,
                                    user.url as user_url,
                                    project.id as id,
                                    project.title as title,
                                    project.image as image,
                                    project.resume as resume,
                                    project.status as status,
                                    project.url as url,
                                    project.amount as required_amount,
                                    support.amount as supported_amount,
                                    FLOOR(support.amount * IF (project.status <> ' . Project::BEING_SUPPORTED . ', 100, 152 ) / project.amount) as supported_percent,
                                    IF (project.status <> ' . Project::EXTENDED . ', 0, ' . Project::EXTENDED_DAYS . ') + DATEDIFF(ADDDATE(project.start_fund_at, project.days), now()) as days_left
                                FROM project
                                LEFT JOIN user 
                                    ON user.id = project.user_id
                                LEFT JOIN (SELECT reward.project_id, SUM( reward.amount ) as amount
                                            FROM support
                                            LEFT JOIN reward ON support.reward_id = reward.id
                                            GROUP BY reward.project_id) AS support
                                    ON project.id = support.project_id    
                                WHERE project.status IN (' . $status_allowed . ')';
        if ($exclude && count($this->exclude) > 0) {
                    $query .= ' AND project.id NOT IN (' . implode(',', $this->exclude) . ')';
        }
                    $query .= ' ORDER BY supported_amount DESC
                                LIMIT 0,' . $maxResults;
        $result = $this->getEntityManager()
                    ->getConnection()
                    ->executeQuery($query)
                    ->fetchAll(\PDO::FETCH_ASSOC);
        if ($exclude) {
            foreach ($result as $project) {
                $this->addExclusion($project['id']);
            }
        }
        return $result;
    }
       
    public function findByUserProject($user_url, $project_url, $status_allowed) {
        $status_allowed = implode(',', $status_allowed);
        $query = 'SELECT p.id,
                                u.username,
                                u.url as user_url,
                                p.title,
                                p.image,
                                c.name as category_name,
                                c.url as category_url,
                                p.description,
                                p.resume,
                                p.status,
                                p.vurl as video,
                                p.aurl as audio,
                                p.url,
                                p.amount as required_amount,
                                s.amount as supported_amount,
                                FLOOR(s.amount * IF (p.status <> ' . Project::BEING_SUPPORTED . ', 100, 152 ) / p.amount) as supported_percent,
                                IF (p.status <> ' . Project::EXTENDED . ', 0, ' . Project::EXTENDED_DAYS . ') + DATEDIFF(ADDDATE(p.start_fund_at, p.days), now()) as days_left,
                                p.applause as applause,
                                IF (p.status = '. Project::BEING_APPLAUDED . ',' . Project::MIN_APPLAUSE . ' - p.applause, null) as applause_left,
                                IF (p.status = '. Project::BEING_APPLAUDED . ', FLOOR(p.applause * 100 / ' . Project::MIN_APPLAUSE . '), null) as applause_percent
                            FROM project as p
                            LEFT JOIN user as u
                                ON u.id = p.user_id
                            LEFT JOIN project_category as c
                                ON c.id = p.category_id
                            LEFT JOIN (SELECT r.project_id, SUM( r.amount ) as amount
                                        FROM support as s
                                        LEFT JOIN reward as r
                                                ON s.reward_id = r.id
                                        GROUP BY r.project_id) AS s
                                ON p.id = s.project_id    
                            WHERE p.status IN (' . $status_allowed .') AND
                                u.url = "' . $user_url . '" AND 
                                p.url = "' . $project_url . '"';
                
        $result = $this->getEntityManager()
            ->getConnection()
            ->executeQuery($query);
        
        
        if ($result->rowCount() == 1) {
            return $result->fetch(\PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    }
        
    public function findByCategory($category, $sortBy, $sortDirection, $maxResults, $offset = 0) {
        $result = null;
        
        $category_join = '';
        $and_where = '';
        switch ($sortBy){
            case 'alfabetico':
                $sqlSortBy = 'project.title';
                break;
            case 'recaudacion':
                $sqlSortBy = 'supported_amount';
                break;
            case 'finalizacion';
                $sqlSortBy = 'days_left';
                break;
            default:
                $sqlSortBy = 'project.title';
        }
        
        $status_allowed = array (   Project::BEING_APPLAUDED,
                                    Project::BEING_SUPPORTED,
                                    Project::DECIDING,
                                    Project::EXTENDED);
        switch ($category){
            case 'novedades':
                break;
            case 'recomendaciones':
                break;
            case 'segunda-fase':
                $status_allowed = array (   Project::EXTENDED);
                break;
            case 'finalizados-exito':
                $status_allowed = array (   Project::ENDED_SUCCESSFULLY);
                break;
            default:
                $category_join = ' LEFT JOIN project_category as category
                                    ON category.id = project.category_id ';
                $and_where = ' AND category.url = "' . $category . '"
                                AND category.enabled = 1 ';
        }
        $status_allowed = implode(',',$status_allowed);
        
        $query = '  SELECT user.username,
                        user.url as user_url,
                        project.title as title,
                        project.image as image,
                        project.resume as resume,
                        project.status as status,
                        project.url as url,
                        project.amount as required_amount,
                        support.amount as supported_amount,
                        FLOOR(support.amount * IF (project.status <> ' . Project::BEING_SUPPORTED . ', 100, 152 ) / project.amount) as supported_percent,
                        IF (project.status <> ' . Project::EXTENDED . ', 0, ' . Project::EXTENDED_DAYS . ') + DATEDIFF(ADDDATE(project.start_fund_at, project.days), now()) as days_left,
                        project.applause as applause,
                        ' . Project::MIN_APPLAUSE . ' - project.applause as applause_left,
                        FLOOR(project.applause * 100 / ' . Project::MIN_APPLAUSE . ') as applauded_percent
                    FROM project
                    LEFT JOIN user 
                        ON user.id = project.user_id 
                    LEFT JOIN (SELECT reward.project_id, SUM( reward.amount ) as amount
                                FROM support 
                                LEFT JOIN reward ON support.reward_id = reward.id
                                GROUP BY reward.project_id) AS support
                        ON project.id = support.project_id' .
                    $category_join .
                    ' WHERE  project.status IN (' . $status_allowed . ')' .
                    $and_where .
                    ' ORDER BY ' . $sqlSortBy . ' ' . $sortDirection;
        $result['num_rows'] = $this->getEntityManager()
                        ->getConnection()
                        ->executeQuery($query)->rowCount();
        $query .= ' LIMIT ' . $offset . ',' . $maxResults;
        
        $result['projects'] = $this->getEntityManager()
                    ->getConnection()
                    ->executeQuery($query)
                    ->fetchAll(\PDO::FETCH_ASSOC);
        
        return $result; 
    }    
    
    public function isApplauded($user_url, $project_url, $id_logged) {
        $query =   'SELECT p.* 
                    FROM project AS p
                    LEFT JOIN user AS u
                        ON p.user_id = u.id
                    LEFT JOIN applause AS a 
                        ON p.id = a.project_id
                    WHERE u.url =  "' . $user_url . '" AND
                        p.url =  "' . $project_url . '" AND
                        a.user_id = ' . $id_logged;
        
        $result = $this->getEntityManager()
                ->getConnection()
                ->executeQuery($query);
        
        if ($result->rowCount() == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    public function isFollowing($user_url, $project_url, $id_logged) {
        $query =   'SELECT p.id 
                    FROM project AS p
                    LEFT JOIN user AS u
                        ON p.user_id = u.id
                    LEFT JOIN follow AS f 
                        ON p.id = f.project_id
                    WHERE u.url =  "' . $user_url . '" AND
                        p.url =  "' . $project_url . '" AND
                        f.user_id = ' . $id_logged;
        
        $result = $this->getEntityManager()
                ->getConnection()
                ->executeQuery($query);
        
        if ($result->rowCount() == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    public function exists($user_url, $project_url) {
        $query =   'SELECT p.id 
                    FROM project AS p
                    LEFT JOIN user AS u
                        ON p.user_id = u.id
                    WHERE u.url =  "' . $user_url . '" AND
                        p.url =  "' . $project_url . '"';
        
        $result = $this->getEntityManager()
                ->getConnection()
                ->executeQuery($query);
        
        if ($result->rowCount() == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    public function findApplauseBoxProjects($maxResults, $offset = 0) {
        $status_allowed = implode(',',array(Project::BEING_APPLAUDED));
        
//      @CHECKME
//      Porque se ordenan los proyectos de la pagina del aplausometro?
        $result = null;
        $query =   'SELECT user.username,
                        user.url as user_url,
                        project.title as title,
                        project.image as image,
                        project.resume as resume,
                        project.status as status,
                        project.url as url,
                        project.applause as applause,
                        ' . Project::MIN_APPLAUSE . ' - project.applause as applause_left,
                        FLOOR(project.applause * 100 / ' . Project::MIN_APPLAUSE . ') as applauded_percent
                    FROM project
                    LEFT JOIN user 
                        ON user.id = project.user_id  
                    WHERE project.applause < ' . Project::MIN_APPLAUSE . '
                        AND project.status IN (' . $status_allowed . ')
                    ORDER BY project.applause DESC';
        
        $result['num_rows'] = $this->getEntityManager()
                        ->getConnection()
                        ->executeQuery($query)->rowCount();
        $query .= ' LIMIT ' . $offset . ',' . $maxResults;
        
        $result['projects'] = $this->getEntityManager()
                    ->getConnection()
                    ->executeQuery($query)
                    ->fetchAll(\PDO::FETCH_ASSOC);
        
        return $result; 
    }
    
    public function findProjectsFromUser($user_url){
        $status_allowed = implode(',', array(   Project::BEING_APPLAUDED,
                                                Project::BEING_SUPPORTED,
                                                Project::DECIDING,
                                                Project::EXTENDED));
    
        $sql = 'SELECT user.username,
                    user.url as user_url,
                    project.title as title,
                    project.image as image,
                    project.resume as resume,
                    project.status as status,
                    project.url as url,
                    project.amount as required_amount,
                    support.amount as supported_amount,
                    FLOOR(support.amount * 100 / project.amount) as supported_percent,
                    IF (project.status <> ' . Project::EXTENDED . ', 0, ' . Project::EXTENDED_DAYS . ') + DATEDIFF(ADDDATE(project.start_fund_at, project.days), now()) as days_left,
                    project.applause as applause,
                    ' . Project::MIN_APPLAUSE . ' - project.applause as applause_left,
                    FLOOR(project.applause * 100 / ' . Project::MIN_APPLAUSE . ') as applauded_percent
                FROM project 
                LEFT JOIN user 
                    ON user.id = project.user_id
                LEFT JOIN applause 
                    ON project.id = applause.project_id
                LEFT JOIN (SELECT reward.project_id, SUM( reward.amount ) as amount
                            FROM support
                            LEFT JOIN reward ON support.reward_id = reward.id
                            GROUP BY reward.project_id) AS support
                    ON project.id = support.project_id    
                WHERE project.status IN (' . $status_allowed . ') AND
                    user.url = "' . $user_url . ' "
                GROUP BY project.id
                ORDER BY project.created_at DESC';

        try {
            return $this->getEntityManager()
                ->getConnection()
                ->executeQuery($sql)
                ->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function findSupportedProjectsFromUser ($user_url) {
        $status_allowed = implode(',',array(Project::BEING_SUPPORTED,
                                            Project::DECIDING,
                                            Project::EXTENDED,
                                            Project::ENDED_SUCCESSFULLY,
                                            Project::ENDED_UNSUCCESSFULLY));
                
        $sql = 'SELECT project.* 
                FROM user
                JOIN support 
                    ON support.user_id = user.id
                LEFT JOIN reward 
                    ON reward.id = support.reward_id
                LEFT JOIN (SELECT user.username,
                                user.url as user_url,
                                project.id,
                                project.title as title,
                                project.image as image,
                                project.resume as resume,
                                project.status as status,
                                project.url as url,
                                project.amount as required_amount,
                                support.amount as supported_amount,
                                FLOOR(support.amount * 100 / project.amount) as supported_percent,
                                IF (project.status <> ' . Project::EXTENDED . ', 0, ' . Project::EXTENDED_DAYS . ') + DATEDIFF(ADDDATE(project.start_fund_at, project.days), now()) as days_left
                            FROM project
                            LEFT JOIN user 
                                ON user.id = project.user_id
                            LEFT JOIN ( SELECT reward.project_id, SUM( reward.amount ) AS amount
                                        FROM support
                                        LEFT JOIN reward
                                            ON support.reward_id = reward.id
                                        GROUP BY reward.project_id) AS support
                                ON project.id = support.project_id) AS project
                    ON project.id = reward.project_id
                WHERE project.status IN (' . $status_allowed . ') AND
                    user.url = "' . $user_url . '"
                GROUP BY project.id';
        try {
            return $this->getEntityManager()
                ->getConnection()
                ->executeQuery($sql)
                ->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    
    public function findFollowingProjectsFromUser($user_url){
        $status_allowed = implode(',',array(Project::BEING_APPLAUDED,
                                            Project::BEING_SUPPORTED,
                                            Project::DECIDING,
                                            Project::EXTENDED,
                                            Project::ENDED_SUCCESSFULLY,
                                            Project::ENDED_UNSUCCESSFULLY));
                
        return $this->getEntityManager()
                ->getConnection()
                ->executeQuery('SELECT project.* 
                                FROM user
                                JOIN follow
                                    ON follow.user_id = user.id
                                LEFT JOIN (SELECT user.username,
                                                user.url as user_url,
                                                project.id,
                                                project.title as title,
                                                project.image as image,
                                                project.resume as resume,
                                                project.status as status,
                                                project.url as url,
                                                project.amount as required_amount,
                                                support.amount as supported_amount,
                                                FLOOR(support.amount * 100 / project.amount) as supported_percent,
                                                IF (project.status <> ' . Project::EXTENDED . ', 0, ' . Project::EXTENDED_DAYS . ') + DATEDIFF(ADDDATE(project.start_fund_at, project.days), now()) as days_left,
                                                project.applause as applause,
                                                ' . Project::MIN_APPLAUSE . ' - project.applause as applause_left,
                                                FLOOR(project.applause * 100 / ' . Project::MIN_APPLAUSE . ') as applauded_percent
                                            FROM project
                                            LEFT JOIN user 
                                                ON user.id = project.user_id
                                            LEFT JOIN ( SELECT reward.project_id, SUM( reward.amount ) AS amount
                                                        FROM support
                                                        LEFT JOIN reward
                                                            ON support.reward_id = reward.id
                                                        GROUP BY reward.project_id) AS support
                                                ON project.id = support.project_id) AS project
                                    ON project.id = follow.project_id
                                WHERE project.status IN (' . $status_allowed . ') AND
                                    user.url = "' . $user_url . '"
                                GROUP BY project.id')
                ->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function findByQuery($query, $maxResults, $offset = 0){
        $status_allowed = implode(',',array(Project::BEING_APPLAUDED,
                                            Project::BEING_SUPPORTED,
                                            Project::DECIDING,
                                            Project::EXTENDED,
                                            Project::ENDED_SUCCESSFULLY,
                                            Project::ENDED_UNSUCCESSFULLY));
        $result = array();
        
        $query = 'SELECT user.username,
                                    user.url as user_url,
                                    project.title as title,
                                    project.image as image,
                                    project.resume as resume,
                                    project.status as status,
                                    project.url as url,
                                    project.amount as required_amount,
                                    support.amount as supported_amount,
                                    FLOOR(support.amount * 100 / project.amount) as supported_percent,
                                    IF (project.status <> ' . Project::EXTENDED . ', 0, ' . Project::EXTENDED_DAYS . ') + DATEDIFF(ADDDATE(project.start_fund_at, project.days), now()) as days_left,
                                    project.applause as applause,
                                    ' . Project::MIN_APPLAUSE . ' - project.applause as applause_left,
                                    FLOOR(project.applause * 100 / ' . Project::MIN_APPLAUSE . ') as applauded_percent
                    FROM project 
                    LEFT JOIN user 
                        ON user.id = project.user_id
                    LEFT JOIN applause 
                        ON project.id = applause.project_id
                    LEFT JOIN (SELECT reward.project_id, SUM( reward.amount ) as amount
                                FROM support
                                LEFT JOIN reward ON support.reward_id = reward.id
                                GROUP BY reward.project_id) AS support
                        ON project.id = support.project_id    
                    WHERE project.status IN (' . $status_allowed . ')
                        AND user.username LIKE "%' . $query . '%"
                    GROUP BY project.id
                    ';
                    
        $result['num_rows'] = $this->getEntityManager()
                        ->getConnection()
                        ->executeQuery($query)->rowCount();
        $query .= ' LIMIT ' . $offset . ',' . $maxResults;
        
        $result['projects'] = $this->getEntityManager()
                    ->getConnection()
                    ->executeQuery($query)
                    ->fetchAll(\PDO::FETCH_ASSOC);
        
        return $result;
    }
}

?>
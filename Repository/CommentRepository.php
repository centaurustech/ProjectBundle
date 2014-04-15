<?php
// src/Crearock/ProjectBundle/Repository/ProjectRepository.php
namespace Crearock\ProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Crearock\ProjectBundle\Entity\Comment;

class CommentRepository extends EntityRepository{
        
    public function findByProject($project_id)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('c')
                ->from('ProjectBundle:Comment', 'c')
                ->join('c.user', 'u')
                ->where('c.project = :project_id')
                ->setParameter('project_id', $project_id)
                ->orderBy('c.id')
//                ->getDQL();
                ->getQuery();
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return false;
        }
    }
    
    public function getNewId($project_id){
        $query = $this->getEntityManager()->createQueryBuilder()
                ->select('MAX(c.id) last_id')
                ->from('ProjectBundle:Comment', 'c')
                ->andWhere('c.project = :project_id')
                ->setParameter('project_id', $project_id)
                ->getQuery();
        try {
            $comment = $query->getSingleResult();
            if ($comment && is_numeric($comment['last_id'])) {
                return $comment['last_id'] + 1;
            } else {
                return 1;
            }
        } catch (\Doctrine\ORM\NoResultException $e) {
            return false;
        }
    }
}
    
?>

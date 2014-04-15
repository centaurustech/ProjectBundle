<?php
namespace Crearock\ProjectBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Crearock\ProjectBundle\Entity\Project;

class MaintenanceCommand extends ContainerAwareCommand
{
    private $i = 0;
    
    protected function configure(){
        parent::configure();
        $this
            ->setName('crearock:maintenance')
            ->setDescription('Ejecuta el script de mantenimiento de la plataforma')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        $connection = $this->getContainer()->get('doctrine')->getEntityManager()->getConnection();
        
        $query = '  UPDATE project 
                    LEFT JOIN (SELECT reward.project_id, SUM( reward.amount ) as amount
                                                FROM support
                                                LEFT JOIN reward ON support.reward_id = reward.id
                                                GROUP BY reward.project_id) AS support
                        ON project.id = support.project_id
                    SET status = IF (ROUND(support.amount * 100 / project.amount) < 66.6, ' . Project::ENDED_UNSUCCESSFULLY . ', 
                        IF (ROUND(support.amount * 100 / project.amount) >= 100, ' . Project::ENDED_SUCCESSFULLY . ', ' . Project::DECIDING . '))
                    WHERE status = '. Project::BEING_SUPPORTED . ' AND 
                        CURDATE() > DATE(start_fund_at) + INTERVAL days DAY';        
        $affected_rows = $connection->executeUpdate($query);
        $output->writeln('Actualizados los proyectos en primera fase de recaudación. Filas afectadas: ' . $affected_rows);
        
        $query = '  UPDATE project 
                    SET status = ' . Project::ENDED_SUCCESSFULLY . '
                    WHERE status = '. Project::EXTENDED . ' AND 
                        CURDATE() > DATE(extended_at) + INTERVAL ' . Project::EXTENDED_DAYS . ' DAY';
        $affected_rows = $connection->executeUpdate($query);
        $output->writeln('Actualizados los proyectos en segunda fase de recaudación. Filas afectadas: ' . $affected_rows);
        
        $query = '  UPDATE project
                    JOIN (
                        SELECT project_id, COUNT( * ) AS count
                        FROM applause
                        GROUP BY project_id) AS applause
                    SET project.applause = applause.count
                    WHERE project.id = applause.project_id';
        $connection->executeQuery($query);
        $affected_rows = $connection->executeUpdate($query);
        $output->writeln('Actualizado el numero de aplausos de los proyectos. Filas afectadas: ' . $affected_rows);
        
        
        $twig_globals = $this->getContainer()->get('twig')->getGlobals();
        $tmp_upload_dir = $this->getContainer()->get('kernel')->getRootDir() . '/../web/' . $twig_globals['g_project']['temp_dir'] ;        
        $this->deleteOldFiles($tmp_upload_dir);
        $output->writeln('Eliminados archivos temporales con mas de 2 días de antiguedad. Archivos eliminados: ' . $this->i);
        
    }
    
    private function deleteOldFiles($path) {
        $dir = opendir ($path);
        
        while ($file = readdir($dir)){
            if( $file != "." && $file != ".."){
                $file_date = new \DateTime(date('Y/m/d H:i:s',filemtime($path.$file)));
                $diff = date_diff($file_date, new \DateTime(date('Y/m/d H:i:s')));
                if( is_dir($path.$file) ){
                    $this->deleteOldFiles($path.$file.'/');
                } else if ($diff->format('%a') > 2){
                    unlink($path.$file);
                    $this->i++;
                }
            }
        }
    }
}
?>
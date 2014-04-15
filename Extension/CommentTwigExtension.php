<?php
//ProjectBundle/Extension/CommentTwigExtension.php
namespace Crearock\ProjectBundle\Extension;

class CommentTwigExtension extends \Twig_Extension {

    public function getFilters() {
        return array(
            'anchor' => new \Twig_Filter_Method($this, 'anchor'),
        );
    }

    public function anchor($string) {
        return preg_replace('/#(\d+)/',
                            '<a href="#$1" title="Ir al comentario #$1">#$1</a>', $string);
    }

    public function getName()
    {
        return 'comment_twig_extension';
    }

}
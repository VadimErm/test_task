<?php

namespace AppBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class AppProjectsExtension extends \Twig_Extension 
{
	  private $generator;
	  
	  public function __construct(UrlGeneratorInterface $generator)
     {
          $this->generator = $generator;
     }
	  
     public function getName() 
     {
     	    return 'app_projects_extension';
     }
     
     public function getFunctions() 
     {
          return array(new \Twig_SimpleFunction(
                           'projects',
                           array($this, 'ShowProjectsFunction')
                           )
                      );      
     }
     
     public function ShowProjectsFunction($projects) 
     {
	    $tree = $this-> formTree($projects);
	    
	    return $this->printProjects($tree);
     }

     public function printProjects($tree) 
      {
      	 foreach ($tree as $value)
	      {   
	            $issues = $this->generator->generate('issues', ['projectId'=>$value['id']]);
	            $comments = $this->generator->generate('comments_by_poject', ['projectId'=>$value['id']]);       
               echo  "<li>" .$value['name'];
               echo "<ul>
                        <li><a href=\"$issues\">Issues</a></li>
                        <li><a href=\"$comments\">Comments</a></li>
                     </ul>";
               
               if(isset($value['child'])) 
               {
                     echo '<ul>';
                     $this->printProjects($value['child']);   
                     echo '</ul>';      
               } 
               echo '</li>';	
	      }     
      }
      
       public function formTree($projects)
     {
           $tree = array(); 
           $sub = array( null => &$tree ); 

           foreach ($projects as  $project) 
           {    
               $id = $project['id'];
               $name = $project['name'];
               
               if(isset($project['parent'])) {
                     
                     $parent = $project['parent']['id'];
                     
                     $child = &$sub[$parent];
                     $child['child'][$id] = array('name'=>$name, 'id'=>$id, 'parent' =>$parent); 
                     $sub[$id] = &$child['child'][$id];  
                     
               } else {
                     $parent = null;
                     
                     $child = &$sub[$parent];
                     $child[$id] = array('name'=>$name, 'id'=>$id); 
                     $sub[$id] = &$child[$id]; 
               }                  
          } 
       
          return $tree;
 
      }
}
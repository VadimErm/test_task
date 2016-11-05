<?php
namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;



class MainController extends Controller 
{
     
	
      public function projectsAction() 
      {
           if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) 
    	     {
                 throw $this->createAccessDeniedException();
           }  
        
           $user = $this->getUser();            
          
           $client = $this->get('redmine_client')->getClient();
           $projects = $client->api('project')->all();
            
           
          
           return $this->render('default/projects.html.twig',
                                 ['projects'=>$projects['projects'],
                                  'user' => $user,
                                 ]);  
      }
      
      public function issuesAction($projectId) 
      {
           if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) 
    	     {
                 throw $this->createAccessDeniedException();
           }  
        
           $user = $this->getUser();             
           
           $client = $this->get('redmine_client')->getClient();
           $issues = $client->api('issue')->all(array('project_id' => $projectId));
           $project = $client->api('project')->show($projectId);
           if(!isset($issues['issues'])) {
                throw $this->createNotFoundException('Unable to find issues.');         
           } 
           
          
           return $this->render('default/issues.html.twig',
                                ['issues'=>$issues['issues'],
                                 'project' => $project['project'], 
                                 'user' => $user                               
                                ]);       
      }
      
      public function showIssueAction($issueId) 
      {
          
           if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) 
    	     {
                 throw $this->createAccessDeniedException();
           }  
        
           $user = $this->getUser();           
           $client = $this->get('redmine_client')->getClient();
           $issue=$client->api('issue')->show($issueId); 
           $project = $project = $client->api('project')->show($issue['issue']['project']['id']);
           
           return $this->render('default/issue.html.twig',
                                 ['issue'=>$issue['issue'],
                                  'user' => $user,
                                  'project' => $project['project']                                  
                                 ]);      
      }
      
      public function trackTimeAction(Request $request, $issueId) 
      {
           
           if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) 
    	     {
                 throw $this->createAccessDeniedException();
           }  
           
           
        
           $user = $this->getUser();               
           $client = $this->get('redmine_client')->getClient();
           
           $issue=$client->api('issue')->show($issueId); 
           $project = $project = $client->api('project')->show($issue['issue']['project']['id']);
           $data = [];
           $form = $this->createFormBuilder($data)
                 ->add('issue', 'text', array('constraints'=> new Assert\Type('numeric'))) 
                 ->add('hours', 'text', array('constraints'=> new Assert\Type('numeric')))
                 ->add('comment', 'text', array('constraints'=> new Assert\NotBlank()))
                 ->add('create', 'submit')
                 ->getForm(); 
                 
            $form->handleRequest($request);
            
            
            if($form->isSubmitted() && $form->isValid()) {
                   $data = $form->getData();
                  
            
                   
                  
                           $client->api('time_entry')->create(array(
                                'issue_id' => $data['issue'],
                                'hours'       => $data['hours'],
                                'comments'    => $data['comment'],
                                ));
                           return $this->redirect($this->generateUrl('issue', ['issueId' => $issueId]));
                   
            }
            return $this->render('default/track_time.html.twig',
                                 ['issueId'=>$issueId,
                                 'form'=>$form->createView(),
                                  'issue'=>$issue['issue'],
                                  'user' => $user,
                                  'project' => $project['project']                                   
                                 ] ); 
      }
      
      
}
?>
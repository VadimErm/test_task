<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Comments;
use AppBundle\Form\CommentsType;



/**
 * Comments controller.
 *
 */
class CommentsController extends Controller
{
    /**
     * Lists all Comments entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $comments = $em->getRepository('AppBundle:Comments')->findAll();

        return $this->render('comments/index.html.twig', array(
            'comments' => $comments,
        ));
    }
    public function showByProjectAction($projectId) 
    {
       if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) 
    	  {
            throw $this->createAccessDeniedException();
        }  
        
       $user = $this->getUser();     
       $client = $this->get('redmine_client')->getClient();      
       $em = $this->getDoctrine()->getManager();
       
       $comments =  $em->getRepository('AppBundle:Comments') ->findBy(['projectId'=>$projectId]);
       $project = $client->api('project')->show($projectId);
     
       
       return $this->render('comments/index.html.twig', array(
            'comments' => $comments,
            'projectId' => $projectId,
            'project' => $project['project'],
            'user' => $user,
        )); 
    }

    /**
     * Creates a new Comments entity.
     *
     */
    public function newAction(Request $request, $projectId)
    {
    	  if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) 
    	  {
            throw $this->createAccessDeniedException();
        }
        $client = $this->get('redmine_client')->getClient(); 
        $project = $client->api('project')->show($projectId);
        $user = $this->getUser();               
        $comment = new Comments();
        $form = $this->createForm(new CommentsType(), $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        	   $comment->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('comments_show', array('id' => $comment->getId(), 'projectId' => $projectId));
        }

        return $this->render('comments/new.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView(),
            'projectId' => $projectId,
            'user' => $user,
            'project' => $project['project']
        ));
    }

    /**
     * Finds and displays a Comments entity.
     *
     */
    public function showAction(Comments $comment, $projectId)
    {    
    
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) 
    	  {
            throw $this->createAccessDeniedException();
        }
        $client = $this->get('redmine_client')->getClient();
        $project = $client->api('project')->show($projectId);
        $user = $this->getUser();
        $deleteForm = $this->createDeleteForm($comment, $projectId);

        return $this->render('comments/show.html.twig', array(
            'comment' => $comment,
            'delete_form' => $deleteForm->createView(),
            'projectId' => $projectId,
            'user' => $user,
            'project' => $project['project'],
        ));
    }

    /**
     * Displays a form to edit an existing Comments entity.
     *
     */
    public function editAction(Request $request, Comments $comment, $projectId)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) 
    	  {
            throw $this->createAccessDeniedException();
        }
        $client = $this->get('redmine_client')->getClient(); 
        $project = $client->api('project')->show($projectId);
        $user = $this->getUser();  
             
        if($comment->getUser()->getId() == $user->getId()) {       
             $deleteForm = $this->createDeleteForm($comment, $projectId);
             $editForm = $this->createForm(new CommentsType(), $comment);
             $editForm->handleRequest($request);

             if ($editForm->isSubmitted() && $editForm->isValid()) {
                  $em = $this->getDoctrine()->getManager();
                  $em->persist($comment);
                  $em->flush();

                  return $this->redirectToRoute('comments_edit', array('id' => $comment->getId(), 'projectId' => $projectId));
             }

             return $this->render('comments/edit.html.twig', array(
                                  'comment' => $comment,
                                  'edit_form' => $editForm->createView(),
                                  'delete_form' => $deleteForm->createView(),
                                  'projectId' => $projectId,
                                  'user' => $user,
                                  'project' => $project['project']
                                  
                                 ));
         }else 
         {
             return $this->render('comments/noright.html.twig');         
         }
    }

    /**
     * Deletes a Comments entity.
     *
     */
    public function deleteAction(Request $request, Comments $comment, $projectId)
    {
        $form = $this->createDeleteForm($comment, $projectId);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
        }

        return $this->redirectToRoute('comments_by_poject', array('projectId' => $projectId));
    }

    /**
     * Creates a form to delete a Comments entity.
     *
     * @param Comments $comment The Comments entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Comments $comment, $projectId)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comments_delete', array('id' => $comment->getId(), 'projectId' => $projectId)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }



}

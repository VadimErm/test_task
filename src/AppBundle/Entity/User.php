<?php


namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Comments;
use Doctrine\Common\Collections\ArrayCollection;


class User extends BaseUser
{
    
    protected $id;
    
    private $comments;

    public function __construct()
    {
        parent::__construct();
        
        $this->comments = new ArrayCollection();
        
    }
    
   

    /**
     * Add comments
     *
     * @param \AppBundle\Entity\Comments $comments
     * @return User
     */
    public function addComment(\AppBundle\Entity\Comments $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \AppBundle\Entity\Comments $comments
     */
    public function removeComment(\AppBundle\Entity\Comments $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Visit
 *
 * @ORM\Table(name="visit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VisitRepository")
 */
class Visit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="visited_on", type="datetime")
     */
    private $visitedOn;

    /**

     * Many Visits have One User.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="visits")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set visitedOn
     *
     * @param \DateTime $visitedOn
     *
     * @return Visit
     */
    public function setVisitedOn($visitedOn)
    {
        $this->visitedOn = $visitedOn;

        return $this;
    }

    /**
     * Get visitedOn
     *
     * @return \DateTime
     */
    public function getVisitedOn()
    {
        return $this->visitedOn;
    }

    /**
     * Set userId
     *
     * @param User $user
     *
     * @return Visit
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }
}


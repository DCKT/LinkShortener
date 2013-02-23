<?php

namespace Web\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DateClick
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Web\MainBundle\Entity\DateClickRepository")
 */
class DateClick
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbClick", type="integer")
     */
    private $nbClick;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nbClick
     *
     * @param integer $nbClick
     * @return DateClick
     */
    public function setNbClick($nbClick)
    {
        $this->nbClick = $nbClick;
    
        return $this;
    }

    /**
     * Get nbClick
     *
     * @return integer 
     */
    public function getNbClick()
    {
        return $this->nbClick;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return DateClick
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
}
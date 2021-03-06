<?php

namespace Web\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Link
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Web\MainBundle\Entity\LinkRepository")
 */
class Link
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="originalURL", type="string", length=300)
     */
    private $originalURL;

    /**
     * @var string
     *
     * @ORM\Column(name="shortenedURL", type="string", length=255)
     */
    private $shortenedURL;

    /**
     * @var integer
     *
     * @ORM\Column(name="clicks", type="integer")
     */
    private $clicks;

    /**
     * @var integer
     *
     * @ORM\Column(name="clicksDay", type="integer")
     */
    private $clicksDay;    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="date")
     */
    private $dateCreated;

    /**
    * @ORM\Column(name="enabled", type="integer")
    */
    private $enabled;

    /**
    *
    * @ORM\ManyToMany(targetEntity="Web\UserBundle\Entity\User", cascade={"persist"})
    */
    private $user;

    /**
    *
    * @ORM\Column(name="timeLastClicked", type="date");
    */
    private $timeLastClicked;

    /**
    *
    * @ORM\ManyToMany(targetEntity="Web\MainBundle\Entity\Referer", cascade={"persist"})
    */
    private $referer;

    /**
    *
    * @ORM\ManyToMany(targetEntity="Web\MainBundle\Entity\Country", cascade={"persist"})
    */
    private $country;

    /**
    *
    * @ORM\ManyToMany(targetEntity="Web\MainBundle\Entity\DateClick", cascade={"persist"})
    */
    private $dateClick;

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
     * Set Name
     *
     * @param string $name
     * @return Link
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get Name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Original URL
     *
     * @param string $originalURL
     * @return Link
     */
    public function setOriginalURL($originalURL)
    {
        $this->originalURL = $originalURL;
    
        return $this;
    }

    /**
     * Get Original URL
     *
     * @return string 
     */
    public function getOriginalURL()
    {
        return $this->originalURL;
    }

    /**
     * Set Shortened URL
     *
     * @param string $shortenedURL
     * @return Link
     */
    public function setShortenedURL($shortenedURL)
    {
        $this->shortenedURL = $shortenedURL;
    
        return $this;
    }

    /**
     * Get Shortened URL
     *
     * @return string 
     */
    public function getShortenedURL()
    {
        return $this->shortenedURL;
    }

    /**
     * Set Clicks
     *
     * @param integer $clicks
     * @return Link
     */
    public function setClicks($clicks)
    {
        $this->clicks = $clicks;
    
        return $this;
    }

    /**
     * Get Clicks
     *
     * @return integer 
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * Set Date created
     *
     * @param \DateTime $dateCreated
     * @return Link
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    
        return $this;
    }

    /**
     * Get Date created
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set enabled
     *
     * @param integer $enabled
     * @return Link
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    
        return $this;
    }

    /**
     * Get enabled
     *
     * @return integer 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
        $this->referer = new \Doctrine\Common\Collections\ArrayCollection();
        $this->country = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add user
     *
     * @param \Web\UserBundle\Entity\User $user
     * @return Link
     */
    public function addUser(\Web\UserBundle\Entity\User $user)
    {
        $this->user[] = $user;
    
        return $this;
    }

    /**
     * Remove user
     *
     * @param \Web\UserBundle\Entity\User $user
     */
    public function removeUser(\Web\UserBundle\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set timeLastClicked
     *
     * @param \DateTime $timeLastClicked
     * @return Link
     */
    public function setTimeLastClicked($timeLastClicked)
    {
        $this->timeLastClicked = $timeLastClicked;
    
        return $this;
    }

    /**
     * Get timeLastClicked
     *
     * @return \DateTime 
     */
    public function getTimeLastClicked()
    {
        return $this->timeLastClicked;
    }

    /**
     * Add referer
     *
     * @param \Web\MainBundle\Entity\Referer $referer
     * @return Link
     */
    public function addReferer(\Web\MainBundle\Entity\Referer $referer)
    {
        $this->referer[] = $referer;
    
        return $this;
    }

    /**
     * Remove referer
     *
     * @param \Web\MainBundle\Entity\Referer $referer
     */
    public function removeReferer(\Web\MainBundle\Entity\Referer $referer)
    {
        $this->referer->removeElement($referer);
    }

    /**
     * Get referer
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * Add country
     *
     * @param \Web\MainBundle\Entity\Country $country
     * @return Link
     */
    public function addCountry(\Web\MainBundle\Entity\Country $country)
    {
        $this->country[] = $country;
    
        return $this;
    }

    /**
     * Remove country
     *
     * @param \Web\MainBundle\Entity\Country $country
     */
    public function removeCountry(\Web\MainBundle\Entity\Country $country)
    {
        $this->country->removeElement($country);
    }

    /**
     * Get country
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set clicksDay
     *
     * @param integer $clicksDay
     * @return Link
     */
    public function setClicksDay($clicksDay)
    {
        $this->clicksDay = $clicksDay;
    
        return $this;
    }

    /**
     * Get clicksDay
     *
     * @return integer 
     */
    public function getClicksDay()
    {
        return $this->clicksDay;
    }

    /**
     * Add dateClick
     *
     * @param \Web\MainBundle\Entity\DateClick $dateClick
     * @return Link
     */
    public function addDateClick(\Web\MainBundle\Entity\DateClick $dateClick)
    {
        $this->dateClick[] = $dateClick;
    
        return $this;
    }

    /**
     * Remove dateClick
     *
     * @param \Web\MainBundle\Entity\DateClick $dateClick
     */
    public function removeDateClick(\Web\MainBundle\Entity\DateClick $dateClick)
    {
        $this->dateClick->removeElement($dateClick);
    }

    /**
     * Get dateClick
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDateClick()
    {
        return $this->dateClick;
    }
}
<?php

namespace Vilka\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vilka\CoreBundle\Entity\EntityTrait\HasUser;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Restaurant
 *
 * @ORM\Table(name="restaurants")
 * @ORM\Entity(repositoryClass="Vilka\CoreBundle\Repository\RestaurantRepository")
 */
class Restaurant
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
     * @ORM\Column(name="location", type="string", length=255)
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="blocked", type="boolean")
     */
    private $blocked;

    /**
     * @var array
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="restaurants")
     */
    protected $users;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="restaurants")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="restaurants")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="District", inversedBy="restaurants")
     * @ORM\JoinColumn(name="district_id", referencedColumnName="id")
     */
    protected $district;

    /**
     * @ORM\ManyToMany(targetEntity="Subway", inversedBy="restaurants")
     */
    protected $subways;

    /**
     * @ORM\ManyToMany(targetEntity="Kitchen", inversedBy="restaurants")
     */
    protected $kitchens;

    /**
     * @ORM\ManyToMany(targetEntity="Classification", inversedBy="restaurants")
     */
    protected $classifications;

    /**
     * @ORM\ManyToMany(targetEntity="Entertainment", inversedBy="restaurants")
     */
    protected $entertainments;

    /**
     * @ORM\ManyToMany(targetEntity="Event", inversedBy="restaurants")
     */
    protected $events;

    /**
     * @ORM\ManyToMany(targetEntity="MenuFeature", inversedBy="restaurants")
     */
    protected $menu_features;

    /**
     * @ORM\ManyToMany(targetEntity="Feature", inversedBy="restaurants")
     */
    protected $features;

    /**
     * @ORM\ManyToMany(targetEntity="Music", inversedBy="restaurants")
     */
    protected $musics;

    /**
     * @ORM\ManyToMany(targetEntity="Card", inversedBy="restaurants")
     */
    protected $cards;

    /**
     * @var float
     *
     * @ORM\Column(name="avg_bill", type="string", length=255)
     */
    private $avg_bill;

    /**
     * @var string
     *
     * @ORM\Column(name="work_time", type="text")
     */
    private $work_time;

    /**
     * @var string
     *
     * @ORM\Column(name="work_time_kitchen", type="string", length=500)
     */
    private $work_time_kitchen;

    /**
     * @var string
     *
     * @ORM\Column(name="phones", type="string", length=500)
     */
    private $phones;

    /**
     * @var string
     *
     * @ORM\Column(name="capacity", type="string", length=255)
     */
    private $capacity;

    /**
     * @var string
     *
     * @ORM\Column(name="entry_price", type="string", length=255)
     */
    private $entry_price;

    /**
     * @var string
     *
     * @ORM\Column(name="parking", type="string", length=255)
     */
    private $parking;

    /**
     * @var string
     *
     * @ORM\Column(name="reservation", type="string", length=255)
     */
    private $reservation;

    /**
     * @var string
     *
     * @ORM\Column(name="discount", type="string", length=255)
     */
    private $discount;

    /**
     * @var string
     *
     * @ORM\Column(name="halls", type="string", length=255)
     */
    private $halls;

    /**
     * @var string
     *
     * @ORM\Column(name="languages", type="string", length=255)
     */
    private $languages;

    /**
     * @var string
     *
     * @ORM\Column(name="legal_info", type="string", length=255)
     */
    private $legal_info;

    /**
     * @var string
     *
     * @ORM\Column(name="smoking", type="string", length=255)
     */
    private $smoking;

    /**
     * @var string
     *
     * @ORM\Column(name="emails", type="string", length=255)
     */
    private $emails;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->subways = new ArrayCollection();
        $this->kitchens = new ArrayCollection();
        $this->classifications = new ArrayCollection();
        $this->entertainments = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->menu_features = new ArrayCollection();
        $this->features = new ArrayCollection();
        $this->musics = new ArrayCollection();
    }

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
     * Set location
     *
     * @param string $location
     * @return Restaurant
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Restaurant
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Restaurant
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set blocked
     *
     * @param boolean $blocked
     * @return Restaurant
     */
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;

        return $this;
    }

    /**
     * Get blocked
     *
     * @return boolean 
     */
    public function getBlocked()
    {
        return $this->blocked;
    }

    /**
     * Add users
     *
     * @param \Vilka\CoreBundle\Entity\User $users
     * @return Restaurant
     */
    public function addUser(\Vilka\CoreBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Vilka\CoreBundle\Entity\User $users
     */
    public function removeUser(\Vilka\CoreBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Restaurant
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set company
     *
     * @param \Vilka\CoreBundle\Entity\Company $company
     * @return Restaurant
     */
    public function setCompany(\Vilka\CoreBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Vilka\CoreBundle\Entity\Company 
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set city
     *
     * @param \Vilka\CoreBundle\Entity\City $city
     * @return Restaurant
     */
    public function setCity(\Vilka\CoreBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Vilka\CoreBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set district
     *
     * @param \Vilka\CoreBundle\Entity\District $district
     * @return Restaurant
     */
    public function setDistrict(\Vilka\CoreBundle\Entity\District $district = null)
    {
        $this->district = $district;

        return $this;
    }

    /**
     * Get district
     *
     * @return \Vilka\CoreBundle\Entity\District 
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * Add subways
     *
     * @param \Vilka\CoreBundle\Entity\Subway $subways
     * @return Restaurant
     */
    public function addSubway(\Vilka\CoreBundle\Entity\Subway $subways)
    {
        $this->subways[] = $subways;

        return $this;
    }

    /**
     * Remove subways
     *
     * @param \Vilka\CoreBundle\Entity\Subway $subways
     */
    public function removeSubway(\Vilka\CoreBundle\Entity\Subway $subways)
    {
        $this->subways->removeElement($subways);
    }

    /**
     * Get subways
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubways()
    {
        return $this->subways;
    }

    /**
     * Add kitchens
     *
     * @param \Vilka\CoreBundle\Entity\Kitchen $kitchens
     * @return Restaurant
     */
    public function addKitchen(\Vilka\CoreBundle\Entity\Kitchen $kitchens)
    {
        $this->kitchens[] = $kitchens;

        return $this;
    }

    /**
     * Remove kitchens
     *
     * @param \Vilka\CoreBundle\Entity\Kitchen $kitchens
     */
    public function removeKitchen(\Vilka\CoreBundle\Entity\Kitchen $kitchens)
    {
        $this->kitchens->removeElement($kitchens);
    }

    /**
     * Get kitchens
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKitchens()
    {
        return $this->kitchens;
    }

    /**
     * Add classifications
     *
     * @param \Vilka\CoreBundle\Entity\Classification $classifications
     * @return Restaurant
     */
    public function addClassification(\Vilka\CoreBundle\Entity\Classification $classifications)
    {
        $this->classifications[] = $classifications;

        return $this;
    }

    /**
     * Remove classifications
     *
     * @param \Vilka\CoreBundle\Entity\Classification $classifications
     */
    public function removeClassification(\Vilka\CoreBundle\Entity\Classification $classifications)
    {
        $this->classifications->removeElement($classifications);
    }

    /**
     * Get classifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClassifications()
    {
        return $this->classifications;
    }

    /**
     * Add entertainments
     *
     * @param \Vilka\CoreBundle\Entity\Entertainment $entertainments
     * @return Restaurant
     */
    public function addEntertainment(\Vilka\CoreBundle\Entity\Entertainment $entertainments)
    {
        $this->entertainments[] = $entertainments;

        return $this;
    }

    /**
     * Remove entertainments
     *
     * @param \Vilka\CoreBundle\Entity\Entertainment $entertainments
     */
    public function removeEntertainment(\Vilka\CoreBundle\Entity\Entertainment $entertainments)
    {
        $this->entertainments->removeElement($entertainments);
    }

    /**
     * Get entertainments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntertainments()
    {
        return $this->entertainments;
    }

    /**
     * Add events
     *
     * @param \Vilka\CoreBundle\Entity\Event $events
     * @return Restaurant
     */
    public function addEvent(\Vilka\CoreBundle\Entity\Event $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \Vilka\CoreBundle\Entity\Event $events
     */
    public function removeEvent(\Vilka\CoreBundle\Entity\Event $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add menu_features
     *
     * @param \Vilka\CoreBundle\Entity\MenuFeature $menuFeatures
     * @return Restaurant
     */
    public function addMenuFeature(\Vilka\CoreBundle\Entity\MenuFeature $menuFeatures)
    {
        $this->menu_features[] = $menuFeatures;

        return $this;
    }

    /**
     * Remove menu_features
     *
     * @param \Vilka\CoreBundle\Entity\MenuFeature $menuFeatures
     */
    public function removeMenuFeature(\Vilka\CoreBundle\Entity\MenuFeature $menuFeatures)
    {
        $this->menu_features->removeElement($menuFeatures);
    }

    /**
     * Get menu_features
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenuFeatures()
    {
        return $this->menu_features;
    }

    /**
     * Add musics
     *
     * @param \Vilka\CoreBundle\Entity\Music $musics
     * @return Restaurant
     */
    public function addMusic(\Vilka\CoreBundle\Entity\Music $musics)
    {
        $this->musics[] = $musics;

        return $this;
    }

    /**
     * Remove musics
     *
     * @param \Vilka\CoreBundle\Entity\Music $musics
     */
    public function removeMusic(\Vilka\CoreBundle\Entity\Music $musics)
    {
        $this->musics->removeElement($musics);
    }

    /**
     * Get musics
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMusics()
    {
        return $this->musics;
    }

    /**
     * Add cards
     *
     * @param \Vilka\CoreBundle\Entity\Card $cards
     * @return Restaurant
     */
    public function addCard(\Vilka\CoreBundle\Entity\Card $cards)
    {
        $this->cards[] = $cards;

        return $this;
    }

    /**
     * Remove cards
     *
     * @param \Vilka\CoreBundle\Entity\Card $cards
     */
    public function removeCard(\Vilka\CoreBundle\Entity\Card $cards)
    {
        $this->cards->removeElement($cards);
    }

    /**
     * Get cards
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * Set avg_bill
     *
     * @param string $avgBill
     * @return Restaurant
     */
    public function setAvgBill($avgBill)
    {
        $this->avg_bill = $avgBill;

        return $this;
    }

    /**
     * Get avg_bill
     *
     * @return string
     */
    public function getAvgBill()
    {
        return $this->avg_bill;
    }

    /**
     * Set work_time
     *
     * @param string $workTime
     * @return Restaurant
     */
    public function setWorkTime($workTime)
    {
        $this->work_time = $workTime;

        return $this;
    }

    /**
     * Get work_time
     *
     * @return string 
     */
    public function getWorkTime()
    {
        return $this->work_time;
    }

    /**
     * Set work_time_kitchen
     *
     * @param string $workTimeKitchen
     * @return Restaurant
     */
    public function setWorkTimeKitchen($workTimeKitchen)
    {
        $this->work_time_kitchen = $workTimeKitchen;

        return $this;
    }

    /**
     * Get work_time_kitchen
     *
     * @return string 
     */
    public function getWorkTimeKitchen()
    {
        return $this->work_time_kitchen;
    }

    /**
     * Set phones
     *
     * @param string $phones
     * @return Restaurant
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;

        return $this;
    }

    /**
     * Get phones
     *
     * @return string 
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set capacity
     *
     * @param string $capacity
     * @return Restaurant
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return string 
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Set entry_price
     *
     * @param string $entryPrice
     * @return Restaurant
     */
    public function setEntryPrice($entryPrice)
    {
        $this->entry_price = $entryPrice;

        return $this;
    }

    /**
     * Get entry_price
     *
     * @return string 
     */
    public function getEntryPrice()
    {
        return $this->entry_price;
    }

    /**
     * Set parking
     *
     * @param string $parking
     * @return Restaurant
     */
    public function setParking($parking)
    {
        $this->parking = $parking;

        return $this;
    }

    /**
     * Get parking
     *
     * @return string 
     */
    public function getParking()
    {
        return $this->parking;
    }

    /**
     * Set reservation
     *
     * @param string $reservation
     * @return Restaurant
     */
    public function setReservation($reservation)
    {
        $this->reservation = $reservation;

        return $this;
    }

    /**
     * Get reservation
     *
     * @return string 
     */
    public function getReservation()
    {
        return $this->reservation;
    }

    /**
     * Set discount
     *
     * @param string $discount
     * @return Restaurant
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return string 
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set halls
     *
     * @param string $halls
     * @return Restaurant
     */
    public function setHalls($halls)
    {
        $this->halls = $halls;

        return $this;
    }

    /**
     * Get halls
     *
     * @return string 
     */
    public function getHalls()
    {
        return $this->halls;
    }

    /**
     * Set languages
     *
     * @param string $languages
     * @return Restaurant
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Get languages
     *
     * @return string 
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Set legal_info
     *
     * @param string $legalInfo
     * @return Restaurant
     */
    public function setLegalInfo($legalInfo)
    {
        $this->legal_info = $legalInfo;

        return $this;
    }

    /**
     * Get legal_info
     *
     * @return string 
     */
    public function getLegalInfo()
    {
        return $this->legal_info;
    }

    /**
     * Set smoking
     *
     * @param string $smoking
     * @return Restaurant
     */
    public function setSmoking($smoking)
    {
        $this->smoking = $smoking;

        return $this;
    }

    /**
     * Get smoking
     *
     * @return string 
     */
    public function getSmoking()
    {
        return $this->smoking;
    }

    /**
     * Set emails
     *
     * @param string $emails
     * @return Restaurant
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Get emails
     *
     * @return string 
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Add features
     *
     * @param \Vilka\CoreBundle\Entity\Feature $features
     * @return Restaurant
     */
    public function addFeature(\Vilka\CoreBundle\Entity\Feature $features)
    {
        $this->features[] = $features;

        return $this;
    }

    /**
     * Remove features
     *
     * @param \Vilka\CoreBundle\Entity\Feature $features
     */
    public function removeFeature(\Vilka\CoreBundle\Entity\Feature $features)
    {
        $this->features->removeElement($features);
    }

    /**
     * Get features
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeatures()
    {
        return $this->features;
    }
}

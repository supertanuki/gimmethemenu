<?php

namespace Application\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="restaurant", indexes={@ORM\Index(name="gg_place_id_idx", columns={"gg_place_id"})})
 * @ORM\Entity(repositoryClass="Application\MainBundle\Repository\RestaurantRepository")
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
     * @ORM\Column(name="gg_place_id", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "5"
     * )
     */
    private $ggPlaceId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "2"
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="full_address", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $fullAddress;

    /**
     * @var float
     *
     * @ORM\Column(name="location_lat", type="float")
     * @Assert\Type(type="float", message="The value {{ value }} is not a valid latitude.")
     */
    private $locationLat;

    /**
     * @var float
     *
     * @ORM\Column(name="location_lng", type="float")
     * @Assert\Type(type="float", message="The value {{ value }} is not a valid longitude.")
     */
    private $locationLng;

    /**
     * @var string
     *
     * @ORM\Column(name="international_phone_number", type="string", length=255, nullable=true)
     */
    private $internationalPhoneNumber;

    /**
     * @var string
     * @Gedmo\Slug(fields={"name"}, updatable=true, separator="-")
     * @ORM\Column(name="slug", type="string", length=255, unique=false)
     */
    private $slug;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="restaurants")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="Locality", inversedBy="restaurants")
     * @ORM\JoinColumn(name="locality_id", referencedColumnName="id", nullable=false)
     */
    protected $locality;

    /**
     * @ORM\OneToMany(targetEntity="RestaurantMenuFile", mappedBy="restaurant", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $restaurantMenuFiles;

    /**
     * @ORM\OneToMany(targetEntity="Dish", mappedBy="restaurant", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $dishes;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->restaurantMenuFiles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get title
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get array of params for generating restaurant url
     *
     * @return array
     */
    public function getParamsForUrl()
    {
        return array(
            'restaurant_slug' => $this->getSlug(),
            'locality_slug' => $this->getLocality()->getSlug(),
            'country_slug' => $this->getCountry()->getSlug()
        );
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
     * Set ggPlaceId
     *
     * @param string $ggPlaceId
     * @return Restaurant
     */
    public function setGgPlaceId($ggPlaceId)
    {
        $this->ggPlaceId = $ggPlaceId;

        return $this;
    }

    /**
     * Get ggPlaceId
     *
     * @return string 
     */
    public function getGgPlaceId()
    {
        return $this->ggPlaceId;
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
     * Set address
     *
     * @param string $address
     * @return Restaurant
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set locationLat
     *
     * @param float $locationLat
     * @return Restaurant
     */
    public function setLocationLat($locationLat)
    {
        $this->locationLat = $locationLat;

        return $this;
    }

    /**
     * Get locationLat
     *
     * @return float 
     */
    public function getLocationLat()
    {
        return $this->locationLat;
    }

    /**
     * Set locationLng
     *
     * @param float $locationLng
     * @return Restaurant
     */
    public function setLocationLng($locationLng)
    {
        $this->locationLng = $locationLng;

        return $this;
    }

    /**
     * Get locationLng
     *
     * @return float 
     */
    public function getLocationLng()
    {
        return $this->locationLng;
    }

    /**
     * Set internationalPhoneNumber
     *
     * @param string $internationalPhoneNumber
     * @return Restaurant
     */
    public function setInternationalPhoneNumber($internationalPhoneNumber)
    {
        $this->internationalPhoneNumber = $internationalPhoneNumber;

        return $this;
    }

    /**
     * Get internationalPhoneNumber
     *
     * @return string 
     */
    public function getInternationalPhoneNumber()
    {
        return $this->internationalPhoneNumber;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Restaurant
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Restaurant
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Restaurant
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set fullAddress
     *
     * @param string $fullAddress
     * @return Restaurant
     */
    public function setFullAddress($fullAddress)
    {
        $this->fullAddress = $fullAddress;

        return $this;
    }

    /**
     * Get fullAddress
     *
     * @return string 
     */
    public function getFullAddress()
    {
        return $this->fullAddress;
    }

    /**
     * Set country
     *
     * @param \Application\MainBundle\Entity\Country $country
     * @return Restaurant
     */
    public function setCountry(\Application\MainBundle\Entity\Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Application\MainBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set locality
     *
     * @param \Application\MainBundle\Entity\Locality $locality
     * @return Restaurant
     */
    public function setLocality(\Application\MainBundle\Entity\Locality $locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return \Application\MainBundle\Entity\Locality 
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Add restaurantMenuFiles
     *
     * @param \Application\MainBundle\Entity\RestaurantMenuFile $restaurantMenuFiles
     * @return Restaurant
     */
    public function addRestaurantMenuFile(\Application\MainBundle\Entity\RestaurantMenuFile $restaurantMenuFiles)
    {
        $this->restaurantMenuFiles[] = $restaurantMenuFiles;

        return $this;
    }

    /**
     * Remove restaurantMenuFiles
     *
     * @param \Application\MainBundle\Entity\RestaurantMenuFile $restaurantMenuFiles
     */
    public function removeRestaurantMenuFile(\Application\MainBundle\Entity\RestaurantMenuFile $restaurantMenuFiles)
    {
        $this->restaurantMenuFiles->removeElement($restaurantMenuFiles);
    }

    /**
     * Get restaurantMenuFiles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRestaurantMenuFiles()
    {
        return $this->restaurantMenuFiles;
    }

    /**
     * Add dishes
     *
     * @param \Application\MainBundle\Entity\Dish $dishes
     * @return Restaurant
     */
    public function addDish(\Application\MainBundle\Entity\Dish $dishes)
    {
        $this->dishes[] = $dishes;

        return $this;
    }

    /**
     * Remove dishes
     *
     * @param \Application\MainBundle\Entity\Dish $dishes
     */
    public function removeDish(\Application\MainBundle\Entity\Dish $dishes)
    {
        $this->dishes->removeElement($dishes);
    }

    /**
     * Get dishes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDishes()
    {
        return $this->dishes;
    }
}

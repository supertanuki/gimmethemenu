<?php

namespace Application\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

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
     */
    private $ggPlaceId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="locality", type="string", length=255)
     */
    private $locality;

    /**
     * @var string
     * @Gedmo\Slug(fields={"locality"}, updatable=true, separator="-")
     * @ORM\Column(name="locality_slug", type="string", length=255)
     */
    private $localitySlug;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     */
    private $country;

    /**
     * @var string
     * @Gedmo\Slug(fields={"country"}, updatable=true, separator="-")
     * @ORM\Column(name="slug_slug", type="string", length=255)
     */
    private $countrySlug;

    /**
     * @var float
     *
     * @ORM\Column(name="location_lat", type="float")
     */
    private $locationLat;

    /**
     * @var float
     *
     * @ORM\Column(name="location_lng", type="float")
     */
    private $locationLng;

    /**
     * @var string
     *
     * @ORM\Column(name="international_phone_number", type="string", length=255)
     */
    private $internationalPhoneNumber;

    /**
     * @var string
     * @Gedmo\Slug(fields={"name"}, updatable=true, separator="-")
     * @ORM\Column(name="slug", type="string", length=255)
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

//    /**
//     * @ORM\ManyToOne(targetEntity="Category", inversedBy="projects")
//     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
//     */
//    protected $category;

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
     * Set locality
     *
     * @param string $locality
     * @return Restaurant
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return string 
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Set localitySlug
     *
     * @param string $localitySlug
     * @return Restaurant
     */
    public function setLocalitySlug($localitySlug)
    {
        $this->localitySlug = $localitySlug;

        return $this;
    }

    /**
     * Get localitySlug
     *
     * @return string 
     */
    public function getLocalitySlug()
    {
        return $this->localitySlug;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Restaurant
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set countrySlug
     *
     * @param string $countrySlug
     * @return Restaurant
     */
    public function setCountrySlug($countrySlug)
    {
        $this->countrySlug = $countrySlug;

        return $this;
    }

    /**
     * Get countrySlug
     *
     * @return string 
     */
    public function getCountrySlug()
    {
        return $this->countrySlug;
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
}

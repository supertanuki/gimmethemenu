<?php

namespace Application\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="dish")
 * @ORM\Entity(repositoryClass="Application\MainBundle\Repository\DishRepository")
 */
class Dish
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
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     * @Assert\Type(type="float", message="The value {{ value }} is not a valid price.")
     */
    private $price;

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
     * @ORM\ManyToOne(targetEntity="Restaurant", inversedBy="dishes")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=false)
     */
    protected $restaurant;

    /**
     * @ORM\ManyToOne(targetEntity="DishType", inversedBy="dishes")
     * @ORM\JoinColumn(name="dish_type_id", referencedColumnName="id", nullable=false)
     */
    protected $dishType;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="dish", cascade={"persist"})
     */
    protected $reviews;

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
     * Set slug
     *
     * @param string $slug
     * @return Dish
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
     * @return Dish
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
     * @return Dish
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
     * Set restaurant
     *
     * @param \Application\MainBundle\Entity\restaurant $restaurant
     * @return Dish
     */
    public function setRestaurant(\Application\MainBundle\Entity\restaurant $restaurant)
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * Get restaurant
     *
     * @return \Application\MainBundle\Entity\restaurant 
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * Set dishType
     *
     * @param \Application\MainBundle\Entity\DishType $dishType
     * @return Dish
     */
    public function setDishType(\Application\MainBundle\Entity\DishType $dishType)
    {
        $this->dishType = $dishType;

        return $this;
    }

    /**
     * Get dishType
     *
     * @return \Application\MainBundle\Entity\DishType 
     */
    public function getDishType()
    {
        return $this->dishType;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Dish
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reviews = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set user
     *
     * @param \Application\MainBundle\Entity\User $user
     * @return Dish
     */
    public function setUser(\Application\MainBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\MainBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add reviews
     *
     * @param \Application\MainBundle\Entity\Review $reviews
     * @return Dish
     */
    public function addReview(\Application\MainBundle\Entity\Review $reviews)
    {
        $this->reviews[] = $reviews;

        return $this;
    }

    /**
     * Remove reviews
     *
     * @param \Application\MainBundle\Entity\Review $reviews
     */
    public function removeReview(\Application\MainBundle\Entity\Review $reviews)
    {
        $this->reviews->removeElement($reviews);
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Get average rating
     *
     * @return integer
     */
    public function getAverageRating()
    {
        $reviews = $this->getReviews();

        if (!count($reviews)) {
            return null;
        }

        $total = 0;
        foreach ($reviews as $review) {
            $total += $review->getRank();
        }

        return round($total / count($reviews));
    }
}

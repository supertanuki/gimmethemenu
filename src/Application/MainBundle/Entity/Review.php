<?php

namespace Application\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="review")
 * @ORM\Entity(repositoryClass="Application\MainBundle\Repository\ReviewRepository")
 * @Vich\Uploadable
 */
class Review
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
     * @ORM\Column(name="review", type="text", nullable=true)
     * @Assert\Length(
     *      min = "20",
     *      minMessage = "Your review must be at least {{ limit }} characters long"
     * )
     */
    private $review;

    /**
     * @var string
     *
     * @ORM\Column(name="personal_note", type="text", nullable=true)
     */
    private $personalNote;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     * @Assert\Type(type="float", message="The value {{ value }} is not a valid price.")
     */
    private $price;

    /**
     * @var integer
     * @ORM\Column(name="rank", type="integer")
     * @Assert\NotBlank(message="Please select between 1 and 5 hearts for rating")
     * @Assert\Range(
     *      min = 1,
     *      max = 5,
     *      minMessage = "Please select between 1 and 5 hearts for rating",
     *      maxMessage = "Please select between 1 and 5 hearts for rating",
     *      invalidMessage = "Please select between 1 and 5 hearts for rating"
     * )
     */
    private $rank;

    /**
     * @var \DateTime
     * @ORM\Column(name="visited_at", type="date")
     * @ Assert\Date()
     */
    private $when;

    /**
     * @Vich\UploadableField(mapping="dish_photo", fileNameProperty="photoName")
     * @Assert\File(
     *     maxSize="5M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"},
     *     mimeTypesMessage = "Please upload a jpg or a png photo"
     * )
     * @var File
     */
    protected $photoFile;

    /**
     * @ORM\Column(type="string", length=255, name="photo_name", nullable=true)
     * @var string
     */
    protected $photoName;

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
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Dish", inversedBy="reviews", cascade={"persist"})
     * @ORM\JoinColumn(name="dish_id", referencedColumnName="id", nullable=false)
     */
    protected $dish;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="review", cascade={"remove"})
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="Yummy", mappedBy="review", cascade={"remove"})
     */
    protected $yummies;

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
     * Set review
     *
     * @param string $review
     * @return Review
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return string 
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set personalNote
     *
     * @param string $personalNote
     * @return Review
     */
    public function setPersonalNote($personalNote)
    {
        $this->personalNote = $personalNote;

        return $this;
    }

    /**
     * Get personalNote
     *
     * @return string
     */
    public function getPersonalNote()
    {
        return $this->personalNote;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Review
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
     * Set rank
     *
     * @param integer $rank
     * @return Review
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer 
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set when
     *
     * @param \DateTime $when
     * @return Review
     */
    public function setWhen($when)
    {
        $this->when = $when;

        return $this;
    }

    /**
     * Get when
     *
     * @return \DateTime 
     */
    public function getWhen()
    {
        return $this->when;
    }

    /**
     * Set photoName
     *
     * @param string $photoName
     * @return Review
     */
    public function setPhotoName($photoName)
    {
        $this->photoName = $photoName;

        return $this;
    }

    /**
     * Get photoName
     *
     * @return string 
     */
    public function getPhotoName()
    {
        return $this->photoName;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Review
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
     * @return Review
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
     * Set user
     *
     * @param \Application\MainBundle\Entity\User $user
     * @return Review
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
     * Set dish
     *
     * @param \Application\MainBundle\Entity\Dish $dish
     * @return Review
     */
    public function setDish(\Application\MainBundle\Entity\Dish $dish)
    {
        $this->dish = $dish;

        return $this;
    }

    /**
     * Get dish
     *
     * @return \Application\MainBundle\Entity\Dish 
     */
    public function getDish()
    {
        return $this->dish;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param \Symfony\Component\HttpFoundation\File\File $photoFile
     */
    public function setPhotoFile(\Symfony\Component\HttpFoundation\File\File $photoFile)
    {
        $this->photoFile = $photoFile;

        if ($photoFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdatedAt(new \DateTime('now'));
        }
    }

    /**
     * @return File
     */
    public function getPhotoFile()
    {
        return $this->photoFile;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->yummies = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add comments
     *
     * @param \Application\MainBundle\Entity\Comment $comments
     * @return Review
     */
    public function addComment(\Application\MainBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Application\MainBundle\Entity\Comment $comments
     */
    public function removeComment(\Application\MainBundle\Entity\Comment $comments)
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

    /**
     * Add yummies
     *
     * @param \Application\MainBundle\Entity\Yummy $yummies
     * @return Review
     */
    public function addYummy(\Application\MainBundle\Entity\Yummy $yummies)
    {
        $this->yummies[] = $yummies;

        return $this;
    }

    /**
     * Remove yummies
     *
     * @param \Application\MainBundle\Entity\Yummy $yummies
     */
    public function removeYummy(\Application\MainBundle\Entity\Yummy $yummies)
    {
        $this->yummies->removeElement($yummies);
    }

    /**
     * Get yummies
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getYummies()
    {
        return $this->yummies;
    }
}

<?php

namespace Application\MainBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Application\MainBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity("email", message="A user already exists with this email")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = "2",
     *      minMessage = "Your first name must be at least {{ limit }} characters long"
     * )
     */
    protected $firstName;

    /**
     * @var string
     * @Gedmo\Slug(fields={"firstName"}, updatable=true, separator="-")
     * @ORM\Column(name="slug", type="string", length=255, unique=false)
     */
    protected $slug;

    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;

    /** @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) */
    protected $facebook_access_token;

    /** @ORM\Column(name="google_id", type="string", length=255, nullable=true) */
    protected $google_id;

    /** @ORM\Column(name="google_access_token", type="string", length=255, nullable=true) */
    protected $google_access_token;

    /** @ORM\Column(name="is_timeline_public", type="boolean", nullable=true, options={"default" = 1}) */
    protected $isTimelinePublic;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $reviews;

    /**
     * @ORM\OneToMany(targetEntity="UserFollowing", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $followed;

    /**
     * @ORM\OneToMany(targetEntity="UserFollowing", mappedBy="userFollowed", cascade={"persist", "remove"})
     */
    protected $followers;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    public function __toString()
    {
        return $this->getFirstName();
    }

    public function __construct()
    {
        parent::__construct();
        $this->reviews = new ArrayCollection();
        $this->followed = new ArrayCollection();
        $this->followers = new ArrayCollection();
    }

    /**
     * Set Email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        parent::setEmail($email);
        // we use email as the username
        $this->setUsername($email);

        return $this;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return User
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param  string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return Project
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
     * @param  \DateTime $updatedAt
     * @return Project
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
     * Add reviews
     *
     * @param \Application\MainBundle\Entity\Review $reviews
     * @return User
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
     * Set facebook_id
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * Get facebook_id
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set facebook_access_token
     *
     * @param string $facebookAccessToken
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebook_access_token = $facebookAccessToken;

        return $this;
    }

    /**
     * Get facebook_access_token
     *
     * @return string 
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * Set google_id
     *
     * @param string $googleId
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->google_id = $googleId;

        return $this;
    }

    /**
     * Get google_id
     *
     * @return string 
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Set google_access_token
     *
     * @param string $googleAccessToken
     * @return User
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->google_access_token = $googleAccessToken;

        return $this;
    }

    /**
     * Get google_access_token
     *
     * @return string 
     */
    public function getGoogleAccessToken()
    {
        return $this->google_access_token;
    }

    /**
     * Set isTimelinePublic
     *
     * @param boolean $isTimelinePublic
     * @return User
     */
    public function setIsTimelinePublic($isTimelinePublic)
    {
        $this->isTimelinePublic = $isTimelinePublic;

        return $this;
    }

    /**
     * Get isTimelinePublic
     *
     * @return boolean 
     */
    public function getIsTimelinePublic()
    {
        return $this->isTimelinePublic;
    }

    /**
     * Get isTimelinePublicLabel
     *
     * @return boolean
     */
    public function getIsTimelinePublicLabel()
    {
        return $this->isTimelinePublic ? 'public' : 'private';
    }

    /**
     * Add followers
     *
     * @param \Application\MainBundle\Entity\User $followers
     * @return User
     */
    public function addFollower(\Application\MainBundle\Entity\User $followers)
    {
        $this->followers[] = $followers;

        return $this;
    }

    /**
     * Remove followers
     *
     * @param \Application\MainBundle\Entity\User $followers
     */
    public function removeFollower(\Application\MainBundle\Entity\User $followers)
    {
        $this->followers->removeElement($followers);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * Add followed
     *
     * @param \Application\MainBundle\Entity\User $followed
     * @return User
     */
    public function addFollowed(\Application\MainBundle\Entity\User $followed)
    {
        $this->followed[] = $followed;

        return $this;
    }

    /**
     * Remove followed
     *
     * @param \Application\MainBundle\Entity\User $followed
     */
    public function removeFollowed(\Application\MainBundle\Entity\User $followed)
    {
        $this->followed->removeElement($followed);
    }

    /**
     * Get followed
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowed()
    {
        return $this->followed;
    }

    /**
     * Check if user is followed
     *
     * @param User $user
     * @return Boolean
     */
    public function isFollowed(\Application\MainBundle\Entity\User $user)
    {
        foreach($this->getFollowed() as $followed) {
            if ($followed->getUserFollowed() == $user) {
                return true;
            }
        }

        return false;
    }
}

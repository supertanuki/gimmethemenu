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
     * @ORM\OneToMany(targetEntity="Review", mappedBy="user")
     */
    protected $reviews;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="followings")
     **/
    protected $followers;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
     * @ORM\JoinTable(name="user_followings",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="following_user_id", referencedColumnName="id")}
     *      )
     **/
    protected $followings;

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
        $this->followings = new ArrayCollection();
        $this->followers = new ArrayCollection();
    }

    /**
     * Set Email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        parent::setEmail($email);
        // we use email as the username
        $this->setUsername($email);
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

//    /**
//     * Set lastName
//     *
//     * @param  string $lastName
//     * @return User
//     */
//    public function setLastName($lastName)
//    {
//        $this->lastName = $lastName;
//
//        return $this;
//    }
//
//    /**
//     * Get lastName
//     *
//     * @return string
//     */
//    public function getLastName()
//    {
//        return $this->lastName;
//    }
//
//    /**
//     * Set address
//     *
//     * @param  string $address
//     * @return User
//     */
//    public function setAddress($address)
//    {
//        $this->address = $address;
//
//        return $this;
//    }
//
//    /**
//     * Get address
//     *
//     * @return string
//     */
//    public function getAddress()
//    {
//        return $this->address;
//    }
//
//    /**
//     * Set postalCode
//     *
//     * @param  string $postalCode
//     * @return User
//     */
//    public function setPostalCode($postalCode)
//    {
//        $this->postalCode = $postalCode;
//
//        return $this;
//    }
//
//    /**
//     * Get postalCode
//     *
//     * @return string
//     */
//    public function getPostalCode()
//    {
//        return $this->postalCode;
//    }
//
//    /**
//     * Set city
//     *
//     * @param  string $city
//     * @return User
//     */
//    public function setCity($city)
//    {
//        $this->city = $city;
//
//        return $this;
//    }
//
//    /**
//     * Get city
//     *
//     * @return string
//     */
//    public function getCity()
//    {
//        return $this->city;
//    }
//
//    /**
//     * Set country
//     *
//     * @param  string $country
//     * @return User
//     */
//    public function setCountry($country)
//    {
//        $this->country = $country;
//
//        return $this;
//    }
//
//    /**
//     * Get country
//     *
//     * @return string
//     */
//    public function getCountry()
//    {
//        return $this->country;
//    }
//
//    /**
//     * Set phone
//     *
//     * @param  string $phone
//     * @return User
//     */
//    public function setPhone($phone)
//    {
//        $this->phone = $phone;
//
//        return $this;
//    }
//
//    /**
//     * Get phone
//     *
//     * @return string
//     */
//    public function getPhone()
//    {
//        return $this->phone;
//    }

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

//    /**
//     * Add projectResponses
//     *
//     * @param  \Application\MainBundle\Entity\ProjectResponse $projectResponses
//     * @return User
//     */
//    public function addProjectResponse(\Application\MainBundle\Entity\ProjectResponse $projectResponses)
//    {
//        $this->projectResponses[] = $projectResponses;
//
//        return $this;
//    }
//
//    /**
//     * Remove projectResponses
//     *
//     * @param \Application\MainBundle\Entity\ProjectResponse $projectResponses
//     */
//    public function removeProjectResponse(\Application\MainBundle\Entity\ProjectResponse $projectResponses)
//    {
//        $this->projectResponses->removeElement($projectResponses);
//    }
//
//    /**
//     * Get projectResponses
//     *
//     * @return \Doctrine\Common\Collections\Collection
//     */
//    public function getProjectResponses()
//    {
//        return $this->projectResponses;
//    }

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
     * Add followings
     *
     * @param \Application\MainBundle\Entity\User $followings
     * @return User
     */
    public function addFollowing(\Application\MainBundle\Entity\User $followings)
    {
        $this->followings[] = $followings;

        return $this;
    }

    /**
     * Remove followings
     *
     * @param \Application\MainBundle\Entity\User $followings
     */
    public function removeFollowing(\Application\MainBundle\Entity\User $followings)
    {
        $this->followings->removeElement($followings);
    }

    /**
     * Get followings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFollowings()
    {
        return $this->followings;
    }

    /**
     *
     */
    public function isFollowing($user)
    {
        foreach($this->getFollowings() as $following) {
            if ($following == $user) {
                return true;
            }
        }

        return false;
    }
}

<?php

namespace Application\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DishType
 *
 * @ORM\Table(name="user_following")
 * @ORM\Entity()
 * @UniqueEntity(fields={"user", "userFollowed"}, message="This user is already followed")
 */
class UserFollowing
{
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     * @ORM\Id
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="followed_user_id", referencedColumnName="id", nullable=false)
     * @ORM\Id
     */
    protected $userFollowed;

    /** @ORM\Column(name="is_notification_email", type="boolean", nullable=true) */
    protected $isNotificationEmail;

    /** @ORM\Column(name="is_notification_web", type="boolean", nullable=true) */
    protected $isNotificationWeb;

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserFollowing
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
     * @return UserFollowing
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
     * @return UserFollowing
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
     * Set userFollowed
     *
     * @param \Application\MainBundle\Entity\User $userFollowed
     * @return UserFollowing
     */
    public function setUserFollowed(\Application\MainBundle\Entity\User $userFollowed)
    {
        $this->userFollowed = $userFollowed;

        return $this;
    }

    /**
     * Get userFollowed
     *
     * @return \Application\MainBundle\Entity\User 
     */
    public function getUserFollowed()
    {
        return $this->userFollowed;
    }

    /**
     * Set isNotificationEmail
     *
     * @param boolean $isNotificationEmail
     * @return UserFollowing
     */
    public function setIsNotificationEmail($isNotificationEmail)
    {
        $this->isNotificationEmail = $isNotificationEmail;

        return $this;
    }

    /**
     * Get isNotificationEmail
     *
     * @return boolean 
     */
    public function getIsNotificationEmail()
    {
        return $this->isNotificationEmail;
    }

    /**
     * Set isNotificationWeb
     *
     * @param boolean $isNotificationWeb
     * @return UserFollowing
     */
    public function setIsNotificationWeb($isNotificationWeb)
    {
        $this->isNotificationWeb = $isNotificationWeb;

        return $this;
    }

    /**
     * Get isNotificationWeb
     *
     * @return boolean 
     */
    public function getIsNotificationWeb()
    {
        return $this->isNotificationWeb;
    }
}

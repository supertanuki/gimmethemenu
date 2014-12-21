<?php

namespace Application\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RestaurantMenuFile
 *
 * @ORM\Table(name="restaurant_menu_file")
 * @ORM\Entity
 * @Vich\Uploadable
 */
class RestaurantMenuFile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Restaurant", inversedBy="restaurantMenuFiles")
     * @ORM\JoinColumn(name="restaurant_id", referencedColumnName="id", nullable=false)
     */
    protected $restaurant;

    /**
     * @Vich\UploadableField(mapping="restaurant_menu_file", fileNameProperty="fileName")
     * @Assert\File(
     *     maxSize="5M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"},
     *     mimeTypesMessage = "Please upload a jpg or a png photo"
     * )
     * @var File $fileFile
     */
    protected $fileFile;

    /**
     * @ORM\Column(type="string", length=255, name="file_name", nullable=false)
     * @var string $fileName
     */
    protected $fileName;

    /**
     * @ORM\Column(type="string", length=255, name="largest_file", nullable=true)
     * @var string $largestFile
     */
    protected $largestFile;

    /**
     * @ORM\Column(type="integer", name="largest_width", nullable=true)
     * @var integer $largestWidth
     * this is the width of the "largest" image resized by liip imagine
     */
    protected $largestWidth;

    /**
     * @ORM\Column(type="integer", name="largest_height", nullable=true)
     * @var integer $largestHeight
     * this is the height of the "largest" image resized by liip imagine
     */
    protected $largestHeight;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

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


    public function __toString()
    {
        return $this->getId();
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $fileFile
     */
    public function setFileFile(File $fileFile)
    {
        $this->fileFile = $fileFile;

        if ($fileFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->setUpdatedAt(new \DateTime('now'));
        }
    }

    /**
     * @return File
     */
    public function getFileFile()
    {
        return $this->fileFile;
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
     * Set fileName
     *
     * @param string $fileName
     * @return RestaurantMenuFile
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return RestaurantMenuFile
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
     * @return RestaurantMenuFile
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
     * @return RestaurantMenuFile
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
     * Set user
     *
     * @param \Application\MainBundle\Entity\User $user
     * @return RestaurantMenuFile
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
     * Set largestWidth
     *
     * @param integer $largestWidth
     * @return RestaurantMenuFile
     */
    public function setLargestWidth($largestWidth)
    {
        $this->largestWidth = $largestWidth;

        return $this;
    }

    /**
     * Get largestWidth
     *
     * @return integer 
     */
    public function getLargestWidth()
    {
        return $this->largestWidth;
    }

    /**
     * Set largestHeight
     *
     * @param integer $largestHeight
     * @return RestaurantMenuFile
     */
    public function setLargestHeight($largestHeight)
    {
        $this->largestHeight = $largestHeight;

        return $this;
    }

    /**
     * Get largestHeight
     *
     * @return integer 
     */
    public function getLargestHeight()
    {
        return $this->largestHeight;
    }

    /**
     * Set largestFile
     *
     * @param string $largestFile
     * @return RestaurantMenuFile
     */
    public function setLargestFile($largestFile)
    {
        $this->largestFile = $largestFile;

        return $this;
    }

    /**
     * Get largestFile
     *
     * @return string 
     */
    public function getLargestFile()
    {
        return $this->largestFile;
    }
}

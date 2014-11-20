<?php

namespace Application\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="Application\MainBundle\Repository\ProjectRepository")
 */
class Project
{
    const   STATUS_NOT_PUBLISHED = 10,
            STATUS_PUBLISHED = 20,
            STATUS_CLOSED_FOR_DECISION = 30,
            STATUS_CLOSED = 40;

    public static $STATUS_LABEL = array(
        self::STATUS_NOT_PUBLISHED => 'Non publié',
        self::STATUS_PUBLISHED => 'Publié',
        self::STATUS_CLOSED_FOR_DECISION => 'Attente de décision',
        self::STATUS_CLOSED => 'Terminé'
    );

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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @Gedmo\Slug(fields={"title"}, updatable=true, separator="-")
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text")
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="description_raw", type="text")
     */
    private $rawDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="description_formatter", type="string", length=255)
     */
    private $descriptionFormatter;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_at", type="datetime")
     */
    private $endAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="budget", type="integer")
     */
    private $budget;

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
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="projects")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="ProjectFile", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $projectFiles;

    /**
     * @ORM\OneToMany(targetEntity="ProjectResponse", mappedBy="project", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $projectResponses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->projectFiles = new ArrayCollection();
    }

    /**
     * Get title
     *
     * @return Project
     */
    public function __toString()
    {
        return $this->getTitle();
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
     * Set title
     *
     * @param  string  $title
     * @return Project
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param  string  $slug
     * @return Project
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
     * Set summary
     *
     * @param  string  $summary
     * @return Project
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set description
     *
     * @param  string  $description
     * @return Project
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
     * Set rawDescription
     *
     * @param  string  $rawDescription
     * @return Project
     */
    public function setRawDescription($rawDescription)
    {
        $this->rawDescription = $rawDescription;

        return $this;
    }

    /**
     * Get descriptionFormatter
     *
     * @return string
     */
    public function getDescriptionFormatter()
    {
        return $this->descriptionFormatter;
    }

    /**
     * Set des
     *
     * @param  string  $descriptionFormatter
     * @return Project
     */
    public function setDescriptionFormatter($descriptionFormatter)
    {
        $this->descriptionFormatter = $descriptionFormatter;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getRawDescription()
    {
        return $this->rawDescription;
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
     * Set endAt
     *
     * @param  \DateTime $endAt
     * @return Project
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set status
     *
     * @param  integer $status
     * @return Project
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get raw status
     *
     * @return integer
     */
    public function getRawStatus()
    {
        return $this->status;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        // if the project is ended and it status is published, return status closed
        if ($this->status == self::STATUS_PUBLISHED && $this->getEndAt() <= new \DateTime('now')) {
            return self::STATUS_CLOSED;
        }

        return $this->status;
    }

    /**
     * Set budget
     *
     * @param  integer $budget
     * @return Project
     */
    public function setBudget($budget)
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * Get budget
     *
     * @return integer
     */
    public function getBudget()
    {
        return $this->budget;
    }

    /**
     * Get statusLabel
     *
     * @return integer
     */
    public function getStatusLabel()
    {
        if (array_key_exists($this->getStatus(), self::$STATUS_LABEL)) {
            return self::$STATUS_LABEL[$this->getStatus()];
        }

        return null;
    }

    /**
     * Set category
     *
     * @param  \Application\MainBundle\Entity\Category $category
     * @return Project
     */
    public function setCategory(\Application\MainBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Application\MainBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add projectFile
     *
     * @param  \Application\MainBundle\Entity\ProjectFile $projectFile
     * @return Project
     */
    public function addProjectFile(\Application\MainBundle\Entity\ProjectFile $projectFile)
    {
        $projectFile->setProject($this);
        $this->projectFiles->add($projectFile);

        return $this;
    }

    /**
     * Set projectFiles
     *
     * @param $files
     * @return Project
     */
    public function setProjectFiles($files)
    {
        if (count($files) > 0) {
            foreach ($files as $file) {
                $this->addProjectFile($file);
            }
        }

        return $this;
    }

    /**
     * Remove projectFiles
     *
     * @param \Application\MainBundle\Entity\ProjectFile $projectFile
     */
    public function removeProjectFile(\Application\MainBundle\Entity\ProjectFile $projectFile)
    {
        $this->projectFiles->removeElement($projectFile);
    }

    /**
     * Get projectFiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjectFiles()
    {
        return $this->projectFiles;
    }

    /**
     * Add projectResponses
     *
     * @param  \Application\MainBundle\Entity\ProjectResponse $projectResponses
     * @return Project
     */
    public function addProjectResponse(\Application\MainBundle\Entity\ProjectResponse $projectResponses)
    {
        $this->projectResponses[] = $projectResponses;

        return $this;
    }

    /**
     * Remove projectResponses
     *
     * @param \Application\MainBundle\Entity\ProjectResponse $projectResponses
     */
    public function removeProjectResponse(\Application\MainBundle\Entity\ProjectResponse $projectResponses)
    {
        $this->projectResponses->removeElement($projectResponses);
    }

    /**
     * Get projectResponses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjectResponses()
    {
        return $this->projectResponses;
    }

    /**
     * Get projectResponses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjectResponsesCount()
    {
        $projectResponses = $this->getProjectResponses();

        return count($projectResponses);
    }
}

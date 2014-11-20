<?php

namespace TE\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ProjectResponse
 *
 * @ORM\Table(name="project_response")
 * @ORM\Entity(repositoryClass="TE\Bundle\MainBundle\Repository\ProjectResponseRepository")
 */
class ProjectResponse
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
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_selected", type="boolean", nullable=true)
     */
    private $isSelected;

    /**
     * @ORM\OneToMany(targetEntity="ProjectResponseFile", mappedBy="projectResponse", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $projectResponseFiles;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectResponses")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="projectResponses")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

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
     * Constructor
     */
    public function __construct()
    {
        $this->projectResponseFiles = new ArrayCollection();
    }

    /**
     * Get title
     *
     * @return Project
     */
    public function __toString()
    {
        return $this->getShortDescription();
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
     * Set description
     *
     * @param  string          $description
     * @return ProjectResponse
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
     * Set isSelected
     *
     * @param  boolean $isSelected
     * @return ProjectResponse
     */
    public function setIsSelected($isSelected)
    {
        $this->isSelected = $isSelected;

        return $this;
    }

    /**
     * Get isSelected
     *
     * @return boolean
     */
    public function getIsSelected()
    {
        return $this->isSelected;
    }

    /**
     * Get short description
     *
     * @return string
     */
    public function getShortDescription()
    {
        $nbWords = 5;
        $more = '[...]';

        $description = explode(' ', $this->getDescription(), $nbWords+1);

        if (count($description) < $nbWords) {
            $more = '';
        }

        unset($description[$nbWords]);
        return implode(' ', $description) . ' ' . $more;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime       $createdAt
     * @return ProjectResponse
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
     * @param  \DateTime       $updatedAt
     * @return ProjectResponse
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
     * Set project
     *
     * @param  \TE\Bundle\MainBundle\Entity\Project $project
     * @return ProjectResponse
     */
    public function setProject(\TE\Bundle\MainBundle\Entity\Project $project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \TE\Bundle\MainBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set user
     *
     * @param  \TE\Bundle\MainBundle\Entity\User $user
     * @return ProjectResponse
     */
    public function setUser(\TE\Bundle\MainBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \TE\Bundle\MainBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set projectResponseFiles
     */
    public function setProjectResponseFiles(ArrayCollection $projectResponseFiles)
    {
        foreach ($projectResponseFiles as $projectResponseFile) {
            $this->addProjectResponseFile($projectResponseFile);
        }

        return $this;
    }

    /**
     * Add projectResponseFile
     *
     * @param  \TE\Bundle\MainBundle\Entity\ProjectResponseFile $projectResponseFile
     * @return ProjectResponse
     */
    public function addProjectResponseFile(\TE\Bundle\MainBundle\Entity\ProjectResponseFile $projectResponseFile)
    {
        $projectResponseFile->setProjectResponse($this);
        $this->projectResponseFiles->add($projectResponseFile);

        return $this;
    }

    /**
     * Remove projectResponseFile
     *
     * @param \TE\Bundle\MainBundle\Entity\ProjectResponseFile $projectResponseFile
     */
    public function removeProjectResponseFile(\TE\Bundle\MainBundle\Entity\ProjectResponseFile $projectResponseFile)
    {
        $this->projectResponseFiles->removeElement($projectResponseFile);
    }

    /**
     * Get projectResponseFiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjectResponseFiles()
    {
        return $this->projectResponseFiles;
    }
}

<?php

namespace TE\Bundle\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectFile
 *
 * @ORM\Table(name="project_file")
 * @ORM\Entity
 */
class ProjectFile
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
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade="all")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=false)
     */
    private $media;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectFiles")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    protected $project;

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
     * @param  string      $name
     * @return ProjectFile
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
     * Set project
     *
     * @param  \TE\Bundle\MainBundle\Entity\Project $project
     * @return ProjectFile
     */
    public function setProject(\TE\Bundle\MainBundle\Entity\Project $project = null)
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
     * Set media
     *
     * @param  \Application\Sonata\MediaBundle\Entity\Media $media
     * @return ProjectFile
     */
    public function setMedia(\Application\Sonata\MediaBundle\Entity\Media $media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }
}

<?php

namespace Application\MainBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Application\MainBundle\Entity\Project;

class ProjectRepository extends EntityRepository
{
    private function commonQuery()
    {
        return $this->createQueryBuilder('p');
    }

    public function findPublishedProjectBySlug($slug)
    {
        $qb = $this->commonQuery();
        $qb = $this->filterByPublishedProjects($qb);
        $qb->andWhere('p.slug = :slug')
            ->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findPublishedProjects($category = null)
    {
        $qb = $this->commonQuery();
        $qb = $this->filterByPublishedProjects($qb);

        if ($category != null) {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        $qb = $this->orderByCreatedAt($qb, 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findMyProjects($user)
    {
        $qb = $this->commonQuery();
        $qb = $qb->distinct();
        $qb = $this->filterByPublishedProjects($qb);

        $qb->innerJoin('p.projectResponses', 'responses')
            ->andWhere('responses.user = :user')
            ->setParameter('user', $user);

        $qb = $this->orderByCreatedAt($qb, 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function getActivedAndEndedProjects()
    {
        $qb = $this->commonQuery();

        // only projects with status "publié"
        $qb = $this->filterByActivedProjects($qb);

        // only ended projects
        $qb = $qb->andWhere('p.endAt <= :now')
            ->setParameter('now', new \DateTime('now'));

        return $qb->getQuery()->getResult();
    }

    private function filterByPublishedProjects(QueryBuilder $qb)
    {
        // all projects safe with status "non publié"
        $qb = $qb->andWhere('p.status != :status')
            ->setParameter('status', Project::STATUS_NOT_PUBLISHED);

        return $qb;
    }

    private function filterByActivedProjects(QueryBuilder $qb)
    {
        // only projects with status "publié"
        $qb = $qb->andWhere('p.status = :status')
            ->setParameter('status', Project::STATUS_PUBLISHED);

        return $qb;
    }

    private function orderByCreatedAt(QueryBuilder $qb, $order = 'ASC')
    {
        return $qb->addOrderBy('p.createdAt', $order);
    }
}

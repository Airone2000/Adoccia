<?php

namespace App\Repository;

use App\Entity\Form;
use App\Entity\Value;
use App\Entity\Widget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Value|null find($id, $lockMode = null, $lockVersion = null)
 * @method Value|null findOneBy(array $criteria, array $orderBy = null)
 * @method Value[]    findAll()
 * @method Value[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Value::class);
    }

    public function reAffectValueToWidget(Form $form): void
    {
        $widgetRepository = $this->getEntityManager()->getRepository(Widget::class);
        $mapImmutableIdToId = $widgetRepository->getArrayOfWidgetsIdForForm($form);

        if (!empty($mapImmutableIdToId)) {
            $sets = ''; $ids = '';
            foreach ($mapImmutableIdToId as $item) {
                $sets .= " WHEN v.widget_immutable_id = \"{$item['immutableId']}\" THEN {$item['id']}";
                $ids .= ',"' . $item['immutableId'] . '"';
            }

            $ids = ltrim($ids, ','); $sets = trim($sets);
            $sql = 'UPDATE `value` v SET v.widget_id = (CASE ' . $sets . ' ELSE v.widget_id END) WHERE v.widget_immutable_id IN (' . $ids . ')';
            $this->getEntityManager()->getConnection()->exec($sql);
        }
    }
}
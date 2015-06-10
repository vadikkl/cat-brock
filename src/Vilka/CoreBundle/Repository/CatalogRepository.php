<?php

namespace Vilka\CoreBundle\Repository;
use Vilka\CoreBundle\Manager\HistoryManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Vilka\CoreBundle\Entity\History;


/**
 * CatalogRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CatalogRepository extends \Doctrine\ORM\EntityRepository implements ContainerAwareInterface
{
    protected $enter = "\r\n";
    protected $separator = ",";
    protected $quote = '"';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getCountByPlatform($platform)
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c)');
        $query->where('c.platform = :platform')
            ->setParameter('platform', $platform);
        $this->_setLastDate($query, $platform);
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getOffersByPlatform($platform)
    {
        $query = $this->createQueryBuilder('c');
        $query->select('c.offer');
        $query->where('c.platform = :platform')
            ->setParameter('platform', $platform)
            ->groupBy('c.offer')
            ->orderBy('c.offer')
        ;
        $this->_setLastDate($query, $platform);
        return $query->getQuery()->getResult();
    }

    public function getCategoriesByPlatform($platform)
    {
        $query = $this->createQueryBuilder('c');
        $query->select('c.category');
        $query->where('c.platform = :platform')
            ->setParameter('platform', $platform)
            ->groupBy('c.category')
            ->orderBy('c.category')
        ;
        $this->_setLastDate($query, $platform);
        return $query->getQuery()->getResult();
    }

    protected function _setLastDate(&$query, $platform) {
        $queryDate = $this->createQueryBuilder('c')
            ->select('c.date')->orderBy('c.date', 'DESC');
        $queryDate->where('c.platform = :platform')
            ->setParameter('platform', $platform);
        $queryDate = $queryDate->setMaxResults(1);
        $result = $queryDate->getQuery()->getOneOrNullResult();
        if ($result) {
            $query->andWhere('c.date = :query')
                ->setParameter('query', $result['date']->format('Y-m-d H:i:s'));
        }
    }

    public function getProducts($platform, $offers, $categories, $user)
    {
        $query = $this->createQueryBuilder('c');
        $query->select('c');
        $query->where('c.platform = :platform')
            ->setParameter('platform', $platform);
        if ($offers) {
            $query->andWhere('c.offer IN (:offers)')
                ->setParameter('offers', $offers);
        }
        if ($categories) {
            $query->andWhere('c.category IN (:categories)')
                ->setParameter('categories', $categories);
        }
        $this->_setLastDate($query, $platform);
        return $this->_createCSV($query->getQuery()->getArrayResult(), $platform, $offers, $categories, $user);
    }

    private function _createCSV($output, $source, $offers, $categories, $user)
    {
        $count = count($output);
        if ($count) {
            $csvData = '';
            foreach ($output as $key => $_line) {
                $csvData .= $this->quote . $_line["source"] . $this->quote . $this->separator
                    . $this->quote . $_line["category"] . $this->quote . $this->separator
                    . $this->quote . $_line["offer"] . $this->quote . $this->separator
                    . $this->quote . $_line["article"] . $this->quote . $this->separator
                    . $this->quote . $_line["name"] . $this->quote . $this->separator
                    . $this->quote . $_line["price"] . $this->quote . $this->separator
                    . $this->quote . $_line["beznal"] . $this->quote;
                $csvData .= $this->enter;
            }

            $directoryUser = "/files/" . md5($user->getId().$user->getSalt());
            $directory = getcwd() . $directoryUser;
            if (!is_dir($directory)) {
                mkdir($directory);
            }
            $file = "/" . $source . '_' . date('Y-m-d_h:i:s') . ".csv";
            $fileName = $directory . $file;
            $handle = fopen($fileName, "a+");
            fwrite($handle, $csvData);
            fclose($handle);
            $historyManager = $this->getHistoryManager();
            $params =  '<b>Платформа:</b> ' . $source . "<br>";
            if ($categories) {
                $params .=  '<b>Категории:</b> ' . implode(', ', $categories) . "<br>";
            }
            if ($offers) {
                $params .=  '<b>Магазины:</b> ' . implode(', ', $offers) . "<br>";
            }
            $history = new History();
            $history->setUser($user);
            $history->setDate(new \DateTime('now'));
            $history->setFile($directoryUser . $file);
            $history->setParams($params);
            $history->setCols($count);
            $historyManager->save($history);
            if (file_exists($fileName)) {
                header('Content-Description: File Transfer');
                header("Content-type: application/octet-stream");
                header('Content-Disposition: attachment; filename=' . basename($fileName));
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($fileName));
                readfile($fileName);
                exit;
            }
        } else {
            return false;
        }
    }

    /**
     * @return HistoryManager
     */
    public function getHistoryManager()
    {
        return $this->container->get('vilka.manager.history');
    }
}

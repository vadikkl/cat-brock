<?php

namespace Vilka\CoreBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Serializer\Exception\Exception;
use Vilka\CoreBundle\Command\CommandOutput;
use Vilka\CoreBundle\Entity\Log;

/**
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 *
 * @codeCoverageIgnore
 */
class ParserCommand extends CommandOutput
{

    protected $enter = "\r\n";

    protected $separator = ",";

    protected $quote = '"';

    protected $parser = null;

    /**
     * @var $connection Connection
     * */
    protected $connection = null;

    protected $catalogTable = null;

    protected $logTable = 'log';

    protected $logs = null;

    protected $entityManager = null;

    protected $truncate = true;

    protected $total = 0;

    protected $sleep = 0;

    protected $defaultTable = 'catalog';

    protected $links = array(
        'onliner.by',
        '1k.by',
        'shop.by'
    );

    protected $resources = array(
        'onliner.by' => array(
            'http://catalog.onliner.by/'
        ),
        '1k.by' => array(
            'http://1k.by/'
        ),
        'shop.by' => array(
            'http://shop.by/katalog/'
        )
    );

    protected $catalogTables = array(
        'onliner.by' => 'catalog_onliner',
        '1k.by' => 'catalog_1k',
        'shop.by' => 'catalog_shop'
    );

    protected $sleeps = array(
        'onliner.by' => 1,
        '1k.by' => 2,
        'shop.by' => 1
    );


    protected function configure()
    {
        $this->setName('parser:catalog:start', 'Start sites parser')
            ->addOption('site', null, InputOption::VALUE_REQUIRED, 'Site url', 'onliner.by')
            ->addOption('move', null, InputOption::VALUE_REQUIRED, 'Move data from temp table', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->load($output);

        $site = $input->getOption('site');

        $move = $input->getOption('move');

        $this->sleep = $this->sleeps[$site];

        $doctrine = $this->getDoctrine();
        $this->entityManager = $doctrine->getManager();

        $this->connection = $this->entityManager->getConnection();

        $this->writeSuccessLine('Parsing ' . $site . ' start...');
        $this->_getParser($site, $move);
        $this->writeSuccessLine('Parsing ' . $site . ' finish.');
    }

    /**
     * @return Registry
     */
    private function getDoctrine()
    {
        return $this->get('doctrine');
    }

    public function _getParser($source, $move)
    {
        $this->catalogTable = $this->catalogTables[$source];
        $logsRepository = $this->getRepository('Log');
        $logsRepository->findByPlatform($source);
        $logRes = $logsRepository->findByPlatform($source);
        if ($logRes) {
            $logRes = $logRes[0];
            $this->logs = $logRes ? unserialize($logRes['params']) : null;
            $this->truncate = false;
        }
        //var_dump($this->logs);
        if (!$move) {
            switch ($source) {
                case 'onliner.by':
                    $this->_getOnlinerBy($source);
                    break;
                case '1k.by':
                    $this->_getOneKBy($source);
                    break;
                default:
                    break;
            }
        }
        $this->_copyTable($this->catalogTable, $this->defaultTable, $source);
    }

    protected function _copyTable($fromTable, $toTable, $source) {
        try {
            $this->connection->beginTransaction();
            $this->writeSuccessLine('Start transaction...');
            $this->connection->query("DELETE FROM " . $toTable . " WHERE platform='" . $source . "'");
            $data = "source,category,offer,platform,article,name,price,beznal";
            $sql = "INSERT INTO " . $toTable . " (" . $data . ") SELECT " . $data . " FROM " . $fromTable;
            $this->connection->query($sql);
            $this->connection->query("DELETE FROM " . $this->logTable . " WHERE platform='" . $source . "'");
            $this->connection->commit();
            $this->writeSuccessLine('Finished.');
        } catch (Exception $e) {
            $this->writeErrorLine('Something went wrong!');
            $this->writeSuccessLine('Starting rollbacking transaction...');
            $this->connection->rollback();
            $this->writeSuccessLine('Finished rollbacking transaction.');
            $this->entityManager->close();
            throw $e;
        }
    }

    protected function _replaceQuote($text)
    {
        $text = str_replace('&nbsp;', ' ', $text);
        return trim(str_replace('"', '&quot;', $text));
    }

    protected function _replacePrice($text)
    {
        $text = str_replace('&nbsp;', '', $text);
        $text = str_replace(' ', '', $text);
        return (int)trim(str_replace('"', '&quot;', $text));
    }

    protected function _file_get_html($link)
    {
        sleep($this->sleep);
        return file_get_html($link);
    }

    private function _getOneKBy($source)
    {
        $links = $this->resources[$source];
        foreach ($links as $link) {
            $_html = $this->_file_get_html($link);
            if ($_html) {
                foreach ($_html->find('.b-catalog-nav .catalog-nav_title a') as $_domain) {
                    $domainHref = $_domain->href;
                    $_html = $this->_file_get_html($domainHref);
                    if ($_html) {
                        foreach ($_html->find('.b-categories .categories_item a') as $category) {
                            $this->oneKByIteration($source, $domainHref, $category, $category->href);
                        }
                    }
                }
            }
        }
    }

    protected function oneKByIteration($platform, $href, $category, $categoryHref, $test = false)
    {
        $categoryName = $category->plaintext;
        $_html2 = $this->_file_get_html($categoryHref);
        if ($_html2) {
            foreach ($_html2->find('.b-price .c-green') as $_product) {
                $this->_oneKByData($platform,$href, $_product, $categoryName);
                //break;
            }
            foreach ($_html2->find('.b-price .c-orange') as $_product) {
                $this->_oneKByData($platform, $href, $_product, $categoryName, 1);
                //break;
            }
            $currPage = $_html2->find('.b-paging .current', 0)->next_sibling();
            if ($currPage->href) {
                $categoryHref = rtrim($href, '/') . $currPage->href;
                $this->oneKByIteration($platform, $href, $category, $categoryHref, true);
            }
        }
    }

    protected function _oneKByData($platform, $href, $_product, $categoryName, $beznal = 0)
    {
        $productLink = rtrim($href, '/') . $_product->href;
        $productHrefArr = explode('/', $productLink);
        $html = $this->_file_get_html($productLink);
        if ($html) {
            $output = array();
            if ($html->find('#product-data')) {
                $productName = $html->find('#status-line .crumbs_current', 0)->plaintext;
                $productArt = explode('-', $productHrefArr[count($productHrefArr) - 3]);
                foreach ($html->find('#product-data .b-shop') as $key => $_price) {
                    $price = $_price->find('.shop_price .sell', 0);
                    if (!$price) {
                        $_price->find('.shop_price .cur', 0);
                    }
                    $bind = array(
                        'source' => $this->_replaceQuote($productLink),
                        'category' => $this->_replaceQuote($categoryName),
                        'article' => $this->_replaceQuote($productArt[0]),
                        'name' => $this->_replaceQuote($productName),
                        'price' => $this->_replacePrice($_price->plaintext),
                        'beznal' => $beznal,
                        'platform' => $platform,
                        'offer' => $this->_replaceQuote($_price->find('.shop_reviews img', 0)->alt)
                    );
                    $output[] = $this->_insertMysql($this->catalogTable, $bind);
                }
            } else {
                $productName = $html->find('#status-line .active', 0)->plaintext;
                $productArt = explode('-', $productHrefArr[count($productHrefArr) - 2]);
                foreach ($html->find('.price .retail') as $_price) {
                    $bind = array(
                        'source' => $this->_replaceQuote($productLink),
                        'category' => $this->_replaceQuote($categoryName),
                        'article' => $this->_replaceQuote($productArt[0]),
                        'name' => $this->_replaceQuote($productName),
                        'price' => $this->_replacePrice($_price->plaintext),
                        'offer' => $this->_replaceQuote($html->find('.shoplogo', 0)->alt),
                        'beznal' => 0,
                        'platform' => $platform,
                    );
                    $output[] = $this->_insertMysql($this->catalogTable, $bind);
                }
                foreach ($html->find('.price .cashless') as $_price) {
                    $bind = array(
                        'source' => $this->_replaceQuote($productLink),
                        'category' => $this->_replaceQuote($categoryName),
                        'article' => $this->_replaceQuote($productArt[0]),
                        'name' => $this->_replaceQuote($productName),
                        'price' => $this->_replacePrice($_price->plaintext),
                        'offer' => $this->_replaceQuote($html->find('.shoplogo', 0)->alt),
                        'beznal' => 1,
                        'platform' => $platform,
                    );
                    $output[] = $this->_insertMysql($this->catalogTable, $bind);
                }
            }
            $this->_pushToDB($output);
        }
    }

    private function _getOnlinerBy($source)
    {
        $links = $this->resources[$source];
        foreach ($links as $link) {
            $_html = $this->_file_get_html($link);
            if ($_html) {
                foreach ($_html->find('.main_page .b-catalogitems li') as $_firstKey => $_category) {
                    //$this->writeSuccessLine('first - '.$_firstKey);
                    if ($this->logs && ($_firstKey < $this->logs['first'])) {
                        continue;
                    }
                    $category = $_category->plaintext;
                    $categoryHref = $_category->find('a', 0)->href . '~add=0~sort_by=best~dir=asc~where=onsale~currency=BRB~city=minsk/';
                    $this->onlinerByIteration($_firstKey, $category, $categoryHref, $link, $source);
                }
            }
        }
    }

    protected function onlinerByIteration($_firstKey, $category, $href, $source, $platform, $page = 1)
    {
        $_html = $this->_file_get_html($href);
        if ($_html) {
            if ($this->logs && ($page < $this->logs['page'])) {
                $lastPage = $_html->find('.phed a', -2);
                if ($lastPage) {
                    $lastPage = $lastPage->plaintext;
                    for ($i = $this->logs['page']; $i < $lastPage; $i++) {
                        $pageHref = $href . '~add=0~sort_by=best~dir=asc~where=onsale~currency=BRB~city=minsk~page=' . $i . '/';
                        $this->onlinerByIteration($_firstKey, $category, $pageHref, $source, $platform, $i);
                    }
                }
                return;
            }
            foreach ($_html->find('form[name=product_list] tr') as $_secondKey => $_product) {
                if ($this->logs && ($_secondKey <= $this->logs['second'])) {
                    if ($_secondKey+1 == count($_html->find('form[name=product_list] tr') )) {
                        $this->logs = null;
                        $pageHref = $href . '~add=0~sort_by=best~dir=asc~where=onsale~currency=BRB~city=minsk~page=' . $page++ . '/';
                        $this->onlinerByIteration($_firstKey, $category, $pageHref, $source, $platform, $page++);
                    }
                    continue;
                }
                $this->logs = null;
                $isPrice = $_product->find('.poffers a', 0);
                if ($isPrice) {
                    $productHref = $_product->find('.poffers a', 0)->href;
                    $productHrefArr = explode('/', $productHref);
                    $productArt = $productHrefArr[count($productHrefArr) - 2];
                    $productName = $_product->find('.pdescr .pname a', 0)->plaintext;
                    $productLink = rtrim($source, '/') . $productHref;
                    $html = $this->_file_get_html($productLink);
                    if ($html) {
                        $output = array();
                        foreach ($html->find('.b-offers-list-line-table .js-position-item') as $_offer) {
                            $offerName = $_offer->find('.logo img', 0);
                            if ($offerName) {
                                $price = $_offer->find('.price', 0)->plaintext;
                                $bind = array(
                                    'source' => $this->_replaceQuote($productLink),
                                    'category' => $this->_replaceQuote($category),
                                    'offer' => $this->_replaceQuote($offerName->alt),
                                    'article' => $this->_replaceQuote($productArt),
                                    'name' => $this->_replaceQuote($productName),
                                    'price' => $this->_replacePrice($price),
                                    'beznal' => 0,
                                    'platform' => $platform,
                                );
                                $output[] = $this->_insertMysql($this->catalogTable, $bind);
                            }
                        }
                        $logBinds = array(
                            'params' => serialize(array(
                                'first' => $_firstKey,
                                'page' => $page,
                                'second' => $_secondKey
                            )),
                            'platform' => $platform
                        );
                        $output[] = $this->_replaceMysql($this->logTable, $logBinds);
                        $this->_pushToDB($output);
                    }
                }
                $this->writeSuccessLine('Категория - ' . $_firstKey.'; Страница - '.$page.'; Продукт - '.$_secondKey);
            }
        }
        $lastPage = $_html->find('.phed a', -2);
        if ($lastPage) {
            $lastPage = $lastPage->plaintext;
            for ($i = $page+1; $i < $lastPage; $i++) {
                $pageHref = $href . '~add=0~sort_by=best~dir=asc~where=onsale~currency=BRB~city=minsk~page=' . $i . '/';
                $this->onlinerByIteration($_firstKey, $category, $pageHref, $source, $platform, $i);
            }
        }
    }

    private function _pushToDB($queries)
    {
        $count = count($queries);
        if ($count) {
            $this->connection->beginTransaction();
            $this->writeSuccessLine('Start transaction...');
            try {
                if (!$this->total && $this->truncate) {
                    $this->connection->query('TRUNCATE '.$this->catalogTable);
                }
                foreach ($queries as $query) {
                    $this->connection->query($query);
                }
                $this->connection->commit();
                $this->total += $count-1;
                $this->writeSuccessLine('Finished commiting transactions (' . $count . ')...');
                $this->writeSuccessLine('Total: ' . $this->total);
            } catch (Exception $e) {
                $this->writeErrorLine('Something went wrong!');
                $this->writeSuccessLine('Starting rollbacking transaction...');
                $this->connection->rollback();
                $this->writeSuccessLine('Finished rollbacking transaction.');
                $this->entityManager->close();
                throw $e;
            }
        }
    }

    private function _insertMysql($table, $bind)
    {
        $replaceKeys = implode(', ', array_keys($bind));
        $replaceVal = implode(', ', $this->_wrapQuotes($bind, "'"));
        $this->writeLine($replaceVal);

        return 'INSERT INTO `' . $table . '` (' . $replaceKeys . ') VALUES (' . $replaceVal . ')';
    }

    private function _replaceMysql($table, $bind)
    {
        $replaceKeys = implode(', ', array_keys($bind));
        $replaceVal = implode(', ', $this->_wrapQuotes($bind, "'"));

        return 'REPLACE INTO `' . $table . '` (' . $replaceKeys . ') VALUES (' . $replaceVal . ')';
    }

    protected function _wrapQuotes($arrayValues, $quote = "")
    {
        foreach ($arrayValues as $_key => $_value) {
            $arrayValues[$_key] = $quote . $_value . $quote;
        }

        return $arrayValues;
    }
}

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
use Symfony\Component\Validator\Constraints\DateTime;
use Vilka\CoreBundle\Command\CommandOutput;
use Vilka\CoreBundle\Entity\Log;
use Vilka\CoreBundle\Repository\FileRepository;

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

    protected $start = null;

    protected $limit = 880;

    protected $queries = array();

    protected $transCount = 200;

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
        '1k.by' => 5,
        'shop.by' => 2
    );


    protected function configure()
    {
        $this->setName('parser:catalog:start', 'Start sites parser')
            ->addOption('site', null, InputOption::VALUE_REQUIRED, 'Site url', 'onliner.by')
            ->addOption('move', null, InputOption::VALUE_REQUIRED, 'Move data from temp table', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*var_dump(serialize(array(
            'first' => 21,
            'page' => 3,
            'second' => 29
        )));
        die;*/
        $this->start = time();

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
        if ($logRes && !$move) {
            $logRes = $logRes[0];
            if ($logRes['params'] == 'moving') {
                $this->writeErrorLine('moving');
                die;
            }
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
                case 'shop.by':
                    $this->_getShopBy($source);
                    break;
                default:
                    break;
            }
        }
        $this->_copyTable($this->catalogTable, $this->defaultTable, $source);
    }

    protected function _copyTable($fromTable, $toTable, $source) {
        try {
            $date = date('Y-m-d H:i:s');
            $data = "source,category,offer,platform,article,name,price,beznal";
            $query = "SELECT " . $data . " FROM " . $fromTable;
            $this->connection->executeQuery("UPDATE " . $this->logTable . " SET params='moving' WHERE platform='" . $source . "'");
            $fileSql = $this->_createCSV($query, $source, $toTable, $date);
            $this->connection->beginTransaction();
            $this->writeSuccessLine('Start transaction...');
            $this->connection->query("DELETE FROM " . $toTable . " WHERE platform='" . $source . "'");
            $sql = "INSERT INTO " . $toTable . " (" . $data . ",date) ";
            $sql .= "SELECT " . $data . ",'" . $date . "' FROM " . $fromTable;
            $this->connection->query($sql);
            if ($fileSql) {
                $this->connection->query($fileSql);
            }
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

    private function _createCSV($_query, $source, $toTable, $date)
    {
        $sql = false;
        $step = 30000;
        $limit = 0;
        $time = time();
        $date = date('Y-m-d H:i:s');
        $directoryUser = "/web/files";
        $directory = getcwd() . $directoryUser;
        $file = "/" .$source . '_' . md5($source . $time) . ".csv";
        $fileName = $directory . $file;
        $link = 'files' . $file;
        $query = $_query . ' LIMIT ' . $limit . ' , ' . ($limit+$step);
        $output = $this->connection->executeQuery($query)->fetchAll();
        while (count($output)) {
            $this->writeSuccessLine('Start file ' . $limit . ' , ' . ($limit+$step));
            //$this->connection->beginTransaction();
            $csvData = '';
            foreach ($output as $key => $_line) {
                $_line['date'] = $date;
                $csvData .= $this->quote . $_line["source"] . $this->quote . $this->separator
                    . $this->quote . $_line["category"] . $this->quote . $this->separator
                    . $this->quote . $_line["offer"] . $this->quote . $this->separator
                    . $this->quote . $_line["article"] . $this->quote . $this->separator
                    . $this->quote . $_line["name"] . $this->quote . $this->separator
                    . $this->quote . $_line["price"] . $this->quote . $this->separator
                    . $this->quote . $_line["beznal"] . $this->quote . $this->separator
                    . $this->quote . $_line["date"] . $this->quote;
                $csvData .= $this->enter;
                //$sql = $this->_insertMysql($toTable, $_line, false);
                //$this->connection->query($sql);
            }

            $handle = fopen($fileName, "a+");
            fwrite($handle, $csvData);
            fclose($handle);
            $limit += $step;
            $query = $_query . ' LIMIT ' . $limit . ' , ' . ($limit+$step);
            $output = $this->connection->executeQuery($query)->fetchAll();
            //$this->connection->commit();
        }

        $sql = $this->_replaceMysql('file', array(
            'platform' => $source,
            'link' => $link,
            'date' => $date,
        ));
        return $sql;
    }

    private function _log($message, $source)
    {
        $date = date('Y-m-d H:i:s');
        $fileName = getcwd() . "/app/logs/" . $source . ".log";
        $message = $date . ":  " . $message . $this->enter;
        $handle = fopen($fileName, "a+");
        fwrite($handle, $message);
        fclose($handle);
    }

    protected function _replaceQuote($text)
    {
        $text = str_replace('&nbsp;', ' ', $text);
        $text = str_replace("'", '&#39;', $text);
        $text = str_replace("\\", '&#92;', $text);
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
        if (time()-$this->start >= $this->limit) {
            $this->_pushToDB(true);
            die('Time limit');
        }
        sleep($this->sleep);
        return file_get_html($link);
    }

    private function _getShopBy($source)
    {
        $links = $this->resources[$source];
        foreach ($links as $link) {
            $_html = $this->_file_get_html($link);
            if ($_html) {
                foreach ($_html->find('.Page__ElementPageCatalog a') as $_firstKey => $_category) {
                    $this->_log($_firstKey . ' - ' . $_category->plaintext, $source);
                    if ($this->logs && ($_firstKey < $this->logs['first'])) {
                        continue;
                    }
                    $this->_shopByIteration($_firstKey, $source, $_category->plaintext, 'http://' . $source . $_category->href);
                }
            }
        }
        $this->_pushToDB(true);
    }


    private function _shopByIteration($_firstKey, $source, $categoryTitle, $categoryHref, $page = 1, $postfix = '') {
        if ($this->logs && ($page < $this->logs['page'])) {
            $postfix = '?page_id='.$this->logs['page'].'&amp;page_size=20&amp;currency=BYB&amp;sort=popularity--number&amp;to_order=1';
            $this->_shopByIteration($_firstKey, $source, $categoryTitle, $categoryHref, $this->logs['page'], $postfix);
            return;
        }
        $shopsUrl = 'http://shop.by/model/shop/';
        $shopsUrlPost = '/?page_size=100&page_id=1';
        $_html = $this->_file_get_html($categoryHref . $postfix);
        if ($_html) {
            foreach ($_html->find('.ModelList__ModelBlockRow') as $_secondKey => $_product) {
                if ($this->logs && ($_secondKey <= $this->logs['second'])) {
                    if ($_secondKey+1 == count($_html->find('.ModelList__ModelBlockRow'))) {
                        $this->logs = null;
                        $page++;
                        $postfix = '?page_id='.$page.'&amp;page_size=20&amp;currency=BYB&amp;sort=popularity--number&amp;to_order=1';
                        $this->_shopByIteration($_firstKey, $source, $categoryTitle, $categoryHref, $page, $postfix);
                    }
                    continue;
                }
                $this->logs = null;
                $shopHref = $_product->find('.ModelList__InfoModelBlock a', 2);
                if ($shopHref) {
                    $productName = $_product->find('.ModelList__NameBlock a span', 0)->plaintext;
                    $shopHrefArr = array_reverse(explode('/', $shopHref->href));
                    $productLink = $shopsUrl . $shopHrefArr[1] . $shopsUrlPost;
                    $html = $this->_file_get_html($productLink);
                    if ($html) {
                        foreach ($html->find('.ShopItemList__ItemBlockRow') as $shop) {
                            $bind = array(
                                'source' => $this->_replaceQuote('http://' . $source .$shopHref->href),
                                'category' => $this->_replaceQuote($categoryTitle),
                                'article' => $this->_replaceQuote($shopHrefArr[1]),
                                'name' => $this->_replaceQuote($productName),
                                'price' => $this->_replacePrice($shop->find('.PriceBlock__PriceFirst span', 0)->plaintext),
                                'beznal' => 0,
                                'platform' => $source,
                                'offer' => $this->_replaceQuote($shop->find('.ShopItemList__BlockShopLink a', 0)->plaintext)
                            );
                            $this->queries[] = $this->_insertMysql($this->catalogTable, $bind);
                        }
                    }
                    $logs = array(
                        'platform' => $source,
                        'params' => serialize(
                            array('page' => $page,
                                'first' => $_firstKey,
                                'second' => $_secondKey
                            )
                        )
                    );
                    $this->queries[] = $this->_replaceMysql($this->logTable, $logs);
                    $this->_pushToDB();
                    $this->writeSuccessLine('Категория - ' . $_firstKey.'; Страница - '.$page.'; Продукт - '.$_secondKey);
                }
            }
            foreach ($_html->find('.ShopItemList__ItemBlockRow') as $_secondKey => $_product) {
                if ($this->logs && ($_secondKey <= $this->logs['second'])) {
                    if ($_secondKey+1 == count($_html->find('.ShopItemList__ItemBlockRow'))) {
                        $this->logs = null;
                        $page++;
                        $postfix = '?page_id='.$page.'&amp;page_size=20&amp;currency=BYB&amp;sort=popularity--number&amp;to_order=1';
                        $this->_shopByIteration($_firstKey, $source, $categoryTitle, $categoryHref, $page, $postfix);
                    }
                    continue;
                }
                $this->logs = null;
                $link = $_product->find('.ShopItemList__ItemName', 0);
                $productName = $link->plaintext;
                $productHref = 'http://' . $source . $link->href;
                $bind = array(
                    'source' => $this->_replaceQuote($productHref),
                    'category' => $this->_replaceQuote($categoryTitle),
                    'article' => '',
                    'name' => $this->_replaceQuote($productName),
                    'price' => $this->_replacePrice($_product->find('.PriceBlock__PriceFirst span', 0)->plaintext),
                    'beznal' => 0,
                    'platform' => $source,
                    'offer' => $this->_replaceQuote($_product->find('.ShopItemList__BlockShopLink a', 0)->plaintext)
                );
                $this->queries[] = $this->_insertMysql($this->catalogTable, $bind);
                $logs = array(
                    'platform' => $source,
                    'params' => serialize(
                        array('page' => $page,
                            'first' => $_firstKey,
                            'second' => $_secondKey
                        )
                    )
                );
                $this->queries[] = $this->_replaceMysql($this->logTable, $logs);
                $this->_pushToDB();
                $this->writeSuccessLine('Категория - ' . $_firstKey.'; Страница - '.$page.'; Продукт - '.$_secondKey);
            }
            $nextPage = $_html->find('.Paging__LastPage', 0);
            if ($nextPage) {
                $page++;
                $this->_shopByIteration($_firstKey, $source, $categoryTitle, $categoryHref, $page, $nextPage->href);
            }
        }

    }

    private function _getOneKBy($source)
    {
        $links = $this->resources[$source];
        foreach ($links as $link) {
            $_html = $this->_file_get_html($link);
            if ($_html) {
                foreach ($_html->find('.b-catalog-nav .catalog-nav_title a') as $_firstKey => $_domain) {
                    if ($this->logs && ($_firstKey < $this->logs['first'])) {
                        continue;
                    }
                    $domainHref = $_domain->href;
                    $_html = $this->_file_get_html($domainHref);
                    if ($_html) {
                        foreach ($_html->find('.b-categories .categories_item a.categories_sub') as  $_secondKey => $category) {
                            if ($this->logs && ($_secondKey < $this->logs['second'])) {
                                continue;
                            }
                            $this->oneKByIteration($_firstKey, $_secondKey, $source, $domainHref, $category, $category->href);
                        }
                    }
                }
            }
        }
        $this->_pushToDB(true);
    }

    protected function oneKByIteration($_firstKey, $_secondKey, $platform, $href, $category, $categoryHref, $page = 1)
    {

        if ($this->logs && ($page < $this->logs['page'])) {
            $categoryHref = $categoryHref . 'page' . $this->logs['page'];
            $this->oneKByIteration($_firstKey, $_secondKey, $platform, $href, $category, $categoryHref, $this->logs['page']);
        }
        $categoryName = $category->plaintext;
        //$this->writeSuccessLine('Домен - ' . $_firstKey.'; Категория - ' . $_secondKey.' - ' . $categoryName . '; Страница - '.$page);
        $_html2 = $this->_file_get_html($categoryHref);
        if ($_html2) {
            foreach ($_html2->find('.b-price .c-green') as $_greenKey => $_product) {
                if ($this->logs && (!isset($this->logs['green']) || ($_greenKey <= $this->logs['green']))) {
                    continue;
                }
                $this->logs = null;
                $logs = array(
                    'platform' => $platform,
                    'params' => serialize(
                        array('page' => $page,
                            'first' => $_firstKey,
                            'second' => $_secondKey,
                            'green' => $_greenKey
                        )
                    )
                );
                $this->_oneKByData($logs, $platform, $href, $_product, $categoryName);
                $this->writeSuccessLine('Домен - ' . $_firstKey.'; Категория - ' . $_secondKey.'; Страница - '.$page.'; Продукт нал - '.$_greenKey);
            }
            foreach ($_html2->find('.b-price .c-orange') as $_orangeKey =>  $_product) {
                if ($this->logs && ($_orangeKey <= $this->logs['orange'])) {
                    continue;
                }
                $this->logs = null;
                $logs = array(
                    'platform' => $platform,
                    'params' => serialize(
                        array('page' => $page,
                        'first' => $_firstKey,
                        'second' => $_secondKey,
                        'orange' => $_orangeKey
                        )
                    )
                );
                $this->_oneKByData($logs, $platform, $href, $_product, $categoryName, 1);
                $this->writeSuccessLine('Домен - ' . $_firstKey.'; Категория - ' . $_secondKey.'; Страница - '.$page.'; Продукт безнал - '.$_orangeKey);
            }
            $currPage = $_html2->find('.b-paging .current', 0)->next_sibling();
            if ($page == 1) {
                $currPage = $currPage->next_sibling();
            }
            if ($currPage->href) {
                $categoryHref = rtrim($href, '/') . $currPage->href;
                $this->oneKByIteration($_firstKey, $_secondKey, $platform, $href, $category, $categoryHref, $currPage->plaintext);
            }
        }
    }

    protected function _oneKByData($logs, $platform, $href, $_product, $categoryName, $beznal = 0)
    {
        $productLink = rtrim($href, '/') . $_product->href;
        $productHrefArr = explode('/', $productLink);
        $html = $this->_file_get_html($productLink);
        if ($html) {
            if ($html->find('#product-data')) {
                $productName = $html->find('#status-line .crumbs_current', 0)->plaintext;
                $productArt = explode('-', $productHrefArr[count($productHrefArr) - 3]);
                foreach ($html->find('#product-data .b-shop') as $key => $_price) {
                    $price = $_price->find('.shop_price .sell', 0);
                    if (!$price) {
                        $price = $_price->find('.shop_price .cur', 0);
                    }
                    $bind = array(
                        'source' => $this->_replaceQuote($productLink),
                        'category' => $this->_replaceQuote($categoryName),
                        'article' => $this->_replaceQuote($productArt[0]),
                        'name' => $this->_replaceQuote($productName),
                        'price' => $this->_replacePrice($price->plaintext),
                        'beznal' => $beznal,
                        'platform' => $platform,
                        'offer' => $this->_replaceQuote($_price->find('.shop_reviews img', 0)->alt)
                    );
                    $this->queries[] = $this->_insertMysql($this->catalogTable, $bind);
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
                    $this->queries[] = $this->_insertMysql($this->catalogTable, $bind);
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
                    $this->queries[] = $this->_insertMysql($this->catalogTable, $bind);
                }
            }
            $this->queries[] = $this->_replaceMysql($this->logTable, $logs);
            $this->_pushToDB();
        }
    }

    private function _getOnlinerBy($source)
    {
        $links = $this->resources[$source];
        foreach ($links as $link) {
            var_dump('111');
            $_html = $this->_file_get_html($link);
            if ($_html) {
                var_dump('2222');
                foreach ($_html->find('.            ]i') as $_firstKey => $_category) {
                    //$this->writeSuccessLine('first - '.$_firstKey);
                    if ($this->logs && ($_firstKey < $this->logs['first'])) {
                        continue;
                    }
                    $category = $_category->plaintext;
                    $categoryHref = $_category->find('a', 0)->href;
                    $this->onlinerByIteration($_firstKey, $category, $categoryHref, $link, $source);
                }
            }
        }
        $this->_pushToDB(true);
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
                        $pageHref = $href . '~add=0~sort_by=best~dir=asc~where=actual~currency=BRB~city=minsk~page=' . $i . '/';
                        $this->onlinerByIteration($_firstKey, $category, $pageHref, $source, $platform, $i);
                    }
                }
                return;
            }
            foreach ($_html->find('form[name=product_list] tr') as $_secondKey => $_product) {
                if ($this->logs && ($_secondKey <= $this->logs['second'])) {
                    if ($_secondKey+1 == count($_html->find('form[name=product_list] tr') )) {
                        $this->logs = null;
                        $pageHref = $href . '~add=0~sort_by=best~dir=asc~where=actual~currency=BRB~city=minsk~page=' . $page++ . '/';
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
                                $this->queries[] = $this->_insertMysql($this->catalogTable, $bind);
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
                        $this->queries[] = $this->_replaceMysql($this->logTable, $logBinds);
                        $this->_pushToDB();
                    }
                }
                $this->writeSuccessLine('Категория - ' . $_firstKey.'; Страница - '.$page.'; Продукт - '.$_secondKey);
            }
            $lastPage = $_html->find('.phed a', -2);
            if ($lastPage) {
                $lastPage = $lastPage->plaintext;
                for ($i = $page+1; $i < $lastPage; $i++) {
                    $pageHref = $href . '~add=0~sort_by=best~dir=asc~where=actual~currency=BRB~city=minsk~page=' . $i . '/';
                    $this->onlinerByIteration($_firstKey, $category, $pageHref, $source, $platform, $i);
                }
            }
        }

    }

    private function _pushToDB($force = false)
    {
        $count = count($this->queries);
        if ($count) {
            if (!$force && ($count < $this->transCount)) {
                return;
            }
            $this->connection->beginTransaction();
            $this->writeSuccessLine('Start transaction...');
            try {
                if (!$this->total && $this->truncate) {
                    $this->connection->query('TRUNCATE '.$this->catalogTable);
                }
                foreach ($this->queries as $query) {
                    $this->connection->query($query);
                }
                $this->connection->commit();
                $this->total += $count-1;
                $this->writeSuccessLine('Finished commiting transactions (' . $count . ')...');
                $this->writeSuccessLine('Total: ' . $this->total);
                $this->queries = array();
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

    private function _insertMysql($table, $bind, $write = true)
    {
        $replaceKeys = implode(', ', array_keys($bind));
        $replaceVal = implode(', ', $this->_wrapQuotes($bind, "'"));
        if ($write) {
            $this->writeLine($replaceVal);
        }

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

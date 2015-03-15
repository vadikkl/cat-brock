<?php

namespace Vilka\CoreBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Serializer\Exception\Exception;
use Vilka\CoreBundle\Command\CommandOutput;

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

    protected function configure()
    {
        $this->setName('parser:catalog:start', 'Start sites parser')
            ->addOption('site', null, InputOption::VALUE_REQUIRED, 'Site url', 'onliner.by')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->load($output);

        $site = $input->getOption('site');

        $doctrine = $this->getDoctrine();
        $entityManager = $doctrine->getManager();

        $connection = $entityManager->getConnection();

        $this->writeSuccessLine('Parsing ' . $site . ' start...');
        $this->_getParser($site);
        /*$connection->beginTransaction();
        try {
            $countryRepository = $entityManager->getRepository('RingbeCoreBundle:Country');

            $this->writeLine('Preparing truncation of tables...');

            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $connection->query('DELETE FROM `call_channel`');
            $connection->commit();
            $this->writeLine('Finished commiting transaction...');
        } catch (Exception $e) {
            $this->writeLine('Something went wrong!');
            $this->writeLine('Starting rollbacking transaction...');
            $connection->rollback();
            $this->writeLine('Finished rollbacking transaction...');
            $entityManager->close();
            throw $e;
        }*/

        $this->writeSuccessLine('Parsing ' . $site . ' finish.');
    }

    /**
     * @return Registry
     */
    private function getDoctrine()
    {
        return $this->get('doctrine');
    }

    public function _getParser($source) {
        switch ($source) {
            case 'onliner.by':
                $this->_getOnlinerBy($source);
                break;
            case '1k.by':
                $this->_getOneKBy($source);
                break;
            default:
                return;
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

    protected function _file_get_html($link) {
        sleep(1);
        return file_get_html($link);
    }

    private function _getOneKBy($source)
    {
        $links = $_SESSION[$source];
        $output = array();
        foreach ($links as $link) {
            $_html = $this->_file_get_html($link);
            if ($_html) {
                foreach ($_html->find('.b-list-cat h3 a') as $_domain) {
                    $domainHref = $_domain->href;
                    $_html = $this->_file_get_html($domainHref);
                    if ($_html) {
                        foreach ($_html->find('.block-allitems h3 a') as $category) {
                            $this->oneKByIteration($domainHref, $category, $category->href);
                        }
                    }
                }
            }
        }
    }

    protected function oneKByIteration($href, $category, $categoryHref, $test = false)
    {
        $categoryName = $category->plaintext;
        $_html2 = $this->_file_get_html($categoryHref);
        if ($_html2) {
            foreach ($_html2->find('.products-block a.offers') as $_product) {
                $this->_oneKByData($href, $_product, $categoryName);
                //break;
            }
            foreach ($_html2->find('.products-block a.offersbeznal') as $_product) {
                $this->_oneKByData($href, $_product, $categoryName, 1);
                //break;
            }
            $currPage = $_html2->find('span.pages', 0)->next_sibling();
            if ($currPage->href) {
                $categoryHref = rtrim($href, '/').$currPage->href;
                $this->oneKByIteration($href, $category, $categoryHref, true);
            }
        }
    }

    protected function _oneKByData($href, $_product, $categoryName, $beznal = 0) {
        $productLink = rtrim($href, '/').$_product->href;
        $productHrefArr = explode('/', $productLink);
        $html = $this->_file_get_html($productLink);
        if ($html) {
            $output = array();
            if ($html->find('#product-data')) {
                $productName = $html->find('#status-line .active', 0)->plaintext;
                $productArt = explode('-', $productHrefArr[count($productHrefArr)-3]);
                foreach ($html->find('#product-data .td5') as $key => $_price) {
                    $output[$key] = array(
                        'source' => $this->_replaceQuote($productLink),
                        'category' => $this->_replaceQuote($categoryName),
                        'article' => $this->_replaceQuote($productArt[0]),
                        'name' => $this->_replaceQuote($productName),
                        'price' => $this->_replacePrice($_price->find('.o-price b', 0)->plaintext),
                        'beznal' => $beznal
                    );
                }
                foreach ($html->find('#product-data .o-phones img') as $key => $_img) {
                    $output[$key]['offer'] = $this->_replaceQuote($_img->alt);
                }
            } else {
                $productName = $html->find('#status-line .active', 0)->plaintext;
                $productArt = explode('-', $productHrefArr[count($productHrefArr)-2]);
                foreach ($html->find('.price .retail') as $_price) {
                    $output[] = array(
                        'source' => $this->_replaceQuote($productLink),
                        'category' => $this->_replaceQuote($categoryName),
                        'article' => $this->_replaceQuote($productArt[0]),
                        'name' => $this->_replaceQuote($productName),
                        'price' => $this->_replacePrice($_price->plaintext),
                        'offer' => $this->_replaceQuote($html->find('.shoplogo', 0)->alt),
                        'beznal' => 0
                    );
                }
                foreach ($html->find('.price .cashless') as $_price) {
                    $output[] = array(
                        'source' => $this->_replaceQuote($productLink),
                        'category' => $this->_replaceQuote($categoryName),
                        'article' => $this->_replaceQuote($productArt[0]),
                        'name' => $this->_replaceQuote($productName),
                        'price' => $this->_replacePrice($_price->plaintext),
                        'offer' => $this->_replaceQuote($html->find('.shoplogo', 0)->alt),
                        'beznal' => 1
                    );
                }
            }
            $this->_createCSV($output, '1k');
        }
    }

    private function _getOnlinerBy($source)
    {
        $links = $this->resources[$source];
        foreach ($links as $link) {
            $_html = $this->_file_get_html($link);
            if ($_html) {
                foreach ($_html->find('.main_page .b-catalogitems li') as $_category) {
                    $category = $_category->plaintext;
                    $categoryHref = $_category->find('a', 0)->href;
                    $this->onlinerByIteration($category, $categoryHref, $link);
                    //$this->_createCSV($output, $source);
                }
            }
        }
    }

    protected function onlinerByIteration(/*&$output, */$category, $href, $source)
    {
        $_html = $this->_file_get_html($href);
        if ($_html) {
            foreach ($_html->find('form[name=product_list] tr') as $_product) {
                $isPrice = $_product->find('.poffers a', 0);
                if ($isPrice) {
                    $productHref = $_product->find('.poffers a', 0)->href;
                    $productHrefArr = explode('/', $productHref);
                    $productArt = $productHrefArr[count($productHrefArr)-2];
                    $productName = $_product->find('.pdescr .pname a', 0)->plaintext;
                    $productLink = rtrim($source, '/').$productHref;
                    $html = $this->_file_get_html($productLink);
                    if ($html) {
                        $output = array();
                        foreach ($html->find('.b-offers-list-line-table .js-position-item') as $_offer) {
                            $offerName = $_offer->find('.logo img', 0);
                            if ($offerName) {
                                $price = $_offer->find('.price', 0)->plaintext;
                                $output[] = array(
                                    'source' => $this->_replaceQuote($productLink),
                                    'category' => $this->_replaceQuote($category),
                                    'offer' => $this->_replaceQuote($offerName->alt),
                                    'article' => $this->_replaceQuote($productArt),
                                    'name' => $this->_replaceQuote($productName),
                                    'price' => $this->_replacePrice($price),
                                    'beznal' => 0
                                );
                            }

                        }
                        $this->_createCSV($output, 'onliner');

                    }
                }
            }
        }
        $lastPage = (int)$_html->find('.phed a', -2)->plaintext;
        if ($lastPage) {
            for ($i = 2; $i < $lastPage; $i++) {
                $pageHref = $href.'~add=0~sort_by=best~dir=asc~where=actual~currency=BRB~city=minsk~page='.$i.'/';
                $this->onlinerByIteration(/*$output,*/ $category, $pageHref, $source);
            }
        }
    }

    private function    _createCSV($output, $source)
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
                //if ($key < $count - 1) {
                $csvData .= $this->enter;
                //}
            }
            $fileName = getcwd() . "/files/" . $source /*. '_' . date('Y-m-d_h:i:s')*/ . ".csv";
            if (isset($_SESSION['all'])) {
                $fileName = getcwd() . "/files/all.csv";
            }
            $handle = fopen($fileName, "a+");
            fwrite($handle, $csvData);
            fclose($handle);
        }
    }

    private function _finishAll() {
        setcookie('offset', null);
        $fileName = getcwd() . "/files/all.".$_POST['format'];
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
    }
}

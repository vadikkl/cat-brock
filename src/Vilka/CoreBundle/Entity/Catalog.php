<?php

namespace Vilka\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Catalog
 *
 * @ORM\Table(name="catalog", uniqueConstraints={
 *  @ORM\UniqueConstraint(columns={"id"})}, indexes={
 *  @ORM\Index(name="category_idx", columns={"category"}),
 *  @ORM\Index(name="offer_idx", columns={"offer"}),
 *  @ORM\Index(name="platform_idx", columns={"platform"}),
 *  @ORM\Index(name="price_idx", columns={"price"}),
 *  @ORM\Index(name="date_idx", columns={"date"}),
 *  @ORM\Index(name="category_offer_platform_price_date_idx", columns={"category","offer","platform","price", "date"})
 * })
 * @ORM\Entity(repositoryClass="Vilka\CoreBundle\Repository\CatalogRepository")
 */
class Catalog
{
    static public $PLATFORMS = array(
        'onliner.by' => 'onliner.by',
        '1k.by' => '1k.by',
        'shop.by' => 'shop.by'
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
     * @ORM\Column(name="source", type="string", length=255)
     */
    private $source;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=255)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="offer", type="string", length=255)
     */
    private $offer;

    /**
     * @var string
     *
     * @ORM\Column(name="platform", type="string", length=255)
     */
    private $platform;

    /**
     * @var string
     *
     * @ORM\Column(name="article", type="string", length=255)
     */
    private $article;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=500)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var boolean
     *
     * @ORM\Column(name="beznal", type="boolean")
     */
    private $beznal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


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
     * Set source
     *
     * @param string $source
     *
     * @return Catalog
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Catalog
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set offer
     *
     * @param string $offer
     *
     * @return Catalog
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * Get offer
     *
     * @return string
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * Set platform
     *
     * @param string $platform
     *
     * @return Catalog
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set article
     *
     * @param string $article
     *
     * @return Catalog
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return string
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Catalog
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
     * Set price
     *
     * @param float $price
     *
     * @return Catalog
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set beznal
     *
     * @param boolean $beznal
     *
     * @return Catalog
     */
    public function setBeznal($beznal)
    {
        $this->beznal = $beznal;

        return $this;
    }

    /**
     * Get beznal
     *
     * @return boolean
     */
    public function getBeznal()
    {
        return $this->beznal;
    }

    /**
     * Set date
     *
     * @param integer $date
     *
     * @return History
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }
}


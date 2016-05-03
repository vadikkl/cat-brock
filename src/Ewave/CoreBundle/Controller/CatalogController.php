<?php

namespace Ewave\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Ewave\CoreBundle\Entity\Catalog;
use Ewave\CoreBundle\Form\CatalogType;

/**
 * Catalog controller.
 *
 * @Route("/control/catalog")
 */
class CatalogController extends AdvancedController
{
    /**
     * Lists all Setting entities.
     *
     * @Route("/{platform}", name="ewave_control_catalog")
     * @Template("EwaveCoreBundle:Catalog:index.html.twig")
     */
    public function indexAction(Request $request, $platform = null)
    {
        $allOffers = array();
        $allCategories = array();
        $catalogRepository = $this->getCatalogRepository();
        if ($platform) {
            $allOffers = $catalogRepository->getOffersByPlatform($platform);
            $allCategories = $catalogRepository->getCategoriesByPlatform($platform);
            if (!count($allOffers) || !count($allCategories)) {
                $this->get('session')->getFlashBag()->add(
                    'danger',
                    'Товаров не найдено'
                );
            }
        }
        if ($request->getMethod() == 'POST') {
            $offers = $request->request->get('offers');
            $categories = $request->request->get('categories');
            $user = $this->getUser();
            if (!$catalogRepository->getProducts($platform, $offers, $categories, $user)) {
                $this->get('session')->getFlashBag()->add(
                    'danger',
                    'Товаров не найдено'
                );
            }
        }

        return array (
            'platforms' => Catalog::$PLATFORMS,
            'all_offers' => $allOffers,
            'all_categories' => $allCategories
        );
    }
}

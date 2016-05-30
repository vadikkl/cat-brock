<?php

namespace Ewave\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Export controller.
 *
 * @Route("/control/export")
 */
class ExportController extends AdvancedController
{
    /**
     * Get Export File
     *
     * @Route("/{type}", name="ewave_control_export")
     */
    public function indexAction(Request $request, $type)
    {
        if ($type) {
            $projectRepository = $this->getProjectRepository();
            $projectRepository->export($type);
        }
        $this->get('session')->getFlashBag()->add(
            'danger',
            'Error while exporting'
        );
        return $this->redirect($this->generateUrl('ewave_control_project'));
    }
}

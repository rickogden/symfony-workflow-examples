<?php

namespace App\Controller;

use App\Document\BeerGlass;
use Doctrine\ODM\MongoDB\DocumentManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Workflow\Registry;

class WorkflowController extends AbstractController
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * WorkflowController constructor.
     * @param Registry $registry
     * @param DocumentManager $dm
     */
    public function __construct(Registry $registry, DocumentManager $dm)
    {
        $this->registry = $registry;
        $this->dm = $dm;
    }


    /**
     * @Route("/workflow", name="workflow", methods={"GET"})
     */
    public function index()
    {
        $workflows = [
            'beerglass_basic' => 'clean',
            'beerglass_crap' => 'clean',
        ];
        return $this->render('workflow/index.html.twig', [
            'workflows' => $workflows
        ]);
    }

    /**
     * @Route("/workflow", name="create_workflow", methods={"POST"})
     */
    public function createWorkflow(Request $request)
    {
        $workflowName = $request->request->get('workflow_name');
       // $workflowDocumentClass = $request->request->get('workflow_document');
        $workflowStartingState = $request->request->get('state');

        $doc = new BeerGlass();
        if ($workflowStartingState) {
            $doc->setState($workflowStartingState);
        }

        $this->dm->persist($doc);
        $this->dm->flush();

        return $this->redirect($this->generateUrl('show_workflow',['workflowName' => $workflowName, 'id' => $doc->getId()]));
    }

    /**
     * @param string $workflowName
     * @param BeerGlass $beerGlass
     * @Route("/workflow/{workflowName}/{id}", name="show_workflow", methods={"GET"})
     */
    public function workflow(string $workflowName, string $id)
    {
        $beerGlass = $this->dm->find(BeerGlass::class, $id);
        $workflow = $this->registry->get($beerGlass, $workflowName);
        return $this->render('workflow/show.html.twig',[
            'workflowName' => $workflowName,
            'workflow' => $workflow,
            'beerGlass' => $beerGlass,
        ]);
    }

    /**
     * @param string $workflowName
     * @param string $transition
     * @param BeerGlass $beerGlass
     * @\Sensio\Bundle\FrameworkExtraBundle\Configuration\Route("/workflow/transition/{id}", methods={"POST"})
     */
    public function transition(string $id, Request $request)
    {
        $beerGlass = $this->dm->find(BeerGlass::class, $id);
        $workflowName = $request->request->get('workflow_name');
        $transition = $request->request->get('transition');
        $workflow = $this->registry->get($beerGlass, $workflowName);
        $workflow->apply($beerGlass, $transition);
        $this->dm->flush();
        return $this->redirect($this->generateUrl('show_workflow',['workflowName' => $workflowName, 'id' => $beerGlass->getId()]));
    }
}

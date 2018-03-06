<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Document\BeerGlass;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;

class BeerGlassEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * BeerGlassEventSubscriber constructor.
     *
     * @param DocumentManager $dm
     */
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.complex_events.entered' => 'saveEntity',
            'workflow.complex_events.guard.swig' => 'hasContent',
            'workflow.complex_events.guard.drain_pour_from_partial' => 'hasContent',
            'workflow.complex_events.transition.swig' => 'swig',
            'workflow.complex_events.enter.filling' => 'fill',
            'workflow.complex_events.leave.filling' => 'fill',
            'workflow.complex_events.transition.drain_pour_from_full' => 'drainPour',
            'workflow.complex_events.transition.drain_pour_from_partial' => 'drainPour',
            'workflow.complex_events.transition.finish' => 'finish',
        ];
    }

    public function saveEntity(Event $event): void
    {
        $beerGlass = $event->getSubject();
        $this->dm->persist($beerGlass);
        $this->dm->flush();
    }

    public function hasContent(GuardEvent $event): void
    {
        /** @var BeerGlass $beerGlass */
        $beerGlass = $event->getSubject();
        if (0 === $beerGlass->getFull()) {
            $event->setBlocked(true);
        }
    }

    public function swig(Event $event): void
    {
        /** @var BeerGlass $beerGlass */
        $beerGlass = $event->getSubject();
        $beerGlass->swig();
    }

    public function fill(Event $event): void
    {
        /** @var BeerGlass $beerGlass */
        $beerGlass = $event->getSubject();
        $beerGlass->fill(50);
    }

    public function drainPour(Event $event): void
    {
        /** @var BeerGlass $beerGlass */
        $beerGlass = $event->getSubject();
        $beerGlass->empty();
    }

    public function finish(Event $event): void
    {
        /** @var BeerGlass $beerGlass */
        $beerGlass = $event->getSubject();
        $beerGlass->empty();
    }
}

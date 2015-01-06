<?php

/*
 * This file is part of the Itkg\Core package.
 *
 * (c) Interakting - Business & Decision
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itkg\Core\Listener;

use Itkg\Core\Event\KernelEvent;
use Itkg\Core\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Ajax legacy listener renderer
 * Render any callAjax data
 *
 * @author Pascal DENIS <pascal.denis@businessdecision.com>
 */
class AjaxRenderResponseListener implements EventSubscriberInterface
{
    /**
     * @var \Pelican_Ajax_Adapter_Abstract
     */
    private $adapter;

    /**
     * Constructor
     *
     * @param \Pelican_Ajax_Adapter_Abstract $adapter
     */
    public function __construct(\Pelican_Ajax_Adapter_Abstract $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => 'onResponseLoad'
        );
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onResponseLoad(FilterResponseEvent $event)
    {
        if (!$event->getRequest()->attributes->has('AJAX_PROCESSING')) {
            return;
        }

        $event->setResponse(
            new Response(
                $this->adapter->getResponse(
                    $event->getRequest()->attributes->get('ajax_commands')
                )
            )
        );
    }
}

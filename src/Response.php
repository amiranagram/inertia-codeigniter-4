<?php

namespace Inertia;

use CodeIgniter\Config\View;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response as HttpResponse;

class Response
{
    protected $viewData = [];
    protected $component;
    protected $props;
    protected $rootView;
    protected $version;

    public function __construct($component, $props, $rootView = 'app', $version = null)
    {
        $this->component = $component;
        $this->props = $props;
        $this->rootView = $rootView;
        $this->version = $version;
    }

    public function with($key, $value = null): Response
    {
        if (is_array($key)) {
            $this->props = array_merge($this->props, $key);
        } else {
            $this->props[$key] = $value;
        }

        return $this;
    }

    public function withViewData($key, $value = null)
    {
        if (is_array($key)) {
            $this->viewData = array_merge($this->viewData, $key);
        } else {
            $this->viewData[$key] = $value;
        }

        return $this;
    }

    public function __toString()
    {
        $partialData = $this->request()->getHeader('X-Inertia-Partial-Data');
        $only = array_filter(
            explode(',', $partialData ? $partialData->getValue() : '')
        );

        $partialComponent = $this->request()->getHeader('X-Inertia-Partial-Component');
        $props = ($only && ($partialComponent ? $partialComponent->getValue() : '') === $this->component)
            ? array_only($this->props, $only)
            : $this->props;

        array_walk_recursive($props, static function (&$prop) {
            $prop = closure_call($prop);
        });

        $page = [
            'component' => $this->component,
            'props' => $props,
            'url' => $this->request()->detectPath() !== '/' ?  '/'. $this->request()->detectPath() : '/',
            'version' => $this->version,
        ];

        return $this->make($page);
    }

    private function make($page): string
    {
        $inertia = $this->request()->getHeader('X-Inertia');

        if ($inertia && $inertia->getValue()) {
            $this->response()->setHeader('Vary', 'Accept');
            $this->response()->setHeader('X-Inertia', 'true');
            $this->response()->setHeader('Content-Type', 'application/json');

            return json_encode($page);
        }

        return $this->view($page);
    }

    private function view($page): string
    {
        return Services::renderer()
            ->setData($this->viewData + ['page' => $page], 'raw')
            ->render($this->rootView);
    }

    private function request(): IncomingRequest
    {
        return Services::request();
    }

    private function response(): HttpResponse
    {
        return Services::response();
    }
}

<?php

namespace Inertia;

use Illuminate\Support\Arr;

class Factory
{
    public $sharedProps = [];

    public $rootView = 'app';

    public function setRootView($name)
    {
        $this->rootView = $name;
    }

    public function share($key, $value = null)
    {
        if (is_array($key)) {
            $this->sharedProps = array_merge($this->sharedProps, $key);
        } else {
            Arr::set($this->sharedProps, $key, $value);
        }
    }

    public function getShared($key = null)
    {
        if ($key) {
            return Arr::get($this->sharedProps, $key);
        }

        return $this->sharedProps;
    }

    public function version($version)
    {
        $this->version = $version;
    }

    public function getVersion()
    {
        $version = $this->version instanceof Closure
            ? $this->call($this->version)
            : $this->version;

        return (string) $version;
    }

    /**
     * @param $component
     * @param array $props
     * @return string
     */
    public function render($component, $props = []): string
    {
        return new Response(
            $component,
            array_merge($this->sharedProps, $props),
            $this->rootView,
            $this->getVersion()
        );
    }

    public function app($page)
    {
        return '<div id="app" data-page="' . htmlentities(json_encode($page)) . '"></div>';
    }

    public function call($closure)
    {
        return $closure();
    }

    public function redirect($uri)
    {
        return Services::redirectResponse(null, true)
            ->to($uri, 303);
    }

    public function location($url)
    {
//        return BaseResponse::make('', 409, ['X-Inertia-Location' => $url]);
    }
}

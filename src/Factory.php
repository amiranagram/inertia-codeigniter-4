<?php

namespace Inertia;

use CodeIgniter\HTTP\RedirectResponse;

class Factory
{
    /**
     * @var array
     */
    protected $sharedProps = [];

    /**
     * @var string
     */
    protected $rootView = 'app';

    /**
     * @var mixed
     */
    protected $version;

    /**
     * @param string $name
     *
     * @return void
     */
    public function setRootView(string $name): void
    {
        $this->rootView = $name;
    }

    /**
     * @param $key
     * @param null $value
     *
     * @return void
     */
    public function share($key, $value = null): void
    {
        if (is_array($key)) {
            $this->sharedProps = array_merge($this->sharedProps, $key);
        } else {
            array_set($this->sharedProps, $key, $value);
        }
    }

    /**
     * @param null $key
     * @return array
     */
    public function getShared($key = null): array
    {
        if ($key) {
            return array_get($this->sharedProps, $key);
        }

        return $this->sharedProps;
    }

    /**
     * @param $version
     *
     * @return void
     */
    public function version($version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return (string) closure_call($this->version);
    }

    /**
     * @param $component
     * @param array $props
     *
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

    /**
     * @param $page
     *
     * @return string
     */
    public function app($page): string
    {
        return '<div id="app" data-page="' . htmlentities(json_encode($page)) . '"></div>';
    }

    /**
     * @param $uri
     * @return RedirectResponse
     */
    public function redirect($uri): RedirectResponse
    {
        return $this->redirectResponse()->to($uri, 303);
    }

    /**
     * @return RedirectResponse
     */
    public function redirectResponse(): RedirectResponse
    {
        return Services::redirectResponse(null, true);
    }

    public function location($url)
    {
//        return BaseResponse::make('', 409, ['X-Inertia-Location' => $url]);
    }
}

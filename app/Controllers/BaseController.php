<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package App\Controllers
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * Instance of the main Response object.
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
    }

    /**
     * True when the client should receive JSON auth errors (matches filter behavior).
     */
    protected function requestExpectsJsonResponse(): bool
    {
        if (strtolower($this->request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest') {
            return true;
        }

        return str_contains(strtolower($this->request->getHeaderLine('Accept')), 'application/json');
    }

    /**
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\ResponseInterface
     */
    protected function respondLoginRequired(string $message = 'Please log in to continue.')
    {
        helper('url');

        if ($this->requestExpectsJsonResponse()) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'   => 'error',
                'message'  => $message,
                'redirect' => site_url('login'),
            ]);
        }

        return redirect()->to('/login')->with('error', $message);
    }

    /**
     * Standard JSON response wrapper (Phase 1A groundwork).
     *
     * Consistent schema for AJAX/API endpoints:
     * - success: boolean
     * - message: string|null
     * - data: mixed
     * - csrf_hash/csrf_name: for forms that need token refresh
     */
    protected function jsonResponse(
        bool $success,
        $data = null,
        string $message = '',
        int $statusCode = 200
    ): ResponseInterface {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON([
                'success'   => $success,
                'message'   => $message,
                'data'      => $data,
                'csrf_name' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ]);
    }

    protected function jsonSuccess($data = null, string $message = ''): ResponseInterface
    {
        return $this->jsonResponse(true, $data, $message, 200);
    }

    protected function jsonError(string $message, int $statusCode = 400, $data = null): ResponseInterface
    {
        return $this->jsonResponse(false, $data, $message, $statusCode);
    }
}

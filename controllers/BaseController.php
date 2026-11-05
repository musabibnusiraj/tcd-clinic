<?php

require_once __DIR__ . '/../helpers/AppManager.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/JsonResponse.php';

/**
 * BaseController - the parent of all AJAX controllers.
 *
 * A "controller" receives a request from the browser (an AJAX call),
 * uses the models to do the work, and sends a JSON answer back.
 *
 * What this base class does for every child controller:
 *   1. Allows only logged-in operators.
 *   2. Finds out which "action" was requested (e.g. 'create_user').
 *   3. Calls the matching method of the child controller.
 *   4. Turns any error into a clean JSON error message.
 *
 * To create a new controller:
 *   - extend BaseController
 *   - implement routes() to map  HTTP method + action name  =>  method name
 *   - write one method per action; each one ends with JsonResponse::success()
 *     or JsonResponse::error()
 */
abstract class BaseController
{
    /** @var array Request values: $_POST for POST requests, $_GET for GET requests */
    protected $data = [];

    /**
     * Maps requests to methods. Example:
     *   return [
     *       'POST' => ['create_user' => 'create'],
     *       'GET'  => ['get_user'    => 'get'],
     *   ];
     *
     * @return array
     */
    abstract protected function routes();

    /**
     * Runs the controller. Call this from the endpoint file.
     */
    public function handle()
    {
        Auth::requireOperatorForAjax();

        try {
            $httpMethod = $_SERVER['REQUEST_METHOD'];
            $this->data = ($httpMethod === 'POST') ? $_POST : $_GET;

            $action = $this->data['action'] ?? '';
            $routes = $this->routes();

            if (!isset($routes[$httpMethod][$action])) {
                JsonResponse::error('Invalid request.');
            }

            $methodName = $routes[$httpMethod][$action];
            $this->$methodName();
        } catch (InvalidArgumentException $e) {
            // We threw this ourselves: the message is safe to show to the user
            JsonResponse::error($e->getMessage());
        } catch (Throwable $e) {
            // Unexpected problem (e.g. database error): log it, show a generic message
            error_log($e->getMessage());
            JsonResponse::error('Something went wrong. Please try again.');
        }
    }

    /**
     * Reads one value from the request (a default is returned when missing).
     */
    protected function input($key, $default = '')
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Reads several values from the request at once.
     * Missing values become empty strings, so the result is always complete.
     *
     * @param array $keys Names of the request values to read
     * @return array key => value
     */
    protected function only(array $keys)
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->input($key);
        }
        return $values;
    }

    /**
     * Stops with an error when any of the given request values is empty.
     *
     * @param array  $keys    Names of the required values
     * @param string $message Error text shown to the user
     * @throws InvalidArgumentException
     */
    protected function requireFields(array $keys, $message = 'Required fields are missing!')
    {
        foreach ($keys as $key) {
            if (empty($this->input($key))) {
                throw new InvalidArgumentException($message);
            }
        }
    }
}

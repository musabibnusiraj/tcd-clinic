<?php

/**
 * Project Name: Tooth Care - Channeling Appointments
 * Author: Musab Ibn Siraj
 */

/**
 * JsonResponse - sends the answer of an AJAX request back to the browser.
 *
 * Every AJAX answer has the same shape, so the JavaScript can always read it:
 *   { "success": true|false, "message": "...", "data": ... }
 *
 * Both methods stop the script after sending (they call exit).
 */
class JsonResponse
{
    /**
     * Sends a success answer.
     *
     * @param string $message Text to show to the user
     * @param mixed  $data    Optional extra data (e.g. a database row)
     */
    public static function success($message, $data = null)
    {
        self::send(true, $message, $data);
    }

    /**
     * Sends an error answer.
     *
     * @param string $message Text to show to the user
     */
    public static function error($message)
    {
        self::send(false, $message);
    }

    /**
     * Builds the JSON text and prints it.
     */
    private static function send($success, $message, $data = null)
    {
        $response = ['success' => $success, 'message' => $message];

        // Only add "data" when there is something to send
        if ($data !== null) {
            $response['data'] = $data;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

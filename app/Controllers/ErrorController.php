<?php

namespace Controllers;

use Core\Controller;

class ErrorController extends Controller
{
    public function notFound()
    {
        http_response_code(404);
        $this->render('errors/404', [
            'title' => 'Page Not Found',
            'description' => 'The page you are looking for could not be found.',
            'currentUser' => $this->getCurrentUser()
        ]);
    }

    public function unauthorized()
    {
        http_response_code(403);
        $this->render('errors/unauthorized', [
            'title' => 'Access Denied',
            'description' => 'You do not have permission to access this page.',
            'currentUser' => $this->getCurrentUser()
        ]);
    }

    public function serverError()
    {
        http_response_code(500);
        $this->render('errors/500', [
            'title' => 'Server Error',
            'description' => 'Something went wrong on our end. Please try again later.',
            'currentUser' => $this->getCurrentUser()
        ]);
    }
}
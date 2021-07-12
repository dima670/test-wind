<?php

namespace App\Service;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ICNDbClient
{
    public function getCategories()
    {
        $client = new Client();

        $response = $client->get('http://api.icndb.com/categories');
        $responseObj = json_decode($response->getBody(), true);

        $categories = $responseObj['value'];

        return $categories;
    }

    public function getRandomJoke($category)
    {
        $client = new Client();

        $url = "http://api.icndb.com/jokes/random?limitTo=[{$category}]";

        $response = $client->get($url);
        $jokeObj = json_decode($response->getBody(), true);

        $joke = $jokeObj['value']['joke'];

        return $joke;
    }
}

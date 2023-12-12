<?php
use Classes\Example;

$app->redirect('/home', '/');

$app->get('/example', function ($request, $response, $args){
	$data = ["example" => Example::getExample()];
    return $this->get('view')->render($response, 'example.twig', $data);
})->setName('example');

$app->get('/', function ($request, $response, $args){
	$data = [];
    return $this->get('view')->render($response, 'home.twig', $data);
})->setName('home');

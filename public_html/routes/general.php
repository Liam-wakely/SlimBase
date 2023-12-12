<?php
use Classes\Example;

$app->redirect('/', '/home');

$app->get('/example', function ($request, $response, $args){
	$example = Example::getExample();
	$data = ["example" => $example];
    return $this->get('view')->render($response, 'example.twig', $data);
})->setName('example');

$app->get('/home', function ($request, $response, $args){

	//calling DB:
	//DBWrapper::fetch_all("SELECT * FROM test_table");
	$data = [];
    return $this->get('view')->render($response, 'home.twig', $data);
})->setName('home');

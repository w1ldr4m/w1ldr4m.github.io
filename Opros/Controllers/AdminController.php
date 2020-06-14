<?php

class AdminController extends Controller
{
    public function __construct()
    {
        Route::web($this);
    }

    public function index()
    {
        echo $this->render("index");
    }
}
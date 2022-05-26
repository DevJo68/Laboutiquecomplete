<?php

namespace App\Classe;

use App\Entity\Category;


//Cette classe représente un objet qu fait référence à notre recherche
class Search
{
    /**
     * @var string
     */
    public  $string = "";

    /**
     * @var Category[]
     */
    public array $categories = [];

}
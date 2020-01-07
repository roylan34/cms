<?php

require 'core/config.php';
require 'core/database.php';
require 'core/utils.php';
require 'model/Category.php';


$category = new Category();
$action  = Utils::getValue('action');
$data = array(
    'id'       => Utils::getValue('id'),
    'category' => Utils::getValue('category'),
    'status'   => Utils::getValue('status')
);

switch ($action) {
    case 'all-active':
        $res = $category->getActiveCategories();
        print Utils::jsonEncode($res);
        break;
    case 'all':
        $res = $category->getCategories();
        print Utils::jsonEncode($res);
        break;
    case 'edit':
        $res = $category->getCategoryById($data['id']);
        print Utils::jsonEncode($res);
        break;
    default:
        throw new Exception("Action type not found");
        
        break;
}




?>
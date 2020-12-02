<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Include model.php */
include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'ddwt20_week3', 'ddwt20', 'ddwt20');

/* Credintial */
$cred = set_cred("ddwt20","ddwt20");

/* Create Router instance */
$router = new \Bramus\Router\Router();

// Add routes here
$router->mount('/api', function() use ($router,$db) {
    http_content_type("application/json");

    /* GET for reading all series */
    $router->get('/series', function() use($db) {
        // Retrieve and output information
       echo  json_encode(get_series($db));
    });

    /* Create series */
    $router->post('/series', function() use($db) {
        echo  json_encode(add_serie($db,$_POST));
    });

    /* GET for reading individual series */
    $router->get('/series/(\d+)', function($id) use($db) {
        // Retrieve and output information
        echo  json_encode(get_serieinfo($db,$id));
    });

    $router->put('/series/(\d+)', function($id) use($db) {
        $_PUT = array();
        parse_str(file_get_contents('php://input'), $_PUT);
        $serie_info = $_PUT + ["serie_id" => $id];
        echo  json_encode(update_serie($db,$serie_info));
    });

    $router->delete('/series/(\d+)', function($id) use($db) {
        // Retrieve and output information
        echo  json_encode(remove_serie($db,$id));
    });


});

$router->before('GET|POST|PUT|DELETE', '/api/.*', function() use($cred){
    if (!check_cred($cred)){
        echo 'Authentication required.';
        json_encode(http_response_code(401));
        exit();
    }
    echo "Succesfully authenticated";
});

$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    echo "This url is not valid";
});
// ...
/* Run the router */
$router->run();

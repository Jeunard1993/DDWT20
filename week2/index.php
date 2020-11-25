<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'ddwt20_week2', 'ddwt20','ddwt20');

/* Redunadant code */
/* Get Number of Series */
$nbr_series = count_series($db);
$nbr_users = count_users($db);
$right_column = use_template('cards');
$template = Array(
    1 => Array(
        'name' => 'Home',
        'url' => '/DDWT20/week2/' ),
    2 => Array(
        'name' => 'Overview',
        'url' => '/DDWT20/week2/overview/'
    ),
    3 => Array(
        'name' => 'Add',
        'url' => '/DDWT20/week2/add/'
    ),
    4 => Array(
        'name' => 'My Account',
        'url' => '/DDWT20/week2/myaccount/' ),
    5 => Array(
        'name' => 'Register',
        'url' => '/DDWT20/week2/register/'
    )
);


/* Landing page */
if (new_route('/DDWT20/week2/', 'get')) {
    /* Get Number of Series */
    //$nbr_series = count_series($db);

    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT20' => na('/DDWT20/', False),
        'Week 2' => na('/DDWT20/week2/', False),
        'Home' => na('/DDWT20/week2/', True)
    ]);
    $navigation = get_navigation($template,1);

    /* Page content */
    //$right_column = use_template('cards');
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';
    if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }
    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT20/week2/overview/', 'get')) {
    /* Get Number of Series */
    //$nbr_series = count_series($db);

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT20' => na('/DDWT20/', False),
        'Week 2' => na('/DDWT20/week2/', False),
        'Overview' => na('/DDWT20/week2/overview', True)
    ]);
    $navigation = get_navigation($template,2);

    /* Page content */
    //$right_column = use_template('cards');
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_serie_table(get_series($db),$db);

    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }
    /* Choose Template */
    include use_template('main');
}

/* Single Serie */
elseif (new_route('/DDWT20/week2/serie/', 'get')) {

    /* Get series from db */
    $serie_id = $_GET['serie_id'];

    $serie_info = get_serieinfo($db, $serie_id);
    $display_buttons = check_permissions($serie_info['user']);

    /* Page info */
    $page_title = $serie_info['name'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT20' => na('/DDWT20/', False),
        'Week 2' => na('/DDWT20/week2/', False),
        'Overview' => na('/DDWT20/week2/overview/', False),
        $serie_info['name'] => na('/DDWT20/week2/serie/?serie_id='.$serie_id, True)
    ]);
    $navigation = get_navigation($template,2);

    /* Page content */
    //$right_column = use_template('cards');
    $page_subtitle = sprintf("Information about %s", $serie_info['name']);
    $page_content = $serie_info['abstract'];
    $nbr_seasons = $serie_info['seasons'];
    $creators = $serie_info['creator'];
    $added_by = get_name($db,$serie_info['user']);

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }
    /* Choose Template */
    include use_template('serie');
}

/* Add serie GET */
elseif (new_route('/DDWT20/week2/add/', 'get')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT20/week2/login/');
    }
    /* Get Number of Series */
    //$nbr_series = count_series($db);

    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT20' => na('/DDWT20/', False),
        'Week 2' => na('/DDWT20/week2/', False),
        'Add Series' => na('/DDWT20/week2/new/', True)
    ]);
    $navigation = get_navigation($template,3);

    /* Page content */
    //$right_column = use_template('cards');
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT20/week2/add/';
    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('new');
}

/* Add serie POST */
elseif (new_route('/DDWT20/week2/add/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT20/week2/login/');
    }
    /* Add serie to database */
    $feedback = add_serie($db, $_POST);
    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT20/week2/add/?error_msg=%s', json_encode($feedback)));
}

/* Edit serie GET */
elseif (new_route('/DDWT20/week2/edit/', 'get')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT20/week2/login/');
    }
    /* Get Number of Series */
    //$nbr_series = count_series($db);

    /* Get serie info from db */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_serieinfo($db, $serie_id);

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT20' => na('/DDWT20/', False),
        'Week 2' => na('/DDWT20/week2/', False),
        sprintf("Edit Series %s", $serie_info['name']) => na('/DDWT20/week2/new/', True)
    ]);
    $navigation = get_navigation($template,0);

    /* Page content */
    //$right_column = use_template('cards');
    $page_subtitle = sprintf("Edit %s", $serie_info['name']);
    $page_content = 'Edit the series below.';
    $submit_btn = "Edit Series";
    $form_action = '/DDWT20/week2/edit/';

    /* Choose Template */
    include use_template('new');
}

/* Edit serie POST */
elseif (new_route('/DDWT20/week2/edit/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT20/week2/login/');
    }
    /* Update serie to database */
    $feedback = update_serie($db, $_POST);
    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT20/week2/serie/?serie_id=%s?error_msg=%s',$_POST["serie_id"] , json_encode($feedback)));
}

/* Remove serie */
elseif (new_route('/DDWT20/week2/remove/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT20/week2/login/');
    }
    /* Delete serie to database */
    $serie_id = $_POST['serie_id'];
    $feedback = remove_serie($db, $serie_id);
    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT20/week2/overview/?error_msg=%s', json_encode($feedback)));
}
/* Get account */
elseif (new_route('/DDWT20/week2/myaccount/', 'get')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT20/week2/login/');
    }

    /* Get Number of Series */
    $user = get_name($db,  1);

    /* Page info */
    $page_title = 'My Account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT20' => na('/DDWT20/', False),
        'Week 2' => na('/DDWT20/week2/', False),
        'My Account' => na('/DDWT20/week2/myaccount/', True)
    ]);
    $navigation = get_navigation($template,4);

    /* Page content */
    //$right_column = use_template('cards');
    $page_subtitle = 'Account Overview';
    $page_content = 'Here you can see detail of your account';

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Choose Template */
    include use_template('account');
} elseif (new_route('/DDWT20/week2/register/', 'get')) {
    /* Page info */
    $page_title = 'Register';
    $breadcrumbs = get_breadcrumbs([
        'DDWT20' => na('/DDWT20/', False),
        'Week 2' => na('/DDWT20/week2/', False),
        'Register' => na('/DDWT20/week2/register/', True)
    ]);
  $navigation = get_navigation($template, 5);

  /* Page content */
 $page_subtitle = 'Register on Series Overview!';

 /* Get error msg from POST route */
 if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }

 /* Choose Template */
 include use_template('register');

} elseif (new_route('/DDWT20/week2/register/', 'post')) {
    /* Register user */
    $error_msg = register_user($db, $_POST);
    /* Redirect to homepage */
    redirect(sprintf('/DDWT20/week2/register/?error_msg=%s', json_encode($error_msg)));

} elseif (new_route('/DDWT20/week2/login/', 'get')) {
    /* Check if logged in */
    if ( check_login() ) {
        redirect('/DDWT20/week2/myaccount/');
    }
    /* Page info */
    $page_title = 'Login';
    $breadcrumbs = get_breadcrumbs([
        'DDWT20' => na('/DDWT20/', False),
        'Week 2' => na('/DDWT20/week2/', False),
    ]);
    $navigation = get_navigation($template,0);

    /* Page content */
    $page_subtitle = 'Use your username and password to login';
    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }
    /* Choose Template */
    include use_template('login');
} elseif (new_route('/DDWT20/week2/login/', 'post')) {
    login_user($db,$_POST);
    $feedback = login_user($db,$_POST);

    redirect(sprintf('/DDWT20/week2/login/?error_msg=%s', json_encode($feedback)));
}

 elseif (new_route('/DDWT20/week2/logout/', 'get')) {
    /*Log out*/
     logout_user();

}
else {
    http_response_code(404);
}

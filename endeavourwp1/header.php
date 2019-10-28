<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Ausculta Ltd">
  <link href='https://fonts.googleapis.com/css?family=Nunito Sans' rel='stylesheet'>
  <link rel="icon" href="<?php get_theme_file_uri( 'images/favicon.ico' ); ?>">
  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <div class="container">
    <div class='row'>
      <div class='col-12 align-items-end'> <!-- menu header -->
        <nav class="nav navbar-nav navbar-expand-md navbar-light bg-transparent mr-sm-2" role="navigation">
          <div class='container'>
            <?php
              wp_nav_menu( array(
                'theme_location'    => 'menuheader',
                'depth'             => 2, // 1 = no dropdowns, 2 = with dropdowns
                'container'         => 'div',
                'container_class'   => 'collapse navbar-collapse',
                'container_id'      => 'menuheader-navbar-collapse-1',
                'menu_class'        => 'nav navbar-nav mr-sm-2',
                'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
                'walker'            => new WP_Bootstrap_Navwalker(),
                )
              );
            ?>
          </div> <!-- /container -->
        </nav> <!-- /menuheader -->
      </div> <!-- /col -->
    </div> <!-- /row -->
    <div class='row'> <!-- Logo bar -->
      <div class='col-3 align-self-start'>
        <p><a class="brand" href="<?php echo site_url(); ?>"><img style="padding: 5; width: 108px; height: 102px; float:center; text-align: none;" src="/assets/media/logo.png" alt="<?php bloginfo('name'); ?>" width="108" height="102" /></a></p>
      </div> <!-- /col -->
      <div class='col-6 align-self-center'>
        <h2 style="text-align: center;" border=0>Endeavour Explorer Scout Unit</h2>
        <p class="text-right">By Land... By Sea... By Air...</p>
      </div> <!-- /col -->
      <div class='col-3 align-items-end'>
        <a href="https://www.scouts.org.uk/" border=0><img style="padding: 5; width: 202px; height: 62px; float:none; text-align: center;" src="/assets/media/scouts.jpg" alt="Scouts - Be Prepared" width="202" height="22" /></a>
        <br />
        <a href="https://www.scouts.org.uk/" border=0><img style="padding: 5; width: 202px; height: 22px; float:none; top-margin:10px; text-align: center;" src="/assets/media/explorers.png" alt="Explorers" width="202" height="22" /></a>
      </div> <!-- /col -->
    </div> <!-- row -->
    <div class='row'> <!-- menumain -->
      <div class='col-12 align-self-start'>
        <nav class="nav navbar-nav navbar-expand-md navbar-light bg-white mr-sm-2" role="navigation">
          <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menumain-navbar-collapse-1" aria-controls="menumain-navbar-collapse-1" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <!-- <a class="navbar-brand" href="#"><img src="/assets/media/old-fleur-de-lys.png" width="30" height="30" alt="">&nbsp;<?php bloginfo('name'); ?></a> -->
            <?php 
              $menu_args = array(
                'theme_location'  => 'menumain',
                'depth'           => 2, // 1 = no dropdowns, 2 = with dropdowns
                'container'       => 'div',
                'container_class' => 'collapse navbar-collapse',
                'container_id'    => 'menumain-navbar-collapse-1',
                'menu_class'      => 'nav navbar-nav navbar-expand-md navbar-light bg-white mr-auto',           
                'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
                'walker'          => new WP_Bootstrap_Navwalker(),
                'menu'            => 'menu_visitors',
              );
              if (is_user_logged_in()) { 
                $menu_args['menu'] = 'menu_users';
              } 
              wp_nav_menu( $menu_args );
            ?>
          </div> <!-- /container -->
        </nav> <!-- /menumain -->
      </div> <!-- /col -->
    </div> <!-- /row -->
    <div class='row'> <!-- quote area -->
      <div class='col-12 align-self-center' id="quotearea">
      </div> <!-- /col -->
    </div> <!-- /row quote area -->
 
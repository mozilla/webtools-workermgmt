<?php
$breadcrumbs = isset($breadcrumbs)?$breadcrumbs:array(array('home'));
$logout_link = isset ($logout_link) ? $logout_link : HTML::anchor('authenticate/logout', "Logout");
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?php echo HTML::chars($title) ?></title>
        <?php 
            echo HTML::style('http://bugzilla.mozilla.org/skins/standard/global.css');
            echo HTML::style('http://bugzilla.mozilla.org/skins/custom/global.css');
            echo HTML::style('media/css/main.css');
            echo HTML::style('media/css/redmond/jquery-ui-1.7.2.custom.css');
        ?>
        <?php echo isset($css_extra)?$css_extra:''; ?>
        <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
        <link rel="Top" href="https://bugzilla.mozilla.org/" />     
    </head>
    <body>
        <div id="container">
            <div id="mozilla-org"><a href="http://www.mozilla.org/" title="Visit mozilla.org">Visit mozilla.org</a></div>
            <div id="header">
                <h1><?php echo isset($main_title) ? HTML::chars($main_title) : "Mozilla Corporation - Forms" ?></h1>
            </div>
            <div id="sub_header">
                <ul class="breadcrumb">
                <?php while ($crumb = array_shift($breadcrumbs)) { ?>
                    <li><?php echo isset($crumb[0]) ? $crumb[0] : HTML::anchor(current($crumb), key($crumb)) ?></li>
                <?php } ?>
                </ul>
                <div id="site_nav"><?php echo $logout_link; ?></div>
            </div>
            <?php echo client::messageFetchHtml(); ?>
            <?php echo $content ?>

        </div>
        <script type="text/javascript">var URL_BASE = "<?php echo URL::base(); ?>";</script>
        <?php
            echo HTML::script('media/js/jquery-1.4.2.min.js');
            echo HTML::script('media/js/jquery-ui-1.7.2.custom.min.js');
            echo HTML::script('media/js/webtools.js');
        ?>
        <?php echo isset($js_extra)?$js_extra:''; ?>
    </body>
</html>
<?php function main_template($content) { ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A layout example that shows off a responsive email layout.">

    <title>Twirper</title>

    <link rel="stylesheet" href="https://yui-s.yahooapis.com/pure/0.6.0/pure-min.css">
    <link rel="stylesheet" href="/css/custom.css"> 
    <!--[if lt IE 9]>
        <script src="http://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.js"></script>
    <![endif]-->
</head>
<body>
<div id="layout" class="content pure-g">
    <div id="nav" class="pure-u-1">
        <div class="nav-inner">
            <div class="pure-menu-horizontal">
                <ul class="pure-menu-list">
                    <li class="pure-menu-item"><a href="index.php" class="pure-menu-link">Home</a></li>
                    <li class="pure-menu-item"><a href="notifications.php" class="pure-menu-link">Notifications</a></li>
                    <li class="pure-menu-item-separator"></li>
                    <li class="pure-menu-item">
                        <div class="search-bar">
                            <form class="pure-form" action="search.php">
                                <input type="text" class="pure-input-rounded" placeholder="Search"/>
                            </form>
                        </div>
                    </li>
                </ul>                
            </div>
        </div>
    </div>
<?php $content(); ?>
</div>

<script src="https://yui-s.yahooapis.com/3.17.2/build/yui/yui-min.js"></script>

<script>
    YUI().use('node-base', 'node-event-delegate', function (Y) {

        var menuButton = Y.one('.nav-menu-button'),
            nav        = Y.one('#nav');

        // Setting the active class name expands the menu vertically on small screens.
        menuButton.on('click', function (e) {
            nav.toggleClass('active');
        });

        // Your application code goes here...

    });
</script>
</body>
</html>

<?php } ?>

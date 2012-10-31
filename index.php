<?php

function get( $param )
{
    return isset( $_GET[$param] )
        ? $_GET[ $param ]
        : '';
}

function classesFor( $line )
{
    $classes = array();

    if ( substr($line,0,1) == '+' ) {
        $classes[] = 'add';
    }

    if ( substr($line,0,1) == '-' ) {
        $classes[] = 'remove';
    }

    return implode( ' ', $classes );
}

function filesFrom( $lines ) 
{
    $file = false;
    $files = array();
    $index = -1;
    $totalLines = count( $lines );

    while ( ++$index < $totalLines ) {

        $line = $lines[ $index ];

        if ( preg_match('/Index: (.*)/',$line,$matches) ) {
            if ( $file ) { $files[] = $file; }
            $file = array(
                'name' => $matches[ 1 ],
                'old' => $lines[ $index + 1 ],
                'new' => $lines[ $index + 2 ],
                'lines' => array()
            );
            $index += 4;
        }

        else {
            $file[ 'lines' ][] = $line;
        }

    }

    if ( $file ) { $files[] = $file; }

    return $files;
}

$url1 = get( 'url1' );
$url2 = get( 'url2' );

?>
<!DOCTYPE html>
<html>
<head>
<title>SVN Differ</title>

<link type="text/css" rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
<link type="text/css" rel="stylesheet" href="css/default.css" />

</head>

<div class="container">
    <div class="row">
        <div class="span12">
            <h1>SVN Differ</h1>
            <form class="form-horizontal" method="get" action="">
                <div class="control-group">
                    <label class="control-label">URL 1:</label>
                    <div class="controls">
                        <input type="text" name="url1" value="<?php echo htmlentities($url1) ?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">URL 2:</label>
                    <div class="controls">
                        <input type="text" name="url2" value="<?php echo htmlentities($url2) ?>" />
                    </div>
                </div>
                <input type="submit" class="btn btn-primary" value="View Changes" />
            </form>
        </div>
    </div>

    <?php if ( $url1 && $url2 ) { ?>

        <div class="row">
            <div class="span12">
                <?php

                    $diffcmd = sprintf( "svn diff '%s' '%s'", $url1, $url2 );

                    exec( $diffcmd, $lines );

                    foreach ( filesFrom($lines) as $file ) {

                        echo '<h2>' . $file['name'] . '</h2>';
                        echo '<div class="code">';

                        foreach( $file['lines'] as $line ) {
                            echo sprintf(
                                '<pre class="%s">%s</pre>',
                                classesFor( $line ),
                                htmlentities( $line )
                            );
                        }

                        echo '</div>';

                    }

                ?>
            </div>
        </div>
    <?php } ?>

    <div class="row">
        <div class="span12">
            <div class="footer">Copyleft</div>
        </div>
    </div>

</div>

</body>
</html>


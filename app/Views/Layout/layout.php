<!DOCTYPE html>
<html>
    <head>
        <title><?php if (isset($title)) : echo $this->escape($title); endif; ?></title>
    </head>
    <body>
        <?php echo $_content; ?>
    </body>
</html>

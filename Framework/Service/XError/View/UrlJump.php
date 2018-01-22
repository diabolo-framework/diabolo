<?php 
$vars = get_defined_vars();
$config = $vars['config'];
$url = $vars['url'];
$params = $vars['params'];
?>
<html>
  <head>
    <title>Jumpping...</title>
    <?php if ( 'get' === strtolower($config['method']) ) : ?>
    <meta http-equiv="refresh" content="0; url=<?php echo $url;?>" />
    <?php endif; ?>
  </head>
  <body>
    <?php if ( 'post' === strtolower($config['method']) ) : ?>
    <form action="<?php echo $url;?>" method="post" id="form">
      <?php foreach ( $params as $key => $value ) : ?>
      <input type="hidden" name="<?php echo $key; ?>"  value="<?php echo $value; ?>">
      <?php endforeach; ?>
    </form>
    <script>document.getElementById('form').submit()</script>
    <?php endif; ?>
  </body>
</html>

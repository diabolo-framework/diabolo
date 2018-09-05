<?php 
$vars = get_defined_vars();
$url = $vars['url'];
$params = $vars['params'];
?>
<html>
  <head>
    <title>Jumpping...</title>
    <?php if ( 'get' === strtolower($this->method) ) : ?>
    <meta http-equiv="refresh" content="0; url=<?php echo $url;?>" />
    <?php endif; ?>
  </head>
  <body>
    <?php if ( 'post' === strtolower($this->method) ) : ?>
    <form action="<?php echo $url;?>" method="post" id="form">
      <?php foreach ( $params as $key => $value ) : ?>
      <input type="hidden" name="<?php echo $key; ?>"  value="<?php echo $value; ?>">
      <?php endforeach; ?>
    </form>
    <script>document.getElementById('form').submit()</script>
    <?php endif; ?>
  </body>
</html>

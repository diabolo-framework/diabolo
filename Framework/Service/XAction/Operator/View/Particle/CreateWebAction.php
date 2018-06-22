<?php 
$vars = get_defined_vars();
$form = $vars['form'];
$success = $vars['success'];
$message = $vars['message'];
?>
<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2> Create New Web Action </h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content row">
        <div class="col-md-offset-3 col-sm-offset-3 col-xs-offset-3 col-md-4 col-sm-4 col-xs-4">
          <br>
          <form method="post" action="/">
            <label> Name <span class="required">*</span> </label>
            <input 
              type="text" 
              required="required" 
              class="form-control" 
              name="form[name]"
              value="<?php echo @$form['name']; ?>"
              placeholder="My/DemoActionName"
            >
            
            <br>
            <label> Parameters <span class="required">*</span> </label>
            <input 
              type="text" 
              required="required" 
              class="form-control" 
              name="form[name]"
              value="<?php echo @$form['params']; ?>"
              placeholder="param1, param2, param3 ..."
            >
            
            <br>
            <label> Layout <span class="required">*</span> </label>
            <input 
              type="text" 
              required="required" 
              class="form-control" 
              name="form[name]"
              value="<?php echo @$form['layout']; ?>"
              placeholder="Name/Of/Layout"
            >
            
            <br>
            <label> View Name <span class="required">*</span> </label>
            <input 
              type="text" 
              required="required" 
              class="form-control" 
              name="form[name]"
              value="<?php echo @$form['view']; ?>"
              placeholder="Name/Of/ParticleView"
            >
          
            <div class="ln_solid"></div>
            <div class="form-group">
              <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <button type="submit" class="btn btn-success">Create</button>
              </div>
            </div>
          </form>
        </div>
        <div class="col-md-offset-2 col-sm-offset-2 col-xs-offset-2 col-md-8 col-sm-8 col-xs-8">
          <br>
          <?php if ( true === $success ) : ?>
          <div class="alert alert-success alert-dismissible fade in" role="alert">
            <strong>Success!</strong><br> 
            The module has been created at : <br>
            <?php echo $module['path']; ?> <br>
            
            <br>
            <a class="btn btn-round btn-primary" href="javascript:parent.location.reload();">刷新</a>
          </div>
          <?php elseif ( false === $success) : ?>
          <div class="alert alert-danger alert-dismissible fade in" role="alert">
            <strong>Error!</strong><br> 
            <?php echo $message; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
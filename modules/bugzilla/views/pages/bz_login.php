
    <?php echo form::open('/authenticate/login',array('accept-charset'=>'utf-8')); ?>

      <?php echo form::hidden('form_token', $_SESSION['form_token'] = uniqid()); ?>
      <div class="input">
        <?php echo form::label('bz_username', 'Bugzilla E-mail Address:'); ?>
        <?php echo form::input('bz_username=id', $bz_username); ?>
        <?php client::validation('bz_username'); ?>
      </div>
      <div class="input">
        <?php echo form::label('bz_password', 'Bugzilla Password:'); ?>
        <?php echo form::password('bz_password=id', $bz_password); ?>
        <?php client::validation('bz_password'); ?>
      </div>
      <div>
        <input class="submit" type="submit" name="submit" value="Login" />
      </div>
    <?php echo form::close(); ?>
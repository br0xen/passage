<!DOCTYPE html>
<html>
  <head>
    <title>PasSage - The Password Sage</title>
    <?php foreach($stylesheets as $css): ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $css; ?>">
    <?php endforeach; ?>
    <meta name="viewport" content="width=device-width">
  </head>
  <body>
    <div class="box shade">
      <div class="header">
        <h1>PasSage - The Password Sage</h1>
        <?php if(isset($pw)): ?>
        <div class="clear"></div>
        <div class="password_display form_field">
          <input id="password_display" class="form_input" type="text" value="<?php echo $pw;?>" readonly="readonly" />
          <div class="clear"></div>
        </div>
        <?php endif; ?>
      </div>
      <div class="clear"></div>
      <div class="content">
        <div class="col_50 box big_left">
          <form id="gen_form" method="POST" action="/gen">
            <div class="form_field">
              <label class="form_label" for="pin">Pin:</label>
              <input class="form_input" id="pin" type="password" name="pin" value="<?php echo (isset($pin))?$pin:'';?>"/>
            </div>
            <div class="form_field">
              <label class="form_label" for="checksum">Checksum:</label>
              <input class="form_input" id="checksum" type="text" name="checksum" value="" readonly="readonly" tabindex="-1"/>
            </div>
            <div class="form_field">
              <label class="form_label" for="door_id">Door ID:</label>
              <input class="form_input" id="door_id" type="text" name="door_id" <?php echo (isset($pin))?'autofocus':'';?> />
            </div>
            <div class="form_field">
              <label class="form_label" for="length">Length:</label>
              <input class="form_input" id="length" type="text" name="length" value="<?php echo (isset($length))?$length:'12';?>" />
            </div>
            <div class="form_field">
              <label class="form_label" for="remember">Remember Doors?*</label>
              <input class="form_checkbox" type="checkbox" name="remember" <?php echo (isset($remember_doors)&&!empty($remember_doors))?'checked="checked"':'';?>>
            </div>
<!--
            <div class="hide box smaller" id="advanced_div">
              <div class="header">
                <h4>Password Options</h4>
                <div class="clear"></div>
              </div>
              <div class="content">
                <div class="form_field">
                  <label class="form_label" for="lowers">Lowercase?</label>
                  <input class="form_checkbox" type="checkbox" name="lowers" <?php echo (isset($lowers)&&!empty($lowers))?'checked="checked"':'';?>>
                </div>
                <div class="form_field">
                  <label class="form_label" for="capitals">Capitals?</label>
                  <input class="form_checkbox" type="checkbox" name="capitals" <?php echo (isset($capitals)&&!empty($capitals))?'checked="checked"':'';?>>
                </div>
                <div class="form_field">
                  <label class="form_label" for="numbers">Numbers?</label>
                  <input class="form_checkbox" type="checkbox" name="numbers" <?php echo (isset($numbers)&&!empty($numbers))?'checked="checked"':'';?>>
                </div>
                <div class="form_field">
                  <label class="form_label" for="symbols">Symbols?</label>
                  <input class="form_checkbox" type="checkbox" name="symbols" <?php echo (isset($symbols)&&!empty($symbols))?'checked="checked"':'';?>>
                </div>
              </div>
            </div>
-->
            <div class="form_buttons">
              <button class="primary" type="submit">Generate</button>
            </div>
            <div class="clear"></div>
          </form>
        </div>
        <div class="col_50 box big_right">
        <?php if(count($door_history) > 0): ?>
          <div class="header">
            <h3>Door History</h3>
            <div class="form_buttons">
              <form action="/clearHistory">
                <button class="warning" type="submit">Clear History</button>
              </form>
            </div>
            <div class="clear"></div>
          </div>
          <?php foreach($door_history as $a_door): ?>
          <div class="small_box door_history_entry <?php echo ($a_door == $door_id)?'active':'';?>" data-door_id="<?php echo $a_door; ?>"><?php echo $a_door; ?></div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="header">
            <h3>No History</h3>
          </div>
          <div class="clear"></div>
        <?php endif; ?>
        </div>
        <div class="bottom clear">
          * If 'Remember Doors' is set, door ids will be stored in a cookie. Pin numbers are not stored anywhere.
        </div>
      </div>
    </div>
    <?php foreach($scripts as $js): ?>
    <script src="<?php echo $js;?>"></script>
    <?php endforeach; ?>
  </body>
</html>

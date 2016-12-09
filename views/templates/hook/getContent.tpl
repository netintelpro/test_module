<fieldset>
  <h2>Weather Module Configuration</h2>
  <div class="panel" style="width: 50%;">
    <div class="panel-heading">
      <legend><img src="../img/admin/cog.gif" alt="" width="16" />Configuration</legend>
    </div>
    <form action="" method="post">
      <div class="form-group clearfix">
        <label class="col-lg-3">Enter Zipcode:</label>
        <div class="col-lg-9">
         
          <input type="text" id="zipcode" name="zipcode"  value="{$zipcode}"/>
          
        </div>
      </div>


      <div class="panel-footer">
        <input class="btn btn-default pull-right" type="submit" name="weather_form_button" value="Save" />
      </div>
    </form>
  </div>
</fieldset>
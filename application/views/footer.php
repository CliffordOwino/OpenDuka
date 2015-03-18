  </div>
</div>
<!--end of container -->
    <!-- Footer
    ================================================== -->
<!--top footer -->
<div>
<div class= "container col-md-12 col-lg-12 pad">
<div class = "row col-md-4 col-lg-4 marg" id="partnersoi">
  <h3>Sources </h3>
        <p>
        Our database contains information on people, companies and organisations, as well as their linkages at specified periods of time.
        While we make every attempt to make this information as accurate as possible, we take no responsibility for its authenticity.
        The current information is derived from the Kenya Gazette, Handsards and procurement websites. 
        We will be incorporating more information from different sources soon. 
      </p>
</div>
<div class = "row col-md-4 col-lg-4">
    <div class = "row col-md-12 marg" id="partners">
      <h3> Developed by</h3>
      <div class="oi_icon">
        <img src="<?php echo base_url(); ?>assets/img/oi-grey-lg.png" alt="oi" align="middle"></img>
      </div>
    </div>
    <div class="col-md-12 marg" id="partner-logos">
      <div id="partners"><h4> In partnership with</h4></div>
      <div class=" atti_icon col-md-6 ">
        <img src="<?php echo base_url(); ?>assets/img/atti-logo.jpg" alt="atti" align="right"></img>
      </div>
      <div class="klr_icon col-md-6">
      <img src="<?php echo base_url(); ?>assets/img/klr-logo.png" alt="klr" align="right"></img>
    </div>
    </div>
  
</div>
<div class = "row col-md-4 col-lg-4 marg" id="partners" >
<!--?php/*
//If the form is submitted
if(isset($_POST['submit'])) {

  //Check to make sure that the name field is not empty
  if(trim($_POST['cname']) == '') {
    $hasError = true;
  } else {
    $name = trim($_POST['cname']);
  }

  //Check to make sure sure that a valid email address is submitted
  if(trim($_POST['email']) == '')  {
    $hasError = true;
  } else if (!filter_var( trim($_POST['email'], FILTER_VALIDATE_EMAIL ))) {
    $hasError = true;
  } else {
    $email = trim($_POST['email']);
  }


  

  //If there is no error, send the email
  if(!isset($hasError)) {
    $emailTo = 'kevinkavaiw@gmail.com'; // Put your own email address here
    $subject = 'Open Duka Contact Form';
    $headers = 'From: Open Duka <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
    $body = "Name: $name \n\nEmail: $email  \n\nMessage:\n Hi, am subscribing to Open Duka newsletters";
    

    mail($emailTo, $subject, $body, $headers);
    $emailSent = true;
  }
}*/
?-->
  <form role="form" method="post" action="<?php echo base_url() . index_page(); ?>/email" id="mailinglist">
    <h3>Mailing List </h3>
    <p>Open Duka is a work in progress (and a labour of love) for us and we shall continue to add new features and data. Sign up here if you want to keep in touch with the progress we make.</p>
      <input type="text" name="cname" value="" placeholder="Enter Name" class="form-control" /></br>
      <input type="text" name="email" value="" placeholder="Enter email address" class="form-control" /></br>
      <input type="submit" name="submit" value="Submit" class="btn btn-primary" />
    </form>
</div>

</div>

<!--end of top-footer-->


      <div id="footer" class="navbar-inverse row">
        <div class="footer-links pull-left">
          <a href="<?php echo base_url() . index_page(); ?>/about">About Us&nbsp;</a>|
<!--
          <a>&nbsp;Terms&nbsp;</a>|
-->
          <a href="https://github.com/OpenInstitute/OpenDuka/">&nbsp;Get the code&nbsp;</a>|
<!--
          <a>&nbsp;Support us</a>
-->
        </div>

        <div class="copyright col-md-7 col-lg-7">
          <p>            
            <a rel="license" href="http://creativecommons.org/licenses/by/4.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/4.0/80x15.png" /></a><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title"> Open Duka</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="http://openinstitute.com" property="cc:attributionName" rel="cc:attributionURL">Open Institute</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/4.0/">Creative Commons Attribution 4.0 International License</a>
          </p>
        </div>

        <div class="social-media pull-right">
          <a href="https://www.facebook.com/OpenDuka"> 
            <img src="<?php echo base_url(); ?>assets/img/facebook.png">
          </a>
           <a href="https://www.twitter.com/OpenDuka">
            <img src="<?php echo base_url(); ?>assets/img/twitter.png">
          </a>
          <a href="https://plus.google.com/103250451649972917771/">
            <img src="<?php echo base_url(); ?>assets/img/googleplus.png">
          </a>

          <a href="https://github.com/OpenInstitute/OpenDuka/">
            <img src="<?php echo base_url(); ?>assets/img/github.png">
          </a>
        </div>
        <!-- <p>Page rendered in <strong>{elapsed_time}</strong> seconds</p> -->
      </div><!-- .footer -->
</div>

</div>

</div>
    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
    <script src="<?php echo base_url();?>assets/js/jquery.js"></script>
    <script src="<?php echo base_url();?>assets/js/bootstrap.js"></script>


    <script src="<?php echo base_url();?>assets/js/holder/holder.js"></script>
    <script src="<?php echo base_url();?>assets/js/google-code-prettify/prettify.js"></script>

    <script src="<?php echo base_url();?>assets/js/application.js"></script>
    <script src="<?php echo base_url();?>assets/js/ajaxfileupload.js"></script>

<script>
 (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
 (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
 m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
 })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

 ga('create', 'UA-34157316-3', 'openduka.org');
 ga('send', 'pageview');

</script>

  </body>
</html>

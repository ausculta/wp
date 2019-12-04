    <hr>
    <div class='row'> <!-- footer -->
      <div class='col-12' id="footer">
        <footer class="blog-footer">
          <?php if ( is_active_sidebar( 'footer-copyright-text' ) ) { dynamic_sidebar( 'footer-copyright-text' ); } ?>
          <p style="padding:0px;font-size:small" class="text-center">Copyright © 2017-19 Endeavour Explorer Scouts Unit (Wiltshire North), All rights reserved.
          <br />For more information: <a href="mailto:infos -AT- endeavouresu.uk">email: infos - AT - endeavouresu.uk</a>.
          <br />It is the <a href="http://members.scouts.org.uk/supportresources/search?cat=299,304" target="_blank">policy of the Scout Association to safeguard the welfare of all members</a>. For further information and online safety advice visit <a href="http:/getsafeonline.org/" target="_blank">getsafeonline.org</a> and <a href="http://thinkuknow.co.uk/" target="_blank">thinkuknow.co.uk</a>.
          <br />The Scout Association is a Registered Charity: 306101 (England and Wales) / SC038437 (Scotland)</p>
          <?php wp_footer(); ?>
        </footer>
      </div> <!-- /col -->
    </div> <!-- /row -->
  </div><!-- /container -->
  <script src="https://kit.fontawesome.com/b3babe9ce8.js" crossorigin="anonymous"></script>
  <script type='text/javascript'>
    jQuery(document).ready(function () {
      jQuery('.navbar .dropdown-item').on('click', function (e) {
          var $el = jQuery(this).children('.dropdown-toggle');
          var $parent = $el.offsetParent(".dropdown-menu");
          jQuery(this).parent("li").toggleClass('open');

          if (!$parent.parent().hasClass('navbar-nav')) {
              if ($parent.hasClass('show')) {
                  $parent.removeClass('show');
                  $el.next().removeClass('show');
                  $el.next().css({"top": -999, "left": -999});
              } else {
                  $parent.parent().find('.show').removeClass('show');
                  $parent.addClass('show');
                  $el.next().addClass('show');
                  // $el.next().css({"top": $el[0].offsetTop, "left": $parent.outerWidth() - 4});
                  $el.next().css({"left": $parent.outerWidth() - 4});
                }
              // e.preventDefault();
              e.stopPropagation();
          }
      });
      jQuery('.navbar .dropdown').on('hidden.bs.dropdown', function () {
        jQuery(this).find('li.dropdown').removeClass('show open');
        jQuery(this).find('ul.dropdown-menu').removeClass('show open');
      });
    });
  </script>
</body>
</html>
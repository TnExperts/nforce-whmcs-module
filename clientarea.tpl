{literal}
<link rel="stylesheet" type="text/css" media="all" href="modules/servers/online/css/online.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
{/literal}
<h1 id="cntrlhead">Control Panel</h1>
<hr>
<ul class="nav nav-pills nav-tabs">
                          <li {if $get.b eq "network" || $get.b eq ""}class="active"{/if} ><a href="clientarea.php?action=productdetails&id={$srvid}&b=network">Network</a></li>
              <li {if $get.b eq "power"}class="active"{/if} ><a href="clientarea.php?action=productdetails&id={$srvid}&b=power">Power</a></li>

            </ul>
                        <div class="content">
                        <!-- Error handling message -->
<br />
                        {$message}
<br />
                        {if $get.b eq "network" || $get.b eq ""}
                        {$info}
                               	<h5>Bandwidth Graph (Billing Month)</h5>
                                {$bwimage}
                                <br /><br /><br /><br />
                                {$bwnumber}
                        {elseif $get.b eq "power"}
                        {$info}
            {/if}
                        </div>
                        {literal}
                        <script>
                        $(document).ready(function () {
                $(window).scrollTop($('#cntrlhead').offset().top);
                                });
                                $(function(){
   $(".alert-message").delegate("a.close", "click", function(event) {
        event.preventDefault();
        $(this).closest(".alert-message").fadeOut(function(event){
            $(this).remove();
        });
    });
   });
   var fade_out = function() {
  $(".alert-message").fadeOut().empty();
}

setTimeout(fade_out, 5000);
                        </script>
                        {/literal}
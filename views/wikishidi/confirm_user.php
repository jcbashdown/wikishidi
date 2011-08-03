<div id="content">
	<div class="content-bg">

		<!-- start block -->
		<div class="big-block">
			<h1>Get Alerts</h1>

			<?php //if($show_mobile == TRUE) { ?>
			<!-- Mobile Alert TODO add back in from alerts if wanted -->

			<!-- / Mobile Alert -->
			<?php //} ?>
<!--TODO lang stuff-->
			<!-- Email-->
			<div class="green-box">
				<?php
				if ($email)
				{
					echo "<h3>".Kohana::lang('alerts.email_ok_head')."</h3>";
				}
				?>
<!--Here-->
				<div class="alert_response">
					<?php
					if ($email)
					{
						echo Kohana::lang('alerts.email_alert_request_created')."<u><strong>".
							$alert_email."</strong></u>.".
							Kohana::lang('alerts.verify_code');
					}
					?>
					<div class="alert_confirm">
						<div class="label">
							<u><?php echo Kohana::lang('alerts.email_code'); ?></u>
						</div>
						<?php
						print form::open('/wikishidi/verify');
						print "Verification Code:<BR>".form::input('alert_code', '', ' class="text"')."<BR>";
						print "Email Address:<BR>".form::input('alert_email', $email, ' class="text"')."<BR>";
						print form::submit('button', 'Confirm My Alert Request', ' class="btn_submit"');
						print form::close();
						?>
					</div>
				</div>
			</div>
			<!-- / Email-->
		</div>
		<!-- end block -->
	</div>
</div>
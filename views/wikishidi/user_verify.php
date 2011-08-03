<div id="content">
	<div class="content-bg">
		<!-- start block -->
		<div class="big-block">
			<!-- green-box/ red-box depending on verification result -->
			<?php
			// SWITCH based on the value of the $errno
			switch ($errno)
			{
				// IF the code provided was not found ...
				case ER_CODE_NOT_FOUND:
				?>
					<div class="red-box">
						<div class="alert_response">
                                                    Sorry! The code entered was not found.
            				<?php //TODO echo Kohana::lang('alerts.code_not_found'); ?>
						</div>
					</div>
					<?php
					break;
				// IF the code provided means the alert has already been verified ...
				case ER_CODE_ALREADY_VERIFIED:
				?>
					<div class="red-box">
						<div class="alert_response" align="center">
                                                        Sorry! The code entered has already been verified.
							<?php //TODOecho Kohana::lang('alerts.code_already_verified'); ?>
						</div>
					</div>
					<?php
					break;
				// IF the code provided means the code is now verified ...
				case ER_CODE_VERIFIED:
				?>
					<div class="green-box">
						<div class="alert_response" align="center">
                                                        Account successfully verified!
							<?php //echo Kohana::lang('alerts.code_verified'); ?>
						</div>
					</div>
					<?php
					break;
			} // End switch
			?>
      </div>
	<!-- end block -->
	</div>
</div>

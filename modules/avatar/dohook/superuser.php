<?php
		if ($session['user']['superuser'] & SU_AUDIT_MODERATION) {
			addnav("Validations");
			addnav("Validate Avatars","runmodule.php?module=avatar&op=validate");
		}
?>
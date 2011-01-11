<?php

//make this bigger
$things = array(
	"Big Ben borrowed your computer for two minutes and filled it up with viruses and really freaky porn.",
	"Big Ben keeps forwarding chain emails to you.",
	"Big Ben made your kitten cry so he could lick its tears.",
);

//you don't have to specify the length of the array to get a random val, this does it for you
$chosenthing = array_rand($things);

print ("<html xmlns=\"http://www.w3.org/1999/xhtml\"><head><title>Big Ben raped your bicycle</title><link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\"></head><body>");
print ("<a href=\"\">".$things[$chosenthing]."</a>");
print ("</body></html>");

?>
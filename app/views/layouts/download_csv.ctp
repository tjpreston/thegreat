<?php
header( 'Content-Type: text/csv' );
header( 'Content-Disposition: attachment;filename='.$filename);
echo $content_for_layout;
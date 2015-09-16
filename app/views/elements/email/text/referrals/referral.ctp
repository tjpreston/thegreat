<?php extract($data); ?>
Your friend, <?php echo $sender_name; ?>, has recommended you look at a product on <?php echo Configure::read('Site.name'); ?>.

http://<?php echo $_SERVER['HTTP_HOST']; ?>/<?php echo $record['ProductMeta']['url']; ?> 

<?php if (!empty($message)): ?>
Your friend also left you this message:
<?php echo $message; ?> 
<?php endif; ?>

Hope you'll take a look!


<?php echo Configure::read('Site.name'); ?> 
http://<?php echo $_SERVER['HTTP_HOST']; ?>


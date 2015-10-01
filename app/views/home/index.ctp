<?php ?>

<div class="grid_24 banners">
    <ul>
        <li>
            <div class="banner" style="background-image: url('/img/banners/home1.jpg');">
                <div class="text">
                    <!-- Homepage text is now stored in the DB -- TJP -->
                    <h2><span class="face1"><?php echo $pagedata['Staticpage']['header_1']; ?></span> <span class="face2"><?php echo $pagedata['Staticpage']['header_2']; ?></span></h2>
                    <p><?php echo $pagedata['Staticpage']['header_3']; ?></p>
                </div>
            </div>
        </li>
    </ul>
</div>

<div class="grid_12 intro">
        <!-- <h2><span class="face1">It's</span> <span class="face2">British Quality</span></h2> -->
    <p class="dropcap intro">
		<?php echo $pagedata['Staticpage']['description_1']; ?>
    </p>
</div>

<a href="/gift-finder">
    <div class="grid_12" id="gift-finder">
        <h2><span class="face1">Gift</span><span class="face2">Finder</span></h2>
        <p>If it's a gift you’re looking for,<br /> we can help you search.</p>
    </div>
</a>

<div class="grid_24 featured-categories">
    <a href="/home">
        <div class="grid_6 alpha">
            <div class="image">
                <img src="/img/featured-categories/homeware.jpg" alt="Home" />
            </div>
                <!--<h3 class="border-top-bottom"><span class="face1">Dazzling</span> <span class="face2">Home</span></h3>-->

                <!-- Hacktastic way of reproducing the above allowing text to be editable - TJP 25/9/15 -->
                <?php   $token = strtok($pagedata['Staticpage']['category_text_1'], " ");
                        echo '<h3 class="border-top-bottom"><span class="face1">' . $token . '</span>'  
                              . ' '  . '<span class="face2">' . strtok(" ") . '</span></h3>'; 
                ?>


        </div>
    </a>
    <a href="/jewellery">
        <div class="grid_6">
            <div class="image">
                <img src="/img/featured-categories/jewellery.jpg" alt="Sparking Jewellery" />
            </div>
            <!--<h3 class="border-top-bottom"><span class="face1">Sparkling</span> <span class="face2">Jewellery</span></h3>-->
                <?php   $token = strtok($pagedata['Staticpage']['category_text_2'], " ");
                        echo '<h3 class="border-top-bottom"><span class="face1">' . $token . '</span>'  
                              . ' '  . '<span class="face2">' . strtok(" ") . '</span></h3>'; 
                ?>        
        </div>
    </a>
    <!-- Originally they left the front page with a broken href: <a href="#"> -->
    <a href="baby-child-toys">
        <div class="grid_6">
            <div class="image">
                <img src="/img/featured-categories/Baby-Child.jpg" alt="Baby and Child" />
            </div>
            <!--<h3 class="border-top-bottom"><span class="face1">Cute</span> <span class="face2">Baby/Child</span></h3>-->
                <?php   $token = strtok($pagedata['Staticpage']['category_text_3'], " ");
                        echo '<h3 class="border-top-bottom"><span class="face1">' . $token . '</span>'  
                              . ' '  . '<span class="face2">' . strtok(" ") . '</span></h3>'; 
                ?>
        </div>
    </a>
    <a href="/bath-body">
        <div class="grid_6 omega">
            <div class="image">
                <img src="/img/featured-categories/Bath-Body.jpg" alt="Bath and Body" />
            </div>
            <!--<h3 class="border-top-bottom"><span class="face1">Fresh</span> <span class="face2">Bath/Body</span></h3>-->
                <?php   $token = strtok($pagedata['Staticpage']['category_text_4'], " ");
                        echo '<h3 class="border-top-bottom"><span class="face1">' . $token . '</span>'  
                              . ' '  . '<span class="face2">' . strtok(" ") . '</span></h3>'; 
                ?>
        </div>
    </a>
</div>
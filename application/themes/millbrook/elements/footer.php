<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

</div>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <?php
                $columnOne = new GlobalArea('Footer - Column 1');
                if ($columnOne->getTotalBlocksInArea($c) > 0) {
                    $columnOne->display($c);
                } else {
                    ?>
                    <p class="footer-eyebrow">Millbrook Church</p>
                    <h3>A local church shaped by worship, prayer, and welcome.</h3>
                    <p>
                        We gather to follow Jesus together and serve our community with hope, hospitality,
                        and practical love.
                    </p>
                    <?php
                }
                ?>
            </div>

            <div class="footer-col">
                <?php
                $columnTwo = new GlobalArea('Footer - Column 2');
                if ($columnTwo->getTotalBlocksInArea($c) > 0) {
                    $columnTwo->display($c);
                } else {
                    ?>
                    <h4>Gatherings</h4>
                    <ul class="footer-links">
                        <li><span>Sunday Worship</span><strong>11:00am</strong></li>
                        <li><span>Home Group</span><strong>Every other Thursday, 7:30pm</strong></li>
                    </ul>
                    <?php
                }
                ?>
            </div>

            <div class="footer-col">
                <?php
                $columnThree = new GlobalArea('Footer - Column 3');
                if ($columnThree->getTotalBlocksInArea($c) > 0) {
                    $columnThree->display($c);
                } else {
                    ?>
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="/#home-vision">Vision</a></li>
                        <li><a href="/resources/sermons">Teachings</a></li>
                        <li><a href="/#home-next-steps">Visit</a></li>
                        <li><a href="/giving">Giving</a></li>
                    </ul>
                    <?php
                }
                ?>
            </div>

            <div class="footer-col">
                <?php
                $columnFour = new GlobalArea('Footer - Column 4');
                if ($columnFour->getTotalBlocksInArea($c) > 0) {
                    $columnFour->display($c);
                } else {
                    ?>
                    <h4>Contact</h4>
                    <p>
                        Millbrook Community Centre<br>
                        Drumahoe Road<br>
                        Millbrook<br>
                        BT40 2PF
                    </p>
                    <p>
                        <a href="mailto:info@millbrooknazarene.co.uk">info@millbrooknazarene.co.uk</a>
                    </p>
                    <?php
                }
                ?>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Millbrook Church of the Nazarene. All rights reserved.</p>
            <p>Millbrook Community Centre, Drumahoe Road, Millbrook</p>
        </div>
    </div>
</footer>

<?php Loader::element('footer_required'); ?>
<script src="<?php echo $view->getThemePath(); ?>/js/main.js"></script>

</body>
</html>
